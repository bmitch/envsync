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

    /**
     * @test
     */
    public function it_can_get_a_env_file_from_a_folder()
    {
        file_put_contents("{$this->path}/.env", 'foobar.txt');
        $files = $this->fileCollector
                    ->get('.env')
                    ->from($this->path);

        $this->assertCount(1, $files);
        $this->assertContains('.env', $files[0]);
    }

    /**
     * @test
     */
    public function it_can_get_a_env_example_file_from_a_folder()
    {
        file_put_contents("{$this->path}/.env.example", 'foobar.txt');
        $files = $this->fileCollector
                    ->get('.env.example')
                    ->from($this->path);

        $this->assertCount(1, $files);
        $this->assertContains('.env.example', $files[0]);
    }
    /**
     * @test
     */
    public function it_can_get_multiple_files_with_same_extension_from_a_folder()
    {
        file_put_contents("{$this->path}/foo.php", 'foo.txt');
        file_put_contents("{$this->path}/bar.php", 'bar.txt');
        file_put_contents("{$this->path}/baz.php", 'baz.txt');
        $files = $this->fileCollector
                    ->get('.php')
                    ->from($this->path);

        $this->assertCount(3, $files);
        $this->assertContains('foo.php', $files[0]);
        $this->assertContains('bar.php', $files[1]);
        $this->assertContains('baz.php', $files[2]);
    }


    /**
     * @test
     */
    public function if_no_files_found_returns_empty_array()
    {
        $files = $this->fileCollector
                    ->get('.php')
                    ->from($this->path);

        $this->assertCount(0, $files);
    }

    public function setup()
    {
        parent::setup();
        $this->fileCollector = new Bmitch\Envsync\Collectors\FileCollector;
    }
}
