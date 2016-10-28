<?php

namespace Bmitch\Envsync\Collectors;

class FileCollector
{
    /**
     * Regular expression pattern used to find files.
     * @var string
     */
    protected $filePattern;

    /**
     * Sets the regex pattern to use to find files.
     * @param  string $regex Regular Expression.
     * @return self
     */
    public function get($regex)
    {
        $this->filePattern = $regex;
        return $this;
    }

    /**
     * Looks in the provided $folder for files that match
     * $this->filePattern and returns them.
     * @param  string $folder Folder path.
     * @return array
     */
    public function from($folder)
    {
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($folder));
        $files = [];

        foreach ($iterator as $file) {
            if ($file->isDir()) {
                continue;
            }

            if (! preg_match("/{$this->filePattern}/", $file->getFilename())) {
                continue;
            }

            $files[] = $file->getPathname();
        }

        return $files;
    }
}
