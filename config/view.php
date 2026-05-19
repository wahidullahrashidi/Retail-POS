<?php

$compiledPath = env('VIEW_COMPILED_PATH');

if (! $compiledPath && PHP_OS_FAMILY === 'Windows' && in_array(env('APP_ENV', 'production'), ['local', 'testing'], true)) {
    $compiledPath = storage_path('framework/views/runtime/' . PHP_SAPI . '-' . getmypid());
}

return [
    'paths' => [
        resource_path('views'),
    ],

    'compiled' => $compiledPath ?: realpath(storage_path('framework/views')) ?: storage_path('framework/views'),
];
