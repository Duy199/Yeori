<?php
/**
 * Th			<a href="<?php echo esc_url( __( 'https://wordpress.org/', 'duydev' ) ); ?>">
			<?php
				printf( esc_html__( 'Proudly powered by %s', 'duydev' ), 'WordPress' );emplate for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package DuyDev
 */

?>

	<footer id="colophon" class="site-footer">
		<!-- <div class="site-info">
			<a href="<?php echo esc_url( __( 'https://wordpress.org/', 'artemis' ) ); ?>">
				<?php
				printf( esc_html__( 'Proudly powered by %s', 'artemis' ), 'WordPress' );
				?>
			</a>
			<span class="sep"> | </span>
				<?php
				printf( esc_html__( 'Theme: %1$s by %2$s.', 'duydev' ), 'duydev', '<a href="http://duytr.com/">DuyDev</a>' );
				?>
		</div> -->
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
