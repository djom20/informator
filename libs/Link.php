<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Link
 *
 * @author sedinson
 */
class Link {
    
    static function Url ($controller = CONTROLLER_BASE, $action = ACTION_NAME, $get = array (), $sufix = BasicSuffix) {
        $config = Config::singleton();
        
        $array_get = array();
        $union = '/';
        
        foreach ($get as $key => $value) {
            if(is_numeric($key)) {
                array_push($array_get, $value);
            } else {
                array_push($array_get, "{$key}={$value}");
                $union = '&';
            }
        }
        
        $controller = ($controller === "")? ControllerName : $controller;
        $action = ($action === "")? ActionName : $action;
        $params = implode($union, $array_get);
        
        return "{$config->get('BaseUrl')}/{$controller}/{$action}{$sufix}{$params}";
    }
    
    static function Go ($controller = "", $action = "", $get = array (), $sufix = BasicSuffix) {
        $config = Config::singleton();
        
        $array_get = array();
        $union = '/';
        
        foreach ($get as $key => $value) {
            if(is_numeric($key)) {
                array_push($array_get, $value);
            } else {
                array_push($array_get, "{$key}={$value}");
                $union = '&';
            }
        }
        
        $params = implode($union, $array_get);
        header("Location:{$config->get('BaseUrl')}/{$controller}/{$action}{$sufix}{$params}");
    }
    
    static function loadStyle ($href, $type = "text/css", $rel = "stylesheet", $media = "screen") {
        $config = Config::singleton();
        $dirBase = (substr($href, 0, 4) === "http")? "" : $config->get('BaseUrl') . '/';
        echo "<link href=\"{$dirBase}{$href}\" rel=\"{$rel}\" media=\"{$media}\" type=\"{$type}\"/>\n";
    }
    
    static function loadScript ($src, $type = "text/javascript") {
        $config = Config::singleton();
        $dirBase = (substr($src, 0, 4) === "http")? "" : $config->get('BaseUrl') . '/';
        echo "<script type=\"{$type}\" src=\"{$dirBase}{$src}\"></script>\n";
    }
    
    static function Absolute ($directory = "") {
        $config = Config::singleton();
        $dirBase = (substr($directory, 0, 4) === "http")? "" : $config->get('BaseUrl') . '/';
        return "{$dirBase}{$directory}";
    }
    
    static function loadProjectBase () {
        Link::loadScript('Scripts/load.php?file=js/projectbase.js&type=text/js');
        echo "
            <script>
                var Link = new Linker('" . Link::Absolute() . "');
                Link.setExtension('.html?');
            </script>";
    }
}