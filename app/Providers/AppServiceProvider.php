<?php

namespace App\Providers;

use App\Http\Controllers\AppLayoutController;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Storage;
use Illuminate\Filesystem\FilesystemAdapter;
use League\Flysystem\Filesystem;
use Masbug\Flysystem\GoogleDriveAdapter;
use Google_Client;
use Google_Service_Drive;
use Spatie\Dropbox\Client as DropboxClient;
use Spatie\FlysystemDropbox\DropboxAdapter;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        AppLayoutController::shareLayoutData();

        // Google
        Storage::extend('google', function ($app, $config) {

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
}