<?php

namespace src\Helpers;

class StringHelper
{
    public static function snakeToCamel($snakeCaseString): string
    {
        $words = explode('_', $snakeCaseString);
        $camelCaseString = $words[0];

        for ($i = 1; $i < count($words); $i++) {
            $camelCaseString .= ucfirst($words[$i]);
        }

        return $camelCaseString;
    }

    public static function camelToSnake($camelCaseString): string
    {
        $snakeCaseString = preg_replace_callback(
            '/([a-z])([A-Z])/',
            function ($matches) {
                return $matches[1] . '_' . strtolower($matches[2]);
            },
            $camelCaseString
        );

        return strtolower($snakeCaseString);
    }

    public static function pascalToSnake($pascalCaseString): string
    {
        $camelCaseString = lcfirst($pascalCaseString);
        return self::camelToSnake($camelCaseString);
    }
}