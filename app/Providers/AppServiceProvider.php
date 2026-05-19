<?php

namespace App\Providers;

use App\Http\Controllers\AppLayoutController;
use App\Models\Setting;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Filesystem\FilesystemAdapter;
use League\Flysystem\Filesystem;
use Masbug\Flysystem\GoogleDriveAdapter;
use Google_Client;
use Google_Service_Drive;
use Spatie\Dropbox\Client as DropboxClient;
use Spatie\FlysystemDropbox\DropboxAdapter;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        AppLayoutController::shareLayoutData();
        $this->repairStaleViewTempFiles();
        $this->applyRuntimeSettings();

        // Google
        Storage::extend('google', function ($app, $config) {
            $locale = app()->getLocale();

        Carbon::setLocale($locale === 'dr' ? 'fa' : $locale);

            $client = new Google_Client();
            $client->setClientId($config['clientId']);
            $client->setClientSecret($config['clientSecret']);
            $client->refreshToken($config['refreshToken']);


            $service = new Google_Service_Drive($client);

            $adapter = new GoogleDriveAdapter(
                $service,
                $config['folderId']
            );

            return new FilesystemAdapter(
                new Filesystem($adapter),
                $adapter,
                $config
            );
        });

        // Dropbox
        Storage::extend('dropbox', function ($app, $config) {

            $client = new DropboxClient(
                $config['authorization_token']
            );

            $adapter = new DropboxAdapter($client);

            return new FilesystemAdapter(
                new Filesystem($adapter),
                $adapter,
                $config
            );
        });
    }

    private function repairStaleViewTempFiles(): void
    {
        $viewsPath = storage_path('framework/views');
        File::ensureDirectoryExists($viewsPath);
        File::ensureDirectoryExists(config('view.compiled', $viewsPath));
        File::ensureDirectoryExists(base_path('bootstrap/cache'));

        if (! app()->environment(['local', 'testing']) || random_int(1, 100) !== 1) {
            return;
        }

        foreach (File::glob($viewsPath . DIRECTORY_SEPARATOR . '*.tmp') ?: [] as $tmpFile) {
            if (File::lastModified($tmpFile) < now()->subMinutes(10)->timestamp) {
                File::delete($tmpFile);
            }
        }
    }

    private function applyRuntimeSettings(): void
    {
        try {
            $timezone = Setting::get('timezone', config('app.timezone'));
            if (is_string($timezone) && in_array($timezone, timezone_identifiers_list(), true)) {
                date_default_timezone_set($timezone);
            }
        } catch (\Throwable) {
            date_default_timezone_set(config('app.timezone'));
        }
    }
}
