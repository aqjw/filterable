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
    protected $signature = 'filter:create
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
        $path = $this->getSourceFilePath();

        $this->makeDirectory(dirname($path));

        $contents = $this->getSourceFile();
        $filterName = $this->argument('name');

        // blue style
        $style = new OutputFormatterStyle('blue');
        $this->output->getFormatter()->setStyle('blue', $style);

        if (!$this->files->exists($path)) {
            $this->files->put($path, $contents);
            $this->info("Filter <blue>{$filterName}</blue> created.");
            return Command::SUCCESS;
        }

        $this->warn("Filter <blue>{$filterName}</blue> already exits.");
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
            'GroupName' => $this->getGroupName('\\'),
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
     * @param $stub
     * @param array $stubVariables
     * @return string
     */
    public function getStubContents($stub, $stubVariables = []): string
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
        if ($field = $this->option('field')) {
            return $field;
        }

        $filterName = $this->argument('name');
        $field = Str::snake($filterName);

        if (str_starts_with($field, 'by_')) {
            $field = str_replace('by_', '', $field);
        }

        return $field;
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