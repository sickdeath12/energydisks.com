<?php

class ppBioColumns {

	public $columnCount = 0;
	public $flexColumnCount = 0;
	public $initialWorkingArea;
	public $workingArea;
	public $gutterWidth;
	public $gutterCount;
	public $spanningColWidth;
	public $biopicOffset;
	public $colWidthsOrig = array();
	public $colWidthsFirstPass = array();
	public $colWidths = array();
	protected static $instance;


	public static function data() {
		if ( !self::$instance ) {
			self::$instance = new ppBioColumns();
		}
		return self::$instance;
	}


	protected function __construct() {

		// setup initial working area: blog width - 2x bio margings
		$leftRightPadding = ppOpt::orVal( 'bio_lr_padding', ppOpt::id( 'content_margin' ) );
		$this->initialWorkingArea = ppOpt::id( 'blog_width' ) - ( $leftRightPadding * 2 );
		$this->workingArea = $this->initialWorkingArea;

		// gutter width
		$this->gutterWidth = intval( ppOpt::orVal( 'bio_gutter_width', ppOpt::id( 'content_margin' ) ) );


		// setup width of  spanning column, optionally accounitng for biopic
		if ( !ppOpt::test( 'biopic_display', 'off' ) && ppImg::id( 'biopic1' )->exists ) {
			$this->accountForBiopic();
		} else {
			$this->spanningColWidth = $this->workingArea;
		}

		// loop through available bio columns, checking each one
		for ( $i = 1; $i <= pp::num()->maxBioWidgetColumns; $i++ ) {

			if ( ppWidgetUtil::areaHasWidgets( 'bio-col-' . $i ) ) {

				// we have widgets, count this as a column
				$this->columnCount++;

				// get width information for this column
				$this->colWidthsOrig[$i] = $col = new ppWidgetAreaWidth( 'bio-col-' . $i );

				$this->colWidths[$i] = (object) array( 'width' => $col->width, 'minWidth' => $col->minWidth );
			}
		}


		// now that we know column count, subtract gutters from working area
		$this->gutterCount = $this->columnCount - 1;
		if ( $this->gutterCount < 0 ) {
			$this->gutterCount = 0;
		}
		$this->workingArea = $this->workingArea - ( $this->gutterWidth * $this->gutterCount );



		/* temp pass: calculate using minimum width values */
		$_workingArea = $this->workingArea;
		foreach ( $this->colWidths as $column ) {

			// count our flex columns
			if ( $column->width == 'flex' ) {
				$this->flexColumnCount++;

			// subtract width of known columns from working area
			} elseif ( $column->width ) {
				$_workingArea = $_workingArea - $column->width;
			}
		}

		// compute temp-pass flexible column width, assuming we're using minimum values
		$_flexColumnCount = $this->flexColumnCount;
		if ( $_flexColumnCount == 0 ) {
			$_flexColumnCount = 1;
		}
		$_flexColumnWidth = $_workingArea / $_flexColumnCount;

		// now we have a computed flex width, if it's bigger than min-width of a column
		// reset that column to 'flex' to recalulate
		foreach ( $this->colWidths as $colNum => $column ) {
			if ( $column->minWidth ) {
				if ( $_flexColumnWidth > $column->minWidth ) {
					$this->colWidths[$colNum]->minWidth = '';
					$this->colWidths[$colNum]->width = 'flex';
				// or else use the min width as the actual width
				} else {
					$this->colWidths[$colNum]->width = $this->colWidths[$colNum]->minWidth;
					$this->colWidths[$colNum]->minWidth = '';
					$this->flexColumnCount--;
				}
			}
		}
		$this->colWidthsFirstPass = $this->colWidths;



		/* final pass: min-widths that were less than temp flex width were reset to flex */

		// subtract known column widths from working-area
		foreach ( $this->colWidths as $column ) {
			if ( $column->width != 'flex' ) {
				$this->workingArea = $this->workingArea - $column->width;
			}
		}

		// calculate final flex column width
		if ( $this->flexColumnCount == 0 ) {
			$this->flexColumnCount = 1;
		}
		$flexColumnWidth = round( $this->workingArea / $this->flexColumnCount, 0 ) - 1;



		// use the final flex column width for the return width of flex columns
		foreach ( $this->colWidths as $colNum => $column ) {
			if ( $column->width == 'flex' ) {
				$this->colWidths[$colNum]->width = $flexColumnWidth;
			}
		}

		$this->colWidths = apply_filters( 'pp_biocolwidths', $this->colWidths );
	}


	/* return css for bio columns */
	public function css() {

		// spanning column
		$css =
		"#bio-widget-spanning-col {
			width:{$this->spanningColWidth}px;
			float:left;
			margin-right:0;
		}\n";

		// non-spanning column margin
		if ( $this->biopicOffset ) {
			$side = ppOpt::id( 'biopic_align' );
			$css .=
			"#bio-widget-col-wrap {
				margin-{$side}:{$this->biopicOffset}px;
			}\n";
		}

		// regular columns
		foreach ( $this->colWidths as $colNum => $col ) {
			$css .= "#bio-col-$colNum {\n\twidth:{$col->width}px;";
			// if it is the last column
			if ( !isset( $this->colWidths[$colNum + 1] ) ) {
				// remove the right margin (gutter)
				$css .= "\n\tmargin-right:0;";
			}
			$css .= "\n}\n";
		}

		return $css;
	}



	private function accountForBiopic() {
		 // biopic counts as column for sake of calculations
		$this->columnCount++;

		if ( ppOpt::test( 'biopic_border', 'on' ) ) {
			$picBorderWidth = ppOpt::id( 'biopic_border_width', 'int' ) * 2;
		} else {
			$picBorderWidth = 0;
		}

		$this->workingArea = $this->workingArea - ppImg::id( 'biopic1' )->width - $picBorderWidth;
		$this->spanningColWidth = $this->workingArea - $this->gutterWidth;
	 	$this->biopicOffset = ppImg::id( 'biopic1' )->width + $picBorderWidth + $this->gutterWidth;
	}


	public static function flushCache() {
		self::$instance = null;
	}
}

