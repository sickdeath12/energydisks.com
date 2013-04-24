<form id="searchform-no-results" class="blog-search" method="get" action="<?php echo pp::site()->url ?>">

	<div>
		<input id="s-no-results" name="s" class="text" type="text" value="<?php the_search_query() ?>" size="40" />
		<input class="button" type="submit" value="<?php echo ppOpt::translate( 'search_notfound_button' ) ?>" />
	</div>

</form>