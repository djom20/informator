<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Display {

    private $name;
    public $_VARS;

    public function __construct($name, $_VARS = array()) {
        $this->name = $name;
        $this->_VARS = $_VARS;
    }

    public function show() {
        //$name es el nombre de nuestra plantilla, por ej, listado.php
        //$vars es el contenedor de nuestras variables, es un arreglo del tipo llave => valor, opcional.
        //Traemos una instancia de nuestra clase de configuracion.
        $config = Config::singleton();

        //Armamos la ruta a la plantilla
        $path = $config->get('viewsFolder') . $this->name;

        //Si no existe el fichero en cuestion, tiramos un 404
        if (file_exists($path) == false) {
            trigger_error('Template `' . $path . '` does not exist.', E_USER_NOTICE);
            return false;
        }

        //Cargar las variables en el array vars
        $_VARS = $this->_VARS;

        //Finalmente, incluimos la plantilla.
        include($path);
    }

}

?>
