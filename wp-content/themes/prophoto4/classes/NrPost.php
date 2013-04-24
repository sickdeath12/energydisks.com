<?php

abstract class NrPost {


	protected $id;
	protected $title;
	protected $tags = 'not yet requested';
	protected $wpObj;
	protected $categories;
	protected $author;
	protected $unfilteredContent;
	protected $content;
	protected $excerpt;
	protected $mobileExcerpt;
	protected $firstImgTag;
	protected $comments;
	protected static $globalPost;
	protected static $ImgTagClass = 'NrImgTag';


	public static function fromGlobal() {
		if ( self::$globalPost === null ) {
			global $post;
			if ( empty( $post ) || !isset( $post->ID ) ) {
				self::error( 'Post requested from global var when none available in NrPost::fromGlobal()' );
				return false;
			} else {
				self::$globalPost = class_exists( 'ppPost' ) ? new ppPost( $post->ID ) : new Post( $post->ID );
			}
		}
		return self::$globalPost;
	}


	public function __construct( $input ) {

		if ( is_object( $input ) ) {
			$this->wpObj = $input;
			$this->id = $this->wpObj->ID;

		} else if ( is_numeric( $input ) ) {
			$this->id = intval( $input );
			$this->wpObj = get_post( $this->id );
			if ( !is_object( $this->wpObj ) ) {
				self::error( "Failure to retrieve WordPress post object for ID $this->id" );
				return;
			}

		} else if ( is_string( $input) ) {
			global $wpdb;
			$article = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE post_name = '$input'" );
			if ( $article ) {
				$wpObj = get_page( $article );
				if ( is_object( $wpObj ) ) {
					$this->wpObj = $wpObj;
					$this->id = $this->wpObj->ID;
					return;
				}
			}

			self::error( 'Invalid input to NrPost::__construct()' );
		}
	}


	public static function setGlobalPost( ppPost $post ) {
		self::$globalPost = $post;
	}


	public static function resetGlobalPost() {
		self::$globalPost = null;
	}


	public function id() {
		return $this->id;
	}


	public function title() {
		if ( $this->title == null ) {
			$this->title = get_the_title( $this->id );
		}
		return $this->title;
	}


	public function type() {
		return $this->wpObj->post_type;
	}


	public function categoryIDs() {
		if ( $this->categories == null ) {
			$this->categories();
		}
		return array_map( create_function( '$cat', 'return $cat->id();' ), $this->categories );
	}


	public function categoryNames() {
		if ( $this->categories == null ) {
			$this->categories();
		}
		return array_map( create_function( '$cat', 'return $cat->name();' ), $this->categories );
	}


	protected function categories() {
		$wpCategories = get_the_category( $this->id() );
		$this->categories = array();
		foreach ( $wpCategories as $wpCategory ) {
			$this->categories[] = new ppCategory( $wpCategory );
		}
	}


	abstract public function excerpt();


	public function content() {
		if ( $cachedContent = $this->cache( 'content' ) ) {
			return $cachedContent;
		} else {
			$content = str_replace( ']]>', ']]&gt;', $this->filteredContent() );
			$this->cache( 'content', $content );
			return $content;
		}
	}


	protected function filteredContent() {
		if ( $cachedFiltered = $this->cache( 'filtered_content' ) ) {
			return $cachedFiltered;
		} else {
			$filtered = apply_filters( 'the_content', $this->unfilteredContent() );
			$this->cache( 'filtered_content', $filtered );
			return $filtered;
		}
	}


	protected function partiallyFilteredContent() {
		return $this->filteredContent();
	}


	abstract public function render();


	public function permalink() {
		return get_permalink( $this->id );
	}


	public function authorName() {
		if ( !$this->author ) {
			$this->author = get_userdata( $this->wpObj->post_author );
		}
		return $this->author->display_name;
	}


	public function authorProfileUrl() {
		if ( !$this->author ) {
			$this->author = get_userdata( $this->wpObj->post_author );
		}
		return $this->author->user_url;
	}


	public function authorArchiveUrl() {
		if ( !$this->author ) {
			$this->author = get_userdata( $this->wpObj->post_author );
		}
		return get_author_posts_url( $this->authorID(), $this->author->user_nicename );
	}


	public function authorDesc() {
		if ( !$this->author ) {
			$this->author = get_userdata( $this->wpObj->post_author );
		}
		return $this->author->user_description;
	}


	public function authorID() {
		return (int) $this->wpObj->post_author;
	}


	public function password() {
		return $this->wpObj->post_password;
	}


	public function passwordRequired() {
		if ( !$this->password() ) {
			return false;
		} else {
			return !NrUtil::COOKIE( 'wp-postpass_' . COOKIEHASH, $this->password() );
		}
	}


	public function comments() {
		$this->error( 'No functionality coded yet for NrPost::comments(), see ppPost for example.' );
		return array();
	}


	public function commentsOpen() {
		$open = ( 'open' == $this->wpObj->comment_status );
		return apply_filters( 'comments_open', $open, $this->id );
	}


	public function commentsClosed() {
		return !$this->commentsOpen();
	}


	public function commentsCount() {
		return apply_filters( 'get_comments_number', count( $this->comments() ), $this->id );
	}


	public function publishedDate( $dateFormat = null ) {
		$raw = $this->wpObj->post_date ? $this->wpObj->post_date : $this->wpObj->post_date_gmt;
		if ( $dateFormat ) {
			return mysql2date( $dateFormat, $raw );
		} else {
			return $raw;
		}
	}


	public function tags() {
		if ( $this->tags === 'not yet requested' ) {
			$tagObjects = get_the_tags( $this->id );
			$this->tags = array();
			if ( false === $tagObjects || empty( $tagObjects ) ) {
				return $this->tags;
			}
			foreach ( $tagObjects as $tag ) {
				$this->tags[] = $tag->name;
			}
		}
		return $this->tags;
	}


	public function slug() {
		return $this->wpObj->post_name;
	}


	protected function firstImgTag() {
		if ( $this->firstImgTag === null ) {

			preg_match_all( "/<img[^>]+src=(?:\"|')([^\"']+)(?:\"|')[^>]+>/i", $this->partiallyFilteredContent(), $matches  );

			if ( empty( $matches ) || !isset( $matches[1] ) ) {
				$this->firstImgTag = false;

			} else {

				foreach ( (array) $matches[1] as $index => $src ) {
					if ( class_exists( 'pp' ) ) {
						$src = preg_replace( '/^(?:\.\.)?(?:\/)?wp-content/', pp::site()->wpurl . '/wp-content', $src );
					}
					if ( $this->unusableExcerptSrc( $src ) ) {
						continue;
					} else {
						$imgTag = call_user_func( array( self::$ImgTagClass, 'createFromHtml' ), $matches[0][$index] );
						$imgTag->src( $src );
						break;
					}
				}

				if ( !isset( $imgTag ) || !is_object( $imgTag ) ) {
					$this->firstImgTag = false;

				} else {
					$this->firstImgTag = $imgTag;
				}
			}
		}

		return $this->firstImgTag;
	}


	public function hasFeaturedImg() {
		if ( !function_exists( 'has_post_thumbnail' ) ) {
			require_once( ABSPATH . '/wp-includes/post-thumbnail-template.php' );
		}
		return has_post_thumbnail( $this->id );
	}


	public function featuredImgSrc( $size = 'fullsize' ) {
		if ( $this->hasFeaturedImg() ) {
			$featuredImg = $this->newPostImg( get_post_thumbnail_id( $this->id ) );
			if ( $size == 'fullsize' ) {
				return $featuredImg->src();
			} else {
				$thumb = $featuredImg->thumb( $size );
				return $thumb ? $thumb->src() : '';
			}
		} else {
			return '';
		}
	}


	public function featuredImgTag( $size = 'fullsize' ) {
		if ( !$this->hasFeaturedImg() ) {
			return '';
		} else {
			$featuredImg = $this->newPostImg( get_post_thumbnail_id( $this->id ) );
			$featuredImgTag = $featuredImg->tagObj();
			$featuredImgTag->addClass( 'pp-featured-img' );
			$featuredImgTag->addClass( "pp-featured-img-$size" );
			return $featuredImgTag->markup();
		}
	}


	public function excerptImgSrc( $size = 'fullsize' ) {
		if ( $featuredImgSrc = $this->featuredImgSrc( $size ) ) {
			return $featuredImgSrc;

		} else {
			if ( class_exists( 'ppOpt' ) && ppOpt::test( 'dig_for_excerpt_image', 'false' ) ) {
				return '';

			} else if ( !$this->firstImgTag() ) {
				return '';

			} else {
				$excerptImg = $this->findRelatedImg( $this->firstImgTag()->src(), $size );

				if ( 'fullsize' == $size && !$excerptImg ) {
					return $this->firstImgTag()->src();

				} else {
					return $excerptImg ? $excerptImg->src() : '';
				}
			}
		}
	}


	public function excerptImgTag( $size = 'fullsize' ) {
		$excerptImgSrc = $this->excerptImgSrc( $size );
		if ( $excerptImgSrc ) {
			$ImgTagClass = self::$ImgTagClass;
			$excerptImg = new $ImgTagClass( $excerptImgSrc );
			$excerptImg->addClass( 'excerpt-img' );
			$excerptImg->addClass( "excerpt-img-{$size}" );
			return $excerptImg->markup();
		} else {
			return '';
		}
	}


	public function rawContent() {
		return $this->wpObj->post_content;
	}


	public function rawExcerpt() {
		return $this->wpObj->post_excerpt;
	}


	public function unfilteredContent() {
		if ( $cachedUnfiltered = $this->cache( 'unfiltered_content' ) ) {
			return $cachedUnfiltered;
		} else {
			$this->ensureWpGlobalsSet();
			$unfilteredContent = get_the_content( $this->readMoreText() );
			$this->cache( 'unfiltered_content', $unfilteredContent );
			return $unfilteredContent;
		}
	}


	protected function cache( $type, $set = null ) {
		if ( $set == null ) {
			if ( NrUtil::GET( 'preview', 'true' ) && isset( $_GET['preview_id'] ) ) {
				return false;
			}
			$cached = wp_cache_get( 'pp_article_' . $type . '_' . $this->id(), 'pp_theme' );
			if ( $cached && is_string( $cached ) ) {
				return $cached;
			} else {
				return false;
			}
		} else {
			wp_cache_add( 'pp_article_' . $type . '_' . $this->id(), $set, 'pp_theme' );
		}
	}


	public function wpObj() {
		return $this->wpObj;
	}


	public function resetAuthor() {
		$this->author = null;
	}


	abstract protected function readMoreText();


	protected function ensureWpGlobalsSet() {
		unset( $GLOBALS['post'] );
		$GLOBALS['post'] = $this->wpObj;
		setup_postdata( $this->wpObj );
	}


	public function flushCache() {
		$this->content = null;
		$this->excerpt = null;
		wp_cache_delete( 'pp_post_content_' . $this->id() );
		wp_cache_delete( 'pp_post_excerpt_' . $this->id() );
	}



	protected function findRelatedImg( $src, $size ) {
		self::error( 'No functionality yet for NrPost::findRelatedImg(), see in ppPost.' );
		return '';
	}


	protected function newPostImg( $ID ) {
		return new NrPostImg( $ID );
	}


	protected function unusableExcerptSrc( $src ) {
		if ( NrUtil::isIn( '/smilies/',  $src ) ) {
			return true;
		} else if ( NrUtil::isIn( '/blank.gif', $src ) ) {
			return true;
		} else if ( NrUtil::isIn( 'pp-grid-placeholder-', $src ) ) {
			return true;
		} else if ( !NrUtil::startsWith( $src, 'http' ) ) {
			return true;
		} else if ( !NrUtil::isWebSafeImg( $src ) ) {
			return true;
		} else {
			return false;
		}
	}


	protected static function error( $msg ) {
		if ( class_exists( 'ppIssue') ) {
			new ppIssue( $msg, 'tech' );
		} else {
			trigger_error( $msg, E_USER_WARNING );
		}
	}


	public static function clearGlobalPost() {
		self::$globalPost = null;
	}

}

