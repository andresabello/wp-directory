<?php 
/**
*
* content.php
* Default template for displaying content
*
**/
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="row">
		<!-- If the Post has a featured image-->
		<?php if( has_post_thumbnail() && !post_password_required() ) : ?>
				<div class="col-md-4">
					<figure class="entry-thumbnail"><?php the_post_thumbnail( 'full', array( 'class'  => 'img-responsive' ) ); ?></figure>
				</div>
				<div class="col-md-8">
		<?php else: ?> 
			<div class="col-md-12">
		<?php endif; ?>
		<!-- End If the Post has a featured image-->
			<!-- header -->
			<header class="entry-header">
				<h1><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h1>
				<p class="entry-meta"> <?php pi_post_meta();?> </p>
			</header>
			<!-- end header -->
			<!-- content -->
			<div class="entry-content">
				<?php 
				if( is_search() || is_category() || is_archive() || !is_single() ){
					the_excerpt();
				}else{
					the_content( __('Continue reading <icon class="glyphicon glyphicon-arrow-right"></icon>', 'pidirectory') );
					wp_link_pages();
				}
				?>
			</div>
			<!-- end content -->
			<!-- footer -->
			<footer class="entry-footer">
				<?php 
				if( is_single() && get_the_author_meta('description') ){
					echo '<h2>' . __('Written by ', 'pidirectory') . get_the_author() . '</h2>';
					echo '<p>' . the_author_meta('description') . '</p>';

				}
				?>
			</footer>
		</div>
	</div>
</article>