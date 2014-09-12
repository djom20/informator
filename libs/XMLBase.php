<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of XMLBase
 *
 * @author sedinson
 */
abstract class XMLBase {

    //put your code here
    protected $xml = null;
    protected $config;

    public function __construct() {
        $this->config = Config::singleton();
        $this->init();
    }

    abstract function init();

    function loadXML($url) {
        try {
            $tmp = file_get_contents($url);
            $str = $this->stripInvalidXml($tmp);
            $this->xml = new SimpleXMLElement($str);
        } catch (Exception $e) {
            $str = file_get_contents($url);
            die("Error: " . $e->getMessage());
        }
    }

    function stripInvalidXml($value) {
        $some_string = preg_replace('/[\x00-\x08\x10\x0B\x0C\x0E-\x19\x7F]' .
                '|[\x00-\x7F][\x80-\xBF]+' .
                '|([\xC0\xC1]|[\xF0-\xFF])[\x80-\xBF]*' .
                '|[\xC2-\xDF]((?![\x80-\xBF])|[\x80-\xBF]{2,})' .
                '|[\xE0-\xEF](([\x80-\xBF](?![\x80-\xBF]))|(?![\x80-\xBF]{2})|[\x80-\xBF]{3,})/S', '?', $value);

        return $some_string;
    }

    function getXML() {
        return $this->xml;
    }

    function findChildren($names = array(), $attr = array()) {
        $nodes = array($this->xml);
        for ($i = 0; $i < count($names); $i++) {   //Recorrer el array dependiendo de el numero de hijos que se quiera llegar, dados por el usuario
            $tmp = array();
            foreach (array_slice($nodes, 0) as $node) {   //Recorrer todos los nodos pasados en la raiz del xml
                $children = $node->$names[$i];
                foreach ($children as $child) {   //Recorrer todos los hijos del nodo
                    $sw = true;
                    if (count($attr) > $i) {   //Verificar que se hayan pasado atributos en el array para el atributo a verificar
                        foreach ($attr[$i] as $key => $value) {   //Verificar que sean los atributos que se buscan del hijo
                            $sw = (!$sw) ? false : ($child[$key] === $value);
                        }
                    }
                    if ($sw) {   //Si todos los atributos los tenia el hijo, agregarlo al array
                        array_push($tmp, $child);
                    }
                }
            }

            //Tomar como nodos el array generado anteriormente.
            $nodes = array_slice($tmp, 0);
        }

        //Regresar todos los hijos que quedaron en el ultimo nivel. Estos son los que se buscan :)
        return $nodes;
    }

    static function getChildrenAttributes($children) {
        $tmp = array();
        foreach ($children as $child) {
            array_push($tmp, XMLBase::getAttributes($child));
        }

        return $tmp;
    }

    static function getAttributes($node) {
        $tmp = array();
        foreach ($node->attributes() as $key => $value) {
            $tmp[$key] = "$value";
        }

        return $tmp;
    }

    function xml2array($get_attributes = 1, $priority = 'tag') {
        $contents = $this->xml->asXML();

        if (!$contents)
            return array();

        if (!function_exists('xml_parser_create')) {
            return array();
        }

        $parser = xml_parser_create('');
        xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8");
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
        xml_parse_into_struct($parser, trim($contents), $xml_values);
        xml_parser_free($parser);

        if (!$xml_values)
            return;

        $xml_array = array();
        $parents = array();
        $opened_tags = array();
        $arr = array();

        $current = &$xml_array;

        $repeated_tag_index = array();

        foreach ($xml_values as $data) {
            unset($attributes, $value);

            extract($data);

            $result = array();

            $attributes_data = array();

            if (isset($value)) {
                if ($priority == 'tag')
                    $result = $value;
                else
                    $result['value'] = $value;
            }

            if (isset($attributes) and $get_attributes) {
                foreach ($attributes as $attr => $val) {
                    if ($priority == 'tag')
                        $attributes_data[$attr] = $val;
                    else
                        $result['attr'][$attr] = $val;
                }
            }

            if ($type == "open") {
                $parent[$level - 1] = &$current;
                if (!is_array($current) or (!in_array($tag, array_keys($current)))) {
                    $current[$tag] = $result;

                    if ($attributes_data)
                        $current[$tag . '_attr'] = $attributes_data;

                    $repeated_tag_index[$tag . '_' . $level] = 1;

                    $current = &$current[$tag];
                }
                else {
                    if (isset($current[$tag][0])) {
                        $current[$tag][$repeated_tag_index[$tag . '_' . $level]] = $result;

                        $repeated_tag_index[$tag . '_' . $level]++;
                    } else {
                        $current[$tag] = array($current[$tag], $result);

                        $repeated_tag_index[$tag . '_' . $level] = 2;

                        if (isset($current[$tag . '_attr'])) {
                            $current[$tag]['0_attr'] = $current[$tag . '_attr'];

                            unset($current[$tag . '_attr']);
                        }
                    }
                    $last_item_index = $repeated_tag_index[$tag . '_' . $level] - 1;
                    $current = &$current[$tag][$last_item_index];
                }
            } elseif ($type == "complete") {
                if (!isset($current[$tag])) {
                    $current[$tag] = $result;
                    $repeated_tag_index[$tag . '_' . $level] = 1;
                    if ($priority == 'tag' and $attributes_data)
                        $current[$tag . '_attr'] = $attributes_data;
                }
                else {
                    if (isset($current[$tag][0]) and is_array($current[$tag])) {
                        $current[$tag][$repeated_tag_index[$tag . '_' . $level]] = $result;

                        if ($priority == 'tag' and $get_attributes and $attributes_data) {
                            $current[$tag][$repeated_tag_index[$tag . '_' . $level] . '_attr'] = $attributes_data;
                        }

                        $repeated_tag_index[$tag . '_' . $level]++;
                    } else {

                        $current[$tag] = array($current[$tag], $result);

                        $repeated_tag_index[$tag . '_' . $level] = 1;

                        if ($priority == 'tag' and $get_attributes) {
                            if (isset($current[$tag . '_attr'])) {
                                $current[$tag]['0_attr'] = $current[$tag . '_attr'];
                                unset($current[$tag . '_attr']);
                            }

                            if ($attributes_data) {
                                $current[$tag][$repeated_tag_index[$tag . '_' . $level] . '_attr'] = $attributes_data;
                            }
                        }
                        $repeated_tag_index[$tag . '_' . $level]++;
                    }
                }
            } elseif ($type == 'close') {
                $current = &$parent[$level - 1];
            }
        }

        return ($xml_array);
    }

}

?>
