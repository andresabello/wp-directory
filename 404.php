<?php 
/**
 * 404.php
 *
 * The template for displaying 404 pages (Not Found).
 */
?>

<?php get_header(); ?>
    <!-- content -->
    <div class="container">
		<div class="row">
			<div class="main-content col-md-8" role="main">
				<h1><?php _e( 'Error 404 - Nothing Found', 'pidirectory' ); ?></h1>

				<p><?php _e( 'It looks like nothing was found here. Maybe try a search?', 'pidirectory' ); ?></p>

				<?php get_search_form(); ?>
			</div>
		</div> <!-- end row -->
	</div> <!-- end container -->
	<!-- end content -->
<?php get_footer(); ?>