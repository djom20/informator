<?php

class ModelBase {

    protected $db;
    protected $table;
    protected $config;

    public function __construct($table) {
        $this->db = SPDO::singleton();
        $this->table = htmlentities($table);
        $this->config = Config::singleton();
    }

    public function lastID() {
        return $this->db->lastInsertId();
    }

    public function query($query, $values = array()) {
        $result = $this->db->prepare($query);
        $result->execute($values);

        return $result->fetchAll();
    }

    public static function executeOnly($query, $values = array()) {
        $result = $this->db->prepare($query);
        $result->execute($values);

        return $result->rowCount();
    }

    public function select($where = array(), $last = "") {
        $table = $this->table;
        $query1 = "DESC {$table}";
        $result1 = $this->db->query($query1);
        $rows1 = $result1->fetchAll();
//        $rows1 = $this->models[$table];

        $str1 = "";
        foreach ($rows1 as $row1) {
            if ($where[":{$row1[0]}"]) {
                $str1 .= "{$row1[0]} = :{$row1[0]} AND ";
            }
        }

        $str1 = (strlen($str1) > 0) ? substr($str1, 0, -5) : $str1;

        $query2 = "SELECT * FROM {$table}" . ((strlen($str1) > 0) ? " WHERE {$str1}" : "") . " $last;";
        $result2 = $this->db->prepare($query2);
        $result2->execute($where);

        return $result2->fetchAll();
    }

    public function count($where = array()) {
        $table = $this->table;
        $query1 = "DESC {$table}";
        $result1 = $this->db->query($query1);
        $rows1 = $result1->fetchAll();
//        $rows1 = $this->models[$this->table];

        $str1 = "";
        foreach ($rows1 as $row1) {
            if ($where[":{$row1[0]}"]) {
                $str1 .= "{$row1[0]} = :{$row1[0]} AND ";
            }
        }

        $str1 = (strlen($str1) > 0) ? substr($str1, 0, -5) : $str1;

        $query2 = "SELECT COUNT(*) FROM {$table}" . ((strlen($str1) > 0) ? " WHERE {$str1}" : "") . ";";
        $result2 = $this->db->prepare($query2);
        $result2->execute($where);
        $array = $result2->fetchAll();

        return $array[0][0];
    }

    public function insert($values = array()) {
        $table = $this->table;
        $query1 = "DESC {$table}";
        $result1 = $this->db->query($query1);
        $rows1 = $result1->fetchAll();
//        $rows1 = $this->models[$this->table];
        $params = array ();

        $tmp1 = "";
        $tmp2 = "";
        foreach ($rows1 as $row1) {
            if ($values[":{$row1[0]}"]) {
                $params[":{$row1[0]}"] = urldecode($values[":{$row1[0]}"]);
                $tmp1 .= "{$row1[0]}, ";
                $tmp2 .= ":{$row1[0]}, ";
            }
        }

        $str1 = (strlen($tmp1) > 0) ? substr($tmp1, 0, -2) : $tmp1;
        $str2 = (strlen($tmp2) > 0) ? substr($tmp2, 0, -2) : $tmp2;

        $query2 = "INSERT INTO {$table} ({$str1}) VALUES ({$str2});";
        $result2 = $this->db->prepare($query2);
        $result2->execute($params);

        return $result2->rowCount() > 0;
    }

    public function insertorupdate($values = array()) {
        $table = $this->table;
        $query1 = "DESC {$table}";
        $result1 = $this->db->query($query1);
        $rows1 = $result1->fetchAll();
//        $rows1 = $this->models[$this->table];
        $params = array ();

        $tmp1 = "";
        $tmp2 = "";
        $tmp3 = "";
        foreach ($rows1 as $row1) {
            if (!empty($values[":{$row1[0]}"])) {
                $params[":{$row1[0]}"] = urldecode($values[":{$row1[0]}"]);
                $tmp3 .= "{$row1[0]} = :{$row1[0]}, ";
                $tmp2 .= ":{$row1[0]}, ";
                $tmp1 .= "{$row1[0]}, ";
            }
        }

        $str1 = (strlen($tmp1) > 0) ? substr($tmp1, 0, -2) : $tmp1;
        $str2 = (strlen($tmp2) > 0) ? substr($tmp2, 0, -2) : $tmp2;
        $str3 = (strlen($tmp3) > 0) ? substr($tmp3, 0, -2) : $tmp3;

        $query2 = "INSERT INTO {$table} ({$str1}) VALUES ({$str2}) ON DUPLICATE KEY UPDATE {$str3};";
        $result2 = $this->db->prepare($query2);
        $result2->execute($params);

        return $result2->rowCount() > 0;
    }

    public function update($id, $values = array()) {
        $table = $this->table;
        $query1 = "DESC {$table}";
        $result1 = $this->db->query($query1);
        $rows1 = $result1->fetchAll();
//        $rows1 = $this->models[$this->table];
        $params = array ();

        $str1 = "";
        foreach ($rows1 as $row1) {
            if ($values[":{$row1[0]}"]) {
                $params[":{$row1[0]}"] = urldecode($values[":{$row1[0]}"]);
                $str1 .= "{$row1[0]} = :{$row1[0]}, ";
            }
        }

        $str1 = (strlen($str1) > 0) ? substr($str1, 0, -2) : $str1;

        $query2 = "UPDATE {$table} SET {$str1} WHERE id{$table} = {$id};";
        $result2 = $this->db->prepare($query2);
        $result2->execute($params);

        return $result2->fetchAll();
    }

    public function delete($id) {
        $table = $this->table;

        $query2 = "DELETE FROM {$table} WHERE id{$table} = {$id};";
        $result2 = $this->db->query($query2);

        return $result2->fetchAll();
    }

}