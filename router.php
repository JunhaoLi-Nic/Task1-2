<?php
// router.php
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); // 只取 path

// Serve API requests from backend/api/
if (preg_match('#^/api/(.*)$#', $uri, $matches)) {
    $apiFile = __DIR__ . '/backend/api/' . $matches[1];
    error_log('Looking for API file: ' . $apiFile); // <--- 加这一行
    if (file_exists($apiFile)) {
        require $apiFile;
        return true;
    } else {
        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'API endpoint not found.']);
        return true;
    }
}

// Serve static files from frontend/
$file = __DIR__ . '/frontend' . $uri;
if ($uri === '/' || !file_exists($file)) {
    require __DIR__ . '/frontend/index.html';
} else {
    return false; // Let the server handle the static file
} 