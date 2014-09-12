<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Device
 *
 * @author sedinson
 */
class Device {

    //put your code here
    private static $instance;
    private $device = null;

    public function __construct() {
        $this->device = new Mobile_Detect();
    }

    private static function singleton() {
        if (!isset(self::$instance)) {
            $c = __CLASS__;
            self::$instance = new $c;
        }

        return self::$instance;
    }

    public static function isMobile() {
        $c = Device::singleton();

        return ($c->device->isMobile() && !$c->device->isTablet());
    }

    public static function isTablet() {
        $c = Device::singleton();

        return $c->device->isTablet();
    }

}

?>
