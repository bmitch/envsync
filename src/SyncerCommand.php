<?php

namespace Bmitch\Envsync;

use Illuminate\Console\Command;

class SyncerCommand extends Command
{

    /**
     * The name of the command
     * @var string
     */
    protected $signature      = 'bmitch:env-sync {folder=. : Folder to scan"}';
    
    /**
     * The command's description.
     * @var string
     */
    protected $description    = 'Checks source code, .env file, .env.example file and ensures all are synced.';
    
    /**
     * Regex pattern to extract environmenet variables
     * from a PHP file.
     * @var  string
     */
    protected $phpFilePattern = "/env\(['\"]([A-Za-z_]{1,})/";
    
    /**
     * Regex pattern to extract environment variables
     * from an environment file.
     * @var string
     */
    protected $envFilePattern = "/([A-Za-z_]{1,})=/";

    /**
     * Supressing this for now.
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * Runs the command.
     * @return void
     */
    public function handle()
    {
        $files          = $this->getFiles();
        $env['source']  = $this->getEnvsFromFiles($files);
        $env['example'] = $this->getEnvsFrom('.env.example');
        $env['env']     = $this->getEnvsFrom('.env');
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

        $this->table($headers, $data);
    }

    /**
     * Gets all the PHP files to inspect.
     * @return array
     */
    protected function getFiles()
    {
        $folder = $this->argument('folder');
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($folder));
        $files = [];

        foreach ($iterator as $file) {
            if ($file->isDir()) {
                continue;
            }

            if ($file->getExtension() != 'php') {
                continue;
            }
            $files[] = $file->getPathname();
        }

        return $files;
    }

    /**
     * Gets a list of environment varibles from
     * the provided $files.
     * @param  array $files Files.
     * @return array
     */
    protected function getEnvsFromFiles(array $files)
    {
        $envs = [];
        foreach ($files as $file) {
                $envs = array_merge($envs, $this->getEnvsFrom($file));
        }
        return $envs;
    }

    /**
     * Gets a list of environment varibles
     * from the provided $file.
     * @param  string $file Path and file name.
     * @return array
     */
    protected function getEnvsFrom($file)
    {
        $contents = file_get_contents($file);

        $envs = [];

        if (preg_match("/.*.php/", $file)) {
            preg_match_all($this->phpFilePattern, $contents, $matches);
        } else {
            preg_match_all($this->envFilePattern, $contents, $matches);
        }

        if (isset($matches[1])) {
            $envs = array_merge($envs, $matches[1]);
        }

        return $envs;
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
}
