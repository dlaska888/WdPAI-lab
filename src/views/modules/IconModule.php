<?php

namespace modules;

class IconModule
{
    static function render($iconName): string
    {
        $svgFilePath = "public/assets/svg/$iconName.svg";

        $svgContent = file_get_contents($svgFilePath);

        if (!$svgContent) {
            error_log("Error reading SVG file: $iconName.svg");
            return "";
        }

        return $svgContent;
    }
}