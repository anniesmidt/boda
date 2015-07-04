<?php
/**
 * The sidebar for the blog *
 * @package boda
 */

if ( ! is_active_sidebar( 'blog' ) ) {
	return;
}
?>

<div id="secondary" class="widget-area" role="complementary">
	<?php dynamic_sidebar( 'blog' ); ?>
</div><!-- #secondary -->

