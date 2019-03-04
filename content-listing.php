<?php 
/**
*
* content-listing.php
* Default template for displaying content listing
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
				<div class="col-md-8 listing-wrapper">
		<?php else: ?> 
			<div class="col-md-12 listing-wrapper">
		<?php endif; ?>
		<!-- End If the Post has a featured image-->
			<!-- header -->
			<header class="entry-header">
				<h3><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h3>
			</header>

			<!-- end header -->
			<!-- content -->
			<div class="entry-content">
				<?php 
					$post_id = get_the_ID();
					$address = get_post_meta( $post_id, 'pi_address', true );
					$city = get_post_meta( $post_id, 'pi_city', true );
					$county = get_post_meta( $post_id, 'pi_county', true );
					$pi_zip_code = get_post_meta( $post_id, 'pi_zip_code', true );
				?>
					<?= $address . '<br>' . ucwords($city) . ', IL, ' . $pi_zip_code . '<br>'; ?>
					<?= '<a href="' . get_the_permalink() . '" rel="bookmark"><button class="btn btn-primary btn-small">Learn More <icon class="glyphicon glyphicon-arrow-right"></icon></button></a>'; ?>
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