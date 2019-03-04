<?php 
/**
 * Adds Pi Directory Advance Search Widget to the WP_Widget Class
 */
class Pi_Directory_Advanced_Search extends WP_Widget{

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'pi_advanced_search', // Base ID
			__( 'Listing Search', 'pidirectory' ), // Name
			array( 'description' => __( 'Search for Listings', 'pidirectory' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
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


		echo $args['before_widget'];
		if ( !empty( $instance['title'] ) ) {
			echo '<h3>' . $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'] . '</h3>';
		}

		echo '<form method="post" class="pi-advanced-form" action="'. esc_url( home_url( '/' ) ) .'">';
			echo '<div class="form-body">';
				echo '<div class="form-group">';	
					echo '<input type="text" name="ls" id="ls" placeholder="' . __( 'City, County or Zip', 'pidirectory' ) . '">';
					echo '<button type="submit" class="btn btn-danger"><icon class="glyphicon glyphicon-search"></icon></button>';
				echo '</div>';
				echo '<input type="hidden" value="' . $str_counties . '" id="str-counties" >';
				echo '<input type="hidden" value="' . $str_cities . '" id="str-cities" >';
				echo '<input type="hidden" value="' . $str_zip . '" id="str-zips" >';
				echo wp_nonce_field( 'pi_listing_nonce_field', 'pi_listing_search_data' );	
			echo '</div>';
		echo '</form>';

		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Listing Search', 'pidirectory' );
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<?php 
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

		return $instance;
	}

}





