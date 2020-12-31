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
        'permissions/create.stub' => 'permissions/create.blade.php',
        'permissions/edit.stub' => 'permissions/edit.blade.php',
        'permissions/index.stub' => 'permissions/index.blade.php',
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
        if (! is_dir($directory = $this->getViewPath('permissions'))) {
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

        $roleController = app_path('Http/Controllers/RoleController.php');
        $permissionController = app_path('Http/Controllers/PermissionController.php');

        if (file_exists($roleController)) {
            if ($this->confirm("The [RoleController.php] file already exists. Do you want to replace it?")) {
                file_put_contents($roleController, $this->compileRoleControllerStub());
            }
        } else {
            file_put_contents($roleController, $this->compileRoleControllerStub());
        }

        if (file_exists($permissionController)) {
            if ($this->confirm("The [PermissionController.php] file already exists. Do you want to replace it?")) {
                file_put_contents($permissionController, $this->compilePermissionControllerStub());
            }
        } else {
            file_put_contents($permissionController, $this->compilePermissionControllerStub());
        }

        file_put_contents(
            base_path('routes/web.php'),
            file_get_contents(__DIR__.'/stubs/routes.stub'),
            FILE_APPEND
        );
    }


    /**
     * Compiles the "RoleController" stub.
     *
     * @return string
     */
    protected function compileRoleControllerStub()
    {
        return str_replace(
            '{{namespace}}',
            $this->laravel->getNamespace(),
            file_get_contents(__DIR__.'/stubs/controllers/RoleController.stub')
        );
    }

    /**
     * Compiles the "PermissionController" stub.
     *
     * @return string
     */
    protected function compilePermissionControllerStub()
    {
        return str_replace(
            '{{namespace}}',
            $this->laravel->getNamespace(),
            file_get_contents(__DIR__.'/stubs/controllers/PermissionController.stub')
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
