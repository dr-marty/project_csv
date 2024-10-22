<?php

return [
    'paths' => ['api/*'],
    'allowed_methods' => ['*'],  // 全てのHTTPメソッドを許可
    'allowed_origins' => ['*'],  // 全てのオリジンを許可
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
];