<?php
/**
 * Shortcode class
 *
 */
if ( ! class_exists('rentCalendarShortcode') ) {

    class rentCalendarShortcode {
  
        public static function reservationButton($atts = [], $content = NULL, $tag = '') {
            ob_start();

            $config = rentCalendarConfig::getInstance();

	        $atts = array_change_key_case((array)$atts, CASE_LOWER);

	        // override default attributes with user attributes
	        $options = shortcode_atts([
		        'products' => '',
		        'lock' => ''
	        ], $atts, $tag);

            require $config->getItem('views_path') . 'reservationButton.php';

            return ob_get_clean();
        }

        public static function reservation($atts = [], $content = NULL, $tag = '') {
        	global $wpdb;
            ob_start();

            $config = rentCalendarConfig::getInstance();

	        $atts = array_change_key_case((array)$atts, CASE_LOWER);

	        // override default attributes with user attributes
	        $options = shortcode_atts([
		        'products' => '',
		        'lock' => ''
	        ], $atts, $tag);

	        if(isset($_POST['products'])) $options['products'] = $_POST['products'];
	        if(isset($_POST['lock'])) $options['lock'] = $_POST['lock'];

	        $categories = $wpdb->get_results("SELECT * FROM `". $wpdb->prefix ."rent_calendar_categories`");
	        $products = $wpdb->get_results("SELECT * FROM `". $wpdb->prefix ."rent_calendar_products`");

            require $config->getItem('views_path') . 'reservation.php';

            return ob_get_clean();
        }
  
    }
  
}