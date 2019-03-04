<?php 
/**
 * front-page.php
 *
 * The template for displaying the home page
 */
	$pi_options = get_option('pi_general_settings');
	$pi_listing_options = get_option('pi_listing_settings');
	$phone_number = ( isset($pi_options['pi_number']) ? $pi_options['pi_number'] : '(999)999-9999' );
	$counties = $pi_listing_options['counties_value'];
	$cities = $pi_listing_options['cities_value']; 

    $listing_options = get_option('pi_listing_settings');
    $counties = $listing_options['counties_value'];
    $counties_array = array();
    $counties_array = explode("\n", $counties );
    $i = 0; 
	$count = count($counties_array);
	foreach ($counties_array as $value) {
		
		
		if( $i < $count - 1 ){
			$str_counties .= $value . ' County, ';
		}else{
			$str_counties .= $value . ' County';
		}
		$i++;
	}

    $cities = $listing_options['cities_value'];
    $cities_array = array();
    $cities_array = explode("\n", $cities );

    $i = 0; 
	$count = count($cities_array);
	foreach ($cities_array as $value) {
		

		if( $i < $count - 1 ){
			$str_cities .= $value . ', ';
		}else{
			$str_cities .= $value;
		}
		$i++;
	}

	$args = array(
		'posts_per_page'   => -1,
		'post_type'        => 'pi_listing',
		'post_status'      => 'publish'
	);
	$listings = get_posts( $args );
	$i = 0; 
	$count = count($listings);
	foreach ($listings as $listing) {
		$meta = get_post_meta($listing->ID, 'pi_zip_code', true);
		if( $i < $count - 1 ){
			$str_zip .= $meta . ', ';
		}else{
			$str_zip .= $meta;
		}
		$i++;
	}

?>

<?php get_header(); ?>
	<!-- Home Page Top Block -->
	<div class="main-cta">
		<div class="container">
			<div class="row">
				<div class="col-md-6">
					<?= pi_listing_search_form(); ?>
					<input type="hidden" value="<?= $str_counties; ?>" id="str-counties" >
					<input type="hidden" value="<?= $str_cities; ?>" id="str-cities" >
					<input type="hidden" value="<?= $str_zip; ?>" id="str-zips" >

				</div>
			</div>
		</div>
	</div>
	<!-- End Home Page Top Block -->
    <!-- content -->
    <div class="home-top-content section">
	    <div class="container">
			<div class="row">
				<div class="col-md-6">
					<img class="hero-image visible-lg hidden-xs hidden-md hidden-sm" src="<?= home_url(); ?>/wp-content/uploads/2015/08/home-page-people-image.png" alt="Alcohol Treatment Centers Illinois">
				</div>
					<div class="col-md-6">
					<h2>End Addiction Here</h2>
					<p class="lead">Our goal is also to provide you with hope.</p>
					<p>At AlcoholTreatmentCentersIllinois.com, the goal is to provide more than just access to the top addiction rehabilitation centers in Illinois, our goal is also to provide you with hope. Extensive research has shown that those who have undergone inpatient treatment at a qualified facility have a much greater chance of maintaining long-term sobriety than those who refuse care. Each patient may require different treatment options, and there is a center in the region that can provide these specialized programs. Take time to explore the vast amount of resources available to you, as our network is constantly growing. By taking the time to explore your options, you can finally put an end to addiction.</p> 
					<p>Discover the many benefits of rehabilitation today.</p>
				</div>
			</div>
		</div> <!-- end container -->
		<!-- end content -->
    </div>
    <!-- Middle Promo CTA -->
    <div class="section grey-row top-promo">
    	<div class="container">
    		<div class="row">
    			<div class="col-md-8">
    				<h2>Ready for a change? Call us now.</h2>
    				<p class="lead">Expert help from one of our sponsors is a call away.</p>
    			</div>
    			<div class="col-md-4 text-center">
    				<p><a href="tel:<?= $phone_number ;?>" class="btn btn-lg btn-danger">Call us now. Free</a></p>
    			</div>
    		</div>
    	</div>
    </div>
    <!-- Middle Promo CTA Ends -->
    <div class="home-mid-content green-row section">
	    <div class="container">
			<div class="row">
				<div class="col-md-12">
					<h2 class="text-center">Searching for treatment for you or your loved one?</h2>
				</div>
				<div class="col-md-12">
					<p class="text-center">Looking for a qualified facility in your area is as easy as clicking the region below. If you would prefer to speak to a representative please feel free to dial <?= '<a href="tel:'. $phone_number .'">' . $phone_number . '</a>' ;?>, an addiction specialist is waiting to hear from you. </p>
				</div>
			</div>
			<?php $args = array(
				'posts_per_page'   => -1,
				'meta_key'         => 'pi_page_att',
				'meta_value'       => 'region',
				'post_type'        => 'page',
				'post_status'      => 'publish',
				'suppress_filters' => true 
			);
			$region_pages = get_posts( $args );
			echo '<div class="row text-center">';
			foreach ($region_pages as $page) {
			 	echo '<div class="col-md-4">';
			 		echo '<a href="'. esc_url( get_permalink( $page->ID ) ) .'" class="feature-wrapper">';
			 		echo '<h3>' . $page->post_title . '</h3>';
			 		echo get_the_post_thumbnail( $page->ID, 'medium' );
			 		echo '<button class="btn btn-danger btn-small">Learn More</button>';
			 		echo '</a>';
			 	echo '</div>';
			 } 
			echo '</div>';
			?>			
	
		</div> <!-- end container -->
		<!-- end content -->
    </div>
	<!-- Support Oranization -->
	<div class="supporting-companies section">
		<div class="container">
			<div class="row">
				<div class="col-md-12 text-center">
					<h2>Organizations We Support</h2>
					<p class="lead">Accredited Organizations</p>
				</div>
				<div class="col-md-12 text-center">
					<ul class="list-inline imgOrganization">
					    <li><img src="<?= home_url(); ?>/wp-content/uploads/2015/08/samhsa.png" alt="Samsha"></li>
					    <li><img src="<?= home_url(); ?>/wp-content/uploads/2015/08/naadac.jpg" alt="NAADAC"></li>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<!-- Explore Types of Treatment -->
	<div class="explore-treatment section grey-row">
		<div class="container">
			<div class="row">
				<div class="col-md-12 text-center">
					<h2>Explore Methods of Treatment </h2>
					<p class="lead">Some of our Methods of Treatment</p>
				</div>
			</div>
			<div class="row">
			<?php 
				$args = array(
					'posts_per_page'   => -1,
					'meta_key'         => 'pi_page_att',
					'meta_value'       => 'treatment',
					'post_type'        => 'page',
					'post_status'      => 'publish',
					'suppress_filters' => true 
				);
				$treatment_pages = get_posts( $args );
				$treatment_ids = array();
				$i = 0;
				foreach ($treatment_pages as $treatment ) {
					if( $i <= 3 ){
						echo '<div class="col-md-3">';
							echo '<div class="pi-image-hover">';
								echo get_image_from_post( $treatment->ID );
								echo '<div class="pi-image-click">';
									echo '<h3>' . str_replace('Illinois', '', $treatment->post_title) . '</h3>';
									echo '<p>' . $treatment->post_excerpt . ' <a href="' . esc_url( get_permalink( $treatment->ID ) ) . '">Learn More <i class="glyphicon glyphicon-arrow-right"></i></a></p>';
								echo '</div>';
							echo '</div>';
						echo '</div>';
						$treatment_ids[] = $treatment->ID; 
					}
					$i++;
				}
			?>											
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="load-more text-center">
					<a href="" id="view-more" class="btn btn-danger btn-lg btn-outline"><i class="glyphicon glyphicon-circle-arrow-down"></i> View All</a>
					</div>
				</div>
			</div>
			<div id="expand-therapies" class="expand-therapies">
				<div class="row">
					<?php 
						$args = array(
							'posts_per_page'   => -1,
							'meta_key'         => 'pi_page_att',
							'meta_value'       => 'treatment',
							'post_type'        => 'page',
							'post__not_in'     => $treatment_ids,
							'post_status'      => 'publish',
							'suppress_filters' => true 
						);
						$treatment_pages = get_posts( $args );
						foreach ($treatment_pages as $treatment ) {
						echo '<div class="col-md-3">';
							echo '<div class="pi-image-hover">';
								echo get_image_from_post( $treatment->ID );
								echo '<div class="pi-image-click">';
									echo '<h3>' . str_replace('Illinois', '', $treatment->post_title) . '</h3>';
									echo '<p>' . $treatment->post_excerpt . ' <a href="' . esc_url( get_permalink( $treatment->ID ) ) . '">Learn More <i class="glyphicon glyphicon-arrow-right"></i></a></p>';
								echo '</div>';
							echo '</div>';
						echo '</div>';
						}
					?>													

					<div class="col-md-3 text-center">
						<h3>Add Review</h3>
						<p></p>
						<p><a href="" class="btn btn-success">Learn More</a></p>
					</div>
					<div class="col-md-3 text-center">
						<h3>Add Facility</h3>
						<p></p>
						<p><a href="" class="btn btn-success">Learn More</a></p>
					</div>											
				</div>												
			</div>			
		</div>
	</div>
	<!-- Bottom CTA -->
	<div class="bottom-cta section black-row promo">
		<div class="container">
			<div class="row">
				<div class="col-md-12 text-center">
					<h2>Free Yourself from Addiction today</h2>
					<h3>Speak to one of our sponsors</h3>
					<p><a href="tel:<?= $phone_number; ?>" class="btn btn-lg btn-outline btn-warning"><i class="glyphicon glyphicon-phone"></i> Call us now</a></p>
				</div>
			</div>
		</div>
	</div>




<?php get_footer(); ?>