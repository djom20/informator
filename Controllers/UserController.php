<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UserController
 *
 * @author edinson
 */
class UserController extends ControllerBase {
    
    public function _Always() {
        if(
            !in_array(ActionName, array (
                'login', 'logout')
            )
        ) {
            if(!isset($_SESSION['user'])) {
                if(!in_array(ActionName, array('delete', 'cpw')) || !isset($_SESSION['admin'])) {
                    HTTP::JSON(401);
                }
            }
        }
    }
    
    function active () {
        if(isset($_SESSION['user'])) {
            $result = $this->getModel('user')->select(array (
                ':iduser' => $_SESSION['user']['iduser']
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
            $result = $this->getModel('user')->select(array(
                    ':user' => $this->post['user'],
                    ':pass' => md5($this->post['pass'])
                )
            );

            if (count($result) == 1) {
                $values = Partial::arrayNames($result, array ('pass', 'secretkey', 'accesskey'));
                
                $response = Partial::createResponse(HTTP::Value(200), $values[0]);
                $_SESSION['user'] = $result[0];
                
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
                'name', 'user', 'pass'
            )
        );
        
        $_empty = Partial::_empty($this->post, array (
                'rol', 'idaccount', 'iduser'
            )
        );
        
        if($_filled && $_empty) {
            if($_SESSION['user']['rol'] == 'root') {
                $user = $this->getModel('user');
                $params = Partial::prefix($this->post, ':');

                $params[':idaccount'] = $_SESSION['user']['idaccount'];
                $params[':pass'] = md5($params[':pass']);
                
                if(empty($params[':accesskey'])) {
                    $params[':accesskey'] = $_SESSION['user']['accesskey'];
                }

                if(empty($params[':secretkey'])) {
                    $params[':secretkey'] = $_SESSION['user']['secretkey'];
                }

                $user->insert($params);

                if($user->lastID() > 0) {
                    HTTP::JSON(200);
                }

                HTTP::JSON(424);
            }
            
            HTTP::JSON(403);
        }
        
        HTTP::JSON(400);
    }
    
    function delete () {
        if(!empty($this->delete['iduser'])) {
            $user = $this->getModel('user');
            
            if($_SESSION['user']['rol'] == 'root' && $_SESSION['user']['iduser'] != $this->delete['iduser']) {
                $result = $user->select(array (
                        ':iduser' => $this->delete['iduser'],
                        ':idaccount' => $_SESSION['user']['idaccount']
                    )
                );

                if(count($result) == 1) {
                    $user->delete($this->delete['iduser']);

                    HTTP::JSON(200);
                }

                HTTP::JSON(404);
            } elseif(isset ($_SESSION['admin'])) {
                $user->delete($this->delete['iduser']);
                HTTP::JSON(200);
            }
            
            HTTP::JSON(403);
        }
        
        HTTP::JSON(400);
    }
    
    function edit () {
        $_empty = Partial::_empty($this->put, array (
                'rol', 'idaccount', 'pass'
            )
        );
        
        if($_empty) {
            $params = Partial::prefix($this->put, ':');
            $user = $this->getModel('user');
            
            if(!empty($this->put['iduser']) && $_SESSION['user']['rol'] == 'root') {
                $res = $user->select(array (
                        ':iduser' => $this->put['iduser'],
                        ':idaccount' => $_SESSION['user']['idaccount']
                    )
                );
                
                if(count($res) == 1) {
                    $user->update($this->put['iduser'], $params);
                    
                    HTTP::JSON(200);
                }
                
                HTTP::JSON(404);
            } else if (empty ($this->put['iduser'])) {
                $user->update($_SESSION['user']['iduser'], $params);
                
                HTTP::JSON(200);
            }
            
            HTTP::JSON(403);
        }
        
        HTTP::JSON(400);
    }
    
    function cpw () {
        $_filled_root = Partial::_filled($this->post, array (
                'old', 'new'
            )
        );
        
        $_filled_user = Partial::_filled($this->post, array (
            'new', 'iduser'
        ));
        
        $user = $this->getModel('user');
        
        if($_filled_root) {
            $res = $user->select(array (
                ':iduser' => $_SESSION['user']['iduser'],
                ':pass' => md5($this->post['old'])
            ));
            
            if(count($res) == 1) {
                $user->update($_SESSION['user']['iduser'], array (
                        ':pass' => md5($this->post['new'])
                    )
                );
                
                HTTP::JSON(200);
            }
            
            HTTP::JSON(403);
        } elseif ($_filled_user) {
            if(isset($_SESSION['admin'])) {
                $user->update($this->post['iduser'], array (
                        ':pass' => md5($this->post['new'])
                    )
                );

                HTTP::JSON(200);
            } else {
                if($this->post['iduser'] == $_SESSION['user']['iduser']) {
                    HTTP::JSON(403);
                }

                $res = $user->select(array (
                    ':iduser' => $this->post['iduser'],
                    ':idaccount' => $_SESSION['user']['idaccount']
                ));

                if(count($res) == 1) {
                    $user->update($this->post['iduser'], array (
                            ':pass' => md5($this->post['new'])
                        )
                    );

                    HTTP::JSON(200);
                }

                HTTP::JSON(403);
            }
        }
        
        HTTP::JSON(400);
    }
}
