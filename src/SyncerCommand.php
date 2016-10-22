<?php

namespace Bmitch\Envsync;

use Illuminate\Console\Command;

class SyncerCommand extends Command
{

    protected $signature      = 'bmitch:env-sync {folder=. : Folder to scan"}';
    
    protected $description    = 'Checks source code, .env file, .env.example file and ensures all are synced.';
    
    protected $phpFilePattern = "/env\(['\"]([A-Za-z_]{1,})/";
    
    protected $envFilePattern = "/([A-Za-z_]{1,})=/";

    public function handle()
    {
        $files          = $this->getFiles();
        $env['source']  = $this->getEnvsFromFiles($files);
        $env['example'] = $this->getEnvsFrom('.env.example');
        $env['env']     = $this->getEnvsFrom('.env');
        $env['all']     = array_unique(array_merge($env['env'], $env['example']));
        $env['all']     = array_unique(array_merge($env['all'], $env['source']));

        $results = [];
        foreach ($env['all'] as $index => $variable) {
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

        $tableData = [

            $data,
        ];
        $this->table($headers, $data);
    }

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

    protected function getEnvsFromFiles($files)
    {
        $envs = [];
        foreach ($files as $file) {
                $envs = array_merge($envs, $this->getEnvsFrom($file));
        }
        return $envs;
    }

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
}
