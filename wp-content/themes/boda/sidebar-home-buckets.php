<?php
/**
 * The sidebar containing the main widget area.
 *
 * @package boda
 */

if ( ! is_active_sidebar( 'home-buckets' ) ) {
	return;
}
?>

<div id="secondary" class="homebuckets widget-area" role="complementary">

	<?php dynamic_sidebar( 'home-buckets' ); ?>

</div><!-- #secondary -->
