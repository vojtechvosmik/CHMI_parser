<?php

    $baseUrl = "http://portal.chmi.cz/files/portal/docs/meteo/rad/data_tr_png_1km/";

    function deleteOldImages() { //Removes all old radar images
        $files = glob('radar_images/*'); 
        foreach($files as $file){
        if(is_file($file))
            unlink($file);
        }
    }

    function getDOM() {
        global $baseUrl;
        $str = file_get_contents($baseUrl);
        $DOM = new DOMDocument;
        libxml_use_internal_errors(true);
        $DOM->loadHTML($str);
        return $DOM;
    }

    function removePurpleMess($image) { //Getting rid of purple Czech Republic border (credit: Jiří Přemyslovský, thx <3)
        $imgWidth = imagesx($image);
        $imgHeight = imagesy($image);
        $newPicture = imagecreatetruecolor($imgWidth, $imgHeight);
        imagesavealpha($newPicture, true);
        $rgb = imagecolorallocatealpha($newPicture, 0, 0, 0, 127);
        imagefill($newPicture, 0, 0, $rgb);
        $color = imagecolorexact($image, 255, 0, 255);
        for ($x = 0; $x < $imgWidth; $x++) {
            for ($y = 0; $y < $imgHeight; $y++) {
                $c = imagecolorat($image, $x, $y);
                if ($c == $color || $c == 0) {
                    imagesetpixel($newPicture, $x, $y, $rgb);
                }else {
                    $colors = imagecolorsforindex($image, $c);
                    $a = imagecolorexactalpha($newPicture, $colors["red"], $colors["green"], $colors["blue"], $colors["alpha"]);
                    imagesetpixel($newPicture, $x, $y, $a);
                }
            }
        }
        return $newPicture;
    }

    function cropImage($url) { //Getting rid of ugly grey CHMI frame
        global $baseUrl;
        $fullUrl = $baseUrl . $url;
        $im = imagecreatefrompng($fullUrl);
        $im = removePurpleMess($im);
        $im2 = imagecrop($im, ['x' => 75, 'y' => 156, 'width' => 567, 'height' => 370]);
        if ($im2 !== FALSE) {
            imagepng($im2, 'radar_images/' . $url);
            imagedestroy($im2);
        }
        imagedestroy($im);
        echo 'loaded -> ' . $url . '<br>';
    }

    function parse($DOM) {
        $finder = new DomXPath($DOM);
        $DOMNodeList = $finder->query("//a");
        for($i = $DOMNodeList->length; $i >= $DOMNodeList->length - 20; $i--){
            $DOMNode = $DOMNodeList->item($i);
            if ($DOMNode != null) {
                $url = $DOMNode->getAttribute("href");
                if ($url != "../") {
                    cropImage($url);
                }
            }
        }
    }

    $DOM = getDOM();
    deleteOldImages();
    parse($DOM);
