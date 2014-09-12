<?php

class Config {

    private $vars;
    private static $instance;

    private function __construct() {
        $this->vars = array();
    }

    public function set($name, $value) {
        $this->vars[$name] = $value;
    }

    public function get($name) {
        if (isset($this->vars[$name])) {
            return $this->vars[$name];
        }
    }

    public static function singleton() {
        if (!isset(self::$instance)) {
            $c = __CLASS__;
            self::$instance = new $c;
        }

        return self::$instance;
    }

}

/*
  Uso:

  $config = Config::singleton();
  $config->set('nombre', 'Federico');
  echo $config->get('nombre');

  $config2 = Config::singleton();
  echo $config2->get('nombre');

 */
?>