<?php

class SPDO extends PDO {

    private static $instance = null;

    public function __construct() {
        $config = Config::singleton();
        parent::__construct($config->get('driver') . ':host=' . $config->get('dbhost') . ';dbname=' . $config->get('dbname'), $config->get('dbuser'), $config->get('dbpass'), array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
    }

    public static function singleton() {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

}

?>