<?php


class ppCommentsRenderer {


	protected $article;
	protected $articleComments;
	protected $articleCommentsCount;
	protected $query;


	public static function countString( $numberComments ) {
		$numberCommentsString = ( $numberComments == 0 ) ? ppOpt::translate( 'no' ) : strval( $numberComments );
		$numberCommentsString = apply_filters( 'comments_number', $numberCommentsString, $numberComments );
		$commentsWord = ( $numberComments != 1 ) ? ppOpt::translate( 'comments' ) : ppOpt::translate( 'comment' );
		return $numberCommentsString . ' ' . $commentsWord;
	}


	public function __construct( ppPost $article, ppQuery $query ) {
		$this->article = $article;
		$this->query   = $query;
	}


	public function render() {
		global $withcomments;

		if ( !$this->doRenderHere() ) {
			$withcomments = false;
			return;
		}

		do_action( 'pp_pre_comments_area' );

		// we call this here for plugins that hook to it internally and for
		// forward compatibility, passing it a blank view and
		// $withcomments = true to ensure that comments_template() runs completely
		$withcomments = true;
		comments_template( $ppFile = '/views/comments_template_blank.php' );

		// don't render comments area if we detect plugin is using it's own comments template file
		if ( NrUtil::isIn( $ppFile, apply_filters( 'comments_template', $ppFile ) ) ) {
			$this->renderViews();
		}

		do_action( 'pp_post_comments_area' );
	}


	public function renderMobile() {
		if ( $this->doRenderHere() && $this->query->isArticle() ) {
			$this->renderViews( 'mobile_' );
		}
	}


	protected function renderViews( $prefix = '' ) {
		if ( ppOpt::test( 'fb_comments_enable', 'true' ) ) {

			// periodically look for missing Facebook comments that fell through the cracks
			$apiReader = new ppFacebookAPIReader( new WpHttpRequest( new WP_Http() ) );
			ppFacebookCommentsHandler::instance()->syncMissingArticleComments( $this->article, $apiReader );

			echo ppFacebook::commentsMarkup( $this->article, $this->query );
		}

		if ( ppOpt::test( 'fb_comments_enable', 'false' ) || ppOpt::test( 'fb_comments_also_show_unique_wp', 'true' ) ) {
			$this->renderView( $prefix . 'comments_area' );
		}

		if ( $this->showAddCommentForm() ) {
			$this->renderView( 'comment_form' );
		}
	}


	public function areaClasses() {
		$areaClasses   = array( 'article-comments', 'entry-comments' ); // entry-comments for plugin compat
		$areaClasses[] = 'layout-' . ppOpt::id( 'comments_layout' );
		$areaClasses[] = $this->articleCommentsCount() ? 'has-comments' : 'no-comments';
		$areaClasses[] = $this->article->commentsOpen() ? 'accepting-comments' : 'not-accepting-comments';
		$areaClasses[] = ppOpt::test( 'comments_show_avatars', 'true' ) ? 'with-avatars' : 'no-avatars';

		if ( ppOpt::test( 'fb_comments_enable', 'true' ) && ppOpt::test( 'fb_comments_add_new', 'fb_only' ) && $this->articleCommentsCount() === 0 ) {
			$areaClasses[] = 'comments-area-hidden';
		}

		if ( $this->query->isBlogPostsPage() ) {
			$commentsShown = ppOpt::test( 'comments_on_home_start_hidden', 'false' );
		} else if ( !$this->query->isArticle() ) {
			$commentsShown = ppOpt::test( 'comments_on_archive_start_hidden', 'false' );
		} else {
			$commentsShown = true;
		}
		$areaClasses[] = $commentsShown ? 'comments-shown' : 'comments-hidden';
		return implode( ' ', $areaClasses );
	}


	public function articleComments() {
		if ( null === $this->articleComments ) {
			$this->articleComments = $this->article->comments();
			if ( ppOpt::test( 'reverse_comments', 'true' ) ) {
				$this->articleComments = array_reverse( $this->articleComments );
			}
			if ( ppOpt::test( 'fb_comments_enable', 'true' ) ) {
				$this->articleComments = ppFacebookCommentsHandler::instance()->addClasses( $this->articleComments, $this->article );
			}
		}
		return $this->articleComments;
	}


	public function articleCommentsCount() {
		if ( null === $this->articleCommentsCount ) {
			$count = apply_filters( 'get_comments_number', count( $this->articleComments() ), $this->article->id() );
			if ( $count > 0 && ppOpt::test( 'fb_comments_enable', 'true' ) ) {
				foreach ( $this->articleComments() as $comment ) {
					if ( NrUtil::isIn( 'added-via-fb', $comment->classes() ) && !NrUtil::isIn( 'from-fb-legacy-permalink', $comment->classes() ) ) {
						$count--;
					}
				}
			}
			$this->articleCommentsCount = $count;
		}
		return $this->articleCommentsCount;
	}


	public function countMarkup() {
		$markup = '';
		if ( ppOpt::test( 'comments_layout', 'minima' ) && ppOpt::test( 'comments_show_hide_method', 'text' ) && $this->articleCommentsCount() > 0 ) {
			$markup .= '<span class="show-text">' . ppOpt::id( 'comments_minima_show_text' ) . ' </span>';
			$markup .= '<span class="hide-text">' . ppOpt::id( 'comments_minima_hide_text' ) . ' </span>';
		}
		return $markup .= self::countString( $this->articleCommentsCount() );
	}


	public function interactLinksMarkup() {
		$title  = '';
		$markup = '';

		if ( !$this->query->isArticle() && ( ppOpt::test( 'fb_comments_enable', 'false' ) || !ppOpt::test( 'fb_comments_add_new', 'fb_only' )) ) {
			$id      = 'addacomment';
			$url     = $this->article->permalink() . '#addcomment';
			$text    = ppOpt::id( 'comments_header_addacomment_link_text' );
			$markup .= ppUtil::renderView( 'comments_interact_link', compact( 'id', 'url', 'text', 'title' ), ppUtil::RETURN_VIEW );
		}

		if ( ppOpt::test( 'comments_header_linktothispost_link_include', 'yes' ) ) {
			$id      = 'linktothispost';
			$url     = $this->article->permalink();
			$text    = ppOpt::id( 'comments_header_linktothispost_link_text' );
			$title   = 'Permalink to' . $this->article->title();
			$markup .= ppUtil::renderView( 'comments_interact_link', compact( 'id', 'url', 'text', 'title' ), ppUtil::RETURN_VIEW );
		}

		if ( ppOpt::test( 'comments_header_emailafriend_link_include', 'yes' ) ) {
			$id      = 'emailafriend';
			$url     = ppHtml::emailFriendHref( $this->article, 'comments_header' );
			$text    = ppOpt::id( 'comments_header_emailafriend_link_text' );
			$markup .= ppUtil::renderView( 'comments_interact_link', compact( 'id', 'url', 'text', 'title' ), ppUtil::RETURN_VIEW );
		}

		return $markup;
	}


	protected function showAddCommentForm() {
		if ( !$this->query->isArticle() ) {
			return false;
		}
		if ( ppOpt::test( 'fb_comments_enable', 'true' ) ) {
			if ( ppOpt::test( 'fb_comments_also_show_unique_wp', 'false' ) ) {
				return false;
			} else {
				return ppOpt::test( 'fb_comments_add_new', 'fb_and_wp' );
			}
		} else {
			return $this->article->commentsOpen();
		}
	}


	protected function renderView( $view ) {
		ppUtil::renderView( $view, array( 'article' => $this->article, 'commentsRenderer' => $this ) );
	}


	protected function doRenderHere() {
		if ( $this->query->is404() ) {
			return false;

		} else if ( $this->query->isGalleryQuasiPage() ) {
			return false;

		} else if ( !apply_filters( 'pp_comments_enable', ppOpt::id( 'comments_enable', 'bool' ) ) ) {
			return false;

		} else if ( $this->article->passwordRequired() ) {
			return false;

		} else if ( $this->article->commentsClosed() && !$this->articleCommentsCount() ) {
			return false;

		} else if ( $this->query->isArchive() && ppOpt::test( 'comments_show_on_archive', 'false' ) ) {
			return false;

		} else {
			return true;
		}
	}

}

