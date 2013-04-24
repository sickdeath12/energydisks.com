<div class="vis-test">

	<h2>
		<b><?php echo preg_replace( '/^test_/', '', $test->test_method ); ?></b>
		<span class="code">code</span>
		<span><?php echo NrHtml::a( $test->iframe_src, 'view' ); ?></span>
		<span><?php echo NrHtml::a( $test->iframe_src . '&render_all=1', 'view all' ); ?></span>
	</h2>

	<iframe src="<?php echo $test->iframe_src ?>" style="<?php echo $test->iframe_style ?>"></iframe>

	<pre class="test-markup"><?php echo $test->markup ?></pre>

	<?php echo $test->note ?>

</div><!-- ss -->