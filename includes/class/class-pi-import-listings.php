<?php
/**
 * Register all actions and filters for the theme.
 *
 * Maintain a list of all hooks that are registered throughout
 * the theme, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 *
 * @package    Pi_Directory
 * @subpackage Pi_Import_Listings/includes
 * @author     Andres Abello <abellowins@gmail.com>
 */ 
class Pi_Import_Listings {
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
	/* Import media from url
	 *
	 * @param string $file_url URL of the existing file from the original site
	 * @param int $post_id The post ID of the post to which the imported media is to be attached
	 *
	 * @return boolean True on success, false on failure
	 */
	public function fetch_media($file_url, $post_id) {
		require_once(ABSPATH . 'wp-load.php');
		require_once(ABSPATH . 'wp-admin/includes/image.php');
		global $wpdb;

		if(!$post_id) {
			return false;
		}

		//directory to import to	
		$artDir = 'wp-content/uploads/importedmedia/';

		//if the directory doesn't exist, create it	
		if(!file_exists(ABSPATH.$artDir)) {
			mkdir(ABSPATH.$artDir);
		}

		//rename the file... alternatively, you could explode on "/" and keep the original file name
		$ext = array_pop(explode(".", $file_url));
		$new_filename = 'blogmedia-'.$post_id.".".$ext; //if your post has multiple files, you may need to add a random number to the file name to prevent overwrites

		if (@fclose(@fopen($file_url, "r"))) { //make sure the file actually exists
			copy($file_url, ABSPATH.$artDir.$new_filename);

			$siteurl = get_option('siteurl');
			$file_info = getimagesize(ABSPATH.$artDir.$new_filename);

			//create an array of attachment data to insert into wp_posts table
			$artdata = array();
			$artdata = array(
				'post_author' => 1, 
				'post_date' => current_time('mysql'),
				'post_date_gmt' => current_time('mysql'),
				'post_title' => $new_filename, 
				'post_status' => 'inherit',
				'comment_status' => 'closed',
				'ping_status' => 'closed',
				'post_name' => sanitize_title_with_dashes(str_replace("_", "-", $new_filename)),											'post_modified' => current_time('mysql'),
				'post_modified_gmt' => current_time('mysql'),
				'post_parent' => $post_id,
				'post_type' => 'attachment',
				'guid' => $siteurl.'/'.$artDir.$new_filename,
				'post_mime_type' => $file_info['mime'],
				'post_excerpt' => '',
				'post_content' => ''
			);

			$uploads = wp_upload_dir();
			$save_path = $uploads['basedir'].'/importedmedia/'.$new_filename;

			//insert the database record
			$attach_id = wp_insert_attachment( $artdata, $save_path, $post_id );

			//generate metadata and thumbnails
			if ($attach_data = wp_generate_attachment_metadata( $attach_id, $save_path)) {
				wp_update_attachment_metadata($attach_id, $attach_data);
			}


				//optional make it the featured image of the post it's attached to
				$rows_affected = $wpdb->insert($wpdb->prefix.'postmeta', array('post_id' => $post_id, 'meta_key' => '_thumbnail_id', 'meta_value' => $attach_id));
		}
		else {
			return false;
		}
		return true;
	}
	public function pi_create_table(){
		global $wpdb;
		$table_name = $wpdb->prefix . "center_ratings";
		$charset_collate = $wpdb->get_charset_collate();
	    $sql = "CREATE TABLE $table_name (
		    id mediumint(9) NOT NULL AUTO_INCREMENT,
		    time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		    center_id int(5) DEFAULT NULL,
		    center_rating varchar(55) DEFAULT '' NOT NULL,
		    UNIQUE KEY id (id)
	    ) $charset_collate;";

	    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	    dbDelta( $sql );

	}
	public function pi_import_rows(){
		global $wpdb;
		$table_name = $wpdb->prefix . "center_ratings";
	    $row = 1;
	    if (($handle = fopen( get_stylesheet_directory_uri()  . "/ptc_center_ratings.csv", "r")) !== FALSE) {
	        while (($data = fgetcsv($handle, 40000, ",")) !== FALSE) {
	            $wpdb->insert( 
	                $table_name, 
	                array( 
	                    'time' => current_time( 'mysql' ), 
	                    'center_id' => $data[1],
	                    'center_rating' => $data[3],
	                ) 
	            );
	        }
	     fclose($handle);
	    }
	}
	public function pi_update_values(){
	    global $wpdb;

	    $table_name = $wpdb->prefix . "center_ratings";
	    $result = $wpdb->get_results( "SELECT * FROM $table_name" );
	    foreach ($result as $key => $value) {
	        $center_id = $value->center_id;
	        $rating = $value->center_rating;
	        $post  = get_post($center_id);
	        // update_post_meta($post->ID, 'pi_rating', $rating);
	        var_dump(get_post_meta($post->ID, 'pi_rating', true));
	    }
	}
	public function get_image_from_post( $post_id ){
		$args = array( 
					'post_type' => 'attachment', 
					'posts_per_page' => 1, 
					'post_status' => 'any', 
					'post_parent' => $post_id 
				); 
		$attachments = get_posts( $args );
		if ( $attachments ) {
			foreach ( $attachments as $post ) {
				$link = wp_get_attachment_link( $post->ID, 'medium' ); 
			}
		}
		return $link; 

	}
	//If image is not available then update it with the one on theme options
	public function pi_update_listing_image(){
		$args = array(
			'posts_per_page'   => -1,
			'order'            => 'DESC',
			'post_type'        => 'pi_listing',
			'post_status'      => 'publish',
			'suppress_filters' => true  
		);
		$posts = get_posts($args);

		foreach ($posts as $post) {
			$thumbnail = get_post_meta($post->ID, '_thumbnail_id', true);
			if( empty($thumbnail) ){
				//Get attchment image by url
				update_post_meta( $post->ID, '_thumbnail_id', 6854);
			}
		}
	}
	function pi_update_county(){
	    global $wpdb;
	    $table_name = $wpdb->prefix . "pi_center_city";
	    $args = array(
	        'posts_per_page'   => -1,
	        'post_type'        => 'pi_listing',
	        'post_status'      => 'publish',
	        'suppress_filters' => true 
	    );

	    $posts = get_posts($args);
	    foreach ($posts as $key => $value) {
	    	var_dump( get_county($value->ID) );
	    }
	    //get_all_rows_from_custom_table($table_name);
		  // var_dump($center_id);
	    // $charset_collate = $wpdb->get_charset_collate();

	    // $sql = "CREATE TABLE $table_name (
	    // id mediumint(9) NOT NULL AUTO_INCREMENT,
	    // time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
	    // center_id int(5) DEFAULT NULL,
	    // phone_number varchar(55) DEFAULT '' NOT NULL,
	    // UNIQUE KEY id (id)
	    // ) $charset_collate;";

	    // require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	    // dbDelta( $sql );
	}
	public function open_csv(){
	    $row = 1;
		if (($handle = fopen( get_stylesheet_directory_uri()  . "/ptc_center_images.csv", "r")) !== FALSE) {
			while (($data = fgetcsv($handle, 12000, ",")) !== FALSE) {
				// var_dump($data);
				$num = count($data);
				$args = array(
					'posts_per_page'   => -1,
					'meta_key'         => 'old_id',
					'meta_value'       => $data[1],
					'post_type'        => 'pi_listing',
					'post_status'      => 'publish',
					'suppress_filters' => true 
				);
				$query = get_posts($args);
				foreach ($query as $key => $value) {
					$old_id = get_post_meta($value->ID, 'old_id', true);
					if($data[1] === $old_id ){
						update_post_meta($value->ID, 'img_url', $data[3]);

					}
				}
			}
			fclose($handle);
		}
	}
	public function update_listing_image(){
	    $args = array(
	        'posts_per_page'   => -1,
	        'post_type'        => 'pi_listing',
	        'post_status'      => 'publish',
	        'suppress_filters' => true 
	    );

	    $posts = get_posts($args);
	    foreach ($posts as $key => $value) {
	        // $pi_zip = get_post_meta($value->ID, 'lat', true);
	        // 
	    	$img_url = get_post_meta($value->ID, 'img_url', true);
	    	$this->fetch_media($img_url, $value->ID);

	    }
	}
}