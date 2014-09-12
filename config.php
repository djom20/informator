<?php
    $config = Config::singleton();

    // Folders' Direction
    $config->set('controllersFolder', 'Controllers/');
    $config->set('modelsFolder', 'Models/');
    $config->set('xmlFolder', 'Models/xml/');
    $config->set('wsFolder', 'Models/services/');
    $config->set('viewsFolder', 'Views/');
    $config->set('templatesFolder', 'Templates/');
    
    $config->set('Template', 'default.php');
    
    // Vars URL
    // $config->set('BaseUrl', 'localhost/informator');
    $config->set('BaseUrl', 'http://54.85.172.192/informator');

    // Data Base Configuration
    $config->set('driver', 'mysql');
    $config->set('dbhost', 'localhost');
    $config->set('dbname', 'dashboard');
    $config->set('dbuser', 'root');
    $config->set('dbpass', 'S0lunt3ch');
    // $config->set('dbpass', 'q6td9.9fmq3');

    // Another Owner Configurations
    $config->set('MDKey', 'ba79c7513cc983ae735fe9f66f100889');
    $config->set('address', '127.0.0.1');
    $config->set('port', '8586');