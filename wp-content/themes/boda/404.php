<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @package boda
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			<section class="error-404 not-found">
				<header class="page-header">
					<h1 class="page-title"><?php esc_html_e( 'Not found.', 'boda' ); ?></h1>
				</header><!-- .page-header -->

				<div class="page-content error-content">
					<p><?php esc_html_e( 'Please use the main menu at the top to navigate to a new page.', 'boda' ); ?></p>

				</div><!-- .page-content -->
			</section><!-- .error-404 -->

		</main><!-- #main -->
	</div><!-- #primary -->

<?php get_footer(); ?>
