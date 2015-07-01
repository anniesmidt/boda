<?php
/**
 Template Name: Case Study Single
 * The template for displaying indvidual case studies
 *
 * @package boda
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			<?php while ( have_posts() ) : the_post(); ?>

				<?php get_template_part( 'template-parts/content', 'page' ); ?>

				
			<?php endwhile; // End of the loop. ?>


<!-- uses ambrosite next/previous plugin to create nav at bottom -->            
<div class="case-prev-next">
<?php previous_page_link_plus( array(
                         'order_by' => 'menu_order',
                         'loop' => false,
                         'max_length' => 37,
                         'tooltip' => 'Previous case',
                         'in_same_parent' => true,
                    ) );?> 
<span class= "divider-pipe">||</span>
<?php next_page_link_plus( array(
                         'order_by' => 'menu_order',
                         'loop' => false,
                         'max_length' => 37,
                         'tooltip' => 'Next case',
                         'in_same_parent' => true,
                    ) );?> 
</div>

                    
                    

		</main><!-- #main -->
	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
