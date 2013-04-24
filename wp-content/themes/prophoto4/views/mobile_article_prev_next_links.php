<div id="<?php echo $wrapperID ?>" class="ui-grid-a mobile-prev-next-links">

	<?php

	if ( isset( $prev->href ) && isset( $prev->text ) ) { ?>
	<div class="ui-block-a">
		<a href="<?php echo $prev->href ?>" data-role="button" data-icon="arrow-l" data-direction="reverse"<?php echo $prev->rel ?>>
			<?php echo $prev->text ?>
		</a>
	</div><?php
	}

	if ( isset( $next->href ) && isset( $next->text ) ) { ?>
	<div class="ui-block-b">
		<a href="<?php echo $next->href ?>" data-role="button" data-icon="arrow-r" data-iconpos="right"<?php echo $next->rel ?>>
			<?php echo $next->text ?>
		</a>
	</div><?php
	}

?>

</div>