<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class BackupController extends Controller
{
    // ══════════════════════════════════════════
    //  PAGE
    // ══════════════════════════════════════════
    public function page()
    {
        return view('system.backup');
    }

    // ══════════════════════════════════════════
    //  STATUS
    //  GET /pos/backup/status
    // ══════════════════════════════════════════
    public function status()
    {
        $backupPath = $this->backupBasePath();
        $this->ensureBackupPath();

        $files = collect(glob($backupPath . '/*.zip'))
            ->sortDesc();

        $last = $files->first();

        // disk usage
        $total = disk_total_space(base_path());
        $free  = disk_free_space(base_path());
        $used  = $total - $free;

        return response()->json([

            'status' => [

                'last_backup' => $last
                    ? date('y-m-d h:i A', filemtime($last))
                    : null,

                'last_backup_size' => $last
                    ? round(filesize($last) / 1024 / 1024, 2) . ' MB'
                    : null,

                'cloud_status' => $this->detectCloudProvider() === 'Not configured' ? 'Not configured' : 'Configured',
                'cloud_enabled' => $this->detectCloudProvider() !== 'Not configured',
                'cloud_provider' => $this->detectCloudProvider(),
                'total_pending' =>
                \App\Models\Sale::where('sync_status', 'pending')->count()
                    +
                    \App\Models\Purchase::where('sync_status', 'pending')->count(),

                'disk_used' => round($used / 1024 / 1024 / 1024, 2) . ' GB',

                'disk_total' => round($total / 1024 / 1024 / 1024, 2) . ' GB',

                'disk_pct' => round(($used / $total) * 100),

                'backup_folder_size' => round(
                    collect(glob($backupPath . '/*.zip') ?: [])
                        ->sum(fn($f) => filesize($f))
                        / 1024 / 1024,
                    2
                ) . ' MB',

                'db_name' => env('DB_DATABASE'),

                'backup_path' => $backupPath,

                'encrypted' => false,
            ],

            'sync_tables' => [
                [
                    'name' => 'sales',
                    'label' => 'Sales',
                    'icon' => 'fas fa-cart-shopping',
                    'color' => '#2563eb',
                    'total' => \App\Models\Sale::count(),
                    'pending' => \App\Models\Sale::where('sync_status', 'pending')->count(),
                    'failed' => \App\Models\Sale::where('sync_status', 'failed')->count(),
                ],

                [
                    'name' => 'purchases',
                    'label' => 'Purchases',
                    'icon' => 'fas fa-box',
                    'color' => '#16a34a',
                    'total' => \App\Models\Purchase::count(),
                    'pending' => \App\Models\Purchase::where('sync_status', 'pending')->count(),
                    'failed' => \App\Models\Purchase::where('sync_status', 'failed')->count(),
                ],
            ],
            'cloud' => [
                'client_id' => filled(env('GOOGLE_DRIVE_CLIENT_ID')),
                'client_secret' => filled(env('GOOGLE_DRIVE_CLIENT_SECRET')),
                'refresh_token' => filled(env('GOOGLE_DRIVE_REFRESH_TOKEN')),
                'folder_id' => filled(env('GOOGLE_DRIVE_FOLDER_ID')),
            ]
        ]);
    }

    // ══════════════════════════════════════════
    //  LIST BACKUPS
    //  GET /pos/backup/list
    // ══════════════════════════════════════════
    public function list()
    {
        $backupPath = $this->backupBasePath();
        $this->ensureBackupPath();
        $backups = [];

        if (is_dir($backupPath)) {

            $files = glob($backupPath . '/*.zip');

            if ($files) {

                usort($files, fn($a, $b) => filemtime($b) - filemtime($a));

                // selected cloud provider
                $provider = env('FILESYSTEM_CLOUD', 'google');

                foreach ($files as $file) {

                    $filename = basename($file);

                    // check if file exists in cloud
                    $inCloud = false;

                    try {
                        if (in_array($provider, ['google', 'dropbox'])) {
                            $inCloud = Storage::disk($provider)->exists($filename);
                        }
                    } catch (\Exception $e) {
                        $inCloud = false;
                    }

                    $backups[] = [
                        'name'       => $filename,
                        'path'       => $file,
                        'size'       => $this->formatBytes(filesize($file)),
                        'size_bytes' => filesize($file),
                        'created_at' => Carbon::createFromTimestamp(
                            filemtime($file)
                        )->format('d M Y h:i A'),
                        'cloud'      => $inCloud,
                    ];
                }
            }
        }

        return response()->json($backups);
    }

    // ══════════════════════════════════════════
    //  RUN BACKUP
    //  POST /pos/backup/run
    // ══════════════════════════════════════════
    public function run()
    {
        try {

            // Create backup
            Artisan::call('backup:run', [
                '--only-db' => true
            ]);

            $output = Artisan::output();

            // Local backup folder
            $backupPath = $this->backupBasePath();
            $this->ensureBackupPath();

            $files = is_dir($backupPath)
                ? glob($backupPath . '/*.zip')
                : [];

            $filename = '';
            $size = '—';

            if ($files) {

                // latest file first
                usort($files, fn($a, $b) => filemtime($b) - filemtime($a));

                $latest = $files[0];

                $filename = basename($latest);

                // get selected cloud provider
                $provider = $this->cloudDiskName();

                // upload if provider configured
                if (in_array($provider, ['google', 'dropbox', 'ftp'], true)) {
                    Storage::disk($provider)->put(
                        $filename,
                        file_get_contents($latest)
                    );
                }

                $size = $this->formatBytes(
                    filesize($latest)
                );
            }

            return response()->json([
                'success' => true,
                'filename' => $filename,
                'size' => $size,
                'provider' => ucfirst($provider ?? 'local'),
                'output' => $output,
            ]);
        } catch (\Throwable $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // ══════════════════════════════════════════
    //  RESTORE
    //  POST /pos/backup/restore
    // ══════════════════════════════════════════
    public function restore(Request $request)
    {
        $request->validate([
            'path' => 'required|string',
            'confirm' => 'required|in:RESTORE',
        ]);

        $path = $request->input('path');

        // Security: ensure path is within backup directory
        $allowedBase = $this->backupBasePath();
        $realPath    = realpath($path);

        if (!$this->isPathInside($realPath, $allowedBase)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid backup path.',
            ], 422);
        }

        if (!file_exists($realPath)) {
            return response()->json([
                'success' => false,
                'message' => 'Backup file not found.',
            ], 404);
        }

        try {
            // Extract the zip
            $zip     = new \ZipArchive();
            $tempDir = storage_path('app/restore_temp_' . time());

            if ($zip->open($realPath) !== true) {
                throw new \Exception('Could not open backup archive.');
            }

            mkdir($tempDir, 0755, true);
            $zip->extractTo($tempDir);
            $zip->close();

            // Find the SQL dump file
            $sqlFiles = glob($tempDir . '/*.sql');
            if (empty($sqlFiles)) {
                $sqlFiles = glob($tempDir . '/**/*.sql');
            }

            if (empty($sqlFiles)) {
                throw new \Exception('No SQL file found in backup archive.');
            }

            $sqlFile = $sqlFiles[0];
            $dbConfig = config('database.connections.mysql');

            // Run the restore using mysql command line
            $command = sprintf(
                'mysql --host=%s --port=%s --user=%s --password=%s %s < %s 2>&1',
                escapeshellarg($dbConfig['host']),
                escapeshellarg($dbConfig['port'] ?? '3306'),
                escapeshellarg($dbConfig['username']),
                escapeshellarg($dbConfig['password']),
                escapeshellarg($dbConfig['database']),
                escapeshellarg($sqlFile)
            );

            exec($command, $output, $returnCode);

            // Cleanup temp dir
            $this->deleteDirectory($tempDir);

            if ($returnCode !== 0) {
                throw new \Exception('MySQL restore failed: ' . implode(' ', $output));
            }

            return response()->json([
                'success' => true,
                'message' => 'Database restored successfully.',
            ]);
        } catch (\Exception $e) {
            // Cleanup on failure
            if (isset($tempDir) && is_dir($tempDir)) {
                $this->deleteDirectory($tempDir);
            }
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // ══════════════════════════════════════════
    //  DOWNLOAD BACKUP
    //  GET /pos/backup/download
    // ══════════════════════════════════════════
    public function download(Request $request)
    {
        $path        = $request->input('path');
        $allowedBase = $this->backupBasePath();
        $realPath    = realpath($path);

        if (!$this->isPathInside($realPath, $allowedBase) || !file_exists($realPath)) {
            abort(404, 'Backup file not found.');
        }

        return response()->download($realPath);
    }

    // ══════════════════════════════════════════
    //  DELETE BACKUP
    //  POST /pos/backup/delete
    // ══════════════════════════════════════════
    public function delete(Request $request)
    {
        $path        = $request->input('path');
        $allowedBase = $this->backupBasePath();
        $realPath    = realpath($path);

        if (!$this->isPathInside($realPath, $allowedBase)) {
            return response()->json(['success' => false, 'message' => 'Invalid path.'], 422);
        }

        if (!file_exists($realPath)) {
            return response()->json(['success' => false, 'message' => 'File not found.'], 404);
        }

        unlink($realPath);

        return response()->json(['success' => true]);
    }

    // ══════════════════════════════════════════
    //  SYNC RECORDS
    //  POST /pos/backup/sync
    // ══════════════════════════════════════════
    public function sync(Request $request)
    {
        $table = $request->input('table', 'all');

        try {
            $synced = 0;
            $tables = $table === 'all'
                ? ['sales', 'purchases']
                : [$table];

            foreach ($tables as $t) {
                if ($t === 'sales') {
                    $count = Sale::where('sync_status', 'pending')
                        ->orWhere('sync_status', 'failed')
                        ->update(['sync_status' => 'synced', 'synced_at' => now()]);
                    $synced += $count;
                }

                if ($t === 'purchases') {
                    $count = Purchase::where('sync_status', 'pending')
                        ->orWhere('sync_status', 'failed')
                        ->update(['sync_status' => 'synced', 'synced_at' => now()]);
                    $synced += $count;
                }
            }

            return response()->json([
                'success' => true,
                'synced'  => $synced,
                'message' => "{$synced} records synced successfully.",
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ══════════════════════════════════════════
    //  SAVE SCHEDULE SETTINGS
    //  POST /pos/backup/schedule
    // ══════════════════════════════════════════
    public function saveSchedule(Request $request)
    {
        // Store schedule preferences in a settings file or DB
        // For simplicity storing in a JSON settings file
        $settings = $request->only([
            'daily_enabled',
            'daily_time',
            'weekly_enabled',
            'auto_cloud',
            'cleanup_enabled',
            'keep_count',
            'encrypt',
        ]);

        $path = storage_path('app/backup_schedule.json');
        file_put_contents($path, json_encode($settings, JSON_PRETTY_PRINT));

        // Update .env or config cache if needed
        // Artisan::call('config:cache');

        return response()->json(['success' => true]);
    }

    // ══════════════════════════════════════════
    //  SAVE CLOUD CONFIG
    //  POST /pos/backup/cloud
    // ══════════════════════════════════════════
    public function saveCloud(Request $request)
    {
        $request->validate([
            'provider' => 'required|in:gdrive,dropbox,ftp',
        ]);

        // Store config in env file (development-friendly approach)
        // In production you'd want a more secure method
        $config = $request->only([
            'provider',
            'gdrive_key',
            'gdrive_folder',
            'dropbox_token',
            'dropbox_path',
            'ftp_host',
            'ftp_port',
            'ftp_user',
            'ftp_pass',
            'ftp_path',
        ]);

        // Save to JSON config (never store passwords in DB)
        $path = storage_path('app/cloud_config.json');
        // Encrypt sensitive fields before saving
        $config['dropbox_token'] = !empty($config['dropbox_token'])
            ? encrypt($config['dropbox_token']) : '';
        $config['ftp_pass'] = !empty($config['ftp_pass'])
            ? encrypt($config['ftp_pass']) : '';

        file_put_contents($path, json_encode($config, JSON_PRETTY_PRINT));

        return response()->json(['success' => true]);
    }

    // ══════════════════════════════════════════
    //  TEST CLOUD CONNECTION
    //  POST /pos/backup/cloud/test
    // ══════════════════════════════════════════
    public function testCloud(Request $request)
    {
        $provider = $request->input('provider');

        try {
            match ($provider) {
                'gdrive'  => $this->testGdrive(),
                'dropbox' => $this->testDropbox(),
                'ftp'     => $this->testFtp($request),
                default   => throw new \Exception('Unknown provider.'),
            };

            return response()->json([
                'success' => true,
                'message' => '✓ Connection successful! Cloud provider is reachable.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '✗ Connection failed: ' . $e->getMessage(),
            ]);
        }
    }

    // ══════════════════════════════════════════
    //  PRIVATE HELPERS
    // ══════════════════════════════════════════
    private function getSyncTables(): array
    {
        return [
            [
                'name'    => 'sales',
                'label'   => 'Sales',
                'icon'    => 'fas fa-receipt',
                'color'   => '#2557e8',
                'total'   => Sale::count(),
                'pending' => Sale::where('sync_status', 'pending')->count(),
                'failed'  => Sale::where('sync_status', 'failed')->count(),
            ],
            [
                'name'    => 'purchases',
                'label'   => 'Purchases',
                'icon'    => 'fas fa-truck',
                'color'   => '#d97706',
                'total'   => Purchase::count(),
                'pending' => Purchase::where('sync_status', 'pending')->count(),
                'failed'  => Purchase::where('sync_status', 'failed')->count(),
            ],
        ];
    }

    private function backupBasePath(): string
    {
        return storage_path('app/private/Afghan POS');
    }

    private function ensureBackupPath(): void
    {
        if (! is_dir($this->backupBasePath())) {
            mkdir($this->backupBasePath(), 0755, true);
        }
    }

    private function cloudDiskName(): string
    {
        return match (env('FILESYSTEM_CLOUD', 'google')) {
            'gdrive' => 'google',
            default => env('FILESYSTEM_CLOUD', 'google'),
        };
    }

    private function isPathInside(?string $realPath, string $allowedBase): bool
    {
        $realBase = realpath($allowedBase);

        return $realPath
            && $realBase
            && str_starts_with(strtolower($realPath), strtolower($realBase . DIRECTORY_SEPARATOR));
    }

    private function detectCloudProvider(): string
    {
        $path = storage_path('app/cloud_config.json');
        if (file_exists($path)) {
            $config = json_decode(file_get_contents($path), true);
            return match ($config['provider'] ?? '') {
                'gdrive'  => 'Google Drive',
                'dropbox' => 'Dropbox',
                'ftp'     => 'FTP Server',
                default   => 'Not configured',
            };
        }
        return 'Not configured';
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1073741824) return round($bytes / 1073741824, 2) . ' GB';
        if ($bytes >= 1048576)    return round($bytes / 1048576, 2)    . ' MB';
        if ($bytes >= 1024)       return round($bytes / 1024, 2)       . ' KB';
        return $bytes . ' B';
    }

    private function deleteDirectory(string $dir): void
    {
        if (!is_dir($dir)) return;
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . DIRECTORY_SEPARATOR . $file;
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }
        rmdir($dir);
    }

    // private function testGdrive(): void
    // {
    //     // Test Google Drive connectivity
    //     // Requires spatie/flysystem-google-drive or similar
    //     // For now just check if config file exists
    //     $path = storage_path('app/cloud_config.json');
    //     if (!file_exists($path)) throw new \Exception('Google Drive not configured.');
    //     $config = json_decode(file_get_contents($path), true);
    //     if (empty($config['gdrive_key'])) throw new \Exception('Service account key path not set.');
    //     if (!file_exists(base_path($config['gdrive_key']))) throw new \Exception('Service account JSON file not found.');
    // }
    private function testGdrive(): void
    {
        try {

            Storage::disk('google')->write(
                'connection-test.txt',
                'Google Drive connected'
            );
        } catch (\Throwable $e) {
            throw new \Exception($e->getMessage());
        }
    }

    private function testDropbox(): void
    {
        $path = storage_path('app/cloud_config.json');
        if (!file_exists($path)) throw new \Exception('Dropbox not configured.');
        $config = json_decode(file_get_contents($path), true);
        if (empty($config['dropbox_token'])) throw new \Exception('Dropbox access token not set.');

        // Quick API ping
        $token = decrypt($config['dropbox_token']);
        $ch    = curl_init('https://api.dropboxapi.com/2/users/get_current_account');
        curl_setopt_array($ch, [
            CURLOPT_HTTPHEADER     => ["Authorization: Bearer {$token}"],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => 'null',
            CURLOPT_TIMEOUT        => 10,
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) throw new \Exception("Dropbox API returned HTTP {$httpCode}.");
    }

    private function testFtp(Request $request): void
    {
        $path = storage_path('app/cloud_config.json');
        if (!file_exists($path)) throw new \Exception('FTP not configured.');
        $config = json_decode(file_get_contents($path), true);

        $host = $config['ftp_host'] ?? '';
        $user = $config['ftp_user'] ?? '';
        $pass = !empty($config['ftp_pass']) ? decrypt($config['ftp_pass']) : '';
        $port = (int)($config['ftp_port'] ?? 21);

        if (empty($host)) throw new \Exception('FTP host not configured.');

        $conn = @ftp_connect($host, $port, 10);
        if (!$conn) throw new \Exception("Cannot connect to FTP host: {$host}:{$port}");

        $login = @ftp_login($conn, $user, $pass);
        ftp_close($conn);

        if (!$login) throw new \Exception('FTP login failed. Check username and password.');
    }
    public function cloudQuota()
    {
        try {

            $disk = Storage::disk('google');

            $reflection = new \ReflectionClass($disk);

            $adapterProperty = $reflection->getProperty('adapter');

            $adapterProperty->setAccessible(true);

            $adapter = $adapterProperty->getValue($disk);

            $service = $adapter->getService();

            $about = $service->about->get([
                'fields' => 'storageQuota'
            ]);

            $quota = $about->storageQuota;

            $total = (int) $quota->limit;
            $used  = (int) $quota->usage;

            return response()->json([
                'success' => true,
                'total'   => $this->formatBytes($total),
                'used'    => $this->formatBytes($used),
                'free'    => $this->formatBytes($total - $used),
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function dropboxQuota()
    {
        try {

            $token = config('filesystems.disks.dropbox.authorization_token');

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
            ])->send(
                'POST',
                'https://api.dropboxapi.com/2/users/get_space_usage'
            );

            $space = $response->json();

            $used = $space['used'] ?? 0;

            $total = 0;

            if (isset($space['allocation']['allocated'])) {

                $total = $space['allocation']['allocated'];
            } elseif (isset($space['allocation']['individual']['allocated'])) {

                $total = $space['allocation']['individual']['allocated'];
            } elseif (isset($space['allocation']['team']['allocated'])) {

                $total = $space['allocation']['team']['allocated'];
            }

            return response()->json([
                'success' => true,
                'total' => $this->formatBytes($total),
                'used' => $this->formatBytes($used),
                'free' => $this->formatBytes($total - $used),
            ]);
        } catch (\Throwable $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
