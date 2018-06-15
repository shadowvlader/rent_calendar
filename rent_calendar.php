<?php
/**
 * @author Vladas Satas <vladassa@gmail.com>
 */
/*
Plugin Name: Rent Calendar
Plugin URI:
Description: Reservation implementation for Wordpress
Author: Vladas Šatas
Version: 1.0
Author URI:
*/
require ABSPATH.'wp-content/plugins/rent_calendar/classes/config.php';
require ABSPATH.'wp-content/plugins/rent_calendar/classes/shortcode.php';
require ABSPATH.'wp-content/plugins/rent_calendar/classes/rentCalendarAdmin.php';

/* Set base configuration */
$config = rentCalendarConfig::getInstance();

$config->addItem('plugin_id', 'rent-calendar');
$config->addItem('plugin_configuration_id', 'rent-calendar-configuration');
$config->addItem('plugin_products_id', 'rent-calendar-products');
$config->addItem('plugin_categories_id', 'rent-calendar-categories');
$config->addItem('plugin_shortcode_id', 'rent-calendar-shortcode');
$config->addItem('plugin_reservations_id', 'rent-calendar-reservations');

$config->addItem('plugin_path', plugin_dir_path(__FILE__));
$config->addItem('views_path', $config->getItem('plugin_path').'views/');

$config->addItem('plugin_url', home_url('/wp-admin/admin.php?page='.$config->getItem('plugin_id')));
$config->addItem('plugin_configuration_url', home_url('/wp-admin/admin.php?page='.$config->getItem('plugin_configuration_id')));
$config->addItem('plugin_products_url', home_url('/wp-admin/admin.php?page='.$config->getItem('plugin_products_id')));
$config->addItem('plugin_categories_url', home_url('/wp-admin/admin.php?page='.$config->getItem('plugin_categories_id')));
$config->addItem('plugin_shortcode_url', home_url('/wp-admin/admin.php?page='.$config->getItem('plugin_shortcode_id')));
$config->addItem('plugin_reservations_url', home_url('/wp-admin/admin.php?page='.$config->getItem('plugin_reservations_id')));

$config->addItem('plugin_form_handler_url', home_url('/wp-content/plugins/'.$config->getItem('plugin_id').'/form-handler.php'));

$config->addItem('plugin_name', 'Rent Calendar');

/**
 * Create admin menus
 */
function rentCalendarMenu() {
    $config = rentCalendarConfig::getInstance();

    add_menu_page($config->getItem('plugin_name'), $config->getItem('plugin_name'), 'level_10', $config->getItem('plugin_id'), array('rentCalendarAdmin', 'adminIndex'), 'dashicons-tag');
    add_submenu_page($config->getItem('plugin_id'), 'Configuration', 'Configuration', 'level_10', $config->getItem('plugin_configuration_id'), array('rentCalendarAdmin', 'adminConfiguration'));
    add_submenu_page($config->getItem('plugin_id'), 'Categories', 'Categories', 'level_10', $config->getItem('plugin_categories_id'), array('rentCalendarAdmin', 'adminCategories'));
    add_submenu_page($config->getItem('plugin_id'), 'Products', 'Products', 'level_10', $config->getItem('plugin_products_id'), array('rentCalendarAdmin', 'adminProducts'));
    add_submenu_page($config->getItem('plugin_id'), 'Shortcode', 'Shortcode', 'level_10', $config->getItem('plugin_shortcode_id'), array('rentCalendarAdmin', 'adminShortcode'));
    add_submenu_page($config->getItem('plugin_id'), 'Reservations', 'Reservations', 'level_10', $config->getItem('plugin_reservations_id'), array('rentCalendarAdmin', 'adminHistory'));
}
add_action('admin_menu', 'rentCalendarMenu');

/**
 * Create shortcode
 */
add_shortcode('reservation', array('rentCalendarShortcode', 'reservation'));
add_shortcode('reservation-button', array('rentCalendarShortcode', 'reservationButton'));

/**
 * Create table for rent calendar on plugin activation
 */
register_activation_hook(__FILE__, array('rentCalendarAdmin', 'pluginInstall'));

/**
 * Delete table for rent calendar on plugin activation
 */
register_deactivation_hook(__FILE__, array('rentCalendarAdmin', 'pluginRemove'));
    
function scripts_and_styles() {
    wp_register_style( 'beatPicker',  plugin_dir_url( __FILE__ ) . 'assets/css/BeatPicker.min.css' );
    wp_register_style( 'rentCalendar',  plugin_dir_url( __FILE__ ) . 'assets/css/rentCalendar.css' );
    wp_enqueue_style( 'beatPicker' );
    wp_enqueue_style( 'rentCalendar' );

    if(get_option("reservation_captcha")){
    	wp_register_script('reCaptcha', 'https://www.google.com/recaptcha/api.js');
    	wp_enqueue_script('reCaptcha');
    }

    wp_register_script( 'beatPicker', plugin_dir_url(__FILE__) . 'assets/js/BeatPicker.min.js', ['beatPickerJQuery']);
    wp_register_script( 'beatPickerJQuery', plugin_dir_url(__FILE__) . 'assets/js/jquery-1.11.0.min.js');
    wp_enqueue_script( 'beatPicker' );
    wp_enqueue_script( 'beatPickerJQuery' );
}
add_action( 'wp_enqueue_scripts', 'scripts_and_styles' );