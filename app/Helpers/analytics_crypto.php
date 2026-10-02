<?php
/**
 * Analytics Secret Encryption and Google OAuth Client Helpers
 */

function analyticsBase64UrlEncode(string $value): string
{
    return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
}

function analyticsEncryptSecret(string $plainText): string
{
    if ($plainText === '' || !function_exists('openssl_encrypt')) {
        return '';
    }

    $key = hash('sha256', SECURE_AUTH_KEY, true);
    $iv = random_bytes(12);
    $tag = '';
    $cipherText = openssl_encrypt($plainText, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag);
    if ($cipherText === false) {
        return '';
    }

    return 'v1:' . base64_encode($iv . $tag . $cipherText);
}

function analyticsDecryptSecret(string $encrypted): string
{
    if ($encrypted === '') {
        return '';
    }

    if (str_starts_with(ltrim($encrypted), '{')) {
        return $encrypted;
    }

    if (!str_starts_with($encrypted, 'v1:') || !function_exists('openssl_decrypt')) {
        return '';
    }

    $payload = base64_decode(substr($encrypted, 3), true);
    if ($payload === false || strlen($payload) < 29) {
        return '';
    }

    $iv = substr($payload, 0, 12);
    $tag = substr($payload, 12, 16);
    $cipherText = substr($payload, 28);
    $key = hash('sha256', SECURE_AUTH_KEY, true);
    $plainText = openssl_decrypt($cipherText, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag);

    return $plainText === false ? '' : $plainText;
}

function analyticsGetCredentials(): ?array
{
    $json = analyticsDecryptSecret((string)getSetting('google_service_account_enc', ''));
    if ($json === '') {
        return null;
    }

    $credentials = json_decode($json, true);
    if (!is_array($credentials)
        || empty($credentials['client_email'])
        || empty($credentials['private_key'])
        || ($credentials['token_uri'] ?? '') !== 'https://oauth2.googleapis.com/token') {
        return null;
    }

    return $credentials;
}

function analyticsHttpRequest(string $method, string $url, array $headers = [], ?string $body = null): array
{
    if (function_exists('curl_init')) {
        $handle = curl_init($url);
        curl_setopt_array($handle, [
            CURLOPT_CUSTOMREQUEST => strtoupper($method),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 8,
            CURLOPT_TIMEOUT => 25,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_FOLLOWLOCATION => false,
        ]);
        if ($body !== null) {
            curl_setopt($handle, CURLOPT_POSTFIELDS, $body);
        }
        $responseBody = curl_exec($handle);
        $status = (int)curl_getinfo($handle, CURLINFO_HTTP_CODE);
        $error = curl_error($handle);
        curl_close($handle);

        return [
            'ok' => $responseBody !== false && $status >= 200 && $status < 300,
            'status' => $status,
            'body' => $responseBody === false ? '' : $responseBody,
            'error' => $error,
        ];
    }

    $options = [
        'http' => [
            'method' => strtoupper($method),
            'header' => implode("\r\n", $headers),
            'content' => $body ?? '',
            'timeout' => 25,
            'ignore_errors' => true,
        ],
    ];
    $responseBody = @file_get_contents($url, false, stream_context_create($options));
    $status = 0;
    foreach ($http_response_header ?? [] as $header) {
        if (preg_match('/^HTTP\/\S+\s+(\d{3})/', $header, $matches)) {
            $status = (int)$matches[1];
        }
    }

    return [
        'ok' => $responseBody !== false && $status >= 200 && $status < 300,
        'status' => $status,
        'body' => $responseBody === false ? '' : $responseBody,
        'error' => $responseBody === false ? 'Không thể kết nối đến Google APIs.' : '',
    ];
}

function analyticsGoogleAccessToken(array $credentials): array
{
    static $cachedToken = null;
    if ($cachedToken !== null) {
        return ['ok' => true, 'token' => $cachedToken];
    }

    if (!function_exists('openssl_sign')) {
        return ['ok' => false, 'error' => 'Máy chủ chưa bật OpenSSL để ký Google OAuth token.'];
    }

    $now = time();
    $header = ['alg' => 'RS256', 'typ' => 'JWT'];
    if (!empty($credentials['private_key_id'])) {
        $header['kid'] = $credentials['private_key_id'];
    }
    $claims = [
        'iss' => $credentials['client_email'],
        'scope' => implode(' ', [
            'https://www.googleapis.com/auth/analytics.readonly',
            'https://www.googleapis.com/auth/webmasters.readonly',
        ]),
        'aud' => $credentials['token_uri'] ?: 'https://oauth2.googleapis.com/token',
        'iat' => $now - 30,
        'exp' => $now + 3500,
    ];

    $unsigned = analyticsBase64UrlEncode(json_encode($header, JSON_UNESCAPED_SLASHES))
        . '.' . analyticsBase64UrlEncode(json_encode($claims, JSON_UNESCAPED_SLASHES));
    $signature = '';
    $privateKey = openssl_pkey_get_private($credentials['private_key']);
    if ($privateKey === false || !openssl_sign($unsigned, $signature, $privateKey, OPENSSL_ALGO_SHA256)) {
        return ['ok' => false, 'error' => 'Private key của Service Account không hợp lệ.'];
    }

    $jwt = $unsigned . '.' . analyticsBase64UrlEncode($signature);
    $response = analyticsHttpRequest(
        'POST',
        $credentials['token_uri'],
        ['Content-Type: application/x-www-form-urlencoded'],
        http_build_query([
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt,
        ])
    );
    $decoded = json_decode($response['body'], true);
    if (!$response['ok'] || empty($decoded['access_token'])) {
        $message = $decoded['error_description'] ?? $decoded['error'] ?? $response['error'] ?? 'Không lấy được Google access token.';
        return ['ok' => false, 'error' => is_string($message) ? $message : 'Google OAuth từ chối yêu cầu.'];
    }

    $cachedToken = $decoded['access_token'];
    return ['ok' => true, 'token' => $cachedToken];
}
