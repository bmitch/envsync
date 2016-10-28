<?php

namespace Bmitch\Envsync\Finders;

class EnvironmentFinder
{
    /**
     * Gets the environment variables defined within $file.
     * @param  string $file Path and filename.
     * @return array
     */
    public function getFromFile($file)
    {
        if (preg_match("/.*.php/", $file)) {
            return PhpFileFinder::find($file);
        } else {
            return EnvFileFinder::find($file);
        }

        return [];
    }

    /**
     * Gets the environment variables defined within the $files.
     * @param  array $files Array of paths and filenames.
     * @return array
     */
    public function getFromFiles(array $files)
    {
        $envs = [];

        foreach ($files as $file) {
            $envs = array_merge($envs, $this->getFromFile($file));
        }

        return $envs;
    }
}
