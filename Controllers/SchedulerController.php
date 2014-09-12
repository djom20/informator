<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SchedulerController
 *
 * @author edinson
 */
class SchedulerController extends ControllerBase {
    //put your code here
    public function _Always() {
        if(!isset($_SESSION['user'])) {
            HTTP::JSON(401);
        }
    }
    
    public function add () {
        $_filled = Partial::_filled($this->post, array (
            '_hour', '_action', 'days', 'instanceID'
        ));
        
        if($_filled) {
            $scheduler = $this->getModel('scheduler');
            
            $values = $this->post;
            $values[':secretkey'] = $_SESSION['user']['secretkey'];
            $values[':accesskey'] = $_SESSION['user']['accesskey'];
            $values[':instanceID'] = $this->post['instanceID'];
            $values[':_action'] = $this->post['_action'];
            $values[':_hour'] = $this->post['_hour'];
                
            for($i=0; $i<count($this->post['days']); $i++) {
                $values[':_day'] = $this->post['days'][$i];
                
                $scheduler->insert($values);
            }
            
            HTTP::JSON(200);
        }
        
        HTTP::JSON(400);
    }
    
    public function get () {
        if(!empty($this->get['instanceID'])) {
            $result = $this->getModel('scheduler_group')->select(array (
                ':instanceID' => $this->get['instanceID']
            ), ' ORDER BY _day, _hour ASC');
            
            $scheduler = Partial::arrayNames($result, array ('accesskey', 'secretkey'));
            
            for($i=0; $i<count($scheduler); $i++) {
                $scheduler[$i]['_day'] = explode(',', $scheduler[$i]['_day']);
            }
            
            HTTP::JSON(Partial::createResponse(HTTP::Value(200), $scheduler));
        }
        
        HTTP::JSON(400);
    }
    
    public function delete () {
        $_filled = Partial::_filled($this->delete, array ('instanceID', '_hour'));
        
        if($_filled) {
            $params = Partial::prefix($this->delete, ':');
            QueryFactory::query(
                "DELETE FROM scheduler WHERE instanceID=:instanceID AND _hour=:_hour;", $params
            );
            
            HTTP::JSON(200);
        }
        
        HTTP::JSON(400);
    }
}
