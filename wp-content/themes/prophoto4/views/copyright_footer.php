<div id="copyright-footer" class="content-bg">

	<p id="user-copyright">
		<?php

		echo ppFooter::userCopyright();
		echo ppFooter::attributionLinks();

		 ?>
	</p>

	<div id="wp-footer-action-output">
		<?php

		wp_footer();
		echo ppHtml::statcounterAnalyticsCode();

		 ?>
	</div>

</div><!-- #copyright-footer -->
