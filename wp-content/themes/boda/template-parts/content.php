<?php
/**
 * Template part for displaying posts.
 *
 * @package boda
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<header class="entry-header">
		<?php the_title( sprintf( '<h1 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h1>' ); ?>

		<?php if ( 'post' == get_post_type() ) : ?>
		<div class="entry-meta">
			<?php boda_posted_on(); ?>
		</div><!-- .entry-meta -->
		<?php endif; ?>
	</header><!-- .entry-header -->
	<div class="entry-content">
				<?php echo the_post_thumbnail('blog-images'); ?>
		
		<?php the_excerpt(); ?>
		

        
		<?php
			wp_link_pages( array(
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'boda' ),
				'after'  => '</div>',
			) );
		?>
	</div><!-- .entry-content -->

	<footer class="entry-footer .readmore">

		<?php boda_entry_footer(); ?>

	</footer><!-- .entry-footer -->
</article><!-- #post-## -->
