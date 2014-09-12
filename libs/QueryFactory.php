<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class QueryFactory {

    public static function query($query, $values = array()) {
        $db = SPDO::singleton();

        $result = $db->prepare($query);
        $result->execute($values);

        return $result->fetchAll();
    }

    public static function executeOnly($query, $values = array()) {
        $db = SPDO::singleton();

        $result = $db->prepare($query);
        $result->execute($values);

        return $result->rowCount();
    }

}

?>
