<?php

class ppSeo {


	const MAX_RECOMMENDED_META_DESC_LENGTH = 300;
	protected $desc;
	protected $article;
	protected $query;


	public function __construct( ppQuery $query, $article ) {
		$this->query = $query;
		$this->article = $article;
	}


	public function titleTag() {
		return NrHtml::title( $this->disabled() ? $this->wpTitle() : $this->title() );
	}


	protected function title() {
		$wpTitle = $this->wpTitle();
		$articleTitle = $this->query->isArticle() ? $this->article->title() : $wpTitle;

		$unprocessedTitle = ppOpt::id( 'seo_title_' . $this->query->typeStringSpecific() );

		if ( $this->query->isPaged() ) {
			$unprocessedTitle .= ' &raquo; page ' . $this->query->pagedNumber();
		}

		$searchReplace = array(
			'%blog_name%'        => pp::site()->name,
			'%blog_description%' => pp::site()->tagline,
			'%post_title%'       => $articleTitle,
			'%page_title%'       => $articleTitle,
			'%category_name%'    => $wpTitle,
			'%archive_date%'     => $wpTitle,
			'%tag_name%'         => $wpTitle,
			'%author_name%'      => $wpTitle,
			'%search_query%'     => $this->query->searchedFor(),
			'»'                  => '&raquo;',
			'«'                  => '&laquo;',
			'›'                  => '&rsaquo;',
			'‹'                  => '&lsaquo;',
			'©'                  => '&copy;',
		);

		return strip_tags( str_replace( array_keys( $searchReplace ), array_values( $searchReplace ), $unprocessedTitle ) );
	}


	public function desc() {
		if ( $this->desc !== null ) {
			return $this->desc;
		}

		$desc = null;

		if ( $this->query->isArticle() && $this->article ) {

			if ( ppOpt::test( 'seo_meta_use_excerpts', 'true' ) && $this->article->rawExcerpt() != '' ) {
				$desc = $this->article->rawExcerpt();
			}

			if ( $desc == null && ppOpt::test( 'seo_meta_auto_generate', 'true' ) ) {
				$content = $this->article->unfilteredContent();

				if ( strlen( $content ) > self::MAX_RECOMMENDED_META_DESC_LENGTH ) {
					$checkCharAt = self::MAX_RECOMMENDED_META_DESC_LENGTH;

					while ( $content[$checkCharAt] != ' ' && $checkCharAt > 0 ) {
						$checkCharAt--;
					}

					$desc = substr( $content, 0, $checkCharAt );
					if ( $desc ) {
						$desc .= '&#8230;';
					}

				} else {
					$desc = $content;
				}
			}
		}

		if ( $desc == null || trim( $desc ) === '' ) {
			$desc = ppOpt::orVal( 'seo_meta_desc', pp::site()->tagline ? pp::site()->tagline : pp::site()->name );
		}

		$desc = self::sanitizeMetaDesc( $desc );

		if ( $this->query->isCategoryArchive() ) {
			$desc = ppOpt::translate( 'category_archives' ) . ' ' . $this->wpTitle() . ' - ' . $desc;
		} else if ( $this->query->isAuthorArchive() ) {
			$desc = ppOpt::translate( 'author_archives' ) .   ' ' . $this->wpTitle() . ' - ' . $desc;
		} else if ( $this->query->isTagArchive() ) {
			$desc = ppOpt::translate( 'tag_archives' ) .      ' ' . $this->wpTitle() . ' - ' . $desc;
		} else if ( $this->query->isMonthArchive() ) {
			$desc = ppOpt::translate( 'archives_monthly' ) .  ' ' . $this->wpTitle() . ' - ' . $desc;
		}

		if ( $this->query->isPaged() ) {
			$desc .= ' &raquo; page ' . $this->query->pagedNumber();
		}

		if ( empty( $desc ) && $this->query->isArticle() && $this->article ) {
			$desc = self::sanitizeMetaDesc( $this->article->title() );
		}

		return $this->desc = $desc;
	}


	public function metaDesc() {
		if ( $this->disabled() ) {
			return '';
		}
		$desc = $this->desc();
		return !empty( $desc ) ? NrHtml::meta( 'name', 'description', 'content', $desc ) : '';
	}


	protected function sanitizeMetaDesc( $desc ) {
		$desc = strip_tags( $desc );
		$desc = preg_replace( '|\[(.+?)\](.+?\[/\\1\])?|s', '', $desc );
		$desc = str_replace( array( "\r\n", "\r", "\n" ), '', $desc );
		$desc = trim( $desc );
		return $desc;
	}


	public function metaKeywords() {
		$userKeywordStr = trim( str_replace( array( ",\n", ", \n", "\n" ), ', ', ppOpt::id( 'seo_meta_keywords' ) ) );
		$globalKeywords = $userKeywordStr ? explode( ', ', $userKeywordStr ) : array();

		if ( $this->query->isPost() && ppOpt::test( 'seo_tags_for_keywords', 'true' ) ) {
			$postKeywords = $this->article->tags();
		} else {
			$postKeywords = array();
		}

		$keywords = array_merge( $postKeywords, $globalKeywords );

		if ( empty( $keywords ) ) {
			return '';
		}

		if ( count( $keywords ) > 10 ) {
			$keywords = array_slice( $keywords, 0, 10 );
		}

		return NrHtml::meta( 'name', 'keywords', 'content', strip_tags( implode( ', ', $keywords ) ) );
	}


	public function metaRobots() {
		$checkPageTypes  = array( 'home', 'author', 'search', 'tag', 'category', 'archive' );
		$currentPageType = $this->query->typeStringSpecific();

		foreach ( $checkPageTypes as $possiblePageType ) {
			if ( $currentPageType == $possiblePageType ) {
				if ( ppOpt::test( "noindex_$currentPageType", 'true' ) ) {
					return NrHtml::meta( 'name', 'robots', 'content', 'noindex' );
				} else {
					return '';
				}
			}
		}
	}


	protected function disabled() {
		return ppOpt::id( 'seo_disable', 'bool' ) || self::pluginDetected();
	}


	protected function wpTitle() {
		return trim( wp_title( '', NO_ECHO ) );
	}


	public static function pluginDetected() {
		if ( class_exists( 'All_in_One_SEO_Pack' ) ) {
			return 'All-in-One SEO Pack';
		} else {
			return false;
		}
	}
}
