<?php

class ppPostHeader {


	private $post;
	private $dateBeforeTitle;
	private $dateSameLineWithTitle;
	private $dateInMeta;


	public static function render( ppPost $post ) {
		$postHeader = new ppPostHeader( $post );
		echo $postHeader->completeMarkup();
	}


	public function completeMarkup() {
		return
			'<div class="article-header ' . ppOpt::id( 'postdate_display' ) . '" data-role="header">' .
				$this->dateBeforeTitle .
				$this->titleDiv() .
				$this->entryMeta() .
			'</div>';
	}


	protected function __construct( ppPost $post ) {
		$this->post = $post;
		$this->setupDateProperties();
	}


	protected function titleDiv() {
		$linkedTitle = NrHtml::a(
			$this->post->permalink(),
			$this->post->title(),
			'title=Permalink to ' . esc_attr( $this->post->title() ) . '&rel=bookmark'
		);

		$title = ( is_singular() || is_404() ) ? $this->post->title() : $linkedTitle;

		if ( $this->editLinkBeforeTitle() ) {
			$title = ppUtil::ob( 'edit_post_link', 'Edit' ) . $title;
		} else {
			$title .= ppUtil::ob( 'edit_post_link', 'Edit' );
		}

		return
			'<div class="article-title-wrap">' .
				$this->dateSameLineWithTitle .
				NrHtml::h( $this->headerTagNum(), $title, 'class=article-title' ) .
			'</div>';
	}


	protected function entryMeta() {
		if ( is_page() || is_404() || ppUtil::isEmptySearch() ) {
			return;
		}

		$metaElements  = '';
		if ( !is_search() ) {
			$metaElements .= $this->categoryList();
			$metaElements .= $this->tagList();
			$metaElements .= $this->commentCount();
		}

		if ( $this->dateInMeta || $metaElements ) {
			return
				'<div class="article-meta article-meta-top">' .
				 	$this->dateInMeta . $metaElements .
				'</div>';
		}
	}


	protected function dateMarkup() {
		return
			'<span class="article-date article-meta-item">
				<span>' .
					$this->publishedDay() . ' ' . $this->publishedTime() .
				'</span>
			</span>';
	}


	protected function publishedDay() {
		if ( ppOpt::test( 'postdate_display', 'boxy' ) ) {
			$day   = $this->post->publishedDate( 'd' );
			$month = $this->post->publishedDate( 'M' );
			$year  = $this->post->publishedDate( 'Y' );
			return
				"<div class='boxy-date-wrap'>
					<span class='boxy-month'>$month</span>
					<span class='boxy-day'>$day</span>
					<span class='boxy-year'>$year</span>
				</div>";

		} else if ( ppOpt::test( 'dateformat', 'custom' ) ) {
			 return $this->post->publishedDate( ppOpt::id( 'dateformat_custom' ) );

		} else {
			return $this->post->publishedDate( ppOpt::id( 'dateformat' ) );
		}
	}


	protected function publishedTime() {
		if ( ppOpt::test( 'show_post_published_time', 'yes' ) && !ppOpt::test( 'postdate_display', 'boxy' ) ) {
			return get_the_time();
		}
	}


	protected function headerTagNum() {
		if ( is_singular() ) {
			return '1';
		} elseif ( !is_archive() ) {
			return '2';
		} else {
			return '3';
		}
	}


	protected function categoryList() {
		if ( ppOpt::test( 'categories_in_post_header', 'yes' ) ) {
			return ppHtml::categoryList();
		}
	}


	protected function tagList() {
		if ( ppOpt::test( 'tags_in_post_header', 'yes' ) ) {
			return ppHtml::tagList();
		}
	}


	protected function commentCount() {
		if ( ppOpt::test( 'comment_count_in_post_header', 'yes' ) && ppOpt::test( 'comments_enable', 'true' ) ) {
			return '<span class="article-header-comment-count article-meta-item">' . ucfirst( ppCommentsRenderer::countString( $this->post->commentsCount() ) ) . '</span>';
		}
	}


	protected function setupDateProperties() {
		if ( is_page() || is_404() || ppUtil::isEmptySearch() ) {
			return;
		}

		if ( ppOpt::test( 'postdate_display', 'boxy' ) ) {
			$this->dateBeforeTitle = $this->dateMarkup();

		} else if ( ppOpt::test( 'postdate_display', 'normal' ) )  {

			switch ( ppOpt::id( 'postdate_placement' ) ) {

				case 'above':
					$this->dateBeforeTitle = $this->dateMarkup();
					break;
				case 'below':
					$this->dateInMeta = $this->dateMarkup();
					break;
				case 'withtitle' :
					$this->dateSameLineWithTitle = $this->dateMarkup();
					break;
				default:
					new ppIssue( 'Unknown "postdate_placement"' );
			}
		}
	}


	protected function editLinkBeforeTitle() {
		if ( !ppOpt::test( 'post_header_align', 'right' ) ) {
			return false;

		} else if ( ppOpt::test( 'postdate_display', 'boxy' ) ) {
			return true;

		} else if ( !ppOpt::test( 'postdate_placement', 'withtitle' ) ) {
			return true;

		} else {
			return false;
		}
	}


	public static function advancedDateCss( $selector = '.article-date span' ) {
		if ( ppOpt::test( 'postdate_advanced_switch', 'off' ) || ppOpt::test( 'postdate_display', 'boxy' ) ) {
			return;
		}

		$postdate_border_width = ppOpt::id( 'postdate_border_width' );
		$postdate_border_top   = ppOpt::test( 'postdate_border_top', 'on' )    ? $postdate_border_width : '0';
		$postdate_border_btm   = ppOpt::test( 'postdate_border_bottom', 'on' ) ? $postdate_border_width : '0';
		$postdate_border_left  = ppOpt::test( 'postdate_border_left', 'on' )   ? $postdate_border_width : '0';
		$postdate_border_right = ppOpt::test( 'postdate_border_right', 'on' )  ? $postdate_border_width : '0';

		return <<<CSS
		$selector {
			margin-right:0;
			border-top-width:{$postdate_border_top}px;
			border-bottom-width:{$postdate_border_btm}px;
			border-left-width:{$postdate_border_left}px;
			border-right-width:{$postdate_border_right}px;
			border-style:[~postdate_border_style];
			border-color:[~postdate_border_color];
			background-color:[~postdate_bg_color];
			padding:[~postdate_tb_padding]px [~postdate_lr_padding]px;
		}
CSS;
	}
}
