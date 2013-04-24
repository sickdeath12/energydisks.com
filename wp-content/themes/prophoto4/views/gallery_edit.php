<form id="edit-pp-gallery" action="" method="post">

	<h1 class="pp-iframe-title pp-iframe-title-galleries">
		<span class="icon"></span>
		Edit ProPhoto <b>Gallery</b>
	</h1>

	<div id="tabs">
		<ul>
			<li><a href="#gallery-info">Gallery</a></li>
			<li><a href="#slideshow-customizations">Slideshow options</a></li>
			<li><a href="#lightbox-customizations">Lightbox options</a></li>

		</ul>

		<div id="gallery-info">


			<?php if ( isset( $_POST['save_changes'] ) ) {  ?>
				<p class="gmail-notice-show" style="margin:0;paddin-top:3px;">
					<span>Changes to your gallery were saved.</span>
				</p>
			<?php } ?>

			<?php echo NrHtml::labledTextInput( 'Title:', 'pp_gallery_title', $gallery->title(), 45 ); ?>

			<?php echo NrHtml::labledTextInput( 'Subtitle:', 'pp_gallery_subtitle', $gallery->subtitle(), 45 ); ?>

			<label>Drag images below to re-order:</label>

			<ul id="reorder-pp-gallery" class="sc">

				<?php

				foreach ( $gallery->imgs() as $img ) {
					echo NrHtml::li(
						$img->thumb()->width( 56 )->height( 56 )->markup() . NrHtml::span( '&#215;', 'title=delete image from gallery' ),
						'id=imgs_' . $img->id() );
				}

				?>

			</ul><!-- #reorder-pp-gallery -->

			<?php

			echo NrHtml::hiddenInput( 'pp_gallery_reorder', '' );

			echo NrHtml::hiddenInput( 'pp_gallery_id', $gallery->id() );

			echo NrHtml::hiddenInput( 'insert_pp_gallery_as', '' );

			echo NrHtml::hiddenInput( 'save_changes', '' );

			echo ppNonce::field( 'create-new-gallery' );

			?>

		</div><!-- #gallery-info -->

		<div id="slideshow-customizations">

			<p>
				These customizations will apply anywhere you insert this gallery as a <b>slideshow</b>.
			</p>

			<?php echo NrHtml::labledTextInput(
				'Proofing page URL:',
				'slideshow_shopping_cart_url',
				$gallery->slideshowOption( 'shoppingCartUrl' ),
				45
			); ?>

			<div id="slideshow-speed">
				<label for="slideshow_hold_time">Override slideshow image hold time:</label>
				<?php echo NrHtml::textInput( 'slideshow_hold_time', $gallery->slideshowOption( 'holdTime' ), 3 ); ?><label>seconds</label>
			</div><!-- #slideshow-speed -->

			<div id="auto_start">
				<?php echo NrHtml::labledCheckbox(
					'slideshow auto-starts without click',
					'slideshow_auto_start',
					( $gallery->slideshowOption( 'autoStart' ) )
				); ?>
			</div>


			<div id="auto_start">
				<?php echo NrHtml::labledCheckbox(
					'remove thumbnail strip with control buttons',
					'slideshow_disable_thumbstrip',
					( $gallery->slideshowOption( 'disableThumbstrip' ) )
				); ?>
			</div>


			<div id="music">
				<?php

				$helpLink = NrHtml::a( 'javascript:jQuery(\'#music p\').slideDown(\'fast\');return false;', '[?]', 'id=music-help' );
				if ( $availableMusicFiles = ppGalleryAdmin::availableMusicFiles() ) {
					echo NrHtml::label( 'Slideshow music: ', 'slideshow_music_file' );
					echo NrHtml::select( 'slideshow_music_file', array_merge( array( 'no music' => '' ), $availableMusicFiles ), $gallery->slideshowOption( 'musicFile' ) );
					echo $helpLink;
					echo NrHtml::p( '
						To add a different song for this slideshow, first upload a new <code>.mp3</code> file through the <a href="' .
						ppUtil::customizeURL( 'galleries', 'audio' ) . '">Galleries customization screen</a>, or <a href="' .
						pp::tut()->ftpAudioFiles . '">manually through FTP</a>.
					' );
				} else {
					echo NrHtml::label( 'Slideshow music: ', 'slideshow_music_file' );
					echo NrHtml::select( 'slideshow_music_file', array( 'no music uploaded' => '' ), '' );
					echo $helpLink;
					echo NrHtml::p( '
						To add music to this slideshow, first upload a song	 in <code>.mp3</code> format through the <a href="' .
						ppUtil::customizeURL( 'galleries', 'audio' ) .'">Galleries customization screen</a>, or <a href="' .
						pp::tut()->ftpAudioFiles . '">manually through FTP</a>, then return to this screen and you will be able 
						to select your uploaded song from the dropdown menu.
					' );
				}

				?>
			</div>

		</div><!-- #slideshow-customizations -->

		<div id="lightbox-customizations">

			<p>
				These customizations will apply anywhere you insert this gallery as a <b>lightbox</b>.
			</p>

			<div id="lightbox-thumb-size">
				<label for="lightbox_requested_thumb_size">Override thumbnail size setting of <?php echo ppOpt::id( 'lightbox_thumb_default_size' ) ?> pixels to:</label>
				<?php echo NrHtml::textInput( 'lightbox_requested_thumb_size', $gallery->lightboxOption( 'thumb_size' ), 3 ); ?><label>pixels</label>
			</div><!-- #slideshow-speed -->

			<div id="show-main-image">
					<?php echo NrHtml::radio(
						'show_main_image',
						array(
							'Show one full-size image, then thumbnails' => 'true',
							'Show just thumbnails' => 'false',
						),
						( $gallery->lightboxOption( 'show_main_image' ) === false ) ? 'false' : 'true'
					); ?>
				</div>

		</div><!-- #lightbox-customizations -->

	</div><!-- #tabs -->



	<input id="pp-gallery-save-changes" type="submit" value="Save changes" class="button disabled" disabled="disabled">


</form>

<div id="gallery-options">

	<form action="<?php echo ppMediaAdmin::uploadIFrameUrl(); ?>" method="post" accept-charset="utf-8">
		<?php echo NrHtml::hiddenInput( 'pp_gallery_id', $gallery->id() ); ?>
		<?php echo wp_nonce_field( 'media-form' ); ?>
		<input type="submit" value="Add images..." class="button">
	</form>

	<form action="<?php echo ppMediaAdmin::uploadIFrameUrl( 'pp_galleries' ); ?>" method="post" accept-charset="utf-8">
		<?php echo NrHtml::hiddenInput( 'pp_gallery_id', $gallery->id() ); ?>
		<?php echo NrHtml::hiddenInput( 'insert_pp_gallery_as', 'slideshow' ); ?>
		<input type="submit" value="Insert as slideshow" class="button">
	</form>

	<form action="<?php echo ppMediaAdmin::uploadIFrameUrl( 'pp_galleries' ); ?>" method="post" accept-charset="utf-8">
		<?php echo NrHtml::hiddenInput( 'pp_gallery_id', $gallery->id() ); ?>
		<?php echo NrHtml::hiddenInput( 'insert_pp_gallery_as', 'lightbox' ); ?>
		<input type="submit" value="Insert as lightbox" class="button">
	</form>

	<form action="<?php echo ppMediaAdmin::uploadIFrameUrl( 'pp_galleries' ); ?>" method="post" accept-charset="utf-8">
		<?php echo NrHtml::hiddenInput( 'pp_gallery_id', $gallery->id() ); ?>
		<?php echo NrHtml::hiddenInput( 'insert_pp_gallery_as', 'fullsize_imgs' ); ?>
		<input type="submit" value="Insert as fullsize images" class="button">
	</form>

	<form action="" method="post" id="done-editing">
		<input type="submit" value="Done editing, close popup" class="button">
	</form>

</div>