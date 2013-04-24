<?php

class ppTwitterSlider_Widget extends ppWidget {

	/* js function for slider widget */
	public static function js() {
		$js = <<<JAVASCRIPT

		function ppSlidingTwitterControls(context) {
			if ( context == undefined ) {
				context = jQuery('body')
			}
			jQuery('.sliding .controls a',context).click(function(){
				ppSlidingTwitterControlClick(jQuery(this));
			});
		}

		function ppSlidingTwitterControlClick(this_click) {
			if ( this_click.hasClass('disabled') ) return false;
			var click_type    = ( this_click.hasClass('prev') ) ? 'prev' : 'next';
			var this_widget   = this_click.parents('.pp-twitter-slider');
			var tweet_height  = parseInt(jQuery('.tweet_height', this_widget).text()) + 5; // 5 = padding below
			var current_tweet = parseInt(jQuery('.twitter_current', this_widget).text());
			var num_tweets    = parseInt(jQuery('.twitter_count', this_widget).text());
			( click_type == 'prev' ) ? current_tweet-- : current_tweet++;
			var new_top = -(current_tweet * tweet_height);
			jQuery('.controls a', this_widget).removeClass('disabled');
			// disable prev/next button when appropriate
			if ( current_tweet == 0 ) {
				jQuery('.prev', this_widget).addClass('disabled');
			} else if ( current_tweet == ( num_tweets - 1 ) ) {
				jQuery('.next', this_widget).addClass('disabled');
			}
			jQuery('.twitter_current', this_widget).text(current_tweet);
			jQuery('.viewer ul', this_widget).animate({top: new_top+'px'},210,'swing');
		}

		ppSlidingTwitterControls();
JAVASCRIPT;
		return apply_filters( 'p4_twitter_slider_js', $js );
	}


	/* css for slider widget */
	public static function css() {
		$prophoto_info = ppUtil::siteData();
		$css = <<<CSS

		/* sliding twitter widget */
		.pp-twitter-slider .controls {
			float:left;
			display:inline;
			padding:1px 0 0 0;
			padding-right:8px;
			width:15px;
		}
		.pp-twitter-slider .controls a {
			display:none;
			margin-bottom:4px;
			cursor:pointer;
			width:15px;
			height:15px;
			overflow:hidden;
			text-indent:-999em;
		}
		.pp-twitter-slider .controls .next {
			background-image:url($prophoto_info->static_resource_url/img/sliding-twitter-btn-next.png);
		}
		.pp-twitter-slider .controls .prev {
			background-image:url($prophoto_info->static_resource_url/img/sliding-twitter-btn-prev.png);
		}
		.pp-twitter-slider .controls .disabled {
			background-position: 0 -15px;
			cursor:default;
		}
		.pp-twitter-slider .viewer {
			float:left;
			display:inline;
			overflow:hidden;
			position:relative;
		}
		.pp-twitter-slider {
			position:relative;
		}
		.has-file .badge-inner {
			position:absolute;
		}
		.pp-twitter-slider ul {
			position:absolute;
			top:0px;
			left:0px;
			margin:0;
		}
		.pp-twitter-slider li {
			line-height:1.1em;
			overflow:hidden;
			margin-bottom:5px !important;
			#margin-bottom:4px !important;
			margin-left:0 !important;
		}
		.pp-twitter-slider .follow-me,
		.pp-twitter-slider .follow-me a {
			font-size:10px;
			font-style:italic;
			margin-top:.5em;
			text-align:right;
		}
		.pp-twitter-slider .follow-me {
			margin:0;
			padding:0;
		}
		.twitter-time {
			white-space:nowrap;
		}
		.pp-twitter-slider a {
			font-size:inherit !important;
			font-family:inherit !important;
		}
CSS;
		return apply_filters( 'p4_twitter_slider_css', $css );
	}


	/* prints markup for widget, used by widget output and form preview */
	function widget_markup( $instance, $args = '' ) {
		extract( $instance );

		$box_width = $tweet_width + 25; // 25 = width of controls
		$image_prefix = pp::site()->extResourceUrl . '/img/';
		$sliding = ( $twitter_count > 1 ) ? 'sliding' : 'not-sliding';
		$img_class = ( $image != 'no' ) ? 'has-file' : 'no-file';
		$twitter_name = isset( $twitter_name ) ? strtolower( $twitter_name ) : ppOpt::id( 'twitter_name' );

		// built in images
		if ( $image == 'A' OR $image == 'B' ) {
			$image_path = $image_prefix . 'twitter-thought-bubble' . $image . '.png';
			$widget_width = ( $image == 'A' ) ? '277' : '194';

		// no image
		} else if ( $image == 'no' ) {
			$image_path = $image_prefix . 'nodefaultimage.gif';
			$widget_width = $box_width;

		// custom uploaded image
		} else {
			$image_path = ppImg::id( 'widget_custom_image_' . $image )->url;
			$widget_width = ppImg::id( 'widget_custom_image_' . $image )->width;
		}

		// front end widget output
		if ( $args ) {
			$content = $loading_text;
			$image_prefix_attr = $controls_click = '';
			$widget_id = $args['widget_id'];

		// widget output for form preview
		} else {
			$widget_id = 'i' . time();
			$image_prefix_attr = ' data-url-start="' . $image_prefix .'"';
			$controls_click = ' onclick="javascript:ppSlidingTwitterControlClick(jQuery(this));"';
			$height = 'style="height:' . $tweet_height .'px;"';
			$content = <<<HTML
			<li {$height}>This is a nice sample tweet with a link <a href="http://bit.ly/u3Alr">http://bit.ly/u3Alr</a> showing you how long 140 characters looks. WordPress plus ProPhoto theme rocks!! <a href="#">about an hour ago</a></li>
			<li {$height}>@<a href="http://twitter.com/prophotoblogs">prophotoblogs</a> how did you guys get so smart, handsome, and helpful? you really are the wind beneath my wings. Lorem ipsum is 140 characters <a href="#">two days ago</a></li>
			<li {$height}>a shorter tweet, that doesn't quite fill up the whole space <a href="#">364 years ago</a></li>
HTML;

			$twitter_count = 3;

			// account for 1px added border
			$box_width = $box_width + 1;
		}

		$markup = <<<HTML

		<div id="{$widget_id}" class="pp-twitter-slider sc pp-html-twitter-widget-{$twitter_name} pp-html-twitter-widget $img_class" style="width:{$widget_width}px">
			<span class="twitter_name js-info">$twitter_name</span>
			<span class="twitter_count js-info">$twitter_count</span>
			<span class="tweet_height js-info">$tweet_height</span>
			<span class="twitter_current js-info">0</span>

			<img src="{$image_path}"{$image_prefix_attr} />

			<div class="badge-inner $sliding" style="height:{$tweet_height}px; width:{$box_width}px; top:{$pos_top}px; left:{$pos_left}px;">
				<div class="controls">
					<a class="prev disabled"{$controls_click}>prev</a>
					<a class="next"{$controls_click}>next</a>
				</div>
				<div class="viewer" style="height:{$tweet_height}px;width:{$tweet_width}px;">
					<ul style="width:{$tweet_width}px;font-family:{$font};font-size:{$fontsize}px">$content</ul>
				</div>
			</div>
		</div> <!-- .pp-twitter-slider  -->
HTML;
		echo apply_filters( 'p4_twitter_slider_markup', $markup, $instance );
	}

	// widget setup
	function __construct() {
		$this->prettyName = 'Sliding Twitter Widget';
		$this->description = 'Show your recent tweets in an interactive, sliding box with optional built-in or custom background image integration.';
		$this->formWidth = 450;
		parent::__construct();
	}

	// widget output
	function widget( $args, $instance ) {
		extract( $args );
		echo $before_widget;
		echo $this->titleMarkup( $args, $instance );
		$this->widget_markup( $instance, $args );
		echo $after_widget;
	}

	// update widget settings
	function update( $new_instance, $old_instance ) {
		if ( $new_instance['image'] == 'add' ) {
			$new_instance['image'] = 'A';
		}
		return apply_filters( 'p4_twitter_slider_update', $new_instance, $old_instance );
	}

	// widget admin form
	function form( $instance ) {

		$defaults = array(
			'twitter_name' => ppOpt::id( 'twitter_name' ),
			'twitter_count' => '8',
			'loading_text' => 'loading...',
			'title' => '',
			'tweet_height' => 87,
			'tweet_width' => 145,
			'pos_top' => 24,
			'pos_left' => 13,
			'image' => 'A',
			'font' => 'Arial, Helvetica, sans-serif',
			'fontsize' => 11,
		);
		$instance = wp_parse_args( (array) $instance, $defaults );
		extract( $instance );

		$increment_controls = '<span class="incrementer"><a class="up" onclick="javascript:twitterSlider.incrementTweetBoxClick(jQuery(this));"><img src="' . pp::site()->extResourceUrl . '/img/arrow-down.png" /></a><a class="down" onclick="javascript:twitterSlider.incrementTweetBoxClick(jQuery(this));"><img src="' . pp::site()->extResourceUrl . '/img/arrow-up.png" /></a></span>';

		// currently available upload slots
		$custom_uploaded_image_options = '';
		for ( $i = 1; $i <= pp::num()->maxCustomWidgetImages; $i++ ) {
			if ( !ppImg::id( 'widget_custom_image_' . $i )->exists ) continue;
			$custom_uploaded_image_options .= "<option value='$i'";
			$custom_uploaded_image_options .= selected( $instance['image'], $i, NO_ECHO );
			$custom_uploaded_image_options .= ">Custom uploaded image #{$i}</option>";
		}

		$img_class = ( $image != 'no' ) ? 'has-file' : 'no-file';

		echo $this->helpLink();

		ob_start();
?>
		<div class="pp-twitter-slider-wrap <?php echo $img_class ?>">

			<?php $this->widget_markup( $instance ) ?>

			<div class="pp-twitter-slider-form">

				<?php echo $this->titleField( $instance ); ?>

				<p>
					<label for="<?php echo $this->get_field_id( 'image' ); ?>">Image:</label>
					<select name="<?php echo $this->get_field_name( 'image' ); ?>" id="<?php echo $this->get_field_id( 'image' ); ?>" class="widefat" onchange="javascript:twitterSlider.imagePreview(jQuery(this));">
						<option value="A"<?php selected( $instance['image'], 'A' ); ?>>Built-in image #1</option>
						<option value="B"<?php selected( $instance['image'], 'B' ); ?>>Built-in image #2</option>
						<?php echo $custom_uploaded_image_options ?>
						<option value="no"<?php selected( $instance['image'], 'no' ); ?>>no background image</option>
						<option value="add">add a custom image...</option>
					</select>
				</p>

				<p class="twitter-slider-img-upload-msg">To add a custom image to position behind your twitter slider, go <a href="<?php echo ppUtil::customizeURL( 'settings', 'widget_images' ) ?>">to this page</a> and upload a new Custom Widget Image. Then return here and you will be able to select it from the list above.</p>

				<p style="margin-bottom:0"><label>Tweet Slider size<span class="with-img"> and position</span>:</label></p>
				<p class="double">
					<span class="incrementable incrementable-2 with-img" data-adjusts="pos_top">
						<label for="<?php echo $this->get_field_id('pos_top'); ?>">Position from top: </label>
						<input class="inline" id="<?php echo $this->get_field_id('pos_top'); ?>" name="<?php echo $this->get_field_name('pos_top'); ?>" type="text" size="4" value="<?php echo $instance['pos_top']; ?>" onchange="javascript:twitterSlider.incrementTweetBoxDirect(jQuery(this));" /><strong>px</strong>
						<?php echo $increment_controls ?>
					</span>
					<span class="incrementable" data-adjusts="tweet_height">
						<label for="<?php echo $this->get_field_id('tweet_height'); ?>">Tweet box height: </label>
						<input class="inline tweet-box-height" id="<?php echo $this->get_field_id('tweet_height'); ?>" name="<?php echo $this->get_field_name('tweet_height'); ?>" type="text" size="4" value="<?php echo $instance['tweet_height']; ?>" onchange="javascript:twitterSlider.incrementTweetBoxDirect(jQuery(this));" /><strong>px</strong>
					<?php echo $increment_controls ?>
					</span>
				</p>

				<p class="double">
					<span class="incrementable incrementable-2 with-img" data-adjusts="pos_left">
						<label for="<?php echo $this->get_field_id('pos_left'); ?>">Position from left: </label>
						<input class="inline" id="<?php echo $this->get_field_id('pos_left'); ?>" name="<?php echo $this->get_field_name('pos_left'); ?>" type="text" size="4" value="<?php echo $instance['pos_left']; ?>" onchange="javascript:incrementTweetBoxDirect(jQuery(this));" /><strong>px</strong>
						<?php echo $increment_controls ?>
					</span>
					<span class="incrementable" data-adjusts='tweet_width'>
						<label class="tweet_width" for="<?php echo $this->get_field_id('tweet_width'); ?>">Tweet box width: </label>
						<input class="inline" id="<?php echo $this->get_field_id('tweet_width'); ?>" name="<?php echo $this->get_field_name('tweet_width'); ?>" type="text" size="4" value="<?php echo $instance['tweet_width']; ?>" onchange="javascript:incrementTweetBoxDirect(jQuery(this));" /><strong>px</strong>
						<?php echo $increment_controls ?>
					</span>
				</p>


				<?php

				echo $this->labledTextInput( 'twitter_name', 'Your Twitter username:', $instance );
				echo $this->labledTextInput( 'twitter_count', 'Number tweets loaded:', $instance, null, 'twitterSlider.tweetCountChange(jQuery(this));' );
				echo $this->labledTextInput( 'loading_text', 'Temporary "loading" text:', $instance );

				?>

				<p>
					<label for="<?php echo $this->get_field_id( 'font' ); ?>">Font family:</label>
					<select name="<?php echo $this->get_field_name( 'font' ); ?>" id="<?php echo $this->get_field_id( 'font' ); ?>"  onchange="javascript:twitterSlider.fontFamilyChange(jQuery(this));">
						<?php $this->fontSelect( $instance['font'] ) ?>
					</select>

					<span style="float:right">
						<label for="<?php echo $this->get_field_id('fontsize'); ?>">Font size:</label>
						<input class="inline" id="<?php echo $this->get_field_id('fontsize'); ?>" name="<?php echo $this->get_field_name('fontsize'); ?>" type="text" value="<?php echo $instance['fontsize']; ?>" size="3" onchange="javascript:twitterSlider.fontSizeChange(jQuery(this));" /><strong>px</strong>
					</span>
				</p>

			</div><!-- .pp-twitter-slider-form  -->
		</div><!-- .pp-twitter-slider -->
	<?php
		$form = ob_get_clean();
		echo apply_filters( 'p4_twitter_slider_form', $form, $instance );
	}



	/* uses p4 defined font families to create font-select dropdown for widgets */
	function fontSelect( $instance_val ) {
		$fonts = explode( '|', FONT_FAMILIES );
		$num_fonts = count( $fonts );

		for ( $i = 0; $i <= $num_fonts; $i = $i+2 ) {
			$font_name  = $fonts[$i+1];
			$font_value = str_replace('"', "'", $fonts[$i]);
			$selected   = selected( $instance_val, $font_value, NO_ECHO );
			if ( !$font_name ) continue;
			echo "<option value=\"$font_value\"$selected>$font_name</option>\n";
		}
	}

}

