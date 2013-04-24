<div id="upload-box-<?php echo $upload->id() ?>" class="upload-box option sc <?php echo $upload->classes(); ?>">

	<div class="upload-box-top sc">

		<?php ppUtil::renderView( 'option_help_icons', array( 'id' => $upload->id() ) ); ?>

		<div class="upload-box-label option-label">

			<?php echo $upload->debug(); ?>

			<p>
				<?php echo $upload->name() ?>
			</p>

		</div>

		<div class="extra-explain">
			<?php echo ppAdmin::optionBlurb( $upload->id() ); ?>
		</div>

	</div>

	<?php echo $upload->aboveOptions(); ?>

	<div class="upload-box-body sc">

		<?php echo $upload->leftTopOptions(); ?>

		<div class="image-info">

			<div class="sc">

				<a href="<?php echo $upload->uploadBtnHref() ?>" class="button-secondary thickbox upload-file-btn">

					<span class="upload">
						<?php echo $upload->uploadBtnLabel() ?>
					</span>

					<span class="replace">
						<?php echo $upload->replaceBtnLabel() ?>
					</span>
				</a>

				<a href="<?php echo $upload->deleteBtnHref() ?>" class="button-secondary thickbox delete-uploaded-file-btn">
					<?php echo $upload->deleteBtnLabel(); ?>
				</a>

			</div>

			<?php echo $upload->fileStatsBox(); ?>

			<?php echo $upload->sizingBox(); ?>

			<?php echo $upload->leftBtmOptions(); ?>

		</div>

		<div class="uploaded-file-display">

			<?php echo $upload->commentMarkup(); ?>

			<?php echo $upload->fileDisplay(); ?>

		</div>

		<?php echo $upload->belowOptions(); ?>

	</div>

</div>