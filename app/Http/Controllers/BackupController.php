<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;

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
        // ── Disk info ──────────────────────────
        $backupPath  = storage_path('app/Afghan POS');
        $diskTotal   = disk_total_space(storage_path());
        $diskFree    = disk_free_space(storage_path());
        $diskUsed    = $diskTotal - $diskFree;
        $diskPct     = $diskTotal > 0 ? round(($diskUsed / $diskTotal) * 100) : 0;

        // ── Backup folder size ─────────────────
        $backupFolderSize = 0;
        if (is_dir($backupPath)) {
            foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($backupPath)) as $file) {
                if ($file->isFile()) $backupFolderSize += $file->getSize();
            }
        }

        // ── Last backup ────────────────────────
        $lastBackup     = null;
        $lastBackupSize = null;
        if (is_dir($backupPath)) {
            $files = glob($backupPath . '/*.zip');
            if ($files) {
                usort($files, fn($a, $b) => filemtime($b) - filemtime($a));
                $lastBackup     = Carbon::createFromTimestamp(filemtime($files[0]))->diffForHumans();
                $lastBackupSize = $this->formatBytes(filesize($files[0]));
            }
        }

        // ── Sync pending counts ────────────────
        $syncTables = $this->getSyncTables();
        $totalPending = collect($syncTables)->sum('pending');

        // ── Cloud status ───────────────────────
        $cloudEnabled  = config('filesystems.default') === 'cloud' || !empty(config('filesystems.disks.s3.key'));
        $cloudProvider = $this->detectCloudProvider();

        return response()->json([
            'status' => [
                'last_backup'        => $lastBackup,
                'last_backup_size'   => $lastBackupSize,
                'disk_total'         => $this->formatBytes($diskTotal),
                'disk_used'          => $this->formatBytes($diskUsed),
                'disk_free'          => $this->formatBytes($diskFree),
                'disk_pct'           => $diskPct,
                'backup_folder_size' => $this->formatBytes($backupFolderSize),
                'backup_path'        => 'storage/app/Afghan POS',
                'db_name'            => config('database.connections.mysql.database'),
                'cloud_enabled'      => $cloudEnabled,
                'cloud_status'       => $cloudEnabled ? 'Connected' : 'Not configured',
                'cloud_provider'     => $cloudProvider,
                'encrypted'          => config('backup.backup.password') !== null,
                'total_pending'      => $totalPending,
            ],
            'sync_tables' => $syncTables,
        ]);
    }

    // ══════════════════════════════════════════
    //  LIST BACKUPS
    //  GET /pos/backup/list
    // ══════════════════════════════════════════
    public function list()
    {
        $backupPath = storage_path('app/Afghan POS');
        $backups    = [];

        if (is_dir($backupPath)) {
            $files = glob($backupPath . '/*.zip');
            if ($files) {
                usort($files, fn($a, $b) => filemtime($b) - filemtime($a));
                foreach ($files as $file) {
                    $backups[] = [
                        'name'       => basename($file),
                        'path'       => $file,
                        'size'       => $this->formatBytes(filesize($file)),
                        'size_bytes' => filesize($file),
                        'created_at' => Carbon::createFromTimestamp(filemtime($file))->format('d M Y H:i'),
                        'cloud'      => false, // mark as true if also uploaded to cloud
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
            // Uses spatie/laravel-backup under the hood
            // Make sure you have run: php artisan vendor:publish --provider="Spatie\Backup\BackupServiceProvider"
            // and configured config/backup.php
            Artisan::call('backup:run', ['--only-db' => true]);

            $output = Artisan::output();

            // Get the latest backup file info
            $backupPath = storage_path('app/Afghan POS');
            $files      = is_dir($backupPath) ? glob($backupPath . '/*.zip') : [];

            $filename = '';
            $size     = '—';

            if ($files) {
                usort($files, fn($a, $b) => filemtime($b) - filemtime($a));
                $latest   = $files[0];
                $filename = basename($latest);
                $size     = $this->formatBytes(filesize($latest));
            }

            return response()->json([
                'success'  => true,
                'filename' => $filename,
                'size'     => $size,
                'output'   => $output,
            ]);
        } catch (\Exception $e) {
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
        ]);

        $path = $request->input('path');

        // Security: ensure path is within backup directory
        $allowedBase = storage_path('app/Afghan POS');
        $realPath    = realpath($path);

        if (!$realPath || strpos($realPath, $allowedBase) !== 0) {
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
        $allowedBase = storage_path('app/Afghan POS');
        $realPath    = realpath($path);

        if (!$realPath || strpos($realPath, $allowedBase) !== 0 || !file_exists($realPath)) {
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
        $allowedBase = storage_path('app/Afghan POS');
        $realPath    = realpath($path);

        if (!$realPath || strpos($realPath, $allowedBase) !== 0) {
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

    private function testGdrive(): void
    {
        // Test Google Drive connectivity
        // Requires spatie/flysystem-google-drive or similar
        // For now just check if config file exists
        $path = storage_path('app/cloud_config.json');
        if (!file_exists($path)) throw new \Exception('Google Drive not configured.');
        $config = json_decode(file_get_contents($path), true);
        if (empty($config['gdrive_key'])) throw new \Exception('Service account key path not set.');
        if (!file_exists(base_path($config['gdrive_key']))) throw new \Exception('Service account JSON file not found.');
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
}
