<?php
    class IndexController extends ControllerBase {
        public function index() {
            $params = array ();
            
            $this->view->show('index/index.php', $params);
        }
        
        public function test () {
            echo HTTP::encrypt(1);
        }
        
        public function ommie () {
            $nombre = $this->get['nombre'];
            
            $result = QueryFactory::query("DESC $nombre;");
            
            echo '<pre>';
            $str = Partial::fetchRows($result, "array (':Field', ':Type'), \n");
            echo $str;
        }
    }
?>
