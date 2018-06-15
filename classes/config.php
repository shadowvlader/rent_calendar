<?php

if ( ! class_exists('rentCalendarConfig') ) {

    final class rentCalendarConfig {

        private static $c_instance;
        private $c_items;

        /**
         * Private constructor, singleton pattern
         */
        private function __construct() {
            $this->c_items = array();
        }

        /**
         * Initialize the object one time
         * @return object
         */
        public static function getInstance() {
            if ( ! self::$c_instance )
                self::$c_instance = new rentCalendarConfig();

            return self::$c_instance;
        }

        /**
         * Add new item to configs
         * @param $item_name: name of the item
         * @param $item_value: item value
         */
        public function addItem($item_name, $item_value) {
            if ( isset( $this->c_items[$item_name] ) )
                trigger_error('Config item already exists: '.$item_name, E_USER_ERROR);

            $this->c_items[$item_name] = $item_value;
        }

        /**
         * @param $item_name
         * @return void | mixed
         */
        public function getItem($item_name) {
            if ( isset( $this->c_items[$item_name] ) )
                return $this->c_items[$item_name];

            trigger_error('Config item does not exists: '.$item_name, E_USER_ERROR);
        }
    }

}