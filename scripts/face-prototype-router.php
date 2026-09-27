<?php

if (PHP_SAPI !== 'cli-server') {
    http_response_code(404);
    exit;
}

$_SERVER['FACE_PROTOTYPE_STARTED_NS'] = hrtime(true);
$root = dirname(__DIR__);
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$assets = [
    '/prototype-assets/app.js' => [$root.'/storage/app/private/face-prototype-assets/app.js', 'text/javascript'],
    '/prototype-assets/app.css' => [$root.'/storage/app/private/face-prototype-assets/app.css', 'text/css'],
    '/prototype-assets/face-api.js' => [$root.'/node_modules/@vladmandic/face-api/dist/face-api.esm.js', 'text/javascript'],
];
foreach (['tiny_face_detector_model-weights_manifest.json', 'tiny_face_detector_model.bin',
    'face_landmark_68_tiny_model-weights_manifest.json', 'face_landmark_68_tiny_model.bin',
    'face_recognition_model-weights_manifest.json', 'face_recognition_model.bin',
] as $filename) {
    $assets['/prototype-assets/models/'.$filename] = [$root.'/node_modules/@vladmandic/face-api/model/'.$filename,
        str_ends_with($filename, '.json') ? 'application/json' : 'application/octet-stream'];
}

if (isset($assets[$path])) {
    [$file, $type] = $assets[$path];
    if (! is_file($file)) {
        http_response_code(404);
        exit('Aset prototipe belum tersedia.');
    }
    header('Content-Type: '.$type);
    header('X-Content-Type-Options: nosniff');
    $isPageAsset = in_array($path, ['/prototype-assets/app.js', '/prototype-assets/app.css'], true);
    header('Cache-Control: '.($isPageAsset ? 'no-store, private' : 'private, max-age=3600'));
    readfile($file);
    exit;
}
if (! in_array($path, ['/uji/absensi-wajah', '/uji/absensi-wajah/access', '/uji/absensi-wajah/match'], true)) {
    http_response_code(404);
    exit('Jalur ini tidak tersedia pada server uji.');
}

require $root.'/vendor/autoload.php';
$app = require $root.'/bootstrap/face-prototype.php';
$app->handleRequest(Illuminate\Http\Request::capture());
