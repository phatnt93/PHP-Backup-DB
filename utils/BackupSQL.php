<?php

class BackupSQL
{

    public function backup()
    {
        $config = Config::get('db');
        $gump = new GUMP();
        $gump->validation_rules([
            'host' => 'required',
            'port' => 'required|integer|min_numeric,1|max_numeric,65535',
            'username' => 'required',
            'password' => 'required',
            'dbnames' => 'required'
        ]);
        $config = $gump->run($config);
        if ($gump->errors()) {
            throw new Exception("Invalid database configuration: " . implode(', ', $gump->get_readable_errors(true)));
        }
        $keepFiles = Config::path('backup.keep_files', 10);
        if (!is_numeric($keepFiles) || $keepFiles < 1) {
            $keepFiles = 10; // Default to 10 if invalid
        }
        $db = Database::connect(
            $config['host'],
            $config['port'],
            $config['username'],
            $config['password']
        );
        if (!$db) {
            throw new Exception("Failed to connect to the database.");
        }
        $dbNames = array_filter(
            array_map('trim', explode(',', $config['dbnames'])),
            function ($name) {
                return !empty($name);
            }
        );
        if (count($dbNames) === 0) {
            throw new Exception("No valid database names provided for backup.");
        }
        // Get db names to backup
        $backupDbNames = [];
        foreach ($dbNames as $key => $dbName) {
            if (strpos($dbName, '*') !== false) {
                $dbNamePattern = str_replace('*', '%', $dbName);
                $query = "SHOW DATABASES LIKE '{$dbNamePattern}'";
                $result = $db->query($query);
                foreach ($result as $row) {
                    $row = array_values($row);
                    $backupDbNames[] = $row[0];
                }
            } else {
                $backupDbNames[] = $dbName;
            }
        }
        // Begin backup process
        foreach ($backupDbNames as $key => $backupDbName) {
            $backupDir = BACKUP_PATH . '/' . $backupDbName;
            Common::createDir($backupDir);

            // Perform backup for each database
            $backupFile = $backupDir . '/' . $backupDbName . '_' . date('Ymd_His') . '.sql';
            $command = "mysqldump --host={$config['host']} --port={$config['port']} --user={$config['username']} --password={$config['password']} {$backupDbName} > {$backupFile}";
            exec($command, $output, $returnVar);

            // Zip the backup file
            $zipFile = $backupFile . '.zip';
            $zip = new ZipArchive();
            if ($zip->open($zipFile, ZipArchive::CREATE) === TRUE) {
                $zip->addFile($backupFile, basename($backupFile));
                $zip->close();
                // Remove the original SQL file after zipping
                unlink($backupFile);
            }

            // Keep only the last $keepFiles backup files
            $files = glob($backupDir . '/*.zip');
            if (count($files) > $keepFiles) {
                // Sort by modification time (newest first)
                usort($files, function ($a, $b) {
                    return filemtime($b) - filemtime($a);
                });
                // Delete files beyond the $keepFiles most recent
                $filesToDelete = array_slice($files, $keepFiles);
                foreach ($filesToDelete as $file) {
                    unlink($file);
                }
            }
        }
    }
}
