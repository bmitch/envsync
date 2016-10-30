<?php

class FileCollectorTest extends FileSystemTest
{
    /**
     * @test
     */
    public function it_can_get_a_php_file_from_a_folder()
    {
    	file_put_contents("{$this->path}/foobar.php", 'foobar.txt');
        $files = $this->fileCollector
                    ->get('.php')
                    ->from($this->path);

        $this->assertCount(1, $files);
        $this->assertContains('foobar.php', $files[0]);
    }

    public function setup()
    {
    	parent::setup();
        $this->fileCollector = new Bmitch\Envsync\Collectors\FileCollector;
    }
}
