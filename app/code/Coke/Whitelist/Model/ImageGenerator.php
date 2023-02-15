<?php

namespace Coke\Whitelist\Model;

use Intervention\Image\ImageManagerStatic;
use Magento\Catalog\Model\Product\Image\UrlBuilder;

class ImageGenerator
{
    /**
     * @var UrlBuilder
     */
    private $urlBuilder;

    /**
     * @var ModuleConfig
     */
    private $config;

    /**
     * ImageGenerator constructor.
     *
     * @param UrlBuilder $urlBuilder
     * @param ModuleConfig $config
     */
    public function __construct(
        UrlBuilder $urlBuilder,
        ModuleConfig $config
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->config     = $config;
    }

    public function generate($imageUrl, $pledgeTextLines, $nameTextLines, $nameOffset, $sku, $fieldPositions = null, $fontType = 0, $debug = false): ?string
    {
        $image = ImageManagerStatic::make($imageUrl);

        if (!$fieldPositions) {
            $fieldPositions = $this->getDefaultFieldPositions($image);
        }

        if ($debug) {
            $fieldPositions = $this->debugFieldPositions($image, $fieldPositions);
        }

        foreach ($fieldPositions as $key => $fieldsPosition) {
            if (isset($pledgeTextLines[$key])) {
                $canterPositionX = $fieldsPosition['start_x'] + 15;
                $canterPositionY = $fieldsPosition['end_y'] - (($fieldsPosition['end_y'] - $fieldsPosition['start_y']) /2 );
                $fontSize = ($fieldsPosition['end_x'] - $fieldsPosition['start_x']) > 210 ? 37 : 38;
                $coords = [
                    'x' => (int)$canterPositionX,
                    'y' => (int)$canterPositionY,
                    'w' => (int)($fieldsPosition['end_x'] - $fieldsPosition['start_x']),
                    'h' => (int)($fieldsPosition['end_y'] - $fieldsPosition['start_y'])
                ];

                $this->renderLine(
                    $image,
                    $pledgeTextLines[$key],
                    $fontSize,
                    $coords,
                    $fontType,
                    0,
                    '#000000',
                    'left'
                );
            }
        }

        $fieldsPosition = $fieldPositions[1]['is_found'] ? $fieldPositions[1] : $fieldPositions[0] ;
        $nameCoords = [
            'x' => (int) ($fieldsPosition['end_x']),
            'y' => (int) ($fieldsPosition['end_y'] + ($nameOffset ?: 80)),
            'w' => (int) ($fieldsPosition['end_x'] - $fieldsPosition['start_x']),
            'h' => (int)($fieldsPosition['end_y'] - $fieldsPosition['start_y'])
        ];
        foreach ($nameTextLines as $line) {
            $fontSize = ($fieldsPosition['end_x'] - $fieldsPosition['start_x']) > 210 ? 16 : 22;
            $color = (strtolower(substr($sku, strrpos($sku, '-') + 1)) == 'dko') ? '#f40000' : '#ffffff';

            $this->renderLine(
                $image,
                $line,
                $fontSize,
                $nameCoords,
                1,
                12,
                $color,
                'right'
            );
            $nameCoords['y'] += 30;
        }

        if ($image) {
            $encoded = $image->encode('data-url');
            $image->destroy();

            return $encoded;
        }

        return null;
    }

    private function getDefaultFieldPositions($image): array
    {
        return ImageFiledsDetector::getFieldCoordinates($image, $this->config->getImageThreshold());
    }

    private function debugFieldPositions($image, $fieldPositions): array
    {
        foreach ($fieldPositions as $qqRow) {
            if ($qqRow['is_found']) {
                for ($y = $qqRow['start_y']; $y < $qqRow['end_y']; $y++) {
                    for ($x = $qqRow['start_x']; $x < $qqRow['end_x']; $x++) {
                        imagesetpixel($image->getCore(), $x, $y, 500);
                    }
                }
            }
        }

        return $fieldPositions;
    }

    private function renderLine($img, $line, $fontsize, $coords, $fontType = 0, $angel = 0,  $color = '#f40000', $align = 'center')
    {
        $fontCorrection = 0;
        switch ($fontType) {
            case 0: $font_file = __DIR__ . '/../media/fonts/covered-by-your-ygrace-regular.ttf'; $fontCorrection = 5; break;
            case 1: $font_file = __DIR__ . '/../media/fonts/nothing-you-could-dobold.ttf';  $fontCorrection = 5; break;
            case 2: $font_file = __DIR__ . '/../media/fonts/gotham-bold.woff'; break;
            case 3: $font_file = __DIR__ . '/../media/fonts/gotham-book.woff'; break;
            case 4: $font_file = __DIR__ . '/../media/fonts/gotham-light.woff'; break;
            case 5: $font_file = __DIR__ . '/../media/fonts/gotham-medium.woff'; break;
            case 6: $font_file = __DIR__ . '/../media/fonts/you-webfont.woff'; break;
            case 7: $font_file = __DIR__ . '/../media/fonts/fine-hand-let-plain-1.0.ttf'; break;
            case 8: $font_file = __DIR__ . '/../media/fonts/sticker2_Folder_Fonts_CoveredByYourGrace.ttf'; $fontCorrection = 5; break;
            default: $font_file = __DIR__ . '/../media/fonts/you-webfont.ttf'; break;
        }

        $testX = $coords['x'];
        $testY = $coords['y'];

        $newFontSize = ImageFiledsDetector::correctFontSize(
            $coords['w'],
            $coords['h'],
            $fontsize,
            $angel,
            $font_file,
            $line
        );
        $newFontSize += $fontCorrection;

        $img->text($line, $testX, $testY, function($font) use ($newFontSize, $font_file, $angel, $color, $align) {
            $font->file($font_file);
            $font->size($newFontSize);
            $font->color($color);
            $font->align($align);
            $font->valign('center');
            $font->angle($angel);
        });

        return $img;
    }
}
