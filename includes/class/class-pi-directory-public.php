<?php 
/**
 * Register all actions and filters for the theme.
 *
 * Maintain a list of all hooks that are registered throughout
 * the theme, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 *
 * @package    Pi_Directory
 * @subpackage Pi_Directory_Public/includes
 * @author     Andres Abello <abellowins@gmail.com>
 */
class Pi_Directory_Public{
	/**
	 * The ID of this Theme.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $theme_name    The ID of this theme.
	 */
	private $theme_name;

	/**
	 * The version of this theme.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this theme.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $theme_name	The name of the theme.
	 * @param      string    $version    The version of this theme.
	 */
	public function __construct( $theme_name, $version ) {

		$this->theme_name = $theme_name;
		$this->version = $version;

	}
	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles(){
		//Styles
		wp_register_style( 'bootstrap', STYLES . '/bootstrap.css', false, '3.3.5' );
	    wp_register_style( $this->theme_name, STYLES . '/custom.css', array( 'bootstrap'), $this->version);
	    wp_enqueue_style( $this->theme_name );

	    //Get user's chosen general theme options
    	$pi_options = get_option('pi_general_settings');
    	$font = $pi_options['pi_font_family'];
    	$body_color = $pi_options['pi_font_color'];
    	$main_color = $pi_options['pi_main_color_picker']; 
		$second_color_hex = $pi_options['pi_second_color_picker']; 
		$second_color_rgba = hextorgba($second_color_hex);

		//Get user's chosen home theme options
		$pi_home = get_option('pi_homepage_settings');
		$main_cta = $pi_home['pi_main_image'];

		//Add google webfonts    	
	    if( $font === 'Droid Sans'){
	        wp_enqueue_style( 'droid-sans', 'http://fonts.googleapis.com/css?family=Droid+Sans:400,700' );
	    }elseif( $font === 'Open Sans'){
	        wp_enqueue_style( 'open-sans', 'http://fonts.googleapis.com/css?family=Open+Sans:400,700' );
	    }elseif( $font === 'Lato'){
	        wp_enqueue_style( 'lato', 'http://fonts.googleapis.com/css?family=Lato:400,700' );
	    }elseif( $font === 'Bitter'){
	        wp_enqueue_style( 'Bitter', 'http://fonts.googleapis.com/css?family=Bitter:400,700' );
	    }

    	//Add user's chosen colors
    	$custom_css = "
			body{
	            color: {$body_color};
            	font-family: '{$font}', sans-serif;
	        }
	        .main-color{
	        	background-color: {$main_color}; 
	        }
	        .second-color{
	        	background-color: {$second_color_hex};
	        }
	       	.home .second-color{
	        	background-color: {$second_color_rgba};
	        }
	        .main-cta{
	        	background-image: url({$main_cta});
	        	background-size: cover;
	        	background-postiion: 50% 50%;
	        	background-repeat: no-repeat;
	        	border-color: {$main_color};
	        }

		";
		wp_add_inline_style( $this->theme_name, $custom_css );	
	}
	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts(){
		//Scripts
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-sortable');
		wp_enqueue_script( 'bootstrap', SCRIPTS . '/bootstrap.min.js', array('jquery'), '3.3.5', true );
		wp_enqueue_script( $this->theme_name, SCRIPTS . '/pi-script.js', array('jquery'), $this->version, true );
		wp_enqueue_script( 'pi-image', SCRIPTS .'/image.js', array( 'jquery-ui-sortable' ), $this->version, true );
		wp_enqueue_script( 'claim-script', SCRIPTS . '/claim-script.js', array('jquery'), $this->version, true );
		// wp_enqueue_script( 'form-script', SCRIPTS . '/pi-form.js', array('jquery'), '1.0.0', true );


		/** localize script to handle ajax using wordpress and not an outside source. piajax is your ajax varible **/

		// wp_enqueue_script( 'modernizr', SCRIPTS . '/modernizr.min.js', array('jquery'), '2.8.3', true );

		//Localize scripts for ajax use
		$data = array(
			'nonce' => wp_create_nonce(  'pi_msg_ajax' ),
			'ajaxURL' => admin_url( 'admin-ajax.php' ),
			'failMessage' => __( 'Request Failed With Code ', 'pidirectory')
		);
		wp_localize_script( $this->theme_name, 'pi_msg_ajax', $data );


		//Only run script on single edit pi listing template
		if ( is_page_template( 'page-edit_listing.php' ) ) {
			wp_enqueue_script( 'pi-plupload', SCRIPTS . '/pi-plupload.js', array( 'jquery','wp-ajax-response', 'plupload-all' ), '1.0.0', true );
			/** localize script to handle ajax using wordpress and not an outside source. piajax is your ajax varible **/
			wp_localize_script( 'pi-plupload', 'piajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'imgs' => IMAGES ));
			wp_localize_script( 'pi-plupload', 'piFile', array( 'maxFileUploadsSingle' => __( 'You may only upload maximum %d file', 'pidirectory' ), 'maxFileUploadsPlural' => __( 'You may only upload maximum %d files', 'pidirectory' ),));
		}
		//Only run script on single pi listing template
		if( is_singular('pi_listing') ){
		    $claim_data = array(
		        'nonce' => wp_create_nonce(  'pi_claim_ajax' ),
		        'ajaxURL' => admin_url( 'admin-ajax.php' ),
		        'failMessage' => __( 'Request Failed With Code ', 'pi_claim_ajax')
		    );
		    wp_localize_script( 'claim-script', 'pi_claim_ajax', $claim_data );
		    wp_enqueue_script( 'pi-listing', SCRIPTS . '/pi-listing.js', array('jquery'), '1.5.7', true );

		}
		//Only run script on home, blog and pi_listing page
		if ( is_front_page() || is_archive() || is_home() ){
	        wp_enqueue_script( 'pi-typehead', SCRIPTS . '/typehead.min.js', array( 'jquery' ), '', true );
	        wp_enqueue_script( 'pi-search' , SCRIPTS . '/pi-search.js', array( 'pi-typehead' ), '', true );
		}
	}
	/**
	*
	* Replace the standard wordpress excerpt read more link
	*
	**/
	public function pi_replace_read_more( $more ) {
		return ' <a class="read-more" href="' . get_permalink( get_the_ID() ) . '">' . __( 'Read More', 'pidirectory' ) . ' <icon class="glyphicon glyphicon-arrow-right"></icon> </a>';
	}
	/**
	*
	* Remove links from comment posts
	*
	**/
	public function pi_comment_post( $incoming_comment ) {
		$incoming_comment['comment_content'] = htmlspecialchars($incoming_comment['comment_content']);
		$incoming_comment['comment_content'] = str_replace( "'", '&apos;', $incoming_comment['comment_content'] );
		return( $incoming_comment );
	}
	/**
	*
	* Never display links on comments
	*
	**/
	public function pi_comment_display( $comment_to_display ) {
		$comment_to_display = str_replace( '&apos;', "'", $comment_to_display );
		return $comment_to_display;
	}
	
	public function pi_rating_field() {
		if( is_singular( 'pi_listing' ) ){
			echo '<p class="comment-form-rating">';
			  	echo '<label for="rating" class="rating-label">'. __('Rating', 'pidirectory') . '<span class="required">*</span></label>';
			  	echo '<input type="hidden" name="rating" value="'. $i .'" id="star-rating">';
				echo '<div class="pi-stars">' .
					'<div class="star-wrapper">';
						for( $i = 1; $i <= 5; $i++ ){
							echo '<div class="star-'. $i .' pi-star" data-number="'. $i .'">';
								echo '<span class="glyphicon glyphicon-star grey" aria-hidden="true"></span>';
							echo '</div>'; 
						}
				    echo '</div>';
				echo '</div>';
			echo '</p>';	
		}
	}
	
	public function save_comment_rating( $comment_id ) {
		if ( ( isset( $_POST['rating'] ) ) && ( $_POST['rating'] != '') ){
			$rating = wp_filter_nohtml_kses($_POST['rating']);
			add_comment_meta( $comment_id, 'pi_rating', $rating );
			$comment = get_comment( $comment_id );
			$status = $comment->comment_approved;
			$post_id = $comment->comment_post_ID;
			$comments = get_comments('post_id=' . $post_id);
			if( $status == 1 ){
				$pi_rating = array();
				foreach($comments as $comment){
					$pi_rating[] = get_comment_meta($comment->comment_ID, 'pi_rating', true);
					$sum = array_sum( $pi_rating );
				}
				$rating = $sum / count($comments);
				update_post_meta( $post_id, 'pi_rating', $rating );					
			}
		}
	}

	public function delete_pi_rating( $comment_id ){

		$comment = get_comment( $comment_id );
		$post_id = $comment->comment_post_ID;
		$comments = get_comments('post_id=' . $post_id);

		$pi_rating = array();
		foreach($comments as $comment){
			$pi_rating[] = get_comment_meta($comment->comment_ID, 'pi_rating', true);
			$sum = array_sum( $pi_rating );
		}
		$rating = $sum / count($comments);
		update_post_meta( $post_id, 'pi_rating', $rating );
	}
	
	public function pi_comment_approved($comment) {
		$post_id = $comment->comment_post_ID;
		$comments = get_comments('post_id=' . $post_id);
		$pi_rating = array();
		foreach($comments as $comment){
			$pi_rating[] = get_comment_meta($comment->comment_ID, 'pi_rating', true);
			$sum = array_sum( $pi_rating );
		}
		$rating = $sum / count($comments);
		update_post_meta( $post_id, 'pi_rating', $rating );
	}
	
	public function verify_rating_submission( $commentdata ) {
		$post_id = $commentdata['comment_post_ID'];
		$post_type = get_post_type($post_id);
		if( $post_type === 'pi_listing' ){
			if ( !isset( $_POST['rating'] ) || $_POST['rating'] === '' ){
				wp_die( __( 'Error: You did not add a rating. Hit the Back button on your Web browser and resubmit your comment with a rating.', 'pidirectory' ) );			
			}else{
				return $commentdata;
			}

		}else{
			return $commentdata;
		}
	}
	/**
	 * Respond to claim listing POST request
	 */
	public function pi_ajax_claim_listing() {
	    //double check that the data is valid. If not, die.
	    if ( !isset( $_POST[ 'userID' ] ) )  {
	        status_header( '400' );
	        die();
	    }

	    $user_id = $_POST[ 'userID' ];
	    $post_id = $_POST[ 'postID' ];
	    $admin_email = 'aabello@recoveryhealthcaresystems.com';

	    //if user is logged in validate nonce and then save their choice
	    if ( is_user_logged_in() ) {
	        if ( ! isset( $_POST[ 'nonce' ] ) || ! wp_verify_nonce( $_POST[ 'nonce' ], 'pi_claim_ajax') ) {
	            status_header( '401' );
	            die();
	        }
	        
	        $claim_status = get_post_meta( $post_id, 'pi_listing_claim_status', true );
	        if( $claim_status === 'pending' || $claim_status === 'claimed'){
	            /*Send email to claimant, stating lsiting already claimed*/
	            $user_data = get_userdata( $user_id );
	            $user_email = $user_data->user_email;
	            $user_nicename = $user_data->user_nicename;
	            $first_headers[] = 'From: WS Web in /<helpline@fordetox.com>';
	            $message = 'Sorry, there is a claim on listing number ' . $post_id  . ' already. If you are the true owner and someone else claimed your listing please contact ' . $admin_email . ' as soon as possible. Thank you!<br>';
	            
	            wp_mail( $user_email, 'Claim Listing Request', $message,  $first_headers );
	            // remove_filter( 'wp_mail_content_type', 'set_html_content_type' );
	            $response = '<div class="alert bs-callout bs-callout-error">We are sorry! it looks like this listing was already claimed. If you are the true owner and someone else claimed your listing please contact ' . $admin_email . ' as soon as possible. Thank you! <button type="button" class="close" data-dismiss="alert">&nbsp; x</button></div>';             
	        }else{
	            /*Send email to claimant*/
	            $user_data = get_userdata( $user_id );
	            $user_email = $user_data->user_email;
	            $user_nicename = $user_data->user_nicename;         
	            $user_message = 'Thank you for claiming listing number ' . $post_id  . '. We will contact your email to veify the information as soon as possible. Thank you!<br>';
	            $headers[] = 'Content-Type: text/html; charset=UTF-8';
	            $headers[] = 'From: Drug Treatment Centers Illinois <info@drugtreatmentcentersillinois.com>';
	            wp_mail( $user_email, 'Claim Listing Request', $user_message,  $headers );
	            // remove_filter( 'wp_mail_content_type', 'set_html_content_type' );
	            /*Sent email to moderator*/
	            $url = home_url();
	            $message = 'Request to claim listing ' . $post_id  . ' on ' . $url . '<br>';
	            
	            update_post_meta( $post_id, 'pi_claimant_user_id', $user_id );
	            update_post_meta( $post_id, 'pi_listing_claim_status', 'pending');
	            
	            wp_mail( $admin_email, 'Claim Listing Request', $message,  $headers );
	            // remove_filter( 'wp_mail_content_type', 'set_html_content_type' );
	            $response = '<div class="alert bs-callout bs-callout-success">Thank you for claiming your listing! We will contact you, via email, to verify it. <button type="button" class="close" data-dismiss="alert">&nbsp; x</button></div>';
	        } 
	    }
	    wp_send_json_success( $response );
	}
	/**
	 * The following handles the ajax plupload functions
	 * @since 3.9.1
	 *
	 * @return void
	 */
	public function pi_handle_frontend_upload(){
	    global $wpdb;
	    $post_id = is_numeric( $_REQUEST['post_id'] ) ? $_REQUEST['post_id'] : 0;
	    $field_id = isset( $_REQUEST['field_id'] ) ? $_REQUEST['field_id'] : '';
	    check_ajax_referer( "pi-upload-images_{$field_id}" );
	    // You can use WP's wp_handle_upload() function:
	    $file       = $_FILES['async-upload'];
	    $file_attr  = wp_handle_upload( $file, array( 'test_form' => false ) );
	    //Get next menu_order
	    $meta = get_post_meta( $post_id, $field_id, false );
	    if( empty( $meta ) ){
	        $next = 0;
	    } else {
	        $meta = implode( ',' , (array) $meta );
	        $max = $wpdb->get_var( "
	            SELECT MAX(menu_order) FROM {$wpdb->posts}
	            WHERE post_type = 'attachment'
	            AND ID in ({$meta})
	        " );
	        $next = is_numeric($max) ? (int) $max + 1: 0;
	    }
	    $attachment = array(
	        'guid'              => $file_attr['url'],
	        'post_mime_type'    => $file_attr['type'],
	        'post_title'        => preg_replace( '/\.[^.]+$/', '', basename( $file['name'] ) ),
	        'post_content'      => '',
	        'post_status'       => 'inherit',
	        'menu_order'        => $next
	    );
	    // Adds file as attachment to WordPress
	    $id = wp_insert_attachment( $attachment, $file_attr['file'], $post_id );
	    if ( ! is_wp_error( $id ) )
	    {
	        wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $file_attr['file'] ) );
	        // Save file ID in meta field
	        add_post_meta( $post_id, $field_id, $id, false );
	        wp_send_json_success( img_html($id) );
	    }
	    exit;
	}

	/**
	 * The following handles the ajax delete functions
	 * @since 3.9.1
	 *
	 * @return void
	 */
	public function pi_ajax_frontend_delete_file(){
	    $post_id       = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;
	    $field_id      = isset( $_POST['field_id'] ) ? $_POST['field_id'] : 0;
	    $attachment_id = isset( $_POST['attachment_id'] ) ? intval( $_POST['attachment_id'] ) : 0;
	    $force_delete  = isset( $_POST['force_delete'] ) ? intval( $_POST['force_delete'] ) : 0;
	    check_ajax_referer( "pi-delete-file_{$field_id}" );
	    delete_post_meta( $post_id, $field_id, $attachment_id );
	    $ok = $force_delete ? wp_delete_attachment( $attachment_id ) : true;
	    if ( $ok )
	        wp_send_json_success();
	    else
	        wp_send_json_error( __( 'Error: Cannot delete file', 'pidirectory' ) );
	}
	/**
	 * The following handles the ajax reorder functions
	 * @since 3.9.1
	 *
	 * @return void
	 */
	public function pi_ajax_frontend_reorder_images(){
	    $field_id = isset( $_POST['field_id'] ) ? $_POST['field_id'] : 0;
	    $order    = isset( $_POST['order'] ) ? $_POST['order'] : 0;
	    $post_id  = isset( $_POST['post_id'] ) ? (int) $_POST['post_id'] : 0;
	    check_ajax_referer( "pi-reorder-images_{$field_id}" );
	    parse_str( $order, $items );
	    delete_post_meta( $post_id, $field_id );
	    foreach ( $items['item'] as $item ){
	        add_post_meta( $post_id, $field_id, $item, false );
	    }
	    wp_send_json_success();
	}

	public function category_id_class( $classes ) {
		foreach ($classes as $key => $value) {
			if( $value == 'hentry'){
				$replace = array( $key => 'entry');
				$classes = array_replace($classes, $replace);
			}
		}	
		return $classes;
	}
	public function pi_listing_home_search_submit( ) {
		// Check if our nonce is set.
		if ( ! isset( $_POST['pi_listing_search_data'] ) ) {
			return;
		}

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $_POST['pi_listing_search_data'], 'pi_listing_nonce_field' ) ) {
			return;
		}

		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		/* OK, it's safe for us to save the data now. */
		// Make sure that it is set.

		if ( !isset($_POST['ls']) ) {
			return ; 
		}else{
			wp_redirect( home_url() . '/listings?ls=' . $_POST['ls'] );
		}

	}
}