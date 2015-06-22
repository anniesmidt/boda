<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package boda
 */

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">





<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
  <div id="page" class="hfeed site">
	  <a class="skip-link screen-reader-text" href="#content">
	  <?php esc_html_e( 'Skip to content', 'boda' ); ?></a>


	<header id="masthead" class="site-header" role="banner">
		
		<h1 class="site-title"> <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
      <img src="http://localhost/boda/wp-content/uploads/2015/06/boda-logo.png""  alt="The Boda Group" /></a>
    </h1>

		<nav id="site-navigation" class="main-navigation" role="navigation">
			<button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
			<?php esc_html_e( 'Primary Menu', 'boda' ); ?></button>
			<?php wp_nav_menu( array( 'theme_location' => 'primary', 'menu_id' => 'primary-menu' ) ); ?>
		</nav><!-- #site-navigation -->
  </div><!-- end PAGE-->		

	</header><!-- #masthead -->

  <!-- add custom post type 'strip' to place where strip goes in page headers -->
    <?php echo get_post_meta($post->ID, "strip", true); ?>

	<div id="content" class="site-content">
