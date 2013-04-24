<?php


class ppTwitterHtml_Widget extends ppWidget {

	// widget setup
	function __construct() {
		$this->prettyName = 'Twitter HTML Badge';
		$this->description = 'ProPhoto theme simple twitter HTML display.  Displays a list of recent tweets.';
		parent::__construct();
	}

	// widget output
	function widget( $args, $instance ) {
		extract( $args );

		$twitter_count  = $instance['twitter_count'];
		$twitter_name   = strtolower( $instance['twitter_name'] );
		$loading_text	= $instance['loading_text'];
		$follow_text    = $instance['follow_text'];
		$link_text      = $instance['link_text'];

		// font size specified
		if ( $instance['font_size'] ) {
			$font_size  = ' style="font-size:' . intval( $instance['font_size'] ) . 'px;"';
			$follow_font_size = intval( intval( $instance['font_size'] ) * 0.85 );
			$follow_font_size = ' style="font-size:' . $follow_font_size . 'px;"';
		} else {
			$font_size  = '';
			$follow_font_size = '';
		}

		echo $before_widget;

		echo $this->titleMarkup( $args, $instance );

		$markup = <<<HTML
		<div id="{$widget_id}" class="p4-twitter-html pp-html-twitter-widget pp-html-twitter-widget-{$twitter_name}">
			<span class="twitter_count js-info">$twitter_count</span>
			<span class="twitter_name js-info">$twitter_name</span>
			<ul{$font_size}>$loading_text</ul>
			<p{$follow_font_size}>$follow_text <a href="http://twitter.com/{$twitter_name}">{$link_text}</a></p>
		</div>
HTML;
		echo apply_filters( 'p4_twitter_html_markup', $markup, $instance );

		echo $after_widget;

	}

	// update widget settings
	function update( $new_instance, $old_instance ) {
		return apply_filters( 'p4_twitter_html_update', $new_instance, $old_instance );
	}

	// widget admin form
	function form( $instance ) {

		$defaults = array(
			'twitter_name'  => ppOpt::id( 'twitter_name' ),
			'follow_text'   => 'Follow me on',
			'link_text'     => 'Twitter',
			'twitter_count' => '5',
			'loading_text'  => 'loading...',
			'title'         => 'What I\'m doing:',
			'font_size'     => '',
		);
		$instance = wp_parse_args( (array) $instance, $defaults );

		echo $this->helpLink();

		ob_start(); ?>

	<div class="p4-twitter-html">

		<div class="p4-twitter-html-form">

			<?php

			echo $this->titleField( $instance );
			echo $this->labledTextInput( 'twitter_name',  'Your Twitter username:', $instance );
			echo $this->labledTextInput( 'twitter_count', 'Number tweets shown:',   $instance );
			echo $this->labledTextInput( 'follow_text',   'Follow link prefix:',    $instance );
			echo $this->labledTextInput( 'link_text',     'Link text:',             $instance );
			echo $this->labledTextInput( 'loading_text',  '"Loading" text:',        $instance );

			?>

			<p>
				<label for="<?php echo $this->get_field_id('font_size'); ?>">Set font size (optional):</label>
				<input class="inline" id="<?php echo $this->get_field_id('font_size'); ?>" name="<?php echo $this->get_field_name('font_size'); ?>" type="text" size="3" value="<?php echo $instance['font_size']; ?>" />px
			</p>
		</div><!-- .p4-twitter-html-form  -->
	</div><!-- .p4-twitter-html  -->
<?php
		$form = ob_get_clean();
		echo apply_filters( 'p4_twitter_html_form', $form, $instance );
	}
}

?>