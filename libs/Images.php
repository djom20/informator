<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Images
 *
 * @author edinson
 */
class Images {

    public static function uploadImage($_file, $formats = array('image/jpeg', 'image/png', 'image/gif')) {
        $image = null;
        if (is_uploaded_file($_file['tmp_name'])) {
            if (in_array($_file['type'], $formats)) {
                $fp = fopen($_file['tmp_name'], 'r');
                $image = fread($fp, filesize($_file['tmp_name']));
                fclose($fp);
            }
        }

        return $image;
    }

    public static function reduceImage($image, $w = 0, $h = 0) {
        $img_org = imagecreatefromjpeg($image);

        $data = getimagesize($image);
        $wa = $data[0];
        $ha = $data[1];

        $ws = ($w == 0) ? $h * $wa / $ha : $w;
        $hs = ($h == 0) ? $w * $ha / $wa : $h;

        $img_dest = imagecreatetruecolor($ws, $hs);
        imagecopyresampled($img_dest, $img_org, 0, 0, 0, 0, $ws, $hs, $wa, $ha);

        $path = "Resource/tmp/" . time() . "_" . rand(0, 9999);
        imagejpeg($img_dest, $path, 100);

        $fp = fopen($path, 'r');
        $tmp = fread($fp, filesize($path));
        fclose($fp);
        unlink($path);

        return $tmp;
    }

}

?>
