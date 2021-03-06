<?php
/**
 * The template for displaying the Cases and Reults page *
 Template Name: Cases
 * @package boda
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">


			<?php while ( have_posts() ) : the_post(); ?>

				<?php get_template_part( 'template-parts/content', 'page' ); ?>

      <?php endwhile; // End of the loop. ?>
<?php get_sidebar('cases'); ?>

			
		</main><!-- #main -->


		
	</div><!-- #primary -->
 


<?php get_footer(); ?>
