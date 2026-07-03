<?php
// Visitor tracking: logs visitor IP, location and user agent to visitor-log.txt
// Safe to include multiple times; the file will only run once per request.
if (defined('VISITOR_LOG_INCLUDED')) return;
define('VISITOR_LOG_INCLUDED', true);

// Don't run in CLI
if (php_sapi_name() === 'cli') return;

function rm_get_visitor_ip() {
    $keys = [
        'HTTP_CLIENT_IP',
        'HTTP_X_FORWARDED_FOR',
        'HTTP_X_FORWARDED',
        'HTTP_FORWARDED_FOR',
        'HTTP_FORWARDED',
        'REMOTE_ADDR'
    ];
    foreach ($keys as $key) {
        if (!empty($_SERVER[$key])) {
            $ip_list = explode(',', $_SERVER[$key]);
            $ip = trim($ip_list[0]);
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
        }
    }
    return $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
}

function rm_get_location_from_ip($ip) {
    // Skip private/local IPs
    if (in_array($ip, ['127.0.0.1', '::1']) || (filter_var($ip, FILTER_VALIDATE_IP) && filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE) === false)) {
        return 'Local/Private IP';
    }

    // Use ip-api.com for simple, free lookups (rate limited)
    $url = "http://ip-api.com/json/" . urlencode($ip) . "?fields=status,country,city,regionName,isp";
    $data = @file_get_contents($url);
    if ($data === false) return 'Location lookup failed';
    $json = json_decode($data, true);
    if ($json && isset($json['status']) && $json['status'] === 'success') {
        return trim((($json['country'] ?? '') . ' • ' . ($json['city'] ?? '') . ' • ' . ($json['regionName'] ?? '') . ' • ' . ($json['isp'] ?? '')) , " \t\n\r\0\x0B•");
    }
    return 'Unknown location';
}

// Avoid logging admin area by checking URI (common admin folder names)
$requestUri = $_SERVER['REQUEST_URI'] ?? '';
$lowerUri = strtolower($requestUri);
if (strpos($lowerUri, '/admin') === 0 || strpos($lowerUri, '/admin-') === 0) {
    return;
}

// Build log
$ip = rm_get_visitor_ip();
$location = rm_get_location_from_ip($ip);
$date = date('Y-m-d H:i:s');
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
$page = $requestUri;
$logLine = "[$date] IP: $ip | Location: $location | Page: $page | Agent: $userAgent" . PHP_EOL;

$file = __DIR__ . '/../visitor-log.txt';
@file_put_contents($file, $logLine, FILE_APPEND | LOCK_EX);

?>
