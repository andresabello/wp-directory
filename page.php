<?php 
/**
 * page.php
 *
 * The template for displaying all pages.
 */
?>

<?php get_header(); ?>
<?php while( have_posts() ) : the_post(); ?>
	<!-- Page Title -->
	<div class="section green-row page-title">
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
						<!-- Article header -->
						<header class="entry-header"> <?php
							// If the post has a thumbnail and it's not password protected
							// then display the thumbnail
							if ( has_post_thumbnail() && ! post_password_required() ) : ?>
								<figure class="entry-thumbnail"><?php the_post_thumbnail('large', array( 'class' => 'img-responsive' ) ); ?></figure>
							<?php endif; ?>
						</header> <!-- end entry-header -->

						<!-- Article content -->
						<div class="entry-content">
							<?php the_content(); ?>

							<?php wp_link_pages(); ?>
						</div> <!-- end entry-content -->

						<!-- Article footer -->
						<footer class="entry-footer">
							<?php 
								if ( is_user_logged_in() ) {
									echo '<p>';
									edit_post_link( __( 'Edit', 'pidirectory' ), '<span class="meta-edit">', '</span>' );
									echo '</p>';
								}
							?>
						</footer> <!-- end entry-footer -->
					</article>
			</div> <!-- end main-content -->
			<?php get_sidebar(); ?>
		</div> <!-- end row -->
	</div> <!-- end container -->
	<!-- end content -->
<?php endwhile; ?>
<?php get_footer(); ?>