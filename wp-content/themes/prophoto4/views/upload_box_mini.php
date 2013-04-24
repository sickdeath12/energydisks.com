<div id="upload-box-<?php echo $upload->id(); ?>" class="mini-img-wrap upload-box option sc <?php echo $upload->classes(); ?>">

	<div class="uploaded-file-display">
		<?php echo $upload->fileDisplay(); ?>
	</div>

	<div class="image-info">

		<div class="mini-img-btn-wrap sc">

			<a href="<?php echo $upload->uploadBtnHref(); ?>" class="button-secondary thickbox upload-file-btn">

				<span class="upload">
					<?php echo $upload->uploadBtnLabel(); ?>
				</span>

				<span class="replace">
					<?php echo $upload->replaceBtnLabel(); ?>
				</span>
			</a>

			<a href="<?php echo $upload->deleteBtnHref(); ?>" class="button-secondary thickbox delete-uploaded-file-btn">
				<?php echo $upload->deleteBtnLabel(); ?>
			</a>

		</div>

	</div>

</div>
