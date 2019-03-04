<?php
/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this theme
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Pi_Directory
 * @subpackage Pi_Directory/includes
 * @author     Andres Abello <abellowins@gmail.com>
 */
class Pi_Directory_i18n {

	/**
	 * The domain specified for this theme.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $domain    The domain identifier for this theme.
	 */
	private $domain;

	/**
	 * Load the theme text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_theme_textdomain() {

		load_theme_textdomain(
			$this->domain,
			THEMEMAIN . '/languages/'
		);

	}

	/**
	 * Set the domain equal to that of the specified domain.
	 *
	 * @since    1.0.0
	 * @param    string    $domain    The domain that represents the locale of this theme.
	 */
	public function set_domain( $domain ) {
		$this->domain = $domain;
	}

}
