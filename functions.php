<?php 

/**
 *  If this file is called directly, abort.
**/
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Constants
 **/
define( 'THEMEMAIN', get_template_directory_uri() );
define( 'IMAGES', THEMEMAIN . '/assets/imgs' );
define( 'SCRIPTS', THEMEMAIN . '/assets/js' );
define( 'STYLES', THEMEMAIN . '/assets/css' );
define( 'FRAMEWORK', get_template_directory() . '/includes' );

/**
*
* Load Framework. All theme's functionality goes here. 
* Starting point of the theme. You can add code at the end of this file. 
* It will work, but the best way to do it is on the pi-directory-helper.php file.
*
**/
require_once( FRAMEWORK . '/class/class-pi-directory-init.php');

/**
 * Begins execution of the theme.
 *
 * Since everything within the theme is registered via hooks,
 * then kicking off the theme from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_pi_directory() {

	$theme = new Pi_Directory();
	$theme->run();

}
run_pi_directory();

/** 
* Starting point of the theme. You can add functions and hooks here. 
* It will work, but I recommend the includes/pi-directory-helper.php file.
* It follows standards set by both WordPress and the PHP community
**/