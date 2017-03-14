<?php

namespace Appzcoder\CrudGenerator\Commands;

use File;
use Illuminate\Console\Command;

class CrudFileCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crud:file
                            {file : The filename for generating the Crud.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Crud including controller, model, views & migrations from a file.';

    /** @var string */
    protected $routeName = '';

    /** @var string */
    protected $controller = '';

    /** @var array */
    protected $defaults = [
        'name' => '',
        '--fields' => '',
        '--fields_from_file' => '',
        '--validations' => '',
        '--controller-namespace' => '',
        '--model-namespace' => '',
        '--pk' => 'id',
        '--pagination' => '25',
        '--indexes' => '',
        '--foreign-keys' => '',
        '--relationships' => '',
        '--route' => 'yes',
        '--route-group' => '',
        '--view-path' => '',
        '--localize' => 'no',
        '--locales' => 'en',
    ];

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $file = $this->argument('file');

        $lines = $this->processJSONFile($file);

        foreach ($lines as $parameters) {
            $this->call('crud:generate', $parameters);
        }
    }

    /**
     * Add routes.
     *
     * @return  array
     */
    protected function addRoutes()
    {
        return ["Route::resource('" . $this->routeName . "', '" . $this->controller . "');"];
    }

    /**
     * @param $file
     * @return array
     */
    protected function processJSONFile($file)
    {
        $json = File::get($file);
        $cruds = json_decode($json);

        return $this->processDefaults($cruds);
    }

    /**
     * @param $cruds
     * @return array
     */
    protected function processDefaults($cruds)
    {
        $lines = [];

        foreach ($cruds as $crud) {

            $result = $this->defaults;
            foreach ($crud as $key => $parameter) {

                if ($key !== 'name') {
                    $result['--' . $key] = $parameter;
                } else {
                    $result['name'] = $parameter;
                }

            }
            $lines[] = $result;

        }

        return $lines;
    }
}
