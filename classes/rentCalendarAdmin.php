<?php
/**
 * Our main class
 */
if ( ! class_exists('rentCalendarAdmin') ) {
  class rentCalendarAdmin {
  
    /**
     * Admin interface > overview
     */
    public function adminIndex() {
      $config = rentCalendarConfig::getInstance();
      require $config->getItem('views_path').'adminIndex.php';
    }
    
    /**
     * Admin interface > configuration
     */
    public function adminConfiguration() {
      $config = rentCalendarConfig::getInstance();
      
      /* Save configuration */
      if ( count($_POST) ) {
        
        $captcha_values = array('yes', 'no');
        $captcha = FALSE;
        if ( isset($_POST['captcha']) && in_array($_POST['captcha'], $captcha_values) ) {
          update_option('reservation_captcha', $_POST['captcha']);
	      	$captcha = TRUE;
          $config_saved = TRUE;
        }

        if($captcha){
					update_option("reservation_captcha_site_key", $_POST['captcha_site_key']);
					update_option("reservation_captcha_secret_key", $_POST['captcha_secret_key']);
        }

        if (isset($_POST['price_method'])) {
          update_option('reservation_price_method', $_POST['price_method']);
          $config_saved = TRUE;
        }
        
        if (isset($_POST['show_prices'])) {
          update_option('reservation_show_prices', $_POST['show_prices']);
          $config_saved = TRUE;
        }
        
        if ( isset($_POST['reservation_page']) && is_numeric($_POST['reservation_page']) && $_POST['reservation_page'] > 0 ) {
          update_option('reservation_page', $_POST['reservation_page']);
          $config_saved = TRUE;
        }
      }

      require $config->getItem('views_path').'adminConfiguration.php';
    }
    
    /**
     * Admin interface > shortcode
     */
    public function adminShortcode() {
      $config = rentCalendarConfig::getInstance();
      require $config->getItem('views_path').'adminShortcode.php';
    }
    
    /**
     * Admin interface > reservations
     */
    public function adminHistory() {
      global $wpdb;
      $config = rentCalendarConfig::getInstance();
	    $reservationActionResponse = NULL;
	    if(isset($_GET['action']) && isset($_GET['id'])){
		    $reservation = $wpdb->get_row("SELECT `name` FROM `". $wpdb->prefix ."rent_calendar` WHERE `id` = '". $wpdb->_real_escape($_GET['id']) ."'");
		    if($reservation) {
		    	if($_GET['action'] == 'delete') {
				    $wpdb->delete( $wpdb->prefix . "rent_calendar", [ 'id' => $_GET['id'] ] );
				    $reservationActionResponse = self::generateActionResponse( true, "Reservation successfully deleted" );
			    } else if($_GET['action'] == 'set-confirmed') {
		    		$wpdb->update($wpdb->prefix . "rent_calendar", ['status' => 'confirmed'], [ 'id' => $_GET['id'] ]);
				    $reservationActionResponse = self::generateActionResponse( true, "Reservation successfully confirmed" );
			    } else if($_GET['action'] == 'set-completed') {
				    $wpdb->update($wpdb->prefix . "rent_calendar", ['status' => 'completed'], [ 'id' => $_GET['id'] ]);
				    $reservationActionResponse = self::generateActionResponse( true, "Reservation successfully completed" );
			    } else if($_GET['action'] == 'set-pending') {
				    $wpdb->update($wpdb->prefix . "rent_calendar", ['status' => 'pending'], [ 'id' => $_GET['id'] ]);
				    $reservationActionResponse = self::generateActionResponse( true, "Reservation successfully set to pending" );
			    }
		    }
	    }

      $reservations = $wpdb->get_results('SELECT * FROM `'. $wpdb->prefix .'rent_calendar`');
      foreach($reservations as $reservation){
      	    $products = $wpdb->get_results("SELECT * FROM `". $wpdb->prefix ."rent_calendar_products` WHERE `id` IN (". $reservation->item_ids .")");
      	    if($products && is_array($products)) $reservation->products = $products;
      }
      require $config->getItem('views_path').'adminHistory.php';
    }

	  /**
	   * Admin interface > products
	   */
    public function adminProducts() {
    	global $wpdb;
	    $config = rentCalendarConfig::getInstance();

	    $productActionResponse = NULL;

	    if(isset($_GET['action']) && isset($_GET['id']) && $_GET['action'] == 'delete'){
		    $product = $wpdb->get_row("SELECT `name` FROM `". $wpdb->prefix ."rent_calendar_products` WHERE `id` = '". $wpdb->_real_escape($_GET['id']) ."'");
		    if($product) {
		    	$wpdb->delete($wpdb->prefix."rent_calendar_products", ['id' => $_GET['id']]);
			    $productActionResponse = self::generateActionResponse(true, "Product successfully deleted");
		    }
	    }

	    if(isset($_POST['new-product'])){
				if(self::validateFormInput('name') && self::validateFormInput('price') && self::validateFormInput('category')){
					$wpdb->insert($wpdb->prefix."rent_calendar_products", [
						'name' => $_POST['name'],
						'price' => $_POST['price'],
						'category' => $_POST['category']
					]);
					$productActionResponse = self::generateActionResponse(true, "Product successfully added");
				} else {
					$productActionResponse = self::generateActionResponse(false,"Product Name, Price and Category must be provided!");
				}
	    }

	    if(isset($_POST['save-product'])){
		    if(self::validateFormInput('name') && self::validateFormInput('price') && self::validateFormInput('id')) {
			    $wpdb->update( $wpdb->prefix . 'rent_calendar_products', [
				        'name' => $_POST['name'],
				        'price' => $_POST['price'],
				        'category' => $_POST['category']
		            ],
			        [
			        	'id' => $_POST['id']
			        ]
			    );
			    $productActionResponse = self::generateActionResponse(true, "Product successfully updated");
		    } else {
			    $productActionResponse = self::generateActionResponse(false,"Product Name, Price and Category must be provided!");
		    }
	    }

	    $categories = $wpdb->get_results("SELECT * FROM `". $wpdb->prefix ."rent_calendar_categories`");
	    $products = $wpdb->get_results("SELECT `". $wpdb->prefix ."rent_calendar_products`.`id`, `". $wpdb->prefix ."rent_calendar_products`.`name`, `". $wpdb->prefix ."rent_calendar_products`.`price`, `". $wpdb->prefix ."rent_calendar_categories`.`name` AS `category_name`
			FROM `". $wpdb->prefix ."rent_calendar_products` 
			LEFT JOIN `". $wpdb->prefix ."rent_calendar_categories` ON
			`". $wpdb->prefix ."rent_calendar_products`.`category` = `". $wpdb->prefix ."rent_calendar_categories`.`id`");

	    require $config->getItem('views_path').'adminProducts.php';
    }

	  /**
	   * Admin interface > categories
	   */
	  public function adminCategories() {
		  global $wpdb;
		  $config = rentCalendarConfig::getInstance();

		  $categoryActionResponse = NULL;

		  if(isset($_GET['action']) && isset($_GET['id']) && $_GET['action'] == 'delete'){
			  $product = $wpdb->get_row("SELECT `name` FROM `". $wpdb->prefix ."rent_calendar_categories` WHERE `id` = '". $wpdb->_real_escape($_GET['id']) ."'");
			  if($product) {
				  $wpdb->delete($wpdb->prefix."rent_calendar_categories", ['id' => $_GET['id']]);
				  $categoryActionResponse = self::generateActionResponse(true, "Category successfully deleted");
			  }
		  }

		  if(isset($_POST['new-category'])){
			  if(self::validateFormInput('name')){
				  $wpdb->insert($wpdb->prefix."rent_calendar_categories", [
					  'name' => $_POST['name']
				  ]);
				  $categoryActionResponse = self::generateActionResponse(true, "Category successfully added");
			  } else {
				  $categoryActionResponse = self::generateActionResponse(false,"Category name must be provided!");
			  }
		  }

		  if(isset($_POST['save-category'])){
			  if(self::validateFormInput('name') && self::validateFormInput('id')) {
				  $wpdb->update( $wpdb->prefix . 'rent_calendar_categories', [
					  'name' => $_POST['name']
			        ],
				    [
					  'id' => $_POST['id']
				    ]
				  );
				  $categoryActionResponse = self::generateActionResponse(true, "Category successfully updated");
			  } else {
				  $categoryActionResponse = self::generateActionResponse(false,"Category name must be provided!");
			  }
		  }

		  $categories = $wpdb->get_results("SELECT * FROM `". $wpdb->prefix ."rent_calendar_categories`");

		  require $config->getItem('views_path').'adminCategories.php';
	  }


    private function validateFormInput($input){
    	if(!isset($_POST[$input]) || empty($_POST[$input])) return false;

    	return true;
    }

	  /**
	   * @param $success
	   * @param $message
	   *
	   * @return object
	   */
    private function generateActionResponse($success, $message){
	    return (object)[
		    "success" => $success,
		    "message" => $message
	    ];
    }

    /**
     * Delete tables for rent calendar
     */
    public static function pluginRemove() {
      global $wpdb;
      $wpdb->query('DROP TABLE `'. $wpdb->prefix .'rent_calendar`');
      $wpdb->query('DROP TABLE `'. $wpdb->prefix .'rent_calendar_products`');
      $wpdb->query('DROP TABLE `'. $wpdb->prefix .'rent_calendar_categories`');
      $wpdb->query('DROP TABLE `'. $wpdb->prefix .'rent_calendar_product_groups`');
    }
    
    /**
     * Create tables for rent calendar
     */
    public static function pluginInstall() {
      global $wpdb;
      $wpdb->query('CREATE TABLE IF NOT EXISTS `'. $wpdb->prefix .'rent_calendar` (
	    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	    `date_from` DATETIME NOT NULL,
	    `date_to` DATETIME NOT NULL,
	    `name` VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	    `phone` VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
	    `item_ids` VARCHAR(64) NOT NULL,
	    `item_type` ENUM("single", "group"), NOT NULL
	    `status` ENUM("pending", "confirmed", "completed") NOT NULL
	    ) ENGINE = MYISAM ;');

      $wpdb->query('CREATE TABLE IF NOT EXISTS `'. $wpdb->prefix .'rent_calendar_products` (
      `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
      `name` VARCHAR(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
      `category` INT(11) NOT NULL,
      `price` DOUBLE(5, 2) NOT NULL
      ) ENGINE = MYISAM ;');

	  $wpdb->query('CREATE TABLE IF NOT EXISTS `'. $wpdb->prefix .'rent_calendar_categories` (
      `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
      `name` VARCHAR(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
      ) ENGINE = MYISAM ;');

      $wpdb->query('CREATE TABLE IF NOT EXISTS `'. $wpdb->prefix .'rent_calendar_product_groups` (
      `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
      `name` VARCHAR(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
      `price` DOUBLE(5, 2) NOT NULL,
      `items` VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
      ) ENGINE = MYISAM ;');
    }   
  }
}