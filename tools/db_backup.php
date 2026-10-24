<?php
// CLI only. Usage: php tools/db_backup.php [--keep=14]
// Run daily from Windows Task Scheduler / cron. Env: DB_BACKUP_DIR (default: outside the web root), MYSQLDUMP_PATH.
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}
require __DIR__ . '/../config.php';
$host = env('DB_HOST', 'localhost'); $port = env('DB_PORT', '3306'); $name = env('DB_NAME', 'scheduler_app'); $user = env('DB_USER', 'root'); $pass = env('DB_PASS', '');
$keep = 14;
foreach ($argv as $a) {
    if (preg_match('/^--keep=(\d+)$/', $a, $m)) {
        $keep = (int) $m[1];
    }
}
$dir = getenv('DB_BACKUP_DIR') ?: dirname(__DIR__, 3) . '/_db_auto_backups/' . $name;
if (!is_dir($dir) && !mkdir($dir, 0750, true)) {
    fwrite(STDERR, "Cannot create $dir\n");
    exit(1);
}
$file = $dir . '/' . $name . '_' . date('Y-m-d_His') . '.sql';
$cmd = [getenv('MYSQLDUMP_PATH') ?: 'mysqldump', '-h', $host, '-P', (string) $port, '-u', $user,
    '--single-transaction', '--routines', '--triggers', '--result-file=' . $file, $name];
// Password goes through the environment, not the command line.
$p = proc_open($cmd, [1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes, null, ['MYSQL_PWD' => (string) $pass] + getenv());
$err = stream_get_contents($pipes[2]);
$code = proc_close($p);
$tail = is_file($file) ? substr((string) file_get_contents($file, false, null, max(0, filesize($file) - 200)), -200) : '';
if ($code !== 0 || strpos($tail, 'Dump completed') === false) {
    @unlink($file);
    fwrite(STDERR, "Backup failed: $err\n");
    exit(1);
}
$old = array_slice(array_reverse(glob($dir . '/' . $name . '_*.sql')), $keep);
array_map('unlink', $old);
echo "Backup written: $file\n";
