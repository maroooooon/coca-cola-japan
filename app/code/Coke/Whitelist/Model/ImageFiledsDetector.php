<?php


namespace Coke\Whitelist\Model;


use Intervention\Image\Image;
use Intervention\Image\ImageManagerStatic;

class ImageFiledsDetector
{
    public static function getFieldCoordinates(Image $img, $threshold = 220)
    {
        $imgCore = $img->getCore();

        $imw = imagesx($imgCore);
        $imh = imagesy($imgCore);
        $position = [];

        $centerX = $imw/2;

        $minY = $imh/4;
        $maxY = $imh - ($imh/4);
        $maxLength = 0;

        for ($j=$minY; $j < $maxY; $j++) {
            $position[$j] = null;
            for ($i = $centerX; $i > 0; $i--) {
                if(!self::isWhiteColor($imgCore, $i, $j, $threshold)) {
                    break;
                }
                $position[$j]['start'] = $i;
            }
            for ($i = $centerX; $i < $imw; $i++) {
                if(!self::isWhiteColor($imgCore, $i, $j, $threshold)) {
                    break;
                }
                $position[$j]['end'] = $i;
            }
            if (isset($position[$j]['start']) && $position[$j]['end']) {
                $position[$j]['length'] = $position[$j]['end'] - $position[$j]['start'];
                $maxLength = ($maxLength < $position[$j]['length']) ? $position[$j]['length'] : $maxLength;
            }
        }

//        uncomment for debug
//        foreach ($position as $j => $isRow) {
//            for($i=$position[$j]['start']; $i<$position[$j]['end'] ;$i++ ) {
//                imagesetpixel ( $imgCore , $i , $j , 100 );
//            }
//        }

        $maxLength = $maxLength * 0.7;

        $fieldStartX = $imw;
        $fieldEndX = 0;

        foreach ($position as $j => $isRow) {
            if (isset($position[$j]['length']) && ($position[$j]['length'] > $maxLength)) {
                $fieldStartX = ($fieldStartX > $position[$j]['start']) ? $position[$j]['start'] : $fieldStartX;
                $fieldEndX = ($fieldEndX < $position[$j]['end']) ? $position[$j]['end'] : $fieldEndX;
                $position[$j] = true;
            } else {
                unset($position[$j]);
            }
        }

        $fieldsPositions = [
            0 => [
                'start_x' => $fieldStartX,
                'start_y' => 0,
                'end_x' => $fieldEndX,
                'end_y' => 0,
                'is_found' => false
            ],
            1 => [
                'start_x' => $fieldStartX,
                'start_y' => 0,
                'end_x' => $fieldEndX,
                'end_y' => 0,
                'is_found' => false
            ],
        ];

        $maxFails = 10;
        $lastSuccessYRowPos = 0;
        foreach($fieldsPositions as &$coordinates) {
            $notRow = 0;
            $lastSuccessYRowPos = self::getNextKeyArray($position, $lastSuccessYRowPos);
            for($row = $lastSuccessYRowPos; $row < array_key_last($position) + ($maxFails*2); $row++) {
                $isRow = (isset($position[$row]) && $position[$row]);

                if($isRow && $coordinates['start_y'] == 0) {
                    $coordinates['start_y'] = $row;
                }
                if(!$isRow) {
                    $notRow++;
                } else {
                    $notRow = 0;
                    $lastSuccessYRowPos = $row;
                }

                if($notRow > $maxFails) {
                    $coordinates['end_y'] = $lastSuccessYRowPos;
                    $coordinates['is_found'] = true;
                    break;
                }
            }
        }

//        uncomment for debug
//        foreach ($fieldsPositions as $qqRow) {
//            if($qqRow['is_found']) {
//                for($y = $qqRow['start_y']; $y < $qqRow['end_y'] ;$y++ ) {
//                    for($x = $qqRow['start_x']; $x<$qqRow['end_x'] ;$x++ ) {
//                        imagesetpixel ( $imgCore , $x , $y , 500 );
//                    }
//                }
//            }
//        }
//        return $img->encode('data-url');

        return $fieldsPositions;
    }

    public static function correctFontSize($fitWidth, $fitHeight, $originalFontsize, $angel, $font_file, $text)
    {
        $type_space = \imagettfbbox($originalFontsize, $angel, $font_file, $text);
        $imageWidth = abs($type_space[2] - $type_space[0]);
        $newFontSize = (int)(($fitWidth * $originalFontsize) / $imageWidth);

        return $originalFontsize < $newFontSize ? $originalFontsize : $newFontSize;
    }

    private static function isWhiteColor($imgCore, $i, $j, $threshold = 220)
    {
        //collect the rgb value from the current pixel of image
        $rgb = ImageColorAt($imgCore, $i, $j);
        // extract r,g,b value separately
        $colors['r'] = ($rgb >> 16) & 0xFF;
        $colors['g'] = ($rgb >> 8) & 0xFF;
        $colors['b'] = $rgb & 0xFF;
        // get value from rgb scale
        $matchedColors = 0;
        foreach ($colors as $color) {
            if ($color > $threshold) {
                $matchedColors++;
            }
        }

        return ($matchedColors >= 2);
    }


    private static function getNextKeyArray($array, $key){
        if ($key == 0) {
            return array_key_first($array);
        }
        $keys = array_keys($array);
        $position = array_search($key, $keys);
        if (isset($keys[$position + 1])) {
            $nextKey = $keys[$position + 1];
        } else {
            $nextKey = $key;
        }

        return $nextKey;
    }

}
