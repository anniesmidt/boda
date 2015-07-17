<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package boda
 */

?>

	</div><!-- #content -->
	
	
	<footer id="colophon" class="site-footer" role="contentinfo">
		<div class="site-info">
  		<div class="footer-menu">
  		<?php get_sidebar('footer'); ?>
      </div>
			
			<div id="social">
			<a href="http://wwww.twitter.com/bodagroup"><img src="http://boda.durablecreative.com/wp-content/uploads/2015/07/twitter-3-24.png"></a>
			&nbsp;
			<a href="https://www.linkedin.com/company/the-boda-group"><img src="http://boda.durablecreative.com/wp-content/uploads/2015/07/linkedin-3-24.png"></a>
			</div>
			
			
		</div><!-- .site-info -->
		<div id="copyright">©<?php echo date('Y'); ?> The Boda Group</div>

	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>
</body>
</html>
