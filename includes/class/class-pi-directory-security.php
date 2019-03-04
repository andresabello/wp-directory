<?php 
/**
 * Define security for theme
 *
 * Loads and defines all security library 
 * for form validation and sanitization.
 *
 * @since      1.0.0
 * @package    Pi_Directory
 * @subpackage Pi_Security/includes
 * @author     Andres Abello <abellowins@gmail.com>
 */
class Pi_Security {
	/**
     * Email Validation and Sanitization
     */
	public function email_check( $email ) {
		$is_valid = is_email( $email ) && $this->check_record( $email );
		
		$sanitized_email = filter_var( $email, FILTER_SANITIZE_EMAIL );
		$is_valid_test = strlen( $sanitized_email ) === strlen( $email );
		
		if ($is_valid_test && $is_valid) {
            return sanitize_email($email);
        }
        return false;
	}
    /**
     * Zip Code Validation and Sanitization
     */
    public function zipcode_check( $zipcode ) {
        //Only numbers allowed
        $is_valid = is_int( $zipcode );

        $safe_zipcode = intval( $zipcode );
        $sanitized_zipcode = filter_var( $safe_zipcode, FILTER_SANITIZE_NUMBER_INT );
        $is_valid_test = strlen( $sanitized_zipcode ) === strlen( $zipcode ) ;

        if( $is_valid_test && $is_valid && $sanitized_zipcode >= 5){
            return substr( $sanitized_zipcode, 0, 5 );
        }

        return false;
    }
    /**
     * Zip Code Validation and Sanitization
     */
    public function phone_check( $phone ) {

        $regex = "/^(\d[\s-]?)?[\(\[\s-]{0,2}?\d{3}[\)\]\s-]{0,2}?\d{3}[\s-]?\d{4}$/i";
        //Only numbers allowed
        $is_valid = ( preg_match( $regex, $phone ) ? $phone : false );
        
        if( $is_valid ){
            $phone_number = preg_replace( "/[^0-9]/", "", $phone );
            $sanitized_phone = filter_var( $phone_number, FILTER_SANITIZE_NUMBER_INT );
            
            return $this->format_phone( $sanitized_phone );

        }
        
        return false;
    }
    public function format_phone( $phone ){
        
        if( strlen($phone) == 7 ){

            return preg_replace("/([0-9]{3})([0-9]{4})/", "$1-$2", $phone);
        }elseif( strlen( $phone) == 10 ){

            return preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "($1) $2-$3", $phone);
        }else{

            return $phone;
        }
    }
    /**
     * Zip Code Validation and Sanitization
     */
    public function sanitize_text( $text ) {
        //WordPress sanitize function
        $sanitized_text = sanitize_text_field( $text );
        //PHP String Sanitize to make sure
        $sanitized_text = filter_var( $sanitized_text, FILTER_SANITIZE_STRING );

        return $sanitized_text;
    }
	/**
     * Get Host and send to Check it
     **/
	protected function check_record($email) {
        $host = substr($email, strpos($email, '@') + 1);
        return $this->check_host($host);
    }
	/**
     * Check Host and Check MX Record
     **/
    protected function check_host($host) {
        return $this->check_MX($host) || (checkdnsrr($host, 'A') || checkdnsrr($host, 'AAAA'));
    }
	/**
     * Check MX record against PHP MX Library
     **/
    protected function check_MX($host) {
        return checkdnsrr($host, 'MX');
    }
}