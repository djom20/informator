<?php

class ErrorController extends ControllerBase {

    public function index() {
        $this->view->show('error/index.php', null);
    }

}

?>
