<?php

abstract class FileSystemTest extends PHPUnit_Framework_TestCase
{

	protected $path = __DIR__ . '/temp';

    protected function createTempFolder()
    {
        mkdir($this->path, 0700, true);
    }

    protected function removeTempFolder()
    {
        if (file_exists($this->path)) {
            array_map('unlink', glob($this->path . '/' . '{,.}[!.,!..]*',GLOB_MARK|GLOB_BRACE));
            rmdir($this->path);
        }
    }

    public function setup()
    {
    	parent::setup();
        $this->removeTempFolder();
        $this->createTempFolder();
    }

    public function teardown()
    {
    	parent::teardown();
        $this->removeTempFolder();
    }
}
