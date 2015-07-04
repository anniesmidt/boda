<?php
/**
 * The sidebar containing the case and results menu
 *
 * @package boda
 */

if ( ! is_active_sidebar( 'cases' ) ) {
	return;
}
?>

<div id="secondary" class="widget-area" role="complementary">
	<?php dynamic_sidebar( 'cases' ); ?>
</div><!-- #secondary -->
