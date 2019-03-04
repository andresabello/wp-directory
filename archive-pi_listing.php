<?php 
/**
 * archive-pi_listings.php
 *
 * The template for displaying archive Listings.
 */
?>

<?php get_header(); ?>
    <!-- header ends -->
    <!-- content -->
    <div class="container">
		<div class="row">
			<div class="main-content col-md-8" role="main">
				<?php 
				if( isset( $_GET['ls']) ){
					$paged = (get_query_var('paged')) ? get_query_var('paged') : 1 ;

					// $a = 'How are you?';

					// if (preg_match('/are/',$a))
					// echo 'true';
					// if( preg_match( '/County/'$_GET['listing_search']  ){

					// }

					// $args = array(
					// 	'posts_per_page'   => 10,
					// 	'orderby'          => 'meta_value',
					// 	'order'            => 'DESC',
					// 	'paged' 			=> $paged,
					//     'meta_query' => array(
					// 		array(
					// 		    'key' => 'pi_city',
					// 		    'value' => $_GET['ls'],
					// 		)
					// 	),
					// 	'post_type'        => 'pi_listing',
					// 	'post_status'      => 'publish',
					// 	'suppress_filters' => true  
					// );

					$args = array(
						'posts_per_page'   => 10,
						'orderby'          => 'meta_value',
						'order'            => 'DESC',
						'paged' 			=> $paged,
					    'meta_query' 	   => array(
					    'relation' 	   	   => 'OR',
							array(
								'key'     => 'pi_city',
								'value'   => $_GET['ls'],
								'compare' => 'LIKE'
							),
							array(
								'key'     => 'pi_county',
								'value'   =>  $_GET['ls'],
								'compare' => 'LIKE'
							),
							array(
								'key'     => 'pi_zip_code',
								'value'   =>  $_GET['ls'],
								'compare' => 'LIKE'
							)
						),
						'post_type'        => 'pi_listing',
						'post_status'      => 'publish',
						'suppress_filters' => true 
					);
					$pi_query = null;
					$pi_query = new WP_Query($args);
					echo '<h1>Found ' . $pi_query->found_posts . ' in Illinois Treatment Centers</h1>';
					if( $pi_query->have_posts() ) {
						while ($pi_query->have_posts()) : $pi_query->the_post();
							
							get_template_part( 'content', 'listing' );

						endwhile;
						$bignum = 999999999;
						if ( $pi_query->max_num_pages <= 1 )
							return;
						
						echo '<div class="row">';
							echo '<div class="col-md-12">';
								echo '<nav class="pagination">';
									echo paginate_links( array(
										'base'         => str_replace( $bignum, '%#%', esc_url( get_pagenum_link($bignum) ) ),
										'format'       => '',
										'current'      => max( 1, get_query_var('paged') ),
										'total'        => $pi_query->max_num_pages,
										'prev_text'    => '<icon class="glyphicon glyphicon-arrow-left"></icon> Previous',
										'next_text'    => 'Next <icon class="glyphicon glyphicon-arrow-right"></icon>',
										'type'         => 'list',
										'end_size'     => 3,
										'mid_size'     => 3
									) );
								echo '</nav>';
							echo '</div>';
						echo '</div>';
					}

					wp_reset_query();
				}else{

					if ( have_posts() ) : ?>
						<header class="page-header">
							<h1><?php _e( 'Illinois Treatment Centers', 'pidirectory' );?></h1>
						</header>

						<?php while( have_posts() ) : the_post(); ?>
							<?php get_template_part( 'content', 'listing' ); ?>
						<?php endwhile; ?>

						<?php pi_paging_nav(); ?>
					<?php else : ?>
						<?php get_template_part( 'content', 'none' ); ?>
					<?php endif; ?>
				<?php } ?>
			</div> <!-- end main-content -->
			<?php get_sidebar(); ?>
		</div> <!-- end row -->
	</div> <!-- end container -->
	<!-- end content -->
<?php get_footer(); ?>