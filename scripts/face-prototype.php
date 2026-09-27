<?php

/** Run only the isolated prototype, never artisan test against the ordinary configuration. */
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require dirname(__DIR__).'/vendor/autoload.php';

$command = $argv[1] ?? 'verify';
if (! in_array($command, ['verify', 'serve', 'test'], true)) {
    fwrite(STDERR, "Usage: php scripts/face-prototype.php [verify|serve|test]\n");
    exit(1);
}

if (! in_array('sqlite', PDO::getAvailableDrivers(), true)) {
    $arguments = [PHP_BINARY, '-d', 'extension=pdo_sqlite', __FILE__, $command];
    if (getenv('FACE_PROTOTYPE_SQLITE_RETRY')) {
        fwrite(STDERR, "Driver pdo_sqlite tidak tersedia. Prototipe dihentikan.\n");
        exit(1);
    }
    putenv('FACE_PROTOTYPE_SQLITE_RETRY=1');
    passthru(implode(' ', array_map('escapeshellarg', $arguments)), $status);
    exit($status);
}

$app = require dirname(__DIR__).'/bootstrap/face-prototype.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$database = $app['db']->connection();
if (! $app->environment('testing') || $database->getPdo()->getAttribute(PDO::ATTR_DRIVER_NAME) !== 'sqlite'
    || $database->getDatabaseName() !== ':memory:' || $database->select("SELECT name FROM sqlite_master WHERE type='table'") !== []) {
    fwrite(STDERR, "Database uji tidak lolos pemeriksaan. Dihentikan.\n");
    exit(1);
}
echo "Terverifikasi: testing / sqlite / :memory: / 0 tabel. Tidak ada migrasi.\n";

if ($command === 'verify') {
    exit(0);
}

if ($command === 'test') {
    $arguments = [PHP_BINARY, '-d', 'extension=pdo_sqlite', dirname(__DIR__).'/vendor/bin/pest',
        '--configuration', dirname(__DIR__).'/phpunit.face-prototype.xml',
        dirname(__DIR__).'/tests/Feature/FacePrototypeTest.php', '--compact', '--fail-on-warning', '--fail-on-risky'];
} else {
    $token = bin2hex(random_bytes(24));
    $port = filter_var(getenv('FACE_PROTOTYPE_PORT') ?: '8765', FILTER_VALIDATE_INT,
        ['options' => ['min_range' => 1024, 'max_range' => 65535]]);
    if ($port === false) {
        fwrite(STDERR, "Port uji tidak valid.\n");
        exit(1);
    }
    $listener = @stream_socket_client('tcp://127.0.0.1:'.$port, $errorNumber, $errorMessage, 1);
    if (is_resource($listener)) {
        fclose($listener);
        fwrite(STDERR, "Port {$port} masih dipakai server lain. Hentikan server uji lama dahulu. Kode baru tidak dibuat untuk digunakan.\n");
        exit(1);
    }
    $arguments = [PHP_BINARY, '-d', 'extension=pdo_sqlite'];
    $opcacheLibrary = rtrim((string) ini_get('extension_dir'), '/\\').DIRECTORY_SEPARATOR
        .(PHP_OS_FAMILY === 'Windows' ? 'php_opcache.dll' : 'opcache.so');
    if (! extension_loaded('Zend OPcache') && is_file($opcacheLibrary)) {
        array_push($arguments, '-d', 'zend_extension='.$opcacheLibrary);
    }
    array_push($arguments, '-d', 'opcache.enable=1', '-d', 'opcache.enable_cli=1',
        '-d', 'opcache.validate_timestamps=1', '-d', 'opcache.revalidate_freq=0',
        '-d', 'opcache.cache_id=face-prototype-'.$port,
        '-S', '127.0.0.1:'.$port, dirname(__DIR__).'/scripts/face-prototype-router.php');
    $server = new Symfony\Component\Process\Process($arguments, dirname(__DIR__), ['FACE_PROTOTYPE_TOKEN' => $token]);
    $server->setTimeout(null);
    $server->start(function (string $type, string $output): void {
        echo $output;
    });
    $context = stream_context_create(['http' => [
        'method' => 'POST', 'timeout' => 1, 'ignore_errors' => true,
        'header' => "Content-Type: application/json\r\nAccept: application/json\r\nAuthorization: Bearer {$token}\r\n",
        'content' => '{}',
    ]]);
    $authorized = false;
    for ($attempt = 0; $attempt < 30 && $server->isRunning(); $attempt++) {
        $response = @file_get_contents('http://127.0.0.1:'.$port.'/uji/absensi-wajah/access', false, $context);
        if ($response !== false && (json_decode($response, true)['authorized'] ?? false) === true) {
            $authorized = true;
            break;
        }
        usleep(100000);
    }
    if (! $authorized) {
        $server->stop();
        fwrite(STDERR, "Server tidak lolos pemeriksaan kode operator. Halaman uji belum siap.\n");
        exit(1);
    }
    echo "Server dan kode operator sudah terverifikasi.\n";
    echo "Buka tautan ini (kode terisi otomatis): http://localhost:{$port}/uji/absensi-wajah#code={$token}\n";
    echo "Kode operator sementara (salin ke halaman): {$token}\n";
    echo "Hanya loopback. Untuk Chrome Android, gunakan USB port forwarding {$port}. Ctrl+C untuk berhenti.\n";
    exit($server->wait());
}
passthru(implode(' ', array_map('escapeshellarg', $arguments)), $status);
exit($status);
