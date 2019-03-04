<?php
/**
 * single-pi_listing.php
 *
 * The template for displaying single lisntings.
 */
?>

<?php get_header(); ?>

    <!-- header ends -->
    <!-- content -->
    <div class="container">
		<div class="row">
			<div class="main-content col-md-12" role="main">
				<?php while( have_posts() ) : the_post();
					$post_id = get_the_ID();
					$user_id =  get_current_user_id(); 
					$web_link = pi_add_http( get_post_meta( $post_id, 'pi_web_link', true ) );
					$email = get_post_meta( $post_id, 'pi_email', true );
					$address = get_post_meta( $post_id, 'pi_address', true );
					$city = get_post_meta( $post_id, 'pi_city', true );
					$county = get_post_meta( $post_id, 'pi_county', true );
					$listing_images = get_post_meta($post_id, 'pi_plupload', false);
					$img_count = count($listing_images) + count(get_the_post_thumbnail());
					$pi_zip_code = get_post_meta( $post_id, 'pi_zip_code', true );
					$claimant_id = get_post_meta( $post_id, 'pi_claimant_user_id', true);
					
					if( isset($pi_phone) ){
						$pi_phone = $pi_phone;
					}elseif( isset($main_phone) ){
						$pi_phone = $main_phone;
					}else{
						$pi_phone = '(999) 999-9999';
					}

					//TODO: If the user owns the listing or user is admin or editor, then display the link.
					$page = get_page_by_title('Add Listing');
					if( !empty($page) ){
						$edit_link = home_url();
						$link = $page->guid . '?lid=' . $post_id;					
					}
					$args = array(
						'status' => 'approve',
						'post_id' => $post_id, // use post_id, not post_ID
					);
					$rating = get_post_meta($post_id, 'pi_rating', true);
				?>
					<article id="post-<?= $post_id; ?>" <?php post_class(); ?>>
						<div class="row">
							<div class="col-md-8">
								<header class="entry-header">
									<h1> <?php the_title(); ?> </h1>	
								</header>
							</div>
							<div class="col-md-4">
								<?= display_claim_status($post_id, $user_id); ?>
							</div>
						</div>
						<div class="row">
							<div class="col-md-4">
								<div class="pi-rating">
									<div class="pi-stars clearfix">
										<strong>Overall Rating: (<?= ( $rating <= 0 ? '0' : round($rating, 2) ); ?> of 5)</strong>
										<div class="star-wrapper">
											<?php 
											for( $i = 1; $i <= 5; $i++ ){
												echo '<div class="star-'. $i .' pi-star">';
												if( $i <= round( $rating ) ){
													echo '<span class="glyphicon glyphicon-star gold" aria-hidden="true"></span>';
												
												}else{
													echo '<span class="glyphicon glyphicon-star grey" aria-hidden="true"></span>';	
												}
												echo '</div>'; 
											}
											?>
									    </div>
									</div>
								</div>
								<button class="btn btn-primary">Add Your Review</button>
								<button class="btn btn-primary">View All Reviews</button>
								<ul class="listing-info">
									<?= ( !empty($address) ? '<li><icon class="glyphicon glyphicon-map-marker"></icon> ' . $address : ''); ?>
									<?= '<br>' . ucwords($city) . ( !empty($pi_zip_code) ? ', ' . $pi_zip_code : '') . ( !empty($county) ? '. ' . $county . ' County' : ' ') . '</li>'; ?>
									<?= ( !empty($web_link) ? '<li><a href="'. $web_link .'" target="_blank">Visit Website</a></li>' : ' '); ?>
									<?= ( !empty($pi_phone) ? '<li><icon class="glyphicon glyphicon-phone"></icon> ' . $pi_phone . '</li>' : ' ' ); ?>
									<?= ( !empty($email) ? '<li><icon class="glyphicon glyphicon-envelope"></icon><a href="mailto:' . antispambot($email, 1)  . '?Subject=Contact%20Request" target="_top">Send Email</a></li>': ' '); ?>
								</ul>
								<p class="entry-meta"> <?php pi_post_meta();?><?= display_edit_listing( $post_id, $link ); ?></p>
							</div>
							<div class="col-md-8">
								<?php if( has_post_thumbnail() && !post_password_required() || isset($listing_images) && $listing_images ) : ?>
										<?php if( $img_count > 1) : ?>
											<div id="listing-slider" class="carousel slide listing-slider" data-ride="carousel" data-interval="7000">
												<!-- Wrapper for slides -->
												<div class="carousel-inner" role="listbox">
													<div class="item active">
														<?php the_post_thumbnail( 'full', array( 'class'  => 'listing-image' ) ); ?>
													</div>
													<?php foreach( $listing_images as $image ) : ?>
														<div class="item">
															<img  class="listing-image" src="<?= wp_get_attachment_url($image); ?>">
														</div>
													<?php endforeach; ?>
												</div>

												<!-- Controls -->
												<a class="left carousel-control" href="#listing-slider" role="button" data-slide="prev">
													<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
													<span class="sr-only">Previous</span>
												</a>
												<a class="right carousel-control" href="#listing-slider" role="button" data-slide="next">
													<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
													<span class="sr-only">Next</span>
												</a>
											</div>
										<?php else: ?>
										<div class="listing-slider">
											<figure class="entry-thumbnail"><?php the_post_thumbnail( 'full', array( 'class'  => 'listing-image' ) ); ?></figure>
										</div>
										<?php endif; ?> 
								<?php endif; ?> 
							</div>
						</div>


						<!-- content -->
						<div class="entry-content">
							<div class="row">
								<div class="col-md-8">
									<?php 
										the_content( __('Continue reading &rarr;', 'pidirectory') );
										wp_link_pages();
									?>
									<?php comments_template('/comments-listings.php'); ?>
								</div>
								<div class="col-md-4">
									<div id="map" style="height: 350px;"></div>
									<?php 
										$lat = get_post_meta($post_id, 'lat', true);
										$lng = get_post_meta($post_id, 'lng', true);

									echo '<script>
										function initMap() {
											var myLatLng = {lat: ' . $lat . ', lng: ' . $lng . '};
											var map = new google.maps.Map(document.getElementById("map"), {
												center: myLatLng,
												zoom: 10
											});
											var marker = new google.maps.Marker({
												position: myLatLng,
												map: map,
												title: "' . get_the_title() . '"
											});
										}
									</script>';

									?>
									<script src="https://maps.googleapis.com/maps/api/js?callback=initMap" async defer></script>
								</div>
							</div>
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
						
					</article>
					
				<?php endwhile; ?>
			</div> <!-- end main-content -->

		</div> <!-- end row -->
	</div> <!-- end container -->
	<!-- end content -->
<?php get_footer(); ?>