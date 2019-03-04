<?php
/**
 * The core theme class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this theme as well as the current
 * version of the theme.
 *
 * @since      1.0.0
 * @package    Pi_Directory
 * @subpackage Pi_Directory/includes
 * @author     Andres Abello <abellowins@gmail.com>
 */ 
class Pi_Directory {
	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the theme.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Pi_Directory_loader    $loader    Maintains and registers all hooks for the theme.
	 */
	protected $loader;

	/**
	 * The unique identifier of this theme.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $theme_name    The string used to uniquely identify this theme.
	 */
	protected $theme_name;

	/**
	 * The unique identifier of this theme.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $theme_default    The string used to uniquely identify this theme.
	 */
	protected $theme_default;

	/**
	 * The current version of the theme.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the theme.
	 */
	protected $version;
	/**
	 * Define the core functionality of the theme.
	 *
	 * Set the theme name and the theme version that can be used throughout the theme.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->theme_name = 'pi-directory';
		$this->version = '1.0.0';

		$this->load_dependencies();
		$this->set_locale();
		
		$this->theme_defaults();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}
	/**
	 * Load the required dependencies for this theme.
	 *
	 * Include the following files that make up the theme:
	 *
	 * - Pi_Directory_Loader. Orchestrates the hooks of the theme.
	 * - Pi_Directory_i18n. Defines internationalization functionality.
	 * - Pi_Directory_Admin. Defines all hooks for the admin area.
	 * - Pi_Directory_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		//Orchestrae the actions and filters of the core theme.
		require_once FRAMEWORK . '/class/class-pi-directory-loader.php';
		//Internationalization functionality of the theme.
		require_once FRAMEWORK . '/class/class-pi-directory-i18n.php';
		//Admin Side and Front End Functionality
		require_once FRAMEWORK . '/class/class-pi-directory-default.php';
		//Admin Side Functionality
		require_once FRAMEWORK . '/admin/class-pi-directory-admin.php';
		//Public Side Functionality
		require_once FRAMEWORK . '/class/class-pi-directory-public.php';	
		//Custom Post Type
		require_once FRAMEWORK . '/class/class-pi-custom-post-type.php';
		//Meta Boxes 
		require_once FRAMEWORK . '/class/class-pi-custom-meta-box.php';
		//Bootstrap Menu Walker.
		require_once FRAMEWORK . '/class/class-pi-bootstrap-navwalker.php';
		//Security, Validation, Sanitization of data
		require_once FRAMEWORK . '/class/class-pi-directory-security.php';
		//Load Advanced Search functionality
		require_once FRAMEWORK . '/class/class-pi-advanced-search.php';
		//Load Forms, Surveys, and Questionnaires
		require_once FRAMEWORK . '/class/class-pi-forms.php';
		//Load theme extra functions
		require_once FRAMEWORK . '/class/class-pi-directory-extras.php';
		//Load theme extra functions
		require_once FRAMEWORK . '/class/class-pi-import-listings.php';
		//Load theme helper functions
		require_once FRAMEWORK . '/pi-directory-helper.php';		
		//Start Loader
		$this->loader = new Pi_Directory_Loader();

	}
	/**
	 * Define the locale for this Theme for internationalization.
	 *
	 * Uses the Pi_Directory_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$theme_i18n = new Pi_Directory_i18n();
		$theme_i18n->set_domain( $this->get_theme_name() );

		$this->loader->add_action( 'theme_setup', $theme_i18n, 'load_pi_directory' );

	}
	/**
	 * Load the required dependencies for this theme.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function theme_defaults() {
		//Listing Custom Post Type Options
		$listings = array(
		    'post_type_name' => 'pi_listing',
		    'singular' => 'Listing',
		    'plural' => 'Listings',
		    'slug' => 'listings'
		);
		$options = array(
			'supports' => array(
				'title', 
				'editor', 
				'thumbnail', 
				'comments'
			)
		);
		$theme = array(
			'name' => $this->get_theme_name(),
			'version' => $this->get_version() 
		);

		//Form Custom Post Type Options
		$forms = array(
		    'post_type_name' => 'pi_form',
		    'singular' => 'Form',
		    'plural' => 'Forms',
		    'slug' => 'listings'
		);
		$form_options = array(
			'public' => false
		);

		//Start Custom post types
		$cpt_listings = new Pi_Custom_Post_Type( $listings, $options, $theme);
		$cpt_forms = new Pi_Custom_Post_Type( $forms, $form_options, $theme);

		$cpt_listings->run();
		$cpt_forms->run();

		//Add plupload meta to Listing Post Type
		$listing_meta = new Pi_Custom_Meta_Box( $this->get_theme_name(), $this->get_version() );

		//Start and Run Form Object
		$forms = new Pi_Directory_Theme_Forms( $this->get_theme_name(), $this->get_version() );
		$extras = new Pi_Directory_Extras( $this->get_theme_name(), $this->get_version() );

		$import = new Pi_Import_Listings( $this->get_theme_name(), $this->get_version() );



		//After theme sets up
		$this->loader->add_action( 'after_theme_setup', $listing_meta, 'activate_pi_directory' );
		//Add Meta Box
		$this->loader->add_action( 'add_meta_boxes', $listing_meta, 'add_meta_box' );
		//Save Post
		$this->loader->add_action( 'save_post', $listing_meta, 'save' );
		//Handle ajax upload
		$this->loader->add_action( 'wp_ajax_pi_plupload_image_upload', $listing_meta, 'pi_handle_upload' );
		//Handle ajax delete
		$this->loader->add_action( 'wp_ajax_pi_delete_file', $listing_meta, 'pi_ajax_delete_file');
		//Handle reorder images
		$this->loader->add_action( 'wp_ajax_pi_reorder_images', $listing_meta, 'pi_ajax_reorder_images' );

        $this->loader->add_action( 'save_post', $listing_meta, 'pi_save_page_att' );
        $this->loader->add_action( 'add_meta_boxes', $listing_meta, 'pi_add_meta_page_attr' ); 
		//Add Forms Admin Menu Page
		$this->loader->add_action( 'admin_menu', $forms, 'register_pi_forms_menu_page');
		//Handle form ajax non users
		$this->loader->add_action( 'wp_ajax_nopriv_pi_form_ajaxhandler', $forms, 'pi_form_ajaxhandler');
		//Handle form ajax users
		$this->loader->add_action( 'wp_ajax_pi_form_ajaxhandler', $forms, 'pi_form_ajaxhandler');
		//Handle input ajax non users
		$this->loader->add_action( 'wp_ajax_nopriv_pi_input_ajaxhandler', $forms, 'pi_input_ajaxhandler');
		//Handle input ajax users
		$this->loader->add_action( 'wp_ajax_pi_input_ajaxhandler', $forms, 'pi_input_ajaxhandler');
		//Handle questionnaire ajax non users
		$this->loader->add_action( 'wp_ajax_nopriv_pi_questionnaire_ajaxhandler', $forms, 'pi_questionnaire_ajaxhandler');
		//Handle questionnaire ajax users
		$this->loader->add_action( 'wp_ajax_pi_questionnaire_ajaxhandler', $forms, 'pi_questionnaire_ajaxhandler');
		//Handle questionnaire next ajax non users
		$this->loader->add_action( 'wp_ajax_nopriv_pi_next_ajaxhandler', $forms, 'pi_next_ajaxhandler');
		//Handle questionnaire next ajax users
		$this->loader->add_action( 'wp_ajax_pi_next_ajaxhandler', $forms, 'pi_next_ajaxhandler');
		//Handle survey ajax users
		$this->loader->add_action( 'wp_ajax_submit_survey', $forms, 'pi_ajax_submit_survey' );
		//Handle survey ajax non users
		$this->loader->add_action( 'wp_ajax_nopriv_submit_survey', $forms, 'pi_ajax_submit_survey' );
		//Define email content type to HTML
		$this->loader->add_filter( 'wp_mail_content_type', $forms, 'pi_set_content_type');
		//extras
		$this->loader->add_action( 'register_form', $extras, 'pi_show_extra_register_fields' );
		//Register the advanced search widget
		$this->loader->add_action( 'widgets_init', $extras, 'register_advanced_search_widget' );
		$this->loader->add_action( 'init', $extras, 'add_excerpt_to_page' );

		//Only use when importing TODO. Allow normal user to do this
		// $this->loader->add_action('init', $import, 'pi_create_table');
		// $this->loader->add_action('init', $import, 'pi_import_rows');
		// $this->loader->add_action('init', $import, 'pi_update_values');
		// $this->loader->add_action('init', $import, 'pi_update_listing_image');
		// $this->loader->add_action('init', $import, 'pi_update_county');	 
	}
	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the theme.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$theme_admin = new Pi_Directory_Admin( $this->get_theme_name(), $this->get_version() );
		// Register admin styles
		$this->loader->add_action( 'admin_enqueue_scripts', $theme_admin, 'enqueue_styles' );
		// Register admin scripts
		$this->loader->add_action( 'admin_enqueue_scripts', $theme_admin, 'enqueue_scripts' );
    	// Register load settings
    	$this->loader->add_action( 'init', $theme_admin, 'load_settings' );
        // Register general options
        $this->loader->add_action( 'admin_init', $theme_admin, 'register_general_settings' ); 
        // Register homepage options
        $this->loader->add_action( 'admin_init', $theme_admin, 'register_homepage_settings' );
        // Register page options
        $this->loader->add_action( 'admin_init', $theme_admin, 'register_page_settings' );
        // Register form options
        $this->loader->add_action( 'admin_init', $theme_admin, 'register_form_settings' ); 
        // Register footer options
        $this->loader->add_action( 'admin_init', $theme_admin, 'register_footer_settings' );  
        // Register css options
        $this->loader->add_action( 'admin_init', $theme_admin, 'register_css_settings' );     
        // Register import options
        $this->loader->add_action( 'admin_init', $theme_admin, 'register_listing_settings' );         
        // Add the page to the admin menu
        $this->loader->add_action( 'admin_menu', $theme_admin, 'add_options_page' );
        // Save Listing Meta
        $this->loader->add_action( 'save_post', $theme_admin, 'save_pi_listing_meta' );
        // Register password extra fields
        $this->loader->add_action( 'register_form', $theme_admin, 'pi_show_extra_register_fields' );
        // Check password extra fields
        $this->loader->add_action( 'register_post', $theme_admin, 'pi_check_extra_register_fields', 10, 3 );
        // Register extra fields to register form
        $this->loader->add_action( 'user_register', $theme_admin, 'pi_register_extra_fields', 100 );
        // Change register form bottom text
        $this->loader->add_filter( 'gettext', $theme_admin, 'pi_edit_password_email_text' ); 

	}
	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the theme.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$theme_public = new Pi_Directory_Public( $this->get_theme_name(), $this->get_version() );
		//Add styles
		$this->loader->add_action( 'wp_enqueue_scripts', $theme_public, 'enqueue_styles' );
		//Add scripts
		$this->loader->add_action( 'wp_enqueue_scripts', $theme_public, 'enqueue_scripts' );
		$this->loader->add_action( 'comment_form_logged_in_after', $theme_public, 'pi_rating_field' );
		$this->loader->add_action( 'comment_form_after_fields', $theme_public, 'pi_rating_field' );
		$this->loader->add_action( 'comment_post',  $theme_public, 'save_comment_rating');
		$this->loader->add_action( 'trashed_comment',  $theme_public, 'delete_pi_rating');
		$this->loader->add_action( 'comment_unapproved_to_approved',  $theme_public, 'pi_comment_approved');
		$this->loader->add_action( 'wp_ajax_claim_listing', $theme_public, 'pi_ajax_claim_listing' );
		$this->loader->add_action( 'wp_ajax_nopriv_claim_listing', $theme_public, 'pi_ajax_claim_listing' );

		$this->loader->add_filter( 'preprocess_comment',  $theme_public, 'verify_rating_submission' );
		$this->loader->add_filter( 'excerpt_more', $theme_public, 'pi_replace_read_more' );
		$this->loader->add_filter( 'preprocess_comment', $theme_public, 'pi_comment_post', '', 1 );
		$this->loader->add_filter( 'comment_text', $theme_public, 'pi_comment_display', '', 1 );
		$this->loader->add_filter( 'comment_text_rss', $theme_public, 'pi_comment_display', '', 1 );
		$this->loader->add_filter( 'comment_excerpt', $theme_public, 'pi_comment_display', '', 1 );
		$this->loader->add_action( 'wp_ajax_pi_plupload_frontend_upload', $theme_public, 'pi_handle_frontend_upload');
		$this->loader->add_action( 'wp_ajax_nopriv_pi_plupload_frontend_upload', $theme_public, 'pi_handle_frontend_upload' );
		$this->loader->add_action( 'wp_ajax_pi_frontend_delete_file', $theme_public, 'pi_ajax_frontend_delete_file');
		$this->loader->add_action( 'wp_ajax_pi_frontend_reorder_images', $theme_public, 'pi_ajax_frontend_reorder_images');
		$this->loader->add_filter( 'post_class', $theme_public, 'category_id_class', 1);
		$this->loader->add_action('init', $theme_public, 'pi_listing_home_search_submit');
		$this->loader->add_action('ac_hook_after_header', $theme_public, 'display_breadcrumbs');
	}
	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the theme used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the theme.
	 */
	public function get_theme_name() {
		return $this->theme_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the theme.
	 *
	 * @since     1.0.0
	 * @return    Pi_Directory_Loader    Orchestrates the hooks of the theme.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the theme.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the theme.
	 */
	public function get_version() {
		return $this->version;
	}
	// public function custom(){
	// 	$theme_admin = new Pi_Directory_Admin( $this->get_theme_name(), $this->get_version() );
	// 	return $theme_admin;
	// }
}