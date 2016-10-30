<?php

use Bmitch\Envsync\Finders\EnvironmentFinder;

class EnvironmentFinderTest extends FileSystemTest
{

    /**
     * The EnvironmentFinder class we'll be testing in this class.
     * @var EnvironmentFinder
     */
    protected $envFinder;

    /**
     * @test
     */
    public function it_can_find_an_environment_varaible_in_a_dot_env_file()
    {
		file_put_contents("{$this->path}/.env", 'FOOBAR=BAZ');
    	$result = $this->envFinder->getFromFile("{$this->path}/.env");
    	$this->assertCount(1, $result);
    	$this->assertEquals('FOOBAR', $result[0]);
    }

    /**
     * @test
     */
    public function it_can_find_multiple_environment_varaibles_in_a_dot_env_file()
    {
		file_put_contents("{$this->path}/.env", "FOOBAR=BAZ\nBAZBAR=FOO");
    	$result = $this->envFinder->getFromFile("{$this->path}/.env");
    	$this->assertCount(2, $result);
    	$this->assertEquals('FOOBAR', $result[0]);
    	$this->assertEquals('BAZBAR', $result[1]);
    }

    /**
     * @test
     */
    public function it_can_find_zero_environment_varaibles_in_a_dot_env_file()
    {
		file_put_contents("{$this->path}/.env", "");
    	$result = $this->envFinder->getFromFile("{$this->path}/.env");
    	$this->assertCount(0, $result);
    }

    /**
     * @test
     */
    public function it_can_find_an_environment_varaible_in_a_dot_env_example_file()
    {
		file_put_contents("{$this->path}/.env.example", 'FOOBAR=BAZ');
    	$result = $this->envFinder->getFromFile("{$this->path}/.env.example");
    	$this->assertCount(1, $result);
    	$this->assertEquals('FOOBAR', $result[0]);
    }

    /**
     * @test
     */
    public function it_can_find_multiple_environment_varaibles_in_a_dot_env_example_file()
    {
		file_put_contents("{$this->path}/.env.example", "FOOBAR=BAZ\nBAZBAR=FOO");
    	$result = $this->envFinder->getFromFile("{$this->path}/.env.example");
    	$this->assertCount(2, $result);
    	$this->assertEquals('FOOBAR', $result[0]);
    	$this->assertEquals('BAZBAR', $result[1]);
    }

    /**
     * @test
     */
    public function it_can_find_zero_environment_varaibles_in_a_dot_env_example_file()
    {
		file_put_contents("{$this->path}/.env.example", "");
    	$result = $this->envFinder->getFromFile("{$this->path}/.env.example");
    	$this->assertCount(0, $result);
    }

    /**
     * @test
     */
    public function it_can_find_an_environment_varaible_in_a_php_file()
    {
		file_put_contents("{$this->path}/foobar.php", "env('FOOBAR');");
    	$result = $this->envFinder->getFromFile("{$this->path}/foobar.php");
    	$this->assertCount(1, $result);
    	$this->assertEquals('FOOBAR', $result[0]);
    }

    /**
     * @test
     */
    public function it_can_find_multiple_environment_varaibles_in_a_php_file()
    {
		file_put_contents("{$this->path}/foobar.php", "env('FOOBAR'); env('BARBAZ');");
    	$result = $this->envFinder->getFromFile("{$this->path}/foobar.php");
    	$this->assertCount(2, $result);
    	$this->assertEquals('FOOBAR', $result[0]);
    	$this->assertEquals('BARBAZ', $result[1]);
    }

    /**
     * @test
     */
    public function it_can_find_zero_environment_varaibles_in_a_php_file()
    {
		file_put_contents("{$this->path}/foobar.php", "");
    	$result = $this->envFinder->getFromFile("{$this->path}/foobar.php");
    	$this->assertCount(0, $result);
    }

    /**
     * @test
     */
    public function if_the_php_file_doesnt_exist_then_no_results_are_found()
    {
    	$result = $this->envFinder->getFromFile("{$this->path}/foobar.php");
    	$this->assertCount(0, $result);
    }

    /**
     * @test
     */
    public function if_the_dot_env_file_doesnt_exist_then_no_results_are_found()
    {
    	$result = $this->envFinder->getFromFile("{$this->path}/.env");
    	$this->assertCount(0, $result);
    }

    /**
     * @test
     */
    public function if_the_dont_env_example_file_doesnt_exist_then_no_results_are_found()
    {
    	$result = $this->envFinder->getFromFile("{$this->path}/.env.example");
    	$this->assertCount(0, $result);
    }

    public function setup()
    {
        parent::setup();
        $this->envFinder = new EnvironmentFinder;
    }
}
