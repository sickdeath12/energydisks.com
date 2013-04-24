<?php

class ppFooter {


	protected static $columnInfo;


	public static function render() {
		if ( ppOpt::test( 'footer_include', 'yes' ) && ppWidgetUtil::footerHasWidgets() ) {
			self::renderMainFooter();
		}

		ppUtil::renderView( 'copyright_footer' );
	}


	protected static function renderMainFooter() {
		$cols = '';

		if ( ppWidgetUtil::areaHasWidgets( 'footer-spanning-col-top' ) ) {
			$areaMarkup = NrHtml::ul( ppWidgetUtil::areaContent( 'footer-spanning-col-top' ), "id=footer-spanning-col-top&class=footer-col footer-spanning-col" );
			$cols .=  preg_replace_callback( '/<img[^>]*>/i', 'ppFooter::maybeDownsizeFooterSpanningColImgs', $areaMarkup );
		}

		for ( $i = 1; $i <= pp::num()->maxFooterWidgetColumns; $i++ ) {
			if ( ppWidgetUtil::areaHasWidgets( "footer-col-$i" ) ) {
				$areaMarkup = NrHtml::ul( ppWidgetUtil::areaContent( "footer-col-$i" ), "id=footer-col-{$i}&class=footer-col footer-non-spanning-col" );
				$cols .= preg_replace_callback( '/<img[^>]*>/i', 'ppFooter::maybeDownsizeFooterColImgs', $areaMarkup );
			}
		}

		if ( ppWidgetUtil::areaHasWidgets( 'footer-spanning-col-btm' ) ) {
			$areaMarkup = NrHtml::ul( ppWidgetUtil::areaContent( 'footer-spanning-col-btm' ), "id=footer-spanning-col-btm&class=footer-col footer-spanning-col" );
			$cols .=  preg_replace_callback( '/<img[^>]*>/i', 'ppFooter::maybeDownsizeFooterSpanningColImgs', $areaMarkup );
		}

		echo NrHtml::div( $cols, 'id=footer&class=sc' );
	}


	protected static function maybeDownsizeFooterImgs( $match, $areaMaxWidth ) {
		return ppGdModify::constrainImgSize( ppImgTag::createFromHtml( $match[0] ), $areaMaxWidth )->markup();
	}


	protected static function maybeDownsizeFooterSpanningColImgs( $match ) {
		return self::maybeDownsizeFooterImgs( $match, self::columnInfo()->spanningWidth );
	}


	protected static function maybeDownsizeFooterColImgs( $match ) {
		return self::maybeDownsizeFooterImgs( $match, self::columnInfo()->columnWidth );
	}


	public static function userCopyright() {
		return ppOpt::orVal( 'custom_copyright', '&copy; ' . date( 'Y' ) . ' ' . pp::site()->name );
	}


	public static function columnInfo() {
		if ( null == self::$columnInfo ) {
			$columnCount = 0;
			$lastColumnNum = 1;
			for ( $i = 1; $i <= pp::num()->maxFooterWidgetColumns; $i++ ) {
				if ( ppWidgetUtil::areaHasWidgets( "footer-col-$i" ) ) {
					$columnCount++;
					$lastColumnNum = $i;
				}
			}
			$columnPadding = ppOpt::cascade( 'footer_col_padding',   'content_margin' );
			$leftPadding   = ppOpt::cascade( 'footer_left_padding',  'content_margin' );
			$rightPadding  = ppOpt::cascade( 'footer_right_padding', 'content_margin' );
			$spanningWidth = ppOpt::id( 'blog_width' ) - $leftPadding - $rightPadding;
			$workingArea   = $spanningWidth - ( ( $columnCount - 1 ) * $columnPadding );
			$columnWidth   = @intval( $workingArea / $columnCount );
			self::$columnInfo = (object) compact( 'lastColumnNum', 'columnWidth', 'rightPadding', 'leftPadding', 'columnPadding', 'spanningWidth' );
		}
		return self::$columnInfo;
	}


	/* These links are manditory, and may not be removed by the user, except by purchasing
	   a link removal license. See: http://www.prophotoblogs.com/support/remove-links/

	   If you modify ANY code in order to remove the NetRivet Inc, or ProPhoto attribution
       links without purchasing a license to do so from us, you are in direct violation
	   of the End User License Agreement (EULA) you agreed to when purchasing. You also:

	   - will no longer receive any customer support
	   - will not receive free patches, bugfixes, or new feature updates
	   - will not receive future major upgrades at a discount
	*/
	public static function attributionLinks() {

		$nrText = array(
			'NetRivet Websites',
			'NetRivet Blogs',
			'NetRivet Sites',
			'NetRivet',
			'NetRivet, Inc.',
		);

		$ppText = array(
			'ProPhoto4 Blog',
			'ProPhoto Blogsite',
			'ProPhoto Blogsite',
			'ProPhoto Blogsite',
			'ProPhoto Photo Blog',
			'ProPhoto Photography Theme',
			'ProPhoto Photography Blog',
			'ProPhoto Photographer Blog',
			'ProPhoto Custom Blog',
			'ProPhoto WordPress Blog',
			'ProPhoto4 WordPress Theme',
			'ProPhoto Photo Theme',
			'ProPhoto Blog Template',
			'ProPhoto Photography Template',
			'ProPhoto Photographer Template',
			'ProPhoto4',
			'ProPhoto 4',
			'P4 Photo Blog',
			'ProPhoto theme',
			'ProPhoto theme',
			'ProPhoto Photography Blogsite',
			'ProPhoto Photography Blogsite',
			'ProPhoto Photographer Blogsite',
			'ProPhoto Photography Website',
			'ProPhoto Photographer Site',
			'ProPhoto Photo Website',
			'ProPhoto Site',
			'ProPhoto Website',
			'ProPhoto Website',
			'ProPhoto Website',
		);

		if ( file_exists( $legacyKeyFile = pp::fileInfo()->p3FolderPath . '/' . md5( 'ga_analytics_code' ) . '.php' ) ) {
			include( $legacyKeyFile );
			@unlink( $legacyKeyFile );
			if ( isset( $key ) && $key == md5( pp::site()->url ) ) {
				ppOpt::update( 'link_removal_verified_hash', md5( ppUid::get() ) );
			}
		}

		if ( ppOpt::test( 'link_removal_verified_hash', md5( ppUid::get() ) ) ) {
			echo NrHtml::span( '', 'id=link-removal-txn-id&title=' . ppOpt::id( 'link_removal_txn_id' ) );

		} else {
			echo NrHtml::span( '|', 'class=pipe' );
			echo NrHtml::a( 'http://www.prophotoblogs.com/', $ppText[array_rand($ppText)], 'title=' . $ppText[array_rand($ppText)] ) . ' ';

			if ( !ppOpt::test( 'des_html_mark', '' ) ) {
				echo ppOpt::id( 'des_html_mark' );

			} else if ( !ppOpt::test( 'dev_html_mark', '' ) ) {
				echo ppOpt::id( 'dev_html_mark' );

			} else {
				echo 'by ' . NrHtml::a( 'http://www.netrivet.com/', $nrText[array_rand($nrText)], 'title=' . $nrText[array_rand($nrText)] );
			}
		}

		do_action( 'pp_post_attribution_links' );
	}
}
