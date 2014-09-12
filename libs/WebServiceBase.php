<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of WebServiceBase
 *
 * @author sedinson
 */
abstract class WebServiceBase {

    private $ws = null;
    protected $config;

    public function __construct() {
        $this->config = Config::singleton();
        $this->init();
    }

    abstract function init();

    function loadWebService($name, $url, $wsdl = false) {
        try {
            $this->ws[$name] = new nusoap_client($url, $wsdl);
        } catch (Exception $e) {
            trigger_error('Imposible consumir `' . $url . '`. ' . $e->getMessage(), E_USER_NOTICE);
        }
    }

    public function call($name, $method, $vars, $domain) {
        $result = $this->ws[$name]->call($method, $vars, $domain);
        // Check for a fault
        if ($this->ws[$name]->fault) {
            //If fault
            echo 'Fault';
        } else {   // Check for errors
            $err = $this->ws[$name]->getError();
            if ($err) {   // Display the error
                die($err);
            } else {   //Result
                return $result;
            }
        }
    }

}

?>
