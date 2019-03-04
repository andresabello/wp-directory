<?php 
/**
 * archive.php
 *
 * The template for displaying archive pages.
 */
?>

<?php get_header(); ?>
    <!-- header ends -->
    <!-- content -->
    <div class="container">
		<div class="row">
			<div class="main-content col-md-8" role="main">
				<?php if ( have_posts() ) : ?>
					<header class="page-header">
						<h1>
							<?php 
								if ( is_day() ) {
									printf( __( 'Daily Archives for %s', 'pidirectory' ), get_the_date() );
								} elseif ( is_month() ) {
									printf( __( 'Monthly Archives for %s', 'pidirectory' ), get_the_date( _x( 'F Y', 'Monthly archives date format', 'pidirectory' ) ) );
								} elseif ( is_year() ) {
									printf( __( 'Yearly Archives for %s', 'pidirectory' ), get_the_date( _x( 'Y', 'Yearly archives date format', 'pidirectory' ) ) );
								} else {
									_e( 'Archives', 'pidirectory' );
								}
							?>
						</h1>
					</header>

					<?php while( have_posts() ) : the_post(); ?>
						<?php get_template_part( 'content', 'index' ); ?>
					<?php endwhile; ?>

					<?php pi_paging_nav(); ?>
				<?php else : ?>
					<?php get_template_part( 'content', 'none' ); ?>
				<?php endif; ?>
			</div> <!-- end main-content -->
			<?php get_sidebar(); ?>
		</div> <!-- end row -->
	</div> <!-- end container -->
	<!-- end content -->
<?php get_footer(); ?>