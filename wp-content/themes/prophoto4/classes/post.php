<?php

class ppPost extends NrPost {


	protected $rawExcerpt;
	protected static $ImgTagClass = 'ppImgTag';


	public function excerpt() {
		if ( is_search() && $this->slug() == 'no-search-results' ) {
			return $this->content();
		}

		if ( $this->excerpt === null ) {
			$cachedExcerpt = wp_cache_get( 'pp_post_excerpt_' . $this->id() );
			if ( $cachedExcerpt && is_string( $cachedExcerpt ) ) {
				$this->excerpt = $cachedExcerpt;

			} else {

				$excerpt = $this->filteredExcerpt();
				$this->mobileExcerpt = preg_replace( '/<a(\s[^>]*)?>(.*?)<\/a>/i', '', $excerpt );
				$this->mobileExcerpt = preg_replace( '/<br( )?(\/)?>/i', '', $this->mobileExcerpt );

				$excerptImgBefore = '';
				$excerptImgAfter  = '';
				if ( ppOpt::test( 'show_excerpt_image', 'true' ) )  {

					$excerptImg = $this->excerptImgTag( ppOpt::id( 'excerpt_image_size' ) );
					if ( $excerptImg ) {
						$excerptImg = NrHtml::a(
							$this->permalink(),
							$excerptImg,
							'class=img-to-permalink&title=' . esc_attr( ppOpt::id( 'read_more_link_text' ) )
						);
					}

					if ( ppOpt::test( 'excerpt_image_size', 'fullsize' ) && ppOpt::test( 'excerpt_image_position', 'after_text' ) ) {
						$excerptImgAfter = $excerptImg;
					} else {
						$excerptImgBefore = $excerptImg;
					}
				}

				$this->excerpt = $excerptImgBefore . $excerpt . $excerptImgAfter . $this->readMoreLink();
				wp_cache_add( 'pp_post_excerpt_' . $this->id(), $this->excerpt, 'pp_theme' );
			}
		}

		return ( pp::browser()->isMobile && ppOpt::test( 'mobile_enable', 'true' ) ) ? $this->mobileExcerpt : $this->excerpt;
	}


	public function comments() {
		if ( $this->comments == null ) {

			/* this is all taken nearly verbatim from commments_template(), to get
			   appropriate approved/unapproved comments based on current user
			*/
			global $user_ID, $wpdb;
			$commenter = wp_get_current_commenter();

			if ( $user_ID ) {
				$comments = $wpdb->get_results( $wpdb->prepare(
					"SELECT * FROM $wpdb->comments
					 WHERE comment_post_ID = %d
					 AND ( comment_approved = '1' OR ( user_id = %d AND comment_approved = '0' ) )
					 ORDER BY comment_date_gmt",
				$this->id, $user_ID ) );

			} else if ( empty( $commenter['comment_author'] ) ) {
				$comments = get_comments( array( 'post_id' => $this->id, 'status' => 'approve', 'order' => 'ASC' ) );

			} else {
				$comments = $wpdb->get_results( $wpdb->prepare(
					"SELECT * FROM $wpdb->comments
					 WHERE comment_post_ID = %d
					 AND ( comment_approved = '1' OR ( comment_author = %s AND comment_author_email = %s AND comment_approved = '0' ) )
					 ORDER BY comment_date_gmt",
				$this->id, wp_specialchars_decode( $commenter['comment_author'],ENT_QUOTES ), $commenter['comment_author_email'] ) );
			}

			$this->comments = array();
			foreach ( (array) $comments as $wpCommentObj ) {
				$this->comments[] = new ppComment( $wpCommentObj );
			}
		}

		return (array) $this->comments;
	}


	public function render() {
		if ( !pp::browser()->isMobile || ppOpt::test( 'mobile_enable', 'false' ) ) {
			ppUtil::renderView( 'article', array( 'article' => $this ) );

		} else {
			if ( is_singular() || ppUtil::isEmptySearch() || ppGallery::isGalleryQuasiPage() ) {
				ppUtil::renderView( 'mobile_article', array( 'article' => $this ) );
			} else {
				ppUtil::renderView( 'mobile_article_excerpt', array( 'article' => $this ) );
			}
		}
	}


	public function hasSlideshowWithMusic() {
		ob_start(); /* buffer b/c bad plugins sometimes echo out junk during filter calls */
		$return = NrUtil::isIn( 'data-music-file="', $this->content() );
		ob_end_clean();
		return $return;
	}


	public function excerptImgTag( $size = 'fullsize' ) {
		$excerptImgSrc = $this->excerptImgSrc( $size );
		if ( $excerptImgSrc ) {
			$excerptImg = new ppImgTag( $excerptImgSrc );
			$excerptImg->addClass( 'pp-excerpt-img' );
			$excerptImg->addClass( "pp-excerpt-img-{$size}" );
			return ppContentFilter::modifyImgs( $excerptImg->markup() );
		} else {
			return '';
		}
	}


	public function filteredExcerpt() {
		$cachedFilteredExcerpt = wp_cache_get( 'pp_post_excerpt_filtered_' . $this->id() );
		if ( $cachedFilteredExcerpt && is_string( $cachedFilteredExcerpt ) ) {
			return $cachedFilteredExcerpt;
		} else {
			$cachedFilteredExcerpt = apply_filters( 'the_excerpt', str_replace(' [...]', '...', $this->unfilteredExcerpt() ) );
			wp_cache_add( 'pp_post_excerpt_filtered_' . $this->id(), $cachedFilteredExcerpt, 'pp_theme' );
			return $cachedFilteredExcerpt;
		}
	}


	public function readMoreLink( $tag = 'p' ) {
		$a = NrHtml::a( $this->permalink(), ppOpt::id( 'read_more_link_text' ), 'title=' . esc_attr( $this->title() ) );
		return NrHtml::tag( $tag, $a, 'class=read-more-wrap' );
	}


	protected function unfilteredExcerpt() {
		$this->ensureWpGlobalsSet();
		$cachedUnfilteredExcerpt = wp_cache_get( 'pp_post_excerpt_unfiltered_' . $this->id() );
		if ( $cachedUnfilteredExcerpt && is_string( $cachedUnfilteredExcerpt ) ) {
			return $cachedUnfilteredExcerpt;
		} else {
			$unfilteredExcerpt = str_replace( trim( strip_tags( ppOpt::id( 'post_signature') ) ), '', get_the_excerpt() );
			wp_cache_add( 'pp_post_excerpt_unfiltered_' . $this->id(), $unfilteredExcerpt, 'pp_theme' );
			return $unfilteredExcerpt;
		}
	}


	protected function readMoreText() {
		return ppOpt::id( 'read_more_link_text' );
	}


	protected function findRelatedImg( $src, $size ) {
		return ppPostImgUtil::relatedImg( $src, $size );
	}


	protected function filteredContent() {
		if ( $cachedFiltered = $this->cache( 'filtered_content' ) ) {
			return $cachedFiltered;
		} else {
			$filtered = ppContentFilter::gridMarkup( $this->partiallyFilteredContent() );
			$filtered = ppContentFilter::absolutizeImgURLs( $filtered );
			$filtered = ppContentFilter::modifyImgs( $filtered );
			$this->cache( 'filtered_content', $filtered );
			return $filtered;
		}
	}


	protected function partiallyFilteredContent() {
		if ( $cachedPartiallyFiltered = $this->cache( 'partially_filtered_content' ) ) {
			return $cachedPartiallyFiltered;
		} else {
			$partiallyFiltered = apply_filters( 'the_content', $this->unfilteredContent() );
			$partiallyFiltered = ppPathfixer::fix( $partiallyFiltered );
			$partiallyFiltered = ppContentFilter::galleryMarkup( $partiallyFiltered );
			$this->cache( 'partially_filtered_content', $partiallyFiltered );
			return $partiallyFiltered;
		}
	}


	protected function cache( $type, $set = null ) {
		// hacky workaround for incredibly poorly coded plugin linkwithin
		if ( function_exists( 'linkwithin_add_code' ) && $set === null ) {
			global $wp_query;
			if ( $wp_query->current_post + 1 == $wp_query->post_count ) {
				return false;
			}
		}
		return parent::cache( $type, $set );
	}


	protected function newPostImg( $ID ) {
		return new ppPostImg( $ID );
	}


	protected static function error( $msg ) {
		new ppIssue( $msg );
	}
}

