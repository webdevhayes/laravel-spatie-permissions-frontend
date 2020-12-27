<?php

namespace Webdevhayes\LaravelSpatiePermissionsFrontend;

use Illuminate\Console\Command;

class LaravelSpatiePermissionsFrontendCommand extends Command
{
    protected $signature = 'laravel-spatie-permissions-frontend { type : The preset type (bootstrap, tailwind) }';

    public $description = 'Scaffold views,routes and controllers for roles and permissions';

    /**
     * The views that need to be exported.
     *
     * @var array
     */
    protected $views = [
        'roles/create.stub' => 'roles/create.blade.php',
        'roles/edit.stub' => 'roles/edit.blade.php',
        'roles/index.stub' => 'roles/index.blade.php',
    ];

    /**
     * Execute the console command.
     *
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    public function handle()
    {
        if (static::hasMacro($this->argument('type'))) {
            return call_user_func(static::$macros[$this->argument('type')], $this);
        }

        if (! in_array($this->argument('type'), ['bootstrap', 'tailwind'])) {
            throw new InvalidArgumentException('Invalid preset.');
        }
        $this->ensureDirectoriesExist();
        $this->exportViews();
        $this->exportBackend();

        $this->info('Scaffolding generated successfully.');
    }

    /**
     * Create the directories for the files.
     *
     * @return void
     */
    protected function ensureDirectoriesExist()
    {
        if (! is_dir($directory = $this->getViewPath('roles'))) {
            mkdir($directory, 0755, true);
        }
    }

    /**
     * Export the authentication views.
     *
     * @return void
     */
    protected function exportViews()
    {
        foreach ($this->views as $key => $value) {
            if (file_exists($view = $this->getViewPath($value))) {
                if (! $this->confirm("The [{$value}] view already exists. Do you want to replace it?")) {
                    continue;
                }
            }

            copy(
                __DIR__.'/stubs/views/'.$this->argument('type').'/'.$key,
                $view
            );
        }
    }

    /**
     * Export the authentication backend.
     *
     * @return void
     */
    protected function exportBackend()
    {
        $this->callSilent('laravel-spatie-permissions-frontend:controllers');

        $controller = app_path('Http/Controllers/RoleController.php');

        if (file_exists($controller)) {
            if ($this->confirm("The [RoleController.php] file already exists. Do you want to replace it?")) {
                file_put_contents($controller, $this->compileControllerStub());
            }
        } else {
            file_put_contents($controller, $this->compileControllerStub());
        }

        file_put_contents(
            base_path('routes/web.php'),
            file_get_contents(__DIR__.'/stubs/routes.stub'),
            FILE_APPEND
        );
    }


    /**
     * Compiles the "HomeController" stub.
     *
     * @return string
     */
    protected function compileControllerStub()
    {
        return str_replace(
            '{{namespace}}',
            $this->laravel->getNamespace(),
            file_get_contents(__DIR__.'/stubs/controllers/RoleController.stub')
        );
    }

    /**
     * Get full view path relative to the application's configured view path.
     *
     * @param  string  $path
     * @return string
     */
    protected function getViewPath($path)
    {
        return implode(DIRECTORY_SEPARATOR, [
            config('view.paths')[0] ?? resource_path('views'), $path,
        ]);
    }
}
