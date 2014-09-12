<?php

class View {

    public function show($name, $_VARS = array()) {
        //$name es el nombre de nuestra plantilla, por ej, listado.php
        //$vars es el contenedor de nuestras variables, es un arreglo del tipo llave => valor, opcional.
        //Traemos una instancia de nuestra clase de configuracion.
        $config = Config::singleton();
        $display = new Display($name, $_VARS);

        //Armamos la ruta a la plantilla
        $path = $config->get('templatesFolder') . $config->get('Template');

        //Si no existe el fichero en cuestion, tiramos un 404
        if (file_exists($path) == false) {
            trigger_error('Template `' . $path . '` does not exist.', E_USER_NOTICE);
            return false;
        }

        //Finalmente, incluimos la plantilla.
        include($path);
    }

}

/*
  El uso es bastante sencillo:
  $vista = new View();
  $vista->show('listado.php', array("nombre" => "Juan"));
 */
?>
