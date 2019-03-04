<?php
/**
 * single.php
 *
 * The template for displaying single posts.
 */
?>

<?php get_header(); ?>
    <!-- header ends -->
<?php while( have_posts() ) : the_post(); ?>
<?php $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'single-post-thumbnail' ); ?>
	<!-- Page Title -->
	<div class="section greenRow pageTitleMod" style="background-image: url('<?php echo $image[0]; ?>'); background-size: cover; background-position: center center">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<h1><?php the_title(); ?></h1>
					<?php echo display_breadcrumbs();?>
				</div>
			</div>
		</div>
	</div>    
    <!-- content -->
    <div class="container">
		<div class="row">
			<div class="main-content col-md-8" role="main">
					<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
						<?php if( has_post_thumbnail() && !post_password_required() ) : ?>
							<figure class="entry-thumbnail"><?php the_post_thumbnail( 'full', array( 'class'  => 'img-responsive' ) ); ?></figure>
						<?php endif; ?> 
						<!-- header -->
						<header class="entry-header">
							<p class="entry-meta"> <?php pi_post_meta();?> </p>
						</header>
						<!-- end header -->
						<!-- content -->
						<div class="entry-content">
							<?php 
								the_content( __('Continue reading &rarr;', 'pidirectory') );
								wp_link_pages();
							?>
						</div>
						<!-- end content -->
						<!-- footer -->
						<footer class="entry-footer">
							<?php 
							if( get_the_author_meta('description') ){
								echo '<h2>' . __('Written by ', 'pidirectory') . get_the_author() . '</h2>';
								echo '<p>' . the_author_meta('description') . '</p>';

							}
							?>
						</footer>
						<?php comments_template(); ?>
					</article>
					
			</div> <!-- end main-content -->
		<?php get_sidebar(); ?>
		</div> <!-- end row -->
	</div> <!-- end container -->
	<!-- end content -->
<?php endwhile; ?>
<?php get_footer(); ?>