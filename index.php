<?php
    //error_reporting(0);
    if(!ob_start("ob_gzhandler")) ob_start();
    
    //Incluimos el FrontController
    require 'libs/FrontController.php';
    require 'libs/AES.php';
    
    //Creando objeto AES
    $coding = "{$_SERVER['HTTP_ACCEPT']}{$_SERVER['HTTP_USER_AGENT']}{$_SERVER['HTTP_ACCEPT_LANGUAGE']}";
    $GLOBALS['_AES'] = new AES(null, md5($coding), 256, AES::M_CBC);
    
    //Algunas constantes de la aplicacion
    define('BasicSuffix', '.json?');
    
    //Lo iniciamos con su método estático main. Hola!
    FrontController::main();