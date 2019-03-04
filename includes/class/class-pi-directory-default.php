<?php 
/**
 * Default functionality of the theme.
 *
 * Defines the theme name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Pi_Directory
 * @subpackage Pi_Directory/admin
 * @author     Andres Abello <abellowins@gmail.com>
 */
class Pi_Directory_Default {
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
	public function pi_setup() {
		//Add support for automatic feed links
		add_theme_support('automatic-feed-links');

		//Add support for post thumbnails
		add_theme_support('post-thumbnails');
		
		//Add html5 support
		add_theme_support( 'html5', array( 'comment-list', 'comment-form', 'search-form', 'gallery', 'caption' ) );
		//Add support for post thumbnails
		register_nav_menus(
			array(
				'main-menu' => __( 'Main Menu', $this->theme_name)
			)
		);
	}
	public function pi_register_sidebars(){
	    //Normal Sidebars
	    register_sidebar( array(
	        'name'          => __( 'Main Sidebar', $this->theme_name ),
	        'id'            => 'sidebar',
	        'description'   => __( 'Widgets in this area will be shown on all posts and pages.', $this->theme_name ),
	        'before_widget' => '<div class="widget" id="%1$s">',
	        'after_widget'  => '</div>',
	        'before_title'  => '<h3>',
	        'after_title'   => '</h3>',
	    ));
	    register_sidebar( array(
	        'name'          => __( 'Blog Sidebar', $this->theme_name ),
	        'id'            => 'sidebar-blog',
	        'description'   => __( 'Widgets in this area will be shown on the blog pages.', $this->theme_name ),
	        'before_widget' => '<div class="widget" id="%1$s">',
	        'after_widget'  => '</div>',
	        'before_title'  => '<h3>',
	        'after_title'   => '</h3>',
	    ));
	    //Footer Sidebars
	    register_sidebar(array(
	        'name'          => __('Footer Left', $this->theme_name),
	        'id'            => 'pi-footer-left',
	        'description'   => __('Left footer widget position.', $this->theme_name),
	        'before_widget' => '<div id="%1$s">',
	        'after_widget'  => '</div>',
	        'before_title'  => '<h3 class="main-color">',
	        'after_title'   => '</h3>'
	    ));
	    register_sidebar(array(
	        'name'          => __('Footer Left Center', $this->theme_name),
	        'id'            => 'pi-footer-center-left',
	        'description'   => __('Left center footer widget position.', $this->theme_name),
	        'before_widget' => '<div id="%1$s">',
	        'after_widget'  => '</div>',
	        'before_title'  => '<h3 class="main-color">',
	        'after_title'   => '</h3>'
	    ));
	    register_sidebar(array(
	        'name'          => __('Footer Right Center', $this->theme_name),
	        'id'            => 'pi-footer-right-center',
	        'description'   => __('Right center footer widget position.', $this->theme_name),
	        'before_widget' => '<div id="%1$s">',
	        'after_widget'  => '</div>',
	        'before_title'  => '<h3 class="main-color">',
	        'after_title'   => '</h3>'
	    ));
	    register_sidebar( array(
	        'name'          => __('Footer Right', $this->theme_name),
	        'id'            => 'pi-footer-right',
	        'description'   => __('Right footer widget position.', $this->theme_name),
	        'before_widget' => '<div id="%1$s">',
	        'after_widget'  => '</div>',
	        'before_title'  => '<h3 class="main-color">',
	        'after_title'   => '</h3>'
	    ));
	}
	// register PiDirectoryAdvancedSearch widget
	public function advanced_search_widget() {
	    register_widget( 'PiDirectoryAdvancedSearch' );
	}
}