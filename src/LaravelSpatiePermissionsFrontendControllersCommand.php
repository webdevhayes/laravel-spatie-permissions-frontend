<?php


namespace Webdevhayes\LaravelSpatiePermissionsFrontend;


use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Symfony\Component\Finder\SplFileInfo;

class LaravelSpatiePermissionsFrontendControllersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laravel-spatie-permissions-frontend:controllers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scaffold the frontend controllers';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if (! is_dir($directory = app_path('Http/Controllers'))) {
            mkdir($directory, 0755, true);
        }

        $filesystem = new Filesystem;

        collect($filesystem->allFiles(__DIR__.'/stubs/controllers'))
            ->each(function (SplFileInfo $file) use ($filesystem) {
                $filesystem->copy(
                    $file->getPathname(),
                    app_path('Http/Controllers/'.Str::replaceLast('.stub', '.php', $file->getFilename()))
                );
            });

        $this->info('Scaffolding generated successfully.');
    }
}