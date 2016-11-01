<?php

namespace Bmitch\Envsync\Builders;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\ConsoleOutput;

class TableBuilder
{
    /**
     * Takes the provided $data and outputs a table on the console output.
     * @param  array  $data Enviroment variable results data.
     * @param  string $mode Mode the program is being run in.
     * @return void
     */
    public function outputTable(array $data, $mode)
    {
        $headers = ['Variable', 'In Source', 'In .env.example', 'In .env'];

        if ($mode == 'ci') {
            $headers = ['Variable', 'In Source', 'In .env.example'];
            foreach ($data as &$element) {
                unset($element['inenv']);
            }
        }

        if ($mode == 'deploy') {
            $headers = ['Variable', 'In Source', 'In .env'];
            foreach ($data as &$element) {
                unset($element['inenvexample']);
            }
        }

        $table = new Table(new ConsoleOutput);

        $table->setHeaders($headers)->setRows($data)->render();
    }
}
