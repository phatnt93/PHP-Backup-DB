<?php

class Common
{

    public static function createDir($dir)
    {
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0755, true)) {
                throw new Exception("Failed to create directory: " . $dir);
            }
        }
    }
}
