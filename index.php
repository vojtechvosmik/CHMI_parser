<?php

    $baseUrl = "http://portal.chmi.cz/files/portal/docs/meteo/rad/data_tr_png_1km/";

    function getDOM() {
        global $baseUrl;
        $str = file_get_contents($baseUrl);
        $DOM = new DOMDocument;
        libxml_use_internal_errors(true);
        $DOM->loadHTML($str);
        return $DOM;
    }

    function cropImage($url) {
        global $baseUrl;
        $fullUrl = $baseUrl . $url;
        $im = imagecreatefrompng($fullUrl);
        $im2 = imagecrop($im, ['x' => 75, 'y' => 156, 'width' => 567, 'height' => 370]);
        if ($im2 !== FALSE) {
            imagepng($im2, 'radar_images/' . $url);
            imagedestroy($im2);
        }
        imagedestroy($im);
        echo '<img src = "' . 'radar_images/' . $url . '">';
        echo "<br><br><br>";
    }

    function parse($DOM) {
        $finder = new DomXPath($DOM);
        $DOMNodeList = $finder->query("//a");
        for($i = $DOMNodeList->length; $i >= $DOMNodeList->length - 20; $i--){
            $DOMNode = $DOMNodeList->item($i);
            if ($DOMNode != null) {
                $url = $DOMNode->getAttribute("href");
                echo $url;
                if ($url != "../") {
                    cropImage($url);
                }
            }
        }
    }

    $DOM = getDOM();
    parse($DOM);