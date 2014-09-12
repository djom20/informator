<?php

abstract class ControllerBase {

    protected $post;
    protected $get;
    protected $view;
    protected $config;
    protected $files;
    protected $put;
    protected $delete;

    function __construct($post, $get, $files, $put, $delete) {
        session_set_cookie_params(31536000, '/');
        session_start();

        $this->config = Config::singleton();
        $this->get = $get;
        $this->put = $put;
        $this->post = $post;
        $this->files = $files;
        $this->delete = $delete;
        $this->view = new View();
    }

    function _Always() {
        
    }

    function getModel($model) {
        $modelName = ucwords($model) . 'Model';
        $strmodel = strtolower($model);
        $modelPath = $this->config->get('modelsFolder') . $modelName . '.php';
        if (is_file($modelPath)) {
            require $modelPath;

            $modelObj = new $modelName($strmodel);
        } else {
            $db = SPDO::singleton();
            $result = $db->query("SHOW TABLES;");
            $rows = $result->fetchAll();

            $sw = false;
            foreach ($rows as $row) {
                if (!$sw) {
                    $sw = ($row[0] === $strmodel);
                }
            }

            if ($sw) {
                $modelObj = new ModelBase($strmodel);
            } else {
                die("<h1>404: No se pudo encontrar {$modelName}.</h1>");
            }
        }

        return $modelObj;
    }

    function getWS($model) {
        $modelName = ucwords($model) . 'Model';
        $modelPath = $this->config->get('wsFolder') . $modelName . '.php';
        if (is_file($modelPath)) {
            require $modelPath;

            $modelObj = new $modelName();
        } else {
            die("<h1>404: No se pudo encontrar {$modelName}.</h1>");
        }

        return $modelObj;
    }

    function getXML($model) {
        $modelName = ucwords($model) . 'Model';
        $modelPath = $this->config->get('xmlFolder') . $modelName . '.php';
        if (is_file($modelPath)) {
            require $modelPath;

            $modelObj = new $modelName();
        } else {
            die("<h1>404: No se pudo encontrar {$modelName}.</h1>");
        }

        return $modelObj;
    }

}

?>
