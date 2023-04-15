<?php

namespace Aqjw\Filterable\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

class CreateFilter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:filter
                            {name : The filter name}
                            {--field= : The field name that is passed in the request}
                            {--group= : The group name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a filter.';


    /**
     * Filesystem instance
     *
     * @var Filesystem
     */
    protected $files;

    /**
     * Create a new command instance.
     *
     * @param Filesystem $files
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        // Get the path for the new filter
        $path = $this->getSourceFilePath();

        // Ensure the directory exists
        $this->makeDirectory(dirname($path));

        // Get the contents of the new filter
        $contents = $this->getSourceFile();

        // Get the full namespace for the new filter
        $filterName = $this->getNamespace() . '\\' . $this->argument('name');

        // Set the output color for the filter name to blue
        $style = new OutputFormatterStyle('blue');
        $this->output->getFormatter()->setStyle('blue', $style);

        // If the filter file doesn't exist, create it and display a success message
        if (!$this->files->exists($path)) {
            $this->files->put($path, $contents);
            $this->info("Filter <blue>{$filterName}</blue> created.");
            return Command::SUCCESS;
        }

        // If the filter file already exists, display a warning message
        $this->warn("Filter <blue>{$filterName}</blue> already exists.");
        return Command::FAILURE;
    }


    /**
     * Return the stub file path
     *
     * @return string
     */
    public function getStubPath(): string
    {
        return __DIR__ . '/../stubs/Filter.php.stub';
    }

    /**
     * Map the stub variables present in stub to its value
     *
     * @return array
     */
    public function getStubVariables(): array
    {
        return [
            'Namespace' => $this->getNamespace(),
            'ClassName' => $this->argument('name'),
            'FieldName' => $this->getFieldName(),
        ];
    }

    /**
     * Get the stub path and the stub variables
     *
     * @return string
     *
     */
    public function getSourceFile(): string
    {
        return $this->getStubContents($this->getStubPath(), $this->getStubVariables());
    }

    /**
     * Replace the stub variables(key) with the desire value
     *
     * @param string $stub
     * @param array $stubVariables
     *
     * @return string
     */
    public function getStubContents(string $stub, array $stubVariables = []): string
    {
        $contents = file_get_contents($stub);

        foreach ($stubVariables as $search => $replace) {
            $contents = str_replace('{{ ' . $search . ' }}', $replace, $contents);
        }

        return $contents;
    }

    /**
     * Get the full path of generate class
     *
     * @return string
     */
    public function getSourceFilePath(): string
    {
        return base_path('App/Filters') . $this->getGroupName('/') . '/' . $this->argument('name') . '.php';
    }

    /**
     * Get the filter namespace
     *
     * @return string
     */
    public function getNamespace(): string
    {
        return 'App\\Filters' . $this->getGroupName('\\');
    }

    /**
     * Get the group name if the option is passed.
     *
     * @param string $prefix
     *
     * @return string
     */
    public function getGroupName(string $prefix = ''): string
    {
        $group = $this->option('group');
        if (!$group) {
            return '';
        }

        return $prefix . $group;
    }

    /**
     * Get the field name.
     *
     * @return string
     */
    public function getFieldName(): string
    {
        // check if the 'field' option is specified
        $field = $this->option('field');
        if ($field !== null) {
            return $field;
        }

        // otherwise, generate the field name based on the filter name
        $filterName = $this->argument('name');
        return str_replace('by_', '', Str::snake($filterName));
    }

    /**
     * Build the directory for the class if necessary.
     *
     * @param  string  $path
     * @return string
     */
    protected function makeDirectory($path): string
    {
        if (!$this->files->isDirectory($path)) {
            $this->files->makeDirectory($path, 0777, true, true);
        }

        return $path;
    }

}
