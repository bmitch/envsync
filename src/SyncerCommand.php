<?php

namespace Bmitch\Envsync;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\ConsoleOutput;
use Bmitch\Envsync\Collectors\FileCollector;
use Bmitch\Envsync\Finders\EnvironmentFinder;

class SyncerCommand
{

    /**
     * Creates a new instance of the SyncerCommand.
     * @param FileCollector     $fileCollector File Collector.
     * @param EnvironmentFinder $envFinder     Environment Variable Filder.
     */
    public function __construct(FileCollector $fileCollector, EnvironmentFinder $envFinder)
    {
        $this->fileCollector = $fileCollector;
        $this->envFinder = $envFinder;
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

        $files = $this->fileCollector
                    ->get('.php')
                    ->from($this->folder);

        $env['source']  = $this->envFinder->getFromFiles($files);
        $env['example'] = $this->envFinder->getFromFile('.env.example');
        $env['env']     = $this->envFinder->getFromFile('.env');
        $env['all']     = $this->getAllEnvs($env);

        $results = [];

        foreach ($env['all'] as $variable) {
            $results[$variable] = [];
            $results[$variable]['inSource']     = in_array($variable, $env['source']) ? 'Yes' : 'No';
            $results[$variable]['inSource']     = in_array($variable, $env['source']) ? 'Yes' : 'No';
            $results[$variable]['inEnvExample'] = in_array($variable, $env['example']) ? 'Yes' : 'No';
            $results[$variable]['inEnv']        = in_array($variable, $env['env']) ? 'Yes' : 'No';
        }

        $data = [];

        foreach ($results as $variable => $result) {
            $data[] = [
                'variable'     => $variable,
                'insource'     => $result['inSource'],
                'inenvexample' => $result['inEnvExample'],
                'inenv'        => $result['inEnv'],
            ];
        }

        $headers = ['Variable', 'In Source', 'In .env.example', 'In .env'];

        $table = new Table(new ConsoleOutput);

        $table->setHeaders($headers)->setRows($data)->render();
    }

    /**
     * Looks through all the current env variables from all
     * sources and makes a master list of all of them.
     * @param  array $currentEnvs Current Env Variables.
     * @return array
     */
    protected function getAllEnvs(array $currentEnvs)
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
