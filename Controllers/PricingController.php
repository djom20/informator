<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PricingController
 *
 * @author edinson
 */
class PricingController extends ControllerBase {
    //put your code here
    public function _Always() {
        if(!isset($_SESSION['idaccount'])) {
            HTTP::JSON(401);
        }
    }
    
    private function returnIfDate($date, $default) {
        if (!empty($date)) {
            $date = urldecode($date);
            $is_Date = preg_match('/^\d{4}\-\d{2}\-\d{2}( \d{2}:\d{2}:\d{2})?$/', $date);
            if ($is_Date == 1) {
                return "'{$date}'";
            } else {
                HTTP::JSON(400);
            }
        } else {
            $is_Date = preg_match('/^\d{4}\-\d{2}\-\d{2}( \d{2}:\d{2}:\d{2})?$/', $default);
            return ($is_Date == 1) ? "'{$default}'" : $default;
        }
    }
    
    public function get () {
        $product = $this->getModel('product')->select();
        $start = $this->returnIfDate($this->get['start'], '0000-00-00');
        $end = $this->returnIfDate($this->get['end'], 'NOW()');
        
        $response = array ();
        for($i=0; $i<count($product); $i++) {
            $tmp = array ();
            $value = QueryFactory::query(
                "SELECT SUM(unblendedcost) "
                . "FROM record "
                . "WHERE idaccount = :idaccount "
                . "AND idproduct = :idproduct "
                . "AND start >= :start "
                . "AND end <= :end;", array (
                    ':idaccount' => $_SESSION['idaccount'],
                    ':idproduct' => $product[$i]['idproduct'],
                    ':start' => $start,
                    ':end' => $end
                )
            );
            
            $response[$product[$i]['name']] = (!empty($value[0][0]))? $value[0][0] : 0;
        }
        
        HTTP::JSON($response);
    }
}
