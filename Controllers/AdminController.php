<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AdminController
 *
 * @author edinson
 */
class AdminController extends ControllerBase {
    //put your code here
    public function _Always() {
        if(
            !in_array(ActionName, array (
                'login', 'logout')
            )
        ) {
            if(!isset($_SESSION['admin'])) {
                HTTP::JSON(401);
            }
        }
    }
    
    function active () {
        if(isset($_SESSION['admin'])) {
            $result = $this->getModel('admin')->select(array (
                ':iduser' => $_SESSION['user']['idadmin']
            ));

            $response = Partial::arrayNames($result, array (
                'pass'
            ));

            HTTP::JSON(Partial::createResponse(HTTP::Value(200), $response[0]));
        }
        
        HTTP::JSON(401);
    }
    
    function login() {
        if (Partial::_filled($this->post, array ('user', 'pass'))) {
            $result = $this->getModel('admin')->select(array(
                    ':user' => $this->post['user'],
                    ':pass' => md5($this->post['pass'])
                )
            );

            if (count($result) == 1) {
                $values = Partial::arrayNames($result, array ('pass'));
                
                $response = Partial::createResponse(HTTP::Value(200), $values[0]);
                $_SESSION['admin'] = $result[0];
                
                HTTP::JSON($response);
            }
            
            HTTP::JSON(404);
        }
        
        HTTP::JSON(400);
    }
    
    function logout () {
        session_destroy();
        
        HTTP::JSON(200);
    }
    
    function register () {
        $_filled = Partial::_filled($this->post, array (
                'user', 'pass'
            )
        );
        
        $_empty = Partial::_empty($this->post, array ('idadmin'));
        
        if($_filled && $_empty) {
            $admin = $this->getModel('admin');
            $params = Partial::prefix($this->post, ':');
            
            $params[':pass'] = md5($params[':pass']);

            $admin->insert($params);

            if($admin->lastID() > 0) {
                HTTP::JSON(200);
            }

            HTTP::JSON(424);
        }
        
        HTTP::JSON(400);
    }
    
    function delete () {
        if(!empty($this->delete['idadmin'])) {
            $this->getModel('admin')->delete(
                $this->delete['idadmin']
            );
            
            HTTP::JSON(200);
        }
        
        HTTP::JSON(400);
    }
    
    function get () {
        $result = $this->getModel('admin')->select();
        $response = Partial::arrayNames($result, array ('pass'));
        
        HTTP::JSON(Partial::createResponse(HTTP::Value(200), $response));
    }
    
    function cpw () {
        $_filled_root = Partial::_filled($this->post, array ('new'));
        
        $admin = $this->getModel('admin');
        
        if($_filled_root) {
            $admin->update($this->post['idadmin'], array (
                    ':pass' => md5($this->post['new'])
                )
            );

            HTTP::JSON(200);
        }
        
        HTTP::JSON(400);
    }
}
