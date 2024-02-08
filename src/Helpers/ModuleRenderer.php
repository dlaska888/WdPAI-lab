<?php

namespace src\Helpers;

use Exception;

class ModuleRenderer
{
    static function renderIcon($iconName) : string
    {
        $svgFilePath = "public/assets/svg/{$iconName}.svg";

        try {
            $svgContent = file_get_contents($svgFilePath);

            if ($svgContent === false) {
                throw new Exception("Failed to read SVG file: $svgFilePath");
            }

            return $svgContent;
        } catch (Exception $e) {
            error_log("Error reading SVG file: " . $e->getMessage());
            return "";
        }
    }
}