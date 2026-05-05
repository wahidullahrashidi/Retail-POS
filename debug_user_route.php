<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::create('/pos/users', 'GET', ['q' => '']);
$response = $kernel->handle($request);
echo "STATUS:" . $response->getStatusCode() . "\n";
echo "CONTENT:" . substr($response->getContent(), 0, 1000) . "\n";
$kernel->terminate($request, $response);
