<?php
/**
 * The sidebar for the newsletter sign up page *
 * @package boda
 */

if ( ! is_active_sidebar( 'newsletter' ) ) {
	return;
}
?>

<div id="secondary" class="widget-area" role="complementary">
	<?php dynamic_sidebar( 'newsletter' ); ?>
</div><!-- #secondary -->

