<?php

define('BASE_PATH', __DIR__);
define('BACKUP_PATH', BASE_PATH . '/backups');
define('LOG_PATH', BASE_PATH . '/logs');

require_once BASE_PATH . '/vendor/autoload.php';
require_once BASE_PATH . '/utils/Database.php';
require_once BASE_PATH . '/utils/Config.php';
require_once BASE_PATH . '/utils/Common.php';
require_once BASE_PATH . '/utils/BackupSQL.php';

date_default_timezone_set('Asia/Ho_Chi_Minh');

try {
    Common::createDir(BACKUP_PATH);
    Common::createDir(LOG_PATH);

    Config::load(BASE_PATH . '/config.ini');

    $backupSQL = new BackupSQL();
    $backupSQL->backup();

    echo "[" . date('Y-m-d H:i:s') . "] Backup completed successfully." . PHP_EOL;
} catch (\Exception $e) {
    echo "[" . date('Y-m-d H:i:s') . "] An error occurred: " . $e->getMessage() . PHP_EOL;
    exit(1);
}
