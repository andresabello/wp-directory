<?php
/**
 * Add Listing Post Type
 *
 * Adds LIsting Post Type along with functionality 
 * for both back and front end.
 *
 * @package    Pi_Directory
 * @subpackage Pi_Directory/admin
 * @author     Andres Abello <abellowins@gmail.com>
 */
class Pi_Custom_Meta_Box Extends Pi_Custom_Post_Type  {
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

		parent::__construct( $theme_name, $version );

	}
	/**
	 * Adds the meta box container.
	 */
	public function add_meta_box( $post_type ) {

		//TODO: Write a method that gets this option from theme options
        $post_types = array('pi_listing');
        
        if ( in_array( $post_type, $post_types )) {
			add_meta_box(
				'pi_listing_information',
				__( 'Listing Information', 
				'pidirectory' ),
				array( $this, 'render_pi_listing_information_meta_box' ),
				$post_type,
				'normal',
				'high'
			);
	        add_meta_box(
	            'pi_image_gallery',
	            __( 'Image Gallery', 'pidirectory' ),
	            array( $this, 'render_gallery_box_content' ),
	            $post_type,
	            'normal',
	            'high'
	        );
        }
	}

	/**
	 * Save the meta when the post is saved.
	 *
	 * @param int $post_id The ID of the post being saved.
	 */
	public function save( $post_id ) {
	
		/*
		 * We need to verify this came from the our screen and with proper authorization,
		 * because save_post can be triggered at other times.
		 */

		// Check if our nonce is set.
		if ( ! isset( $_POST['pi_meta_box_nonce'] ) )
			return $post_id;

		$nonce = $_POST['pi_meta_box_nonce'];

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $nonce, 'pi_meta_box' ) )
			return $post_id;

		// If this is an autosave, our form has not been submitted,
                //     so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
			return $post_id;

		// Check the user's permissions.
		if ( 'page' == $_POST['post_type'] ) {

			if ( ! current_user_can( 'edit_page', $post_id ) )
				return $post_id;
	
		} else {

			if ( ! current_user_can( 'edit_post', $post_id ) )
				return $post_id;
		}

		/* OK, its safe for us to save the data now. */

		// Sanitize the user input.
		$pi_web_link = sanitize_text_field( $_POST['pi_web_link'] );
		$pi_email = sanitize_text_field( $_POST['pi_email'] );
		$pi_address = sanitize_text_field( $_POST['pi_address'] );
		$pi_city = sanitize_text_field( $_POST['pi_city'] );
		$pi_county = sanitize_text_field( $_POST['pi_county'] );
		$pi_zip_code = sanitize_text_field( $_POST['pi_zip_code'] );
	    $pi_status = sanitize_text_field( $_POST['pi_claim_status'] );
	    $pi_reset = sanitize_text_field( $_POST['pi_reset'] );
	    $pi_user = $_POST['pi_user'];

	    // Update the meta field in the database.
		update_post_meta( $post_id, 'pi_web_link', $pi_web_link );
		update_post_meta( $post_id, 'pi_email', $pi_email );
		update_post_meta( $post_id, 'pi_address', $pi_address );
		update_post_meta( $post_id, 'pi_city', trim($pi_city) );
		update_post_meta( $post_id, 'pi_county', trim($pi_county) );
		update_post_meta( $post_id, 'pi_zip_code', $pi_zip_code );
	    update_post_meta( $post_id, 'pi_listing_claim_status', $pi_status );
	    
	    if( $pi_status === 'claimed'){
	        update_post_meta( $post_id, 'pi_claimant_user_id', $pi_user );
	    }

	    if( $pi_reset === 'reset'){
	        delete_post_meta( $post_id, 'pi_claimant_user_id');
	        update_post_meta( $post_id, 'pi_listing_claim_status', 'unclaimed');
	    }
	}
	/**
	 * Prints the box content.
	 * 
	 * @param WP_Post $post The object for the current post/page.
	 */
	public function render_gallery_box_content( $post, $callback_args ) {
	    // Add an nonce field so we can check for it later.
	    wp_nonce_field( 'pi_meta_box', 'pi_meta_box_nonce' );
	    /*
	     * Use get_post_meta() to retrieve an existing value
	     * from the database and use the value for the form.
	     */
	    $field = '';
	    $field = $this->normalize_field( $callback_args['args'] );
	    $meta = get_post_meta($post->ID, $field['field_id']);
	    echo $this->html($meta, $field);
	}
	/**
	 * Render Meta Box content.
	 *
	 * @param WP_Post $post The post object.
	 */
	public function render_pi_listing_information_meta_box( $post ) {
		// Add an nonce field so we can check for it later.
		wp_nonce_field( 'pi_meta_box', 'pi_meta_box_nonce' );

		// Use get_post_meta to retrieve an existing value from the database.
		$web_link = get_post_meta( $post->ID, 'pi_web_link', true );
		$email = get_post_meta( $post->ID, 'pi_email', true );
		$address = get_post_meta( $post->ID, 'pi_address', true );
		$city = get_post_meta( $post->ID, 'pi_city', true );
		$county = get_post_meta( $post->ID, 'pi_county', true );
		$pi_zip_code = get_post_meta( $post->ID, 'pi_zip_code', true );
	    $claim_status = get_post_meta( $post->ID, 'pi_listing_claim_status', true );
	    $claimant = get_post_meta( $post->ID, 'pi_claimant_user_id', true );
	    $claimant_data =  get_userdata( $claimant );
	    $claimant_nicename = $claimant_data->user_nicename;

	    $listing_options = get_option('pi_listing_settings');
	    $counties = $listing_options['counties_value'];
	    $counties_array = array();
	    $counties_array = explode("\n", $counties );
	    
	    $cities = $listing_options['cities_value'];
	    $cities_array = array();
	    $cities_array = explode("\n", $cities );
	    
		$web_link = pi_add_http( $web_link );

		// Display the form, using the current value.
		echo '<label for="pi_web_link">';
		_e( 'Website Link: ', 'pidirectory' );
		echo '</label> ';
		echo '<input type="text" id="pi_web_link" name="pi_web_link"';
                echo ' value="' . esc_attr( $web_link ) . '" size="50" />';

        echo '<label for="pi_email">';
		_e( 'Email: ', 'pidirectory' );
		echo '</label> ';
		echo '<input type="email" id="pi_email" name="pi_email"';
                echo ' value="' . esc_attr( $email ) . '" size="50" />';
        
        echo '<label for="pi_address">';
		_e( 'Address: ', 'pidirectory' );
		echo '</label> ';
		echo '<input type="text" id="pi_address" name="pi_address"';
                echo ' value="' . esc_attr( $address ) . '" size="50" />';
        
        echo '<label for="pi_county">';
		_e( 'County: ', 'pidirectory' );
		echo '</label> ';
		echo '<select name="pi_county">';
			foreach ($counties_array as $key => $c) {
				echo '<option value="' . trim( $c ) . '" '. selected( trim($county), trim($c) ) .' >' . trim( $c ) . '</option>';

			}
		echo '</select>';

        echo '<label for="pi_city">';
		_e( 'City: ', 'pidirectory' );
		echo '</label> ';
		echo '<select name="pi_city">';
			foreach ($cities_array as $key => $ci) {
				echo '<option value="' . trim( $ci ) . '" '. selected( trim($city), trim($ci) ) .' >' . trim( $ci ) . '</option>';
			}
		echo '</select>';

        echo '<label for="pi_zip_code">';
		_e( 'Zip Code: ', 'pidirectory' );
		echo '</label> ';
		echo '<input type="text" id="pi_zip_code" name="pi_zip_code"';
                echo ' value="' . esc_attr( $pi_zip_code ) . '" size="50" />';

	    echo '<label for="pi_claim_status">';
	    _e('Claim Status: ', 'pidirectory'); 
	    echo '</label>';
	    echo '<select name="pi_claim_status">';
	    echo '<option value="unclaimed"' . selected( $claim_status, 'unclaimed' ) . '>Unclaimed</option>';
	    echo '<option value="pending"' . selected( $claim_status, 'pending' ) . '>Pending Approval</option>';
	    echo '<option value="claimed"' . selected( $claim_status, 'claimed' ) . '>Claimed</option>';
	    echo '</select>';
	    echo '<p>Claimant:' . ( $claim_status === 'claimed' ? esc_attr( $claimant_nicename ) : 'Unclaimed' ) . '</p>';
	    echo '<input type="hidden" name="pi_user" value="'. $claimant .'">';
	    echo '<label for="pi_reset">';
	    echo '<input type="checkbox" name="pi_reset" value="reset"' . ( isset ( $claimant ) ? checked( $claimant, 'reset' ) : ' ') .  '>';
	    echo 'Reset Claimant';
	    echo '</label>';

	}
	/**
	 * The following normalizes fields for displaying the right images
	 * @since 3.9.1
	 *
	 * @param  $meta = the meta field where the data is saved
	 *         $field = the name of the field
	 * @return $html
	 */
	public function normalize_field( $field ){
	    $field['field_id'] = 'pi_plupload';
	    $field['id'] = 'pi_plupload';
	    $field = wp_parse_args( $field, array(
	        'id'               => $field['field_id'],
	        'std'              => array(),
	        'force_delete'     => true,
	        'max_file_uploads' => 25,
	        'mime_type'        => '',
	        'clone'            => false,
	    ) );
	    $field['multiple'] = true;
	    $field['js_options'] = array(
	        'runtimes'              => 'html5,silverlight,flash,html4',
	        'file_data_name'        => 'async-upload',
	        //'container'               => $field['id'] . '-container',
	        'browse_button'         => $field['field_id'] . '-browse-button',
	        'drop_element'          => $field['field_id'] . '-dragdrop',
	        'multiple_queues'       => true,
	        'max_file_size'         => wp_max_upload_size() . 'b',
	        'url'                   => admin_url( 'admin-ajax.php' ),
	        'flash_swf_url'         => includes_url( 'js/plupload/plupload.flash.swf' ),
	        'silverlight_xap_url'   => includes_url( 'js/plupload/plupload.silverlight.xap' ),
	        'multipart'             => true,
	        'urlstream_upload'      => true,
	        'filters'               => array(
	            array(
	                'title'      => _x( 'Allowed Image Files', 'image upload', 'pi' ),
	                'extensions' => 'jpg,jpeg,gif,png',
	            ),
	        ),
	        'multipart_params'      => array(
	            'field_id'  => $field['field_id'],
	            'action'    => 'pi_plupload_image_upload',
	        )
	    );
	    return $field;
	}
	/**
	 * The following holds the html where the images will be.
	 * @since 3.9.1
	 *
	 * @param  $meta = the meta field where the data is saved
	 *         $field = the name of the field
	 * @return $html
	 */
	public function html( $meta, $field ){
	    if ( ! is_array( $meta ) )
	        $meta = ( array ) $meta;
	    // Filter to change the drag & drop box background string
	    $i18n_drop   = apply_filters( 'pi_plupload_image_drop_string', _x( 'Drop images here', 'image upload', 'pi' ), $field );
	    $i18n_or     = apply_filters( 'pi_plupload_image_or_string', _x( 'or', 'image upload', 'pi' ), $field );
	    $i18n_select = apply_filters( 'pi_plupload_image_select_string', _x( 'Select Files', 'image upload', 'pi' ), $field );
	    /**
	     * Uploaded images 
	     */ 
	    /** Check for max_file_uploads **/
	    $classes = array( 'pi-drag-drop', 'drag-drop', 'hide-if-no-js', 'new-files');
	    if ( ! empty( $field['max_file_uploads'] ) && count( $meta ) >= (int) $field['max_file_uploads']  )
	        $classes[] = 'hidden';
	    $html = $this->get_uploaded_images( $meta, $field );
	    // Show form upload
	    $html .= sprintf(
	        '<div id="%s-dragdrop" class="%s" data-upload_nonce="%s" data-js_options="%s">
	            <div class = "drag-drop-inside">
	                <p class="drag-drop-info">%s</p>
	                <p>%s</p>
	                <p class="drag-drop-buttons"><input id="%s-browse-button" type="button" value="%s" class="button" /></p>
	            </div>
	        </div>',
	        $field['field_id'],
	        implode( ' ', $classes ),
	        wp_create_nonce( "pi-upload-images_{$field['field_id']}" ),
	        esc_attr( json_encode( $field['js_options'] ) ),
	        $i18n_drop,
	        $i18n_or,
	        $field['field_id'],
	        $i18n_select
	    );
	    return $html;
	}
	/**
	 * The following displays images as thumbnails in order
	 * @since 3.9.1
	 *
	 * @param  $meta = the meta field where the data is saved
	 *         $field = the name of the field
	 * @return $html
	 */
	public function img_html( $image ){
	    $i18n_delete = apply_filters( 'pi_image_delete_string', _x( 'Delete', 'image upload', 'pi' ) );
	    $i18n_edit   = apply_filters( 'pi_image_edit_string', _x( 'Edit', 'image upload', 'pi' ) );
	    $li = '
	        <li id="item_%s">
	            <img src="%s" />
	            <div class="pi-image-bar">
	                <a title="%s" class="pi-edit-file" href="%s" target="_blank">%s</a> |
	                <a title="%s" class="pi-delete-file" href="#" data-attachment_id="%s">&times;</a>
	            </div>
	        </li>
	    ';
	    $src  = wp_get_attachment_image_src( $image, 'thumbnail' );
	    $src  = $src[0];
	    $link = get_edit_post_link( $image );
	    //var_dump($image->ID);
	    return sprintf($li,$image,$src,$i18n_edit, $link, $i18n_edit,$i18n_delete, $image);
	}
	/**
	 * The following gets uploaded images
	 * @since 3.9.1
	 *
	 * @param  $meta = the meta field where the data is saved
	 *         $field = the name of the field
	 * @return $html
	 */
	public function get_uploaded_images( $images, $field ){
	    $reorder_nonce = wp_create_nonce( "pi-reorder-images_{$field['field_id']}" );
	    $delete_nonce = wp_create_nonce( "pi-delete-file_{$field['field_id']}" );
	    $classes = array( 'pi-images', 'pi-uploaded' );
	    if($field['force_delete'] == false){
	        $field['force_delete'] = 0;
	    }else{
	        $field['force_delete'] = 1;
	    }
	    if ( count( $images ) <= 0  )
	        $classes[] = 'hidden';
	    $ul = '<ul class="%s" data-field_id="%s" data-delete_nonce="%s" data-reorder_nonce="%s" data-force_delete="%s" data-max_file_uploads="%s">';
	    $html = sprintf(
	        $ul,
	        implode( ' ', $classes ),
	        $field['field_id'],
	        $delete_nonce,
	        $reorder_nonce,
	        $field['force_delete'],
	        $field['max_file_uploads']
	    );
	    foreach ( $images as $image )
	    {
	        $html .= $this->img_html( $image );
	    }
	    $html .= '</ul>';
	    return $html;
	}
	/**
	 * The following handles the ajax plupload functions
	 * @since 3.9.1
	 *
	 * @return void
	 */
	public function pi_handle_upload(){
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
	        wp_send_json_success( $this->img_html($id) );
	    }
	    exit;
	}
	/**
	 * The following handles the ajax delete functions
	 * @since 3.9.1
	 *
	 * @return void
	 */
	public function pi_ajax_delete_file(){
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
	        wp_send_json_error( __( 'Error: Cannot delete file', 'pi' ) );
	}
	/**
	 * The following handles the ajax reorder functions
	 * @since 3.9.1
	 *
	 * @return void
	 */
	public function pi_ajax_reorder_images(){
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
	/**
	 * The code that runs during theme activation.
	 */
	public function activate_pi_directory() {
	    //Register Post Type
	    $this->register_post_type();
		
	    //Flush all rewrite rules
	    parent::flush(); 

	    //Add Edit and Add Listing Page
	    $this->add_listing_page();
	}

	/**
	 * Register Page to Add and Edit Listing
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	public function add_listing_page(){
		//Adds "add listing" page
		$page = get_page_by_name('Add Listing');
		if (empty($page)) {
			// Create post object
			$pi_page = array(
			  'post_title'    => 'Add Listing',
			  'post_content'  => 'You can add text here.',
			  'post_status'   => 'publish',
			  'post_type'	  => 'page',
			  'post_author'   => 1,
			  'page_template' => 'page-add-listing.php'
			);

			// Insert the post into the database
			wp_insert_post( $pi_page );
		}  
	}
    /**
     * When the post is saved, saves our custom data.
     *
     * @param int $post_id The ID of the post being saved.
     */
    public function pi_save_page_att( $post_id ) {
        /*
         * We need to verify this came from our screen and with proper authorization,
         * because the save_post action can be triggered at other times.
         */

        // Check if our nonce is set.
        if ( ! isset( $_POST['pi_meta_box_nonce'] ) ) {
            return;
        }

        // Verify that the nonce is valid.
        if ( ! wp_verify_nonce( $_POST['pi_meta_box_nonce'], 'pi_save_meta_box_data' ) ) {
            return;
        }

        // If this is an autosave, our form has not been submitted, so we don't want to do anything.
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        // Check the user's permissions.
        if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {

            if ( ! current_user_can( 'edit_page', $post_id ) ) {
                return;
            }

        } else {
            if ( ! current_user_can( 'edit_post', $post_id ) ) {
                return;
            }
        }
        /* OK, it's safe for us to save the data now. */
        // Make sure that it is set.

        if ( isset( $_POST['pi_page_att'] ) ) {
            $pi_page = sanitize_text_field( $_POST['pi_page_att'] );
            // Update the meta field in the database.
            update_post_meta( $post_id, 'pi_page_att', $pi_page );
        }else{
            update_post_meta( $post_id, 'pi_page_att', 'normal' );
        }
    }
    /**
     * Adds a box to the main column on the Post and Page edit screens.
     */
    public function pi_add_meta_page_attr() {

        $screens = array( 'page' );

        add_meta_box(
            'page_attr',
            __( 'Region Page', 'pidirectory' ),
            array($this, 'pi_page_att_callback'),
            'page', 
            'side',
            'default'
        );
    }
    /**
     * Prints the box content.
     * 
     * @param WP_Post $post The object for the current post/page.
     */
    public function pi_page_att_callback( $post ) {

        // Add a nonce field so we can check for it later.
        wp_nonce_field( 'pi_save_meta_box_data', 'pi_meta_box_nonce' );

        /*
         * Use get_post_meta() to retrieve an existing value
         * from the database and use the value for the form.
         */
        $value = get_post_meta( $post->ID, 'pi_page_att', true );

        echo '<label for="pi_page_att">';
        _e( 'Type of Page', 'pidirectory' );
        echo '</label> ';
        echo '<select name="pi_page_att">';
            echo '<option value="normal"' . selected( $value, 'normal' ) . '>Normal</option>';
            echo '<option value="region"' . selected( $value, 'region' ) . '>Region Page</option>';
            echo '<option value="treatment"' . selected( $value, 'treatment' ) . '>Treatment Page</option>';
        echo '</select>';
    }
}