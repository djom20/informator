<?php
    error_reporting(0);
    
    if (!function_exists('getallheaders')) { 
        function getallheaders() {
               $headers = ''; 
           foreach ($_SERVER as $name => $value) {
               if (substr($name, 0, 5) == 'HTTP_') {
                   $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value; 
               }
           }
           return $headers; 
        } 
    }
    
    /*Get a file and a hashtag (Unique ID)*/
    $filecontents = file_get_contents($_GET['file']);
    
    $headers = getallheaders();
    $hash = md5($filecontents);
    
    if(preg_match("/$hash/", $headers['If-None-Match'])) {
        header('HTTP/1.1 304 Not Modified');
    } else {
        header('Content-Type: ' . $_GET['type']);
        header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + 216000));
        header("ETag: \"{$hash}\"");
        
        if (extension_loaded("zlib") && (ini_get("output_handler") != "ob_gzhandler")) {
            ini_set("zlib.output_compression", 1);
        }

        echo $filecontents;
    }
    
    exit();
?>