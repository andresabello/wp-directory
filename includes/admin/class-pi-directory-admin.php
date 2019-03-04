<?php 

/**
 * The admin-specific functionality of the theme.
 *
 * Defines the theme name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Pi_Directory
 * @subpackage Pi_Directory/admin
 * @author     Andres Abello <abellowins@gmail.com>
 */
class Pi_Directory_Admin {
    /**
     * Holds the values to be used in the fields callbacks
     */
 	private $general_settings_key  = 'pi_general_settings';
    private $homepage_settings_key = 'pi_homepage_settings';
    private $page_settings_key     = 'pi_page_settings';
    private $form_settings_key     = 'pi_form_settings';
    private $footer_settings_key   = 'pi_footer_settings';
    private $css_settings_key      = 'pi_css_settings';
    private $listing_settings_key  = 'pi_listing_settings';
    private $plugin_options_key    = 'pi_page_options';
    private $plugin_settings_tabs  = array();
	/**
	 * The ID of this theme.
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
	 * @param      string    $theme_name       The name of this theme.
	 * @param      string    $version    The version of this theme.
	 */
	public function __construct( $theme_name, $version ) {

		$this->theme_name = $theme_name;
		$this->version = $version;

	}
	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( 'thickbox' );
        wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style( 'pi_admin_css', STYLES . '/admin-styles.css', false, $this->version );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		
		$currentScreen = get_current_screen();

		//Scripts
		wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-ui-sortable');
       	wp_enqueue_script( 'thickbox' );
        wp_enqueue_script( 'media-upload' );
        wp_enqueue_script( 'pi-upload', SCRIPTS . '/pi-upload.js', array( 'thickbox', 'media-upload' ), $this->version );
        // Make sure to add the wp-color-picker dependecy to js file
        wp_enqueue_script( 'pi_custom_js', SCRIPTS .'/pi-picker.js', array( 'jquery', 'wp-color-picker' ), $this->version, true  );
        wp_enqueue_script( 'pi-image', SCRIPTS .'/image.js', array( 'jquery-ui-sortable' ), $this->version, true );

		if( $currentScreen->id === "pi_listing" ) {
	        wp_enqueue_script( 'pi-plupload', SCRIPTS . '/pi-plupload.js', array( 'jquery','wp-ajax-response', 'plupload-all' ), $this->version, true );
	        /** localize script to handle ajax using wordpress and not an outside source. piajax is your ajax varible **/
	        wp_localize_script( 'pi-plupload', 'piajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'imgs' => IMAGES ));
	        wp_localize_script( 'pi-plupload', 'piFile', array( 'maxFileUploadsSingle' => __( 'You may only upload maximum %d file', 'pidirectory' ), 'maxFileUploadsPlural' => __( 'You may only upload maximum %d files', 'pidirectory' ),));
		}

	}
	/**
	 * Save the meta when the post is saved.
	 *
	 * @param int $post_id The ID of the post being saved.
	 */
	public function save_pi_listing_meta( $post_id ) {

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
		$pi_zip_code = sanitize_text_field( $_POST['pi_zip_code'] );
	    $pi_status = sanitize_text_field( $_POST['pi_claim_status'] );
	    $pi_reset = sanitize_text_field( $_POST['pi_reset'] );
	    $pi_user = $_POST['pi_user'];

	    // Update the meta field in the database.
		update_post_meta( $post_id, 'pi_web_link', $pi_web_link );
		update_post_meta( $post_id, 'pi_email', $pi_email );
		update_post_meta( $post_id, 'pi_address', $pi_address );
		update_post_meta( $post_id, 'pi_city', $pi_city );
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
	public function load_settings() {
	    $this->general_settings = (array) get_option( $this->general_settings_key );
	    $this->homepage_settings = (array) get_option( $this->homepage_settings_key );
        $this->page_settings = (array) get_option( $this->page_settings_key );
        $this->form_settings = (array) get_option( $this->form_settings_key );
        $this->footer_settings = (array) get_option( $this->footer_settings_key );
        $this->css_settings = (array) get_option( $this->css_settings_key );
        $this->listing_settings = (array) get_option( $this->listing_settings_key );


	    // Merge with defaults
	    $this->general_settings = array_merge( array(
	        'general_option' => 'Home Page Options'
	    ), $this->general_settings );
	    $this->homepage_settings = array_merge( array(
	        'homepage_option' => 'Home Page Options'
	    ), $this->homepage_settings );
        $this->page_settings = array_merge( array(
            'page_option' => 'Page Options'
        ), $this->page_settings );
        $this->form_settings = array_merge( array(
            'form_option' => 'Form Options'
        ), $this->form_settings );
        $this->footer_settings = array_merge( array(
            'footer_option' => 'Footer Options'
        ), $this->footer_settings );
        $this->css_settings = array_merge( array(
            'css_option' => 'CSS Options'
        ), $this->css_settings );
        $this->listing_settings = array_merge( array(
            'listing_option' => 'Listing Options'
        ), $this->listing_settings );
	}
    /**
     * Add options page
     */
    public function add_options_page(){
        $pi_options_page = add_options_page(
            'Settings Admin', 
            'Pi Theme Options', 
            'manage_options', 
             $this->plugin_options_key, 
            array( $this, 'create_admin_page' )
        );


    }
    /**
     * Options page callback
     */
    public function create_admin_page(){
    	$tab = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->general_settings_key;
        // Set class property
        $this->options = get_option( 'pi_option_name' );
        ?>
        <div class="wrap">
			<?php $this->plugin_options_tabs(); ?>
            <br>
            <a href="http://piboutique.com/pi-directory/docs/">Documentation &rarr;</a>           
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
            	wp_nonce_field( 'update-options' );
                settings_fields( $tab);   
                do_settings_sections( $tab );
                submit_button(); 
            ?>
            </form>
        </div>
        <?php
    }
	/*
	 * Renders our tabs in the plugin options page,
	 * walks through the object's tabs array and prints
	 * them one by one. Provides the heading for the
	 * plugin_options_page method.
	 */
	public function plugin_options_tabs() {
		$current_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->general_settings_key;
		screen_icon();
		echo '<h2 class="nav-tab-wrapper">';
		foreach ( $this->plugin_settings_tabs as $tab_key => $tab_caption ) {
			$active = $current_tab == $tab_key ? 'nav-tab-active' : '';
			echo '<a class="nav-tab ' . $active . '" href="?page=' . $this->plugin_options_key . '&tab=' . $tab_key . '">' . $tab_caption . '</a>';	
		}
		echo '</h2>';
	}
    /**
     * Register and add general settings
     */
    public function register_general_settings(){
		$this->plugin_settings_tabs[$this->general_settings_key] = 'General';
		register_setting( 
			$this->general_settings_key, 
			$this->general_settings_key,
            array( $this, 'sanitize' )
		);
		add_settings_section( 
			'section_general', 
			'General Settings', 
			array( $this, 'print_section_info' ), 
			$this->general_settings_key 
		);
        add_settings_field( 
            'pi_logo', 
            'Logo Image', 
            array( $this, 'main_image_callback_general' ), 
            $this->general_settings_key, 
            'section_general',
            'pi_logo' 
        );
        add_settings_field( 
            'pi_font_color', 
            'Font Color', 
            array( $this, 'pi_color_picker' ), 
            $this->general_settings_key,
            'section_general',
            'pi_font_color' 
        );
        add_settings_field( 
            'pi_font_family', 
            'Font Family', 
            array( $this, 'font_family_callback' ), 
            $this->general_settings_key,
            'section_general' 
        );
        add_settings_field( 
            'pi_main_color_picker', 
            'Main Color', 
            array( $this, 'pi_color_picker' ), 
            $this->general_settings_key,
            'section_general',
            'pi_main_color_picker'
        );
        add_settings_field( 
            'pi_second_color_picker', 
            'Secondary Color', 
            array( $this, 'pi_color_picker' ), 
            $this->general_settings_key,
            'section_general',
            'pi_second_color_picker' 
        );
        add_settings_field( 
            'pi_number', 
            'Phone Number', 
            array( $this, 'general_text_callback' ), 
            $this->general_settings_key,
            'section_general',
            'pi_number' 
        );
        add_settings_field( 
            'insurance_logos', 
            'Support Logos', 
            array( $this, 'main_image_callback_general' ), 
            $this->general_settings_key,
            'section_general',
            'insurance_logos'
        );
    }
    /**
     * Register and add homepage settings
     */
    public function register_homepage_settings(){   
		$this->plugin_settings_tabs[$this->homepage_settings_key] = 'Homepage';
		register_setting( 
			$this->homepage_settings_key, 
			$this->homepage_settings_key,
            array( $this, 'sanitize' ) // Sanitize 
		);
        //Main 
        add_settings_section(
            'section_homepage', // ID
            'Home Page Settings', // Title
            array( $this, 'print_section_info' ), // Callback
			$this->homepage_settings_key 
        );
        //Main Image
        add_settings_field( 
            'pi_main_image', 
            'Main Image', 
            array( $this, 'main_image_callback' ), 
            $this->homepage_settings_key,
            'section_homepage',
            'pi_main_image'
        );
        //CTA 1 Image
        add_settings_field( 
            'cta_button_image_one', 
            'CTA Button 1 Image', 
            array( $this, 'main_image_callback' ), 
            $this->homepage_settings_key,
            'section_homepage',
            'cta_button_image_one'
        );
        //CTA 2 Image
        add_settings_field( 
            'cta_button_image_two', 
            'CTA Button 2 Image', 
            array( $this, 'main_image_callback' ), 
            $this->homepage_settings_key,
            'section_homepage',
            'cta_button_image_two'
        );
        //Form on top or bottom
        add_settings_field( 
            'form_position', 
            'Choose Home Form Position', 
            array( $this, 'homeform_position_callback' ), 
            $this->homepage_settings_key, 
            'section_homepage',
            'form_position' 
        );
        //Features
        add_settings_field( 
            'feature_background', 
            'Feature background Color', 
            array( $this, 'home_color_picker' ), 
            $this->homepage_settings_key,
            'section_homepage',
            'feature_background'
        ); 
        add_settings_field( 
            'feature_title_one', 
            'Feature Title One', 
            array( $this, 'text_callback' ), 
            $this->homepage_settings_key,
            'section_homepage',
            'feature_title_one'
        ); 
        add_settings_field( 
            'feature_image_one', 
            'Feature Image One', 
            array( $this, 'main_image_callback' ), 
            $this->homepage_settings_key,
            'section_homepage',
            'feature_image_one'
        );
        add_settings_field( 
            'feature_text_one', 
            'Feature Text One', 
            array( $this, 'textarea_callback' ), 
            $this->homepage_settings_key,
            'section_homepage',
            'feature_text_one'
        );
        add_settings_field( 
            'feature_link_one', 
            'Feature Link One', 
            array( $this, 'textarea_callback' ), 
            $this->homepage_settings_key,
            'section_homepage',
            'feature_link_one'
        );
        add_settings_field( 
            'feature_title_two', 
            'Feature Title Two', 
            array( $this, 'text_callback' ), 
            $this->homepage_settings_key,
            'section_homepage',
            'feature_title_two'
        );  
        add_settings_field( 
            'feature_image_two', 
            'Feature Image Two', 
            array( $this, 'main_image_callback' ), 
            $this->homepage_settings_key,
            'section_homepage',
            'feature_image_two'
        );
        add_settings_field( 
            'feature_text_two', 
            'Feature Text Two', 
            array( $this, 'textarea_callback' ), 
            $this->homepage_settings_key,
            'section_homepage',
            'feature_text_two'
        );
        add_settings_field( 
            'feature_link_two', 
            'Feature Link Two', 
            array( $this, 'textarea_callback' ), 
            $this->homepage_settings_key,
            'section_homepage',
            'feature_link_two'
        );
        add_settings_field( 
            'feature_title_three', 
            'Feature Title Three', 
            array( $this, 'text_callback' ), 
            $this->homepage_settings_key,
            'section_homepage',
            'feature_title_three'
        );  
        add_settings_field( 
            'feature_image_three', 
            'Feature Image Three', 
            array( $this, 'main_image_callback' ), 
            $this->homepage_settings_key,
            'section_homepage',
            'feature_image_three'
        );
        add_settings_field( 
            'feature_text_three', 
            'Feature Text Three', 
            array( $this, 'textarea_callback' ), 
            $this->homepage_settings_key,
            'section_homepage',
            'feature_text_three'
        );
        add_settings_field( 
            'feature_link_three', 
            'Feature Link Three', 
            array( $this, 'textarea_callback' ), 
            $this->homepage_settings_key,
            'section_homepage',
            'feature_link_three'
        );
        add_settings_field( 
            'contact_page_cta', 
            'Contact us CTA Image', 
            array( $this, 'main_image_callback' ), 
            $this->homepage_settings_key,
            'section_homepage',
            'contact_page_cta'
        );
        add_settings_field( 
            'contact_page_cta_text', 
            'Contact us CTA Image', 
            array( $this, 'textarea_callback' ), 
            $this->homepage_settings_key,
            'section_homepage',
            'contact_page_cta_text'
        );
    }
    /**
     * Register and add page settings
     */
    public function register_page_settings(){
        $this->plugin_settings_tabs[$this->page_settings_key] = 'Page';
        register_setting( 
            $this->page_settings_key, 
            $this->page_settings_key,
            array( $this, 'sanitize' )
        );
        add_settings_section( 
            'section_page', 
            'Page Settings', 
            array( $this, 'print_section_info' ), 
            $this->page_settings_key 
        );
        add_settings_field( 
            'title_background', 
            'Title Background', 
            array( $this, 'page_image_callback' ), 
            $this->page_settings_key, 
            'section_page',
            'title_background' 
        );
        add_settings_field( 
            'contact_ribbon', 
            'Contact Us Ribbon', 
            array( $this, 'page_image_callback' ), 
            $this->page_settings_key, 
            'section_page',
            'contact_ribbon' 
        );
        add_settings_field( 
            'contact_text', 
            'Contact Us Text. (Works for home page as well)', 
            array( $this, 'page_textarea_callback' ), 
            $this->page_settings_key, 
            'section_page',
            'contact_text' 
        );
        add_settings_field( 
            'sidebar_position', 
            'Choose Sidebar Position', 
            array( $this, 'sidebar_position_callback' ), 
            $this->page_settings_key, 
            'section_page',
            'sidebar_position' 
        );
        
    }
    /**
     * Register and add page settings
     */
    public function register_form_settings(){
        $this->plugin_settings_tabs[$this->form_settings_key] = 'Form';
        register_setting( 
            $this->form_settings_key, 
            $this->form_settings_key,
            array( $this, 'sanitize' )
        );
        add_settings_section( 
            'section_form', 
            'Form Settings', 
            array( $this, 'print_section_info' ), 
            $this->form_settings_key 
        );
        add_settings_field( 
            'home_form_background', 
            'Home Form Background Image', 
            array( $this, 'form_image_callback' ), 
            $this->form_settings_key, 
            'section_form',
            'home_form_background' 
        );
        add_settings_field( 
            'home_form_background_color', 
            'Home Form Background Color (Image will overwrite)', 
            array( $this, 'form_color_picker_callback' ), 
            $this->form_settings_key, 
            'section_form',
            'home_form_background_color' 
        );
        add_settings_field( 
            'home_form_font_color', 
            'Home Form Font Color', 
            array( $this, 'form_color_picker_callback' ), 
            $this->form_settings_key, 
            'section_form',
            'home_form_font_color' 
        );
        add_settings_field( 
            'regular_form_background', 
            'Regular Form Background Image', 
            array( $this, 'form_image_callback' ), 
            $this->form_settings_key, 
            'section_form',
            'regular_form_background' 
        ); 
        add_settings_field( 
            'regular_form_background_color', 
            'Regular Form Background Color (Image will overwrite)', 
            array( $this, 'form_color_picker_callback' ), 
            $this->form_settings_key, 
            'section_form',
            'regular_form_background_color' 
        );
        add_settings_field( 
            'regular_form_font_color', 
            'Regular Form Font Color', 
            array( $this, 'form_color_picker_callback' ), 
            $this->form_settings_key, 
            'section_form',
            'regular_form_font_color' 
        );       
    }
    /**
     * Register and add footer settings
     */
    public function register_footer_settings(){
        $this->plugin_settings_tabs[$this->footer_settings_key] = 'Footer';
        register_setting( 
            $this->footer_settings_key, 
            $this->footer_settings_key,
            array( $this, 'sanitize' )
        );
        add_settings_section( 
            'section_footer', 
            'Footer Settings', 
            array( $this, 'print_section_info' ), 
            $this->footer_settings_key 
        );
        add_settings_field( 
            'footer_columns', 
            'Footer Columns', 
            array( $this, 'footer_columns_callback' ), 
            $this->footer_settings_key, 
            'section_footer'
        );
        add_settings_field( 
            'footer_logo_image', 
            'Footer Logo Image', 
            array( $this, 'footer_image_callback' ), 
            $this->footer_settings_key, 
            'section_footer',
            'footer_logo_image' 
        );
        add_settings_field( 
            'footer_address', 
            'Footer Address', 
            array( $this, 'footer_textarea_callback' ), 
            $this->footer_settings_key, 
            'section_footer',
            'footer_address' 
        );   
        add_settings_field( 
            'footer_chat_title', 
            'Footer Chat Title', 
            array( $this, 'footer_text_callback' ), 
            $this->footer_settings_key, 
            'section_footer',
            'footer_chat_title' 
        );
        add_settings_field( 
            'footer_background', 
            'Footer Background', 
            array( $this, 'footer_color_picker_callback' ), 
            $this->footer_settings_key, 
            'section_footer',
            'footer_background' 
        );
        add_settings_field( 
            'lower_footer_background', 
            'Lower Footer Background', 
            array( $this, 'footer_color_picker_callback' ), 
            $this->footer_settings_key, 
            'section_footer',
            'lower_footer_background' 
        );
        add_settings_field( 
            'footer_color', 
            'Footer Font Color', 
            array( $this, 'footer_color_picker_callback' ), 
            $this->footer_settings_key, 
            'section_footer',
            'footer_color' 
        );       
    }
    /**
     * Register and add CSS settings
     */
    public function register_css_settings(){
        $this->plugin_settings_tabs[$this->css_settings_key] = 'CSS';
        register_setting( 
            $this->css_settings_key, 
            $this->css_settings_key,
            array( $this, 'sanitize' )
        );
        add_settings_section( 
            'section_css', 
            'Enter Custom CSS', 
            array( $this, 'print_section_info' ), 
            $this->css_settings_key 
        );
        add_settings_field( 
            'css_value', 
            'CSS Code', 
            array( $this, 'css_callback' ), 
            $this->css_settings_key, 
            'section_css',
            'css_value'
        );   
    }
    /**
     * Register and add Import settings
     */
    public function register_listing_settings(){
        $this->plugin_settings_tabs[$this->listing_settings_key] = 'Listing';
        register_setting( 
            $this->listing_settings_key, 
            $this->listing_settings_key,
            array( $this, 'sanitize' )
        );
        add_settings_section( 
            'section_listing', 
            'Listing Options', 
            array( $this, 'print_section_info' ), 
            $this->listing_settings_key 
        );
        add_settings_field( 
            'counties_value', 
            'Add Counties', 
            array( $this, 'counties_callback' ), 
            $this->listing_settings_key, 
            'section_listing',
            'counties_value'
        ); 
        add_settings_field( 
            'cities_value', 
            'Add Cities', 
            array( $this, 'counties_callback' ), 
            $this->listing_settings_key, 
            'section_listing',
            'cities_value'
        );
        add_settings_field( 
            'pi_placeholder', 
            'Placeholder Image', 
            array( $this, 'placeholder_image_callback_general' ), 
            $this->listing_settings_key, 
            'section_listing',
            'pi_placeholder' 
        );   
    }
    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input ){
        //Header Options
        $new_input = array();
        //General options
        
        if( isset( $input['pi_state_abb'] ) )
            $new_input['pi_state_abb'] = sanitize_text_field( $input['pi_state_abb'] );
        if( isset( $input['pi_logo'] ) )
            $new_input['pi_logo'] = sanitize_text_field( $input['pi_logo'] );
        if( isset( $input['pi_font_color'] ) )
            $new_input['pi_font_color'] = sanitize_text_field( $input['pi_font_color'] );
        if( isset( $input['pi_font_family'] ) )
            $new_input['pi_font_family'] = sanitize_text_field( $input['pi_font_family'] );
        if( isset( $input['pi_main_color_picker'] ) )
            $new_input['pi_main_color_picker'] = sanitize_text_field( $input['pi_main_color_picker'] );
        if( isset( $input['pi_second_color_picker'] ) )
            $new_input['pi_second_color_picker'] = sanitize_text_field( $input['pi_second_color_picker'] );
        if( isset( $input['pi_number'] ) )
            $new_input['pi_number'] = sanitize_text_field( $input['pi_number'] );
        if( isset( $input['insurance_logos'] ) )
            $new_input['insurance_logos'] = sanitize_text_field( $input['insurance_logos'] );
        if( isset( $input['pi_menu_picker'] ) )
            $new_input['pi_menu_picker'] = sanitize_text_field( $input['pi_menu_picker'] );
        //Header CTA Options
        if( isset( $input['upper_cta'] ) )
            $new_input['upper_cta'] = htmlspecialchars( $input['upper_cta'] );
        if( isset( $input['pi_main_image_bg'] ) )
            $new_input['pi_main_image_bg'] = sanitize_text_field( $input['pi_main_image_bg'] );
        //Homepage options
        if( isset( $input['pi_main_image'] ) )
            $new_input['pi_main_image'] = sanitize_text_field( $input['pi_main_image'] );
        if( isset( $input['pi_main_image_caption'] ) )
            $new_input['pi_main_image_caption'] = sanitize_text_field( $input['pi_main_image_caption'] );
        if( isset( $input['cta_button_image_one'] ) )
            $new_input['cta_button_image_one'] = sanitize_text_field( $input['cta_button_image_one'] );
        if( isset( $input['cta_button_image_two'] ) )
            $new_input['cta_button_image_two'] = sanitize_text_field( $input['cta_button_image_two'] );
        if( isset( $input['form_position'] ) )
            $new_input['form_position'] = sanitize_text_field( $input['form_position'] );
        //Features
        if( isset( $input['feature_background'] ) )
            $new_input['feature_background'] = sanitize_text_field( $input['feature_background'] );
        if( isset( $input['feature_image_one'] ) )
            $new_input['feature_image_one'] = sanitize_text_field( $input['feature_image_one'] );
        if( isset( $input['feature_image_two'] ) )
            $new_input['feature_image_two'] = sanitize_text_field( $input['feature_image_two'] );
        if( isset( $input['feature_image_three'] ) )
            $new_input['feature_image_three'] = sanitize_text_field( $input['feature_image_three'] );
        if( isset( $input['feature_text_one'] ) )
            $new_input['feature_text_one'] = sanitize_text_field( $input['feature_text_one'] );
        if( isset( $input['feature_text_two'] ) )
            $new_input['feature_text_two'] = sanitize_text_field( $input['feature_text_two'] );
        if( isset( $input['feature_text_three'] ) )
            $new_input['feature_text_three'] = sanitize_text_field( $input['feature_text_three'] );
        if( isset( $input['feature_title_one'] ) )
            $new_input['feature_title_one'] = sanitize_text_field( $input['feature_title_one'] );
        if( isset( $input['feature_title_two'] ) )
            $new_input['feature_title_two'] = sanitize_text_field( $input['feature_title_two'] );
        if( isset( $input['feature_title_three'] ) )
            $new_input['feature_title_three'] = sanitize_text_field( $input['feature_title_three'] );
        if( isset( $input['feature_link_one'] ) )
            $new_input['feature_link_one'] = htmlspecialchars( $input['feature_link_one'] );
        if( isset( $input['feature_link_two'] ) )
            $new_input['feature_link_two'] = htmlspecialchars( $input['feature_link_two'] );
        if( isset( $input['feature_link_three'] ) )
            $new_input['feature_link_three'] = htmlspecialchars( $input['feature_link_three'] );
        if( isset( $input['contact_page_cta'] ) )
            $new_input['contact_page_cta'] = sanitize_text_field( $input['contact_page_cta'] );
        if( isset( $input['contact_page_cta_text'] ) )
            $new_input['contact_page_cta_text'] = htmlspecialchars( $input['contact_page_cta_text'] );
        
        //Page Options
        if( isset( $input['title_background'] ) )
            $new_input['title_background'] = sanitize_text_field( $input['title_background'] );     
        if( isset( $input['contact_ribbon'] ) )
            $new_input['contact_ribbon'] = sanitize_text_field( $input['contact_ribbon'] ); 
        if( isset( $input['contact_text'] ) )
            $new_input['contact_text'] = htmlspecialchars( $input['contact_text'] ); 
        if( isset( $input['sidebar_position'] ) )
            $new_input['sidebar_position'] = sanitize_text_field( $input['sidebar_position'] );       
        
        //Form Options
        if( isset( $input['home_form_background'] ) )
            $new_input['home_form_background'] = sanitize_text_field( $input['home_form_background'] );
        if( isset( $input['home_form_background_color'] ) )
            $new_input['home_form_background_color'] = sanitize_text_field( $input['home_form_background_color'] );
        if( isset( $input['home_form_font_color'] ) )
            $new_input['home_form_font_color'] = sanitize_text_field( $input['home_form_font_color'] );  
        if( isset( $input['regular_form_background'] ) )
            $new_input['regular_form_background'] = sanitize_text_field( $input['regular_form_background'] );
        if( isset( $input['regular_form_background_color'] ) )
            $new_input['regular_form_background_color'] = sanitize_text_field( $input['regular_form_background_color'] );
        if( isset( $input['regular_form_font_color'] ) )
            $new_input['regular_form_font_color'] = sanitize_text_field( $input['regular_form_font_color'] );

        //Footer Options
        if( isset( $input['footer_columns'] ) )
            $new_input['footer_columns'] = sanitize_text_field( $input['footer_columns'] );  
        if( isset( $input['footer_logo_image'] ) )
            $new_input['footer_logo_image'] = sanitize_text_field( $input['footer_logo_image'] );
        if( isset( $input['footer_address'] ) )
            $new_input['footer_address'] = htmlspecialchars( $input['footer_address'] );
        if( isset( $input['footer_chat_title'] ) )
            $new_input['footer_chat_title'] = sanitize_text_field( $input['footer_chat_title'] );
        if( isset( $input['footer_background'] ) )
            $new_input['footer_background'] = sanitize_text_field( $input['footer_background'] ); 
        if( isset( $input['lower_footer_background'] ) )
            $new_input['lower_footer_background'] = sanitize_text_field( $input['lower_footer_background'] );    
        if( isset( $input['footer_color'] ) )
            $new_input['footer_color'] = sanitize_text_field( $input['footer_color'] );
        //CSS Options
        if( isset( $input['css_value'] ) )
            $new_input['css_value'] = $input['css_value'];  

        //Listing Options
        if( isset( $input['counties_value'] ) )
            $new_input['counties_value'] = htmlspecialchars($input['counties_value']); 
        if( isset( $input['cities_value'] ) )
            $new_input['cities_value'] = htmlspecialchars($input['cities_value']); 
        if( isset( $input['pi_placeholder'] ) )
            $new_input['pi_placeholder'] = sanitize_text_field( $input['pi_placeholder'] );

        return $new_input;
    }
    /** 
     * Print the Section text
     */
    public function print_section_info(){
    	print '<p>Enter & Upload your settings below:</p>';
    }
    /** 
     * Get the settings option array and print one of its values
     */
    public function logo_callback(){   
        printf(
            '<span class="upload">
                <input type="text" id="pi_logo" class="regular-text text-upload" name="pi_option_name[pi_logo]" value="%s"/>
                <img src="%s" class="preview-upload"/>
                <input type="button" class="button button-upload" value="Upload an image"/><br>
            </span>',
            isset( $this->options['pi_logo'] ) ? esc_url( $this->options['pi_logo']) : '',
            isset( $this->options['pi_logo'] ) ? esc_url( $this->options['pi_logo']) : ''
        );
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function font_family_callback(){   
        $fonts = array('Open Sans', 'Droid Sans', 'Lato', 'Bitter', 'Helvetica', 'Georgia', 'Trebuchet MS');
        echo '<select id="pi_font_family" name="'.$this->general_settings_key.'[pi_font_family]" value="true">';
            foreach ($fonts as $key => $font) {
                echo '<option value="' . $font . '"';
                if ( $font === $this->general_settings['pi_font_family']) {
                    echo '" selected="selected"';
                }
                echo '>' . $font . '</option>';
             } 
        echo '</select>';
    }
    /** 
     *  General Color Picker
     */    
    public function pi_color_picker( $value ) {    
        printf(
            '<input type="text" name="'.$this->general_settings_key.'['. $value .']" value="%s" class="pi-color-picker" >',
            isset( $this->general_settings[$value] ) ? $this->general_settings[$value] : ''
        );   
    }
    /** 
     * Get the settings option array and print one of its values
     */
    public function general_text_callback($value){
        printf(
            '<input type="text" id="' . $value . '" name="'. $this->general_settings_key .'['. $value .']" value="%s"/>',
            isset( $this->general_settings[$value] ) ? esc_attr( $this->general_settings[$value] ) : ''
        );
    }
    /** 
     * Get the settings option array and print one of its values
     */
    public function main_image_callback_general($value){   
        printf(
            '<span class="upload">
                <input type="text" id="'. $value .'" class="regular-text text-upload" name="'. $this->general_settings_key .'[' . $value . ']" value="%s"/>
                <img src="%s" class="preview-upload"/>
                <input type="button" class="button button-upload" value="Upload an image"/><br/>
            </span>',
            isset( $this->general_settings[$value] ) ? esc_url( $this->general_settings[$value]) : '',
            isset( $this->general_settings[$value] ) ? esc_url( $this->general_settings[$value]) : ''
        );
    }
    /** 
     * Get the settings option array and print one of its values
     */
    public function text_callback($value){
        printf(
            '<input type="text" id="'. $value .'" name="'. $this->homepage_settings_key .'['. $value .']" value="%s"/>',
            isset( $this->homepage_settings[$value] ) ? esc_attr( $this->homepage_settings[$value] ) : ''
        );
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function textarea_callback( $value ){
        printf(
            '<textarea type="text" id="'. $value .'" name="'. $this->homepage_settings_key  .'['. $value .']" class="pi-textarea">%s</textarea>',
            isset( $this->homepage_settings[$value] ) ? esc_attr( $this->homepage_settings[$value]) : ''
        );
    }
    /** 
     * Choose wether form will be on top or bottom. For homepage
     */
    public function homeform_position_callback(){   
        $positions = array('top', 'bottom');
        echo '<select class="form_position" name="'.$this->homepage_settings_key.'[form_position]" value="true">';
            foreach ($positions as $key => $position) {
                echo '<option value="' . $position . '"';
                if ( $position === $this->homepage_settings['form_position']) {
                    echo '" selected="selected"';
                }
                echo '>' . $position . '</option>';
             } 
        echo '</select>';
    }
    /** 
     *  Feature Color Picker
     */    
    public function home_color_picker( $value ) {    
        printf(
            '<input type="text" name="'. $this->homepage_settings_key  .'['. $value .']" value="%s" class="pi-color-picker" >',
            isset( $this->homepage_settings[$value] ) ? $this->homepage_settings[$value] : ''
        );   
    }
    /** 
     * Get the settings option array and print one of its values
     */
    public function main_image_callback($value){   
        printf(
            '<span class="upload">
                <input type="text" id="'. $value .'" class="regular-text text-upload" name="'. $this->homepage_settings_key .'[' . $value . ']" value="%s"/>
                <img src="%s" class="preview-upload"/>
                <input type="button" class="button button-upload" value="Upload an image"/><br/>
            </span>',
            isset( $this->homepage_settings[$value] ) ? esc_url( $this->homepage_settings[$value]) : '',
            isset( $this->homepage_settings[$value] ) ? esc_url( $this->homepage_settings[$value]) : ''
        );
    }
    /** 
     * Display Image option for page
     */
    public function page_image_callback($value){   
        printf(
            '<span class="upload">
                <input type="text" id="'. $value .'" class="regular-text text-upload" name="'. $this->page_settings_key .'[' . $value . ']" value="%s"/>
                <img src="%s" class="preview-upload"/>
                <input type="button" class="button button-upload" value="Upload an image"/><br/>
            </span>',
            isset( $this->page_settings[$value] ) ? esc_url( $this->page_settings[$value]) : '',
            isset( $this->page_settings[$value] ) ? esc_url( $this->page_settings[$value]) : ''
        );
    }
    /** 
     * Text callback option for Page
     */
    public function page_textarea_callback( $value ){
        printf(
            '<textarea type="text" id="'. $value .'" name="'. $this->page_settings_key  .'['. $value .']" class="pi-textarea">%s</textarea>',
            isset( $this->page_settings[$value] ) ? esc_attr( $this->page_settings[$value]) : ''
        );
    }
    /** 
     * Choose wether sidebar will be on the right or left. For page
     */
    public function sidebar_position_callback(){   
        $sides = array('right', 'left');
        echo '<select id="sidebar_position" name="'.$this->page_settings_key.'[sidebar_position]" value="true">';
            foreach ($sides as $key => $side) {
                echo '<option value="' . $side . '"';
                if ( $side === $this->page_settings['sidebar_position']) {
                    echo '" selected="selected"';
                }
                echo '>' . $side . '</option>';
             } 
        echo '</select>';
    }
    /** 
     * Display Image option for form
     */
    public function form_image_callback($value){   
        printf(
            '<span class="upload">
                <input type="text" id="'. $value .'" class="regular-text text-upload" name="'. $this->form_settings_key .'[' . $value . ']" value="%s"/>
                <img src="%s" class="preview-upload"/>
                <input type="button" class="button button-upload" value="Upload an image"/><br/>
            </span>',
            isset( $this->form_settings[$value] ) ? esc_url( $this->form_settings[$value]) : '',
            isset( $this->form_settings[$value] ) ? esc_url( $this->form_settings[$value]) : ''
        );
    }
    /** 
     *  Form Color Picker
     */    
    public function form_color_picker_callback( $value ) {    
        printf(
            '<input type="text" name="'.$this->form_settings_key.'['. $value .']" value="%s" class="pi-color-picker" >',
            isset( $this->form_settings[$value] ) ? $this->form_settings[$value] : ''
        );   
    }
    /** 
     * Display Image option for footer
     */
    public function footer_image_callback($value){   
        printf(
            '<span class="upload">
                <input type="text" id="'. $value .'" class="regular-text text-upload" name="'. $this->footer_settings_key .'[' . $value . ']" value="%s"/>
                <img src="%s" class="preview-upload"/>
                <input type="button" class="button button-upload" value="Upload an image"/><br/>
            </span>',
            isset( $this->footer_settings[$value] ) ? esc_url( $this->footer_settings[$value]) : '',
            isset( $this->footer_settings[$value] ) ? esc_url( $this->footer_settings[$value]) : ''
        );
    }
    /** 
     * Footer textarea render
     */
    public function footer_textarea_callback( $value ){
        printf(
            '<textarea type="text" id="'. $value .'" name="'. $this->footer_settings_key  .'['. $value .']" class="pi-textarea">%s</textarea>',
            isset( $this->footer_settings[$value] ) ? esc_attr( $this->footer_settings[$value]) : ''
        );
    }
    /** 
     * Get the settings option array and print one of its values
     */
    public function footer_text_callback($value){
        printf(
            '<input type="text" id="'. $value .'" name="'. $this->footer_settings_key .'['. $value .']" value="%s"/>',
            isset( $this->footer_settings[$value] ) ? esc_attr( $this->footer_settings[$value] ) : ''
        );
    }
    /** 
     * Choose number of columns for footer
     */
    public function footer_columns_callback(){   
        $columns = array('3', '4');
        echo '<select id="footer_columns" name="'.$this->footer_settings_key.'[footer_columns]" value="true">';
            foreach ($columns as $key => $column) {
                echo '<option value="' . $column . '"';
                if ( $column === $this->footer_settings['footer_columns']) {
                    echo '" selected="selected"';
                }
                echo '>' . $column . '</option>';
             } 
        echo '</select>';
    }
    /** 
     *  Footer Color Picker
     */    
    public function footer_color_picker_callback( $value ) {    
        printf(
            '<input type="text" name="'.$this->footer_settings_key.'['. $value .']" value="%s" class="pi-color-picker" >',
            isset( $this->footer_settings[$value] ) ? $this->footer_settings[$value] : ''
        );   
    }
    /** 
     * CSS Code area render
     */
    public function css_callback( $value ){
        printf(
            '<textarea type="text" id="'. $value .'" name="'. $this->css_settings_key  .'['. $value .']" class="pi-textarea">%s</textarea>',
            isset( $this->css_settings[$value] ) ? esc_attr( $this->css_settings[$value]) : ''
        );
    }
    /** 
     * Import Code area render button
     */
    public function counties_callback( $value ){
        printf(
            '<textarea type="text" id="'. $value .'" name="'. $this->listing_settings_key  .'['. $value .']" class="pi-textarea">%s</textarea>',
            isset( $this->listing_settings[$value] ) ? esc_attr( $this->listing_settings[$value]) : ''
        );
    }
    /** 
     * Get the settings option array and print one of its values from the listing options
     */
    public function placeholder_image_callback_general($value){   
        printf(
            '<span class="upload">
                <input type="text" id="'. $value .'" class="regular-text text-upload" name="'. $this->listing_settings_key .'[' . $value . ']" value="%s"/>
                <img src="%s" class="preview-upload"/>
                <input type="button" class="button button-upload" value="Upload an image"/><br/>
            </span>',
            isset( $this->listing_settings[$value] ) ? esc_url( $this->listing_settings[$value]) : '',
            isset( $this->listing_settings[$value] ) ? esc_url( $this->listing_settings[$value]) : ''
        );
    }
    public function pi_show_extra_register_fields(){
    ?>
        <p>
            <label for="password">Password<br/>
            <input id="password" class="input" type="password" tabindex="30" size="25" value="" name="password" />
            </label>
        </p>
        <p>
            <label for="repeat_password">Repeat password<br/>
            <input id="repeat_password" class="input" type="password" tabindex="40" size="25" value="" name="repeat_password" />
            </label>
        </p>
    <?php
    }
    public function pi_check_extra_register_fields($login, $email, $errors) {
        if ( $_POST['password'] !== $_POST['repeat_password'] ) {
            $errors->add( 'passwords_not_matched', "<strong>ERROR</strong>: Passwords must match" );
        }
        if ( strlen( $_POST['password'] ) < 6 ) {
            $errors->add( 'password_too_short', "<strong>ERROR</strong>: Passwords must be at least six characters long" );
        }
    }
    public function pi_register_extra_fields( $user_id ){
        $userdata = array();

        $userdata['ID'] = $user_id;
        if ( $_POST['password'] !== '' ) {
            $userdata['user_pass'] = $_POST['password'];
        }
        $new_user_id = wp_update_user( $userdata );
    }
    public function pi_edit_password_email_text ( $text ) {
        if ( $text == 'Registration confirmation will be e-mailed to you.' ) {
            $text = 'If you leave password fields empty one will be generated for you. Password must be at least 6 characters long.';
        }
        return $text;
    }
}