<?php
//Get Themes Name
function get_pi_theme(){
    $pi_directory = new Pi_Directory; 
    $theme = array( 
        'name'    => $pi_directory->get_theme_name(), 
        'version' => $pi_directory->get_version() 
    );
    return $theme;
}
function get_security_class(){
	$pi_security = new Pi_Security;
	return $pi_security;
}
function get_extra_class(){
	$extra = new Pi_Directory_Extras;
	return $extra;
}
function get_metabox_class(  ){
    $theme = get_pi_theme();
    $metabox = new Pi_Custom_Meta_Box( $theme['name'], $theme['version'] );
    return $metabox;
}
function get_form_class(  ){
    $theme = get_pi_theme();
    $forms = new Pi_Directory_Theme_Forms( $theme['name'], $theme['version'] );
    return $forms;
}
function get_import_class(  ){
    $theme = get_pi_theme();
    $import = new Pi_Import_Listings( $theme['name'], $theme['version'] );
    return $import;
}
function pi_listing_search_form(){
    $extras = get_extra_class();
    return $extras->listing_search_form();  
}
function get_pi_form_drug_assesment(){
    $forms = get_form_class();
    return $forms->pi_form_drug_assesment_wrapper();
}
function get_pi_form_alcohol_assesment(){
    $forms = get_form_class();
    return $forms->pi_form_alcohol_assesment_wrapper();
}
/**
 * The following normalizes fields for displaying the right images
 * @since 3.9.1
 *
 * @param  $meta = the meta field where the data is saved
 *         $field = the name of the field
 * @return $html
 */
function normalize_field( $field = array() ){
    $pi_plupload = get_metabox_class();
    return $pi_plupload->normalize_field($field);
}
/**
 * The following holds the html where the images will be.
 * @since 3.9.1
 *
 * @param  $meta = the meta field where the data is saved
 *         $field = the name of the field
 * @return $html
 */
function html( $meta, $field ){
    $pi_plupload = get_metabox_class();
    return $pi_plupload->html($meta, $field);
}
/**
*
* Setup theme defaults and register supported features.
*
**/
if( !function_exists('pi_setup') ){
	function pi_setup(){
		//Translations available 
		$lang_dir = THEMEMAIN . '/languages';
		load_theme_textdomain('pidirectory', $lang_dir);

		//Add support for automatic feed links
		add_theme_support('automatic-feed-links');

		//Add support for post thumbnails
		add_theme_support('post-thumbnails');
		
		//Add html5 support
		add_theme_support( 'html5', array( 'comment-list', 'comment-form', 'search-form', 'gallery', 'caption' ) );
		//Add support for post thumbnails
		register_nav_menus(
			array(
				'main-menu' => __( 'Main Menu', 'pidirectory')
			)
		);
	}
	add_action('after_setup_theme', 'pi_setup');
}

/**
*
* Register Sidebar. If Sidebar is not registered use default in sidebar.php
*
**/

function pi_register_sidebars(){
    //Normal Sidebars
    register_sidebar( array(
        'name'          => __( 'Main Sidebar', 'pidirectory' ),
        'id'            => 'sidebar',
        'description'   => __( 'Widgets in this area will be shown on all posts and pages.', 'pidirectory' ),
        'before_widget' => '<div class="widget" id="%1$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3>',
        'after_title'   => '</h3>',
    ));
    register_sidebar( array(
        'name'          => __( 'Blog Sidebar', 'pidirectory' ),
        'id'            => 'sidebar-blog',
        'description'   => __( 'Widgets in this area will be shown on the blog pages.', 'pidirectory' ),
        'before_widget' => '<div class="widget" id="%1$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3>',
        'after_title'   => '</h3>',
    ));
    //Footer Sidebars
    register_sidebar(array(
        'name'          => __('Footer Left', 'pidirectory'),
        'id'            => 'pi-footer-left',
        'description'   => __('Left footer widget position.', 'pidirectory'),
        'before_widget' => '<div id="%1$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="main-color">',
        'after_title'   => '</h3>'
    ));
    register_sidebar(array(
        'name'          => __('Footer Left Center', 'pidirectory'),
        'id'            => 'pi-footer-center-left',
        'description'   => __('Left center footer widget position.', 'pidirectory'),
        'before_widget' => '<div id="%1$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="main-color">',
        'after_title'   => '</h3>'
    ));
    register_sidebar(array(
        'name'          => __('Footer Right Center', 'pidirectory'),
        'id'            => 'pi-footer-right-center',
        'description'   => __('Right center footer widget position.', 'pidirectory'),
        'before_widget' => '<div id="%1$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="main-color">',
        'after_title'   => '</h3>'
    ));
    register_sidebar( array(
        'name'          => __('Footer Right', 'pidirectory'),
        'id'            => 'pi-footer-right',
        'description'   => __('Right footer widget position.', 'pidirectory'),
        'before_widget' => '<div id="%1$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="main-color">',
        'after_title'   => '</h3>'
    ));
}
add_action( 'widgets_init', 'pi_register_sidebars' );
/**
*
* Navigation for posts
*
**/
if ( ! function_exists( 'pi_paging_nav' ) ) {
	function pi_paging_nav() {
		$extra = get_extra_class();	
		return $extra->pi_pagination();
	}
}

if ( ! function_exists( 'pi_post_meta' ) ) {
	function pi_post_meta() {
		$extra = get_extra_class();
		return $extra->post_meta();
	}
}
/**
*
* Turn HEX to RGB with 80% opacity
*
**/
function hextorgba( $hex ) {
	$extra = get_extra_class();
	return $extra->hex_to_rgba( $hex );
}

/**
*
* Get Page Name
*
**/
function get_page_by_name($pagename){
	$pages = get_pages();
	foreach ( $pages as $page ) if ( $page->post_name == $pagename ) return $page;
	return false;
}
/**
*
* Validate email and have it ready to store
*
**/
function pi_email_validate_sanitize( $email ){
	$security = get_security_class();
	return $security->email_check( $email );
}
function pi_listing_validation( $email, $title, $content ){
	$error = array();

	$check_email = pi_email_validate_sanitize( $email );

	if( $check_email === false ){
		$error['email'] = 'Please add a valid Email';
		$has_error = true;		
	}
	
	if( trim( $title ) === '' ) {
		$error['title'] = 'Please add a title';
		$has_error = true;
	}
    
    if ( trim( $content ) === '' ) {
		$error['desc'] = 'Please add a description';
		$has_error = true;
    }

    if ( $has_error = true ) {
    	return $error;
    }
}
/**
*
* Change comment template to review for pi_listing post type comments 
*
**/
function pi_listing_reviews( $comment, $args, $depth ) {
	$extra = get_extra_class();
	$extra->listing_reviews( $comment, $args, $depth );
}

/**
*
* Get Users IP
*
**/
function pi_get_user_ip(){
	if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
		//check ip from share internet
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
		//to check ip is pass from proxy
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
}

function pi_add_http( $url ) {
	if( !empty($url) ){
	    if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
	        $url = "http://" . $url;
	    }		
	}

    return $url;
}
function display_breadcrumbs() {
    $extra = new Pi_Directory_Extras;
    echo '<div class="breadcrumbs">';
        $extra->pi_breadcrumbs();
    echo '</div>';
}
function display_claim_status( $post_id, $user_id ){
	$extra = get_extra_class();
	$content = $extra->claim_status( $post_id, $user_id );
	return $content;
}
function display_edit_listing( $post_id, $link ){
	$extra = get_extra_class();
	$content = $extra->edit_listing_button( $post_id, $link );
	return $content;
}

// Check the form for errors

function get_states(){
	$states = array( 'AL'=>'Alabama', 'AK'=>'Alaska', 'AZ'=>'Arizona', 'AR'=>'Arkansas', 'CA'=>'California', 'CO'=>'Colorado', 'CT'=>'Connecticut', 'DE'=>'Delaware', 'DC'=>'District of Columbia', 'FL'=>'Florida', 'GA'=>'Georgia', 'HI'=>'Hawaii', 'ID'=>'Idaho', 'IL'=>'Illinois', 'IN'=>'Indiana', 'IA'=>'Iowa', 'KS'=>'Kansas', 'KY'=>'Kentucky', 'LA'=>'Louisiana', 'ME'=>'Maine', 'MD'=>'Maryland', 'MA'=>'Massachusetts', 'MI'=>'Michigan', 'MN'=>'Minnesota', 'MS'=>'Mississippi', 'MO'=>'Missouri', 'MT'=>'Montana', 'NE'=>'Nebraska', 'NV'=>'Nevada', 'NH'=>'New Hampshire', 'NJ'=>'New Jersey', 'NM'=>'New Mexico', 'NY'=>'New York', 'NC'=>'North Carolina', 'ND'=>'North Dakota', 'OH'=>'Ohio', 'OK'=>'Oklahoma', 'OR'=>'Oregon', 'PA'=>'Pennsylvania', 'RI'=>'Rhode Island', 'SC'=>'South Carolina', 'SD'=>'South Dakota', 'TN'=>'Tennessee', 'TX'=>'Texas', 'UT'=>'Utah', 'VT'=>'Vermont', 'VA'=>'Virginia', 'WA'=>'Washington', 'WV'=>'West Virginia', 'WI'=>'Wisconsin', 'WY'=>'Wyoming');
	return $states;	
}

function get_location_from_google( $lat, $lng){
	$geolocation = $lat.','.$lng;
	// $request = 'http://maps.googleapis.com/maps/api/geocode/json?latlng='. $geolocation .'&sensor=false';
	// $request = 'https://maps.googleapis.com/maps/api/geocode/json?latlng=' . $geolocation . '&sensor=false&key=AIzaSyCtUNJkS3kg3tpnHG9QtBWB6n7FgsoyXZs';
	$request = 'http://data.fcc.gov/api/block/find?format=json&latitude='. $lat .'&longitude='. $lng .'&showall=true';
 
	$file_contents = file_get_contents($request);
	$json_decode = json_decode($file_contents);
	return $json_decode;
}

function pi_get_county( $post_id ){

	$lat = get_post_meta($post_id, 'lat', true);
	$lng = get_post_meta($post_id, 'lng', true);

	$location = get_location_from_google($lat, $lng);
	return $location->County->name;
}

function pi_format_phone( $phone ){
 	$security = new Pi_Security(); 
 	$sanitize_phone = $security->phone_check( $phone );
 	return $sanitize_phone;
}


















