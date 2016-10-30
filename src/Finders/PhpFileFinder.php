<?php

namespace Bmitch\Envsync\Finders;

class PhpFileFinder
{

    /**
     * Regex pattern to use to find environment variables.
     * @var  string $contents Regular Expression.
     */
    public static $pattern = "/env\(['\"]([A-Za-z_]{1,})/";

    /**
     * Finds the environment variables within the $file.
     * @param  string $file Path and filename.
     * @return array
     */
    public function find($file)
    {
        $contents = file_get_contents($file);

        $envs = [];

        preg_match_all(self::$pattern, $contents, $matches);
        if (isset($matches[1])) {
            $envs = $matches[1];
        }

        return $envs;
    }
}