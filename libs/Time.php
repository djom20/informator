<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Time
 *
 * @author sedinson
 */
class Time {

    static $YEAR = 'year';
    static $MONTH = 'month';

    static function getMonthName($month, $lang = 'spanish') {
        setlocale(LC_TIME, $lang);
        $name = strftime("%B", mktime(0, 0, 0, $month, 1, 2000));
        return $name;
    }

    static function Today($value = "0") {
        $today = getdate();
        return $today[$value];
    }

}

?>
