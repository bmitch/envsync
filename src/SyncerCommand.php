<?php

namespace Bmitch\Envsync;

use Bmitch\Envsync\Collectors\FileCollector;
use Bmitch\Envsync\Finders\EnvironmentFinder;
use Bmitch\Envsync\Builders\TableBuilder;

class SyncerCommand
{

    /**
     * Will be returned when program exits.
     * @var integer
     */
    protected $exitCode = 0;

    /**
     * The mode the program is running in.
     * @var string
     */
    protected $mode;

    /**
     * Instance of the FileCollector class.
     * @var FileCollector
     */
    protected $fileCollector;

    /**
     * Instance of the EnvironmentFilder class.
     * @var EnvironmentFinder
     */
    protected $envFinder;

    /**
     * Instance of the TableBuilder class.
     * @var TableBuilder
     */
    protected $tableBuilder;

    /**
     * The source colde folder that will be inspected.
     * @var string
     */
    protected $folder;

    /**
     * Holds an error message if command runs into an error.
     * @var string
     */
    protected $error;

    /**
     * Creates a new instance of the SyncerCommand.
     * @param FileCollector     $fileCollector File Collector.
     * @param EnvironmentFinder $envFinder     Environment Variable Filder.
     * @param TableBuilder      $tableBuilder  Table Builder.
     */
    public function __construct(FileCollector $fileCollector, EnvironmentFinder $envFinder, TableBuilder $tableBuilder)
    {
        $this->fileCollector = $fileCollector;
        $this->envFinder = $envFinder;
        $this->tableBuilder = $tableBuilder;
    }

    /**
     * Runs the command.
     * @param  array $arguments Command line arguments.
     * @return void
     */
    public function handle(array $arguments)
    {
        if (! $this->argumentsValid($arguments)) {
            echo $this->error;
            return;
        }

        print "\n\nEnvSyncer Report - https://github.com/bmitch/envsync\n";

        $envData = $this->getEnvironmentVariables();

        $results = $this->getResults($envData);

        if ($this->mode == 'ci') {
            $this->checkForCiBuild($results);
        }

        if ($this->mode == 'deploy') {
            $this->checkForDeploy($results);
        }

        $this->tableBuilder->outputTable($results, $this->mode);


        exit($this->exitCode);
    }

    /**
     * Gets a list of the environment variables defined in the
     * source code, .env and .env.example file.
     * @return array
     */
    protected function getEnvironmentVariables()
    {
        $files = $this->fileCollector
                    ->get('.php')
                    ->from($this->folder);

        $env            = [];
        $env['source']  = $this->envFinder->getFromFiles($files);
        $env['example'] = $this->envFinder->getFromFile('.env.example');
        $env['env']     = $this->envFinder->getFromFile('.env');
        $env['all']     = $this->mergeEnvs($env);

        return $env;
    }

    /**
     * Takes the list of environment variables defined and creates
     * a results array showing each variable and where it is or is not
     * defined.
     * @param  array $envData Environment Variable Data.
     * @return array
     */
    protected function getResults(array $envData)
    {
        $results = [];

        foreach ($envData['all'] as $variable) {
            $results[] = [
                'variable'     => $variable,
                'insource'     => in_array($variable, $envData['source']) ? 'Yes' : 'No',
                'inenvexample' => in_array($variable, $envData['example']) ? 'Yes' : 'No',
                'inenv'        => in_array($variable, $envData['env']) ? 'Yes' : 'No',
            ];
        }

        return $results;
    }

    /**
     * Looks through all the current env variables from all
     * sources and makes a master list of all of them.
     * @param  array $currentEnvs Current Env Variables.
     * @return array
     */
    protected function mergeEnvs(array $currentEnvs)
    {
        $allEnvs = [];
        $allEnvs     = array_unique(array_merge($currentEnvs['env'], $currentEnvs['example']));
        $allEnvs     = array_unique(array_merge($allEnvs, $currentEnvs['source']));

        return $allEnvs;
    }

    /**
     * If a variable is defined in the source code but
     * not defined in the .env.example file we write
     * to the message and set response to 1
     * @param  array $results Environment Variable Results.
     * @return void
     */
    protected function checkForCiBuild(array $results)
    {
        foreach ($results as $row) {
            if ($row['insource'] == 'Yes' && $row['inenvexample'] == 'No') {
                $this->exitCode = 1;
            }
        }
    }

    /**
     * If a variable is defined in the source code but
     * not defined in the .env file we write
     * to the message and set response to 1
     * @param  array $results Environment Variable Results.
     * @return void
     */
    protected function checkForDeploy(array $results)
    {
        foreach ($results as $row) {
            if ($row['insource'] == 'Yes' && $row['inenv'] == 'No') {
                $this->exitCode = 1;
            }
        }
    }

    /**
     * Collects the command line arguments.
     * @param  array $arguments Command line arguments.
     * @return boolean
     */
    protected function argumentsValid(array $arguments)
    {
        // Default value
        $this->folder = '.';

        if (isset($arguments[1])) {
            $this->folder = $arguments[1];
            if (! file_exists($this->folder)) {
                $this->error = "Error: Folder {$this->folder} does not exist.";
                return false;
            }
        }

        if (isset($arguments[2])) {
            $this->mode = strtolower($arguments[2]);
            if (!in_array($this->mode, ['ci', 'deploy', 'default'])) {
                $this->error = "Error: Invalid argument {$this->mode}";
                return false;
            }
            return true;
        }

        $this->mode = 'default';

        return true;
    }
}
