<?php

namespace Bmitch\Envsync;

use Bmitch\Envsync\Collectors\FileCollector;
use Bmitch\Envsync\Finders\EnvironmentFinder;
use Bmitch\Envsync\Builders\TableBuilder;

class SyncerCommand
{

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
     * Supressing this for now.
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @param  array $arguments Command line arguments.
     * @return void
     */
    public function handle(array $arguments)
    {
        $this->handleArguments($arguments);

        $envData = $this->getEnvironmentVariables();

        $results = $this->getResults($envData);

        $this->tableBuilder->outputTable($results);
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
     * Collects the command line arguments.
     * @param  array $arguments Command line arguments.
     * @return void
     */
    protected function handleArguments(array $arguments)
    {
        // Default value
        $this->folder = '.';

        if (isset($arguments[1])) {
            $this->folder = $arguments[1];
        }
    }
}
