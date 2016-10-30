<?php

namespace Bmitch\Envsync\Finders;

class EnvFileFinder
{

    /**
     * Regex pattern to use to find environment variables.
     * @var  string $contents Regular Expression.
     */
    public static $pattern = "/([A-Za-z_]{1,})=/";

    /**
     * Finds the environment variables within the $file.
     * @param  string $file Path and filename.
     * @return array
     */
    public static function find($file)
    {
        if (! file_exists($file)) {
            return [];
        }

        $contents = file_get_contents($file);

        $envs = [];

        preg_match_all(self::$pattern, $contents, $matches);
        if (isset($matches[1])) {
            $envs = $matches[1];
        }

        return $envs;
    }
}
