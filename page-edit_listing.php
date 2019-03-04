<?php 
/**
 * page-edit_listing.php
 *
 * Template Name: Edit Listing
 */

	$current_post = get_post( $_GET['lid'] );  
	$user_id = get_current_user_id();
	$claimant = get_post_meta( $current_post->ID, 'pi_claimant_user_id', true );
	$web_link = get_post_meta( $current_post->ID, 'pi_web_link', true );
	$email = get_post_meta( $current_post->ID, 'pi_email', true );
	$address = get_post_meta( $current_post->ID, 'pi_address', true );
	$city = get_post_meta( $current_post->ID, 'pi_city', true );
	$county = get_post_meta( $current_post->ID, 'pi_county', true );
	$pi_zip_code  = get_post_meta( $current_post->ID, 'pi_zip_code', true );
	$featured_img = wp_get_attachment_image_src( get_post_thumbnail_id($current_post->ID), 'medium' );
	$listing_options = get_option('pi_listing_settings');	    
	$counties = $listing_options['counties_value'];
	$counties = explode("\n", $counties );
	$cities = $listing_options['cities_value'];
	$cities = explode("\n", $cities);
	$error = array();
	if( isset($_POST['submitted']) && isset($_POST['pi_edit_listing_nonce_field']) && wp_verify_nonce($_POST['pi_edit_listing_nonce_field'], 'pi_edit_listing_nonce')) {
		
		//Validate data
		$errors = pi_listing_validation( trim( $_POST['listing-email']), trim($_POST['listing-title']), trim($_POST['listing-desc']) );
		
		//Handle File Upload
		if( file_exists($_FILES['listing-image']['tmp_name']) || is_uploaded_file($_FILES['listing-image']['tmp_name']) ){
			// These files need to be included as dependencies when on the front end.
			require_once( ABSPATH . 'wp-admin/includes/image.php' );
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
			require_once( ABSPATH . 'wp-admin/includes/media.php' );
			
			// Let WordPress handle the upload.
			$attachment_id = media_handle_upload( 'listing-image', $current_post->ID );
			
			if ( is_wp_error( $attachment_id ) ) {
				$errors['no_image'] = 'There was an error uploading the image.';
			}else{
				set_post_thumbnail($current_post->ID, $attachment_id);
			}
		}

		if( count($errors) === 0 ) {

			$listing_title = sanitize_text_field($_POST['listing-title']);
			$listing_content = sanitize_text_field($_POST['listing-desc']);
			$listing_address = trim($_POST['listing-address']);
			$listing_city = trim($_POST['listing-city']);
			$listing_county = trim($_POST['listing-county']);
			$listing_zip = trim($_POST['listing-zip']);
			$listing_web = trim($_POST['listing-web']);
			$listing_email = pi_email_validate_sanitize($_POST['listing-email']);
			$listing_img = trim($_POST['listing-image']);


			$post_information = array(
				'ID' 			=> $current_post->ID,
				'post_content' 	=> $listing_content,
				'post_title' 	=> $listing_title,
				'post-type' 	=> 'pi_listing'
			);

			$posted = wp_update_post($post_information);

			if( $posted ){
				update_post_meta($current_post->ID, 'pi_address', sanitize_text_field($listing_address) );
				update_post_meta($current_post->ID, 'pi_city', sanitize_text_field($listing_city) );
				update_post_meta($current_post->ID, 'pi_county', sanitize_text_field($listing_county) );
				update_post_meta($current_post->ID, 'pi_web_link', sanitize_text_field($listing_web) );
				update_post_meta($current_post->ID, 'pi_email', $listing_email );
				update_post_meta($current_post->ID, 'pi_zip_code', sanitize_text_field($listing_zip) );

				// update_post_meta($current_post->ID, 'pi_zip_code', sanitize_text_field($listing_zip) );
				wp_redirect( get_permalink($current_post->ID) );
				exit;
			}

		}else{
			$has_error = true;
		}
	}
?>
<?php get_header(); ?>
    <!-- header ends -->
    
    <!-- content -->
    <div class="container">
		<div class="row">
			<div class="main-content col-md-12" role="main">

				<?php if( is_user_logged_in() && $user_id == $claimant || current_user_can('administrator') ) : ?>
					
					<?php 
						if( $has_error === true ) {
							echo '<ul class="pi-error">';
							foreach ($errors as $e) {
								echo '<li>'. $e .'</li>';
							}
							echo '</ul>'; 
 						}
 					?>
 					<p class="breadcrumbs"><a href="<?php echo ( isset( $_GET['lid'] ) ? $current_post->guid : home_url() ) ;?> "><i class="glyphicon glyphicon-arrow-left"></i> Go Back </a></p>
					<form action="" method="post" enctype="multipart/form-data" class="listing-edit">
						<div class="form-group">
							<label for="listing-title">Title* : </label>
							<input type="text" class="form-control" name="listing-title" value="<?= sanitize_text_field($current_post->post_title); ?>">
						</div>
						<div class="form-group">
							<label for="listing-desc">Description : </label>
							<textarea class="form-control" name="listing-desc" rows="20"><?= esc_textarea($current_post->post_content); ?></textarea>
						</div>
						<div class="form-group">
							<label for="listing-address">Address : </label>
							<input class="form-control" name="listing-address" value="<?= ( isset($address) ? $address : ' ') ; ?>">
						</div>
						<div class="form-group">
							<label for="listing-city">City : </label>
							<select name="listing-city" class="form-control">
							<?php foreach ($cities as $value) : ?>
									<option value="<?= esc_attr( $value ); ?>" <?= selected( $city, trim($value) ); ?> > <?= esc_attr( trim($value) ); ?> </option>
							<?php endforeach; ?>
							</select>
						</div>
						<div class="form-group">
							<label for="listing-county">County : </label>
							<select name="listing-county" class="form-control">
							<?php foreach ($counties as $value) : ?>
									<option value="<?= esc_attr( $value ); ?>" <?= selected( $county, trim($value) ); ?> > <?= esc_attr( trim($value) ); ?> </option>
							<?php endforeach; ?>
							</select>
						</div>
						<div class="form-group">
							<label for="listing-zip">Zip Code* : </label>
							<input class="form-control" name="listing-zip" value="<?= ( isset($pi_zip_code) ? $pi_zip_code : ' ') ; ?>">
						</div>
						<div class="form-group">
							<label for="listing-web">Web Link* : </label>
							<input class="form-control" name="listing-web" value="<?= ( isset($web_link) ? $web_link : ' ') ; ?>">
						</div>
						<div class="form-group">
							<label for="listing-email">Email* : </label>
							<input class="form-control" name="listing-email" value="<?= ( isset($email) ? antispambot($email) : ' ') ; ?>">
						</div>
						<div class="form-group">
							<label for="listing-image">Featured Image : </label><br>
							<img src="<?= $featured_img[0]; ?>" width="150" style="margin-bottom: 20px;">
							<input type="file" name="listing-image">
						</div>
						<div class="form-group">
							<label for="listing-gallery">Gallery Images : </label>
							<div class="pi-plupload-wrapper">
								<?php
								
								$field = ''; 
								$field = normalize_field( $field );
								$meta = get_post_meta($current_post->ID, $field['field_id']);
								echo html($meta, $field);
								echo '<input type="hidden" id="post_ID" value="'. $current_post->ID . '">';
								// echo '<pre>';  print_r(get_post_meta(0)); echo '</pre>';
								?>
							</div>
						</div>
						<?php wp_nonce_field( 'pi_edit_listing_nonce', 'pi_edit_listing_nonce_field' ); ?>
						<input type="hidden" name="listing-id" value="<?= $current_post->ID; ?>">
						<input type="hidden" name="submitted" id="submitted" value="true" />
						<button type="submit" class="btn btn-primary">Update Listing</button>
					</form>
				<?php else: ?>
					<p>Sorry, you don't have access to this area.</p>
				<?php endif; ?>
			</div> <!-- end main-content -->
		</div> <!-- end row -->
	</div> <!-- end container -->
	<!-- end content -->
<?php get_footer(); ?>