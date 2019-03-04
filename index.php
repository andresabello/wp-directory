<?php 
/**
*
* index.php
* Main loop file
*
**/
?>
<?php get_header(); ?>
    <!-- content -->
    <div class="container">
		<div class="row">
			<div class="main-content col-md-8" role="main">
				<?php if( have_posts() ) : while( have_posts() ) : the_post(); ?>
					<?php get_template_part('content', 'index' ); ?>
				<?php endwhile; ?>
				
				<?php pi_paging_nav(); ?>
				
				<?php else : ?>
					<?php get_template_part('content', 'none'); ?>
				<?php endif; ?>
			</div>
			<?php get_sidebar(); ?>
		</div> <!-- end row -->
	</div> <!-- end container -->
	<!-- end content -->
<?php get_footer(); ?>