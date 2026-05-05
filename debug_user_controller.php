<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
Illuminate\Support\Facades\Facade::setFacadeApplication($app);
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
Illuminate\Support\Facades\Auth::loginUsingId(1);
$request = Illuminate\Http\Request::create('/pos/users', 'GET', ['q' => '']);
$controller = new App\Http\Controllers\UserController();
$response = $controller->index($request);
$content = $response->getContent();
echo "STATUS:" . $response->getStatusCode() . "\n";
echo "LENGTH:" . strlen($content) . "\n";
echo substr($content, 0, 1000) . "\n";
