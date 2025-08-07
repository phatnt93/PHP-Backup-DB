<?php

class Config
{
    private static $config = [];

    public static function load($file)
    {
        if (file_exists($file)) {
            self::$config = parse_ini_file($file, true);
        } else {
            throw new Exception("Configuration file not found: " . $file);
        }
    }

    public static function get($key, $default = null)
    {
        return isset(self::$config[$key]) ? self::$config[$key] : $default;
    }

    public static function set($key, $value)
    {
        self::$config[$key] = $value;
    }

    public static function all()
    {
        return self::$config;
    }

    public static function path($path, $default = null)
    {
        $parts = explode('.', $path);
        $value = self::$config;
        foreach ($parts as $part) {
            if (is_array($value) && array_key_exists($part, $value)) {
                $value = $value[$part];
            } else {
                return $default;
            }
        }
        return $value;
    }
}
