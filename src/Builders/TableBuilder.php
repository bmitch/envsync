<?php

namespace Bmitch\Envsync\Builders;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\ConsoleOutput;

class TableBuilder
{
    /**
     * Takes the provided $data and outputs a table on the console output.
     * @param  array $data Enviroment variable results data.
     * @return void
     */
    public function outputTable(array $data)
    {
        $headers = ['Variable', 'In Source', 'In .env.example', 'In .env'];

        $table = new Table(new ConsoleOutput);

        $table->setHeaders($headers)->setRows($data)->render();
    }
}
