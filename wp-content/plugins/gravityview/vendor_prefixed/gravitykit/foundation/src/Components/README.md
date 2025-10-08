# Reusable Components

A collection of reusable components that extend Foundation's functionality.

## Table of Contents

- [SecureDownload](#securedownload)
  - [Features](#features)
  - [How It Works](#how-it-works)
  - [Basic Usage](#basic-usage)
  - [Advanced Usage](#advanced-usage)
  - [Parameters](#parameters)
  - [User Access Control](#user-access-control)
  - [Hooks](#hooks)
  - [Detailed Hook Documentation](#detailed-hook-documentation)
- [NewsletterSignup](#newslettersignup)

---

## `SecureDownload`

The `SecureDownload` component provides a secure way to download files from WordPress without revealing the actual file location. It supports large files through chunked streaming, authentication checks, and usage limits.

### Features

- **Security features**:
  - Encrypted tokens prevent URL tampering
  - Expiring links
  - Capability-based permissions
  - IP address restrictions
  - User-based access control

- **Usage controls**:
  - Download limit enforcement
  - Time-based expiration

- **Performance**:
  - Streaming support for large files
  - Chunked reading to prevent memory exhaustion
  - Range request support for resumable downloads
  - HEAD request support for download managers and CDNs

### How It Works

1. **Token Generation**:
   - File path and options are encrypted into a secure token
   - Token contains all necessary information (file path, restrictions, expiration)
   - No database storage required for token creation
   - Returns a URL with the encrypted token and a short ID for reference

2. **Download Process**:
   - Token is decrypted and validated when accessed
   - Various security checks are performed (expiration, user, IP, etc.)
   - File is read and streamed in chunks to the browser
   - Download history is recorded if enabled

### Basic Usage

```php
$secure_download = \GravityKit\Foundation\Core::secure_download();
```

#### Generate a download link (with smart defaults)
```php
// Default behavior: expires in 1 hour, auto-detects cache duration based on file type
$result = $secure_download->generate_download_url('/path/to/file.pdf');

// Returns:
// [
//     'url' => 'https://site.com/wp-admin/admin-ajax.php?action=gk_download&token=...',
//     'id'  => 'abc123def456' // Short identifier
// ]
```

#### Generate a single-use download link
```php
$result = $secure_download->generate_download_url('/path/to/file.pdf', [
    'limit'      => 1,     // Single use
    'expires_in' => 86400, // 24 hours
]);
```

#### Generate a link restricted to specific users
```php
// Only users with IDs 10, 20, or 30 can download.
$result = $secure_download->generate_download_url('/path/to/file.pdf', [
    'limit' => 1,
    'users' => [10, 20, 30],
]);

// Restrict to current user only.
$result = $secure_download->generate_download_url('/path/to/file.pdf', [
    'limit' => 1,
    'users' => [get_current_user_id()],
]);
```

#### Generate a link that allows exactly 5 downloads
```php
$result = $secure_download->generate_download_url('/path/to/file.pdf', [
    'limit'      => 5,
    'expires_in' => 86400, // 24 hours.
]);
```

#### Generate a link with no expiration
```php
$result = $secure_download->generate_download_url('/path/to/file.pdf', [
    'expires_in' => 0, // No expiration.
]);
```

#### Generate a link restricted to specific IPs
```php
$result = $secure_download->generate_download_url('/path/to/file.pdf', [
    'ips'        => ['192.168.1.1', '10.0.0.1'], // Multiple IPs.
    'expires_in' => 3600,
]);

// Or single IP.
$result = $secure_download->generate_download_url('/path/to/file.pdf', [
    'ips'        => '192.168.1.1',
    'expires_in' => 3600,
]);
```

#### Generate a public/anonymous download link
```php
// No user restrictions - anyone with the link can download
$result = $secure_download->generate_download_url('/path/to/file.pdf', [
    'expires_in' => 86400, // 24 hours
]);
// Note: Files are automatically cached based on their type (e.g., PDFs cache for 1 month)
```

#### Generate a link with custom output filename
```php
$result = $secure_download->generate_download_url('/path/to/internal-file-v2.3.pdf', [
    'filename'   => 'user-guide.pdf', // Custom filename for download.
    'expires_in' => 86400,
]);
```

#### Generate a private download link (no caching)
```php
$result = $secure_download->generate_download_url('/path/to/sensitive-data.xlsx', [
    'expires_in'     => 3600,
    'cache_duration' => 0,         // No caching (private)
]);
```

#### Generate a link with custom cache duration
```php
// Override default cache duration for specific needs
$result = $secure_download->generate_download_url('/path/to/report.pdf', [
    'expires_in'     => 86400,
    'cache_duration' => 7200,      // Cache for 2 hours instead of default 1 month
]);
```

### Advanced Usage

#### Simple tracking with the `track` parameter
```php
// Track everything (IP, user agent, and save to history).
$result = $secure_download->generate_download_url('/path/to/file.pdf', [
    'expires_in' => 86400,
    'track'      => true,  // Tracks ip, user_agent, and enables history.
]);

// Track only specific data.
$result = $secure_download->generate_download_url('/path/to/file.pdf', [
    'expires_in' => 86400,
    'track'      => ['ip', 'history'],  // Only track IP and save to history.
]);

// Track with custom metadata.
$result = $secure_download->generate_download_url('/path/to/file.pdf', [
    'expires_in' => 86400,
    'track'      => ['ip', 'user_agent', 'referrer', 'history'],
    'meta'       => [
        'campaign' => 'email_newsletter',
        'version'  => '1.2.3',
    ],
]);

// Retrieve tracked downloads.
$history = $secure_download->get_download_history($result['id']);

// Retrieve all download history across all tokens.
$all_history = $secure_download->get_download_history(null, [
    'user_id' => get_current_user_id(), // Optional: filter by user.
    'after'   => '2024-01-01',          // Optional: filter by date.
    'limit'   => 100,                   // Optional: limit results.
]);
```

#### Fine-grained control via filters
```php
// For more complex tracking logic, use filters.
add_filter('gk/foundation/secure-download/history-record', function($info, $token_data) {
    // Custom logic based on file type.
    if (strpos($info['file'], '.log') !== false) {
        $info['support_ticket'] = $_GET['ticket'] ?? 'none';
    }
    return $info;
}, 10, 2);
```

#### Custom IP detection
```php
add_filter('gk/foundation/secure-download/visitor-ip', function($ip) {
    // Custom IP detection logic.
    if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
        return $_SERVER['HTTP_CF_CONNECTING_IP']; // Cloudflare.
    }

    return $ip;
});
```

### Parameters

The `generate_download_url()` method accepts the following parameters:

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `$file_path` | string | required | The absolute or relative path to the file |
| `$args` | array | `[]` | Optional arguments array with the following keys: |

#### Arguments array (`$args`)

| Key | Type | Default | Description |
|-----|------|---------|-------------|
| `expires_in` | int | 3600 | Time in seconds until the link expires (1 hour default). Set to 0 for no expiration |
| `limit` | int | 0 | Maximum number of downloads allowed (0 = unlimited, 1 = single use) |
| `capabilities` | array | `[]` | Array of capabilities required to download |
| `ips` | string\|array | `[]` | Single IP or array of IPs allowed to download |
| `users` | int\|array | `[]` | Single user ID or array of user IDs allowed |
| `track` | bool\|array | `false` | Track downloads. `true` = track all (ip, user_agent, history), array = track specific (e.g. `['ip', 'history']`) |
| `meta` | array | `[]` | Additional metadata to include in the token |
| `filename` | string | `''` | Custom filename to use when downloading (empty string uses original filename) |
| `cache_duration` | int | (not set) | Cache duration in seconds. `0` = no cache (private), `> 0` = specific duration. If not set, auto-detects based on file type |

### User Access Control

The `users` parameter is the primary way to restrict downloads by user:

- **For user-specific downloads**: Set `users` to an array of user IDs (e.g., `[1, 2, 3]`) or a single user ID
- **For current user only**: Use `'users' => [get_current_user_id()]`
- **For public/anonymous downloads**: Omit the `users` parameter or set it to an empty array `[]`

When tracking is enabled (via the `track` parameter), the system automatically records which user actually downloaded the file, based on the current user ID at the time of download. This download history is separate from the access control provided by `users`.

### Hooks

#### Filters

- `gk/foundation/secure-download/token-data` - Modify token data before encryption
- `gk/foundation/secure-download/validate-token` - Modify token validation result
- `gk/foundation/secure-download/history-record` - Modify history data for the download
- `gk/foundation/secure-download/save-history` - Control whether to store download history
- `gk/foundation/secure-download/visitor-ip` - Customize IP address detection
- `gk/foundation/secure-download/error-response` - Customize error handling and messages
- `gk/foundation/secure-download/history-length` - Control the maximum number of history entries per token
- `gk/foundation/secure-download/headers` - Modify HTTP headers sent during file download

#### Actions

- `gk/foundation/secure-download/before-download` - Fired before a file download starts
- `gk/foundation/secure-download/after-download` - Fired after a file download completes
- `gk/foundation/secure-download/record-download` - Fired when a download should be recorded
- `gk/foundation/secure-download/error` - Fired when a download error occurs

### Detailed Hook Documentation

#### Filters

##### `gk/foundation/secure-download/token-data`
Modify token data before encryption.

```php
add_filter('gk/foundation/secure-download/token-data', function($token_data, $file_path, $args) {
    // Add custom data to the token.
    $token_data['custom_field'] = 'custom_value';

    return $token_data;
}, 10, 3);
```

**Parameters:**
- `$token_data` (array) - The token data array.
- `$file_path` (string) - The file path being secured.
- `$args` (array) - Original arguments passed to `generate_download_url()`.

##### `gk/foundation/secure-download/validate-token`
Override the token validation result. This filter runs after all validation checks and can turn a failed validation into a successful one.

```php
// Allow expired tokens for administrators.
add_filter('gk/foundation/secure-download/validate-token', function($validation_result, $token_data, $token, $failure_code, $exception) {
    // If validation failed due to expiration and user is admin, allow it.
    if ($failure_code === 'expired' && current_user_can('manage_options')) {
        return $token_data; // Override the failure
    }

    return $validation_result;
}, 10, 5);

// Log validation failures.
add_filter('gk/foundation/secure-download/validate-token', function($validation_result, $token_data, $token, $failure_code, $exception) {
    if ($validation_result === false && $failure_code) {
        error_log("Download token validation failed: $failure_code");
    }

    return $validation_result;
}, 10, 5);
```

**Parameters:**
- `$validation_result` (array|false) - The validation result (token data array if valid, false if invalid).
- `$token_data` (array|null) - Raw token data array (may be invalid or partial).
- `$token` (string) - The original encrypted token.
- `$failure_code` (string|null) - Machine-readable failure code (e.g., 'expired', 'user_not_allowed', 'ip_not_allowed', 'missing_capability', 'file_unreadable', 'download_limit_exceeded').
- `$exception` (Exception|null) - The exception instance if validation failed.

##### `gk/foundation/secure-download/history-record`
Add tracking data to download records (privacy-conscious - disabled by default).

```php
add_filter('gk/foundation/secure-download/history-record', function($download_info, $token_data) {
    // Only track if explicitly requested.
    if (!empty($token_data['meta']['enable_tracking'])) {
        $download_info['ip_address'] = $_SERVER['REMOTE_ADDR'];
        $download_info['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
        $download_info['referrer'] = $_SERVER['HTTP_REFERER'] ?? '';
    }

    return $download_info;
}, 10, 2);
```

**Parameters:**
- `$download_info` (array) - Basic download information.
- `$token_data` (array) - Complete token data.

##### `gk/foundation/secure-download/save-history`
Control whether to store download history.

```php
// Enable history for specific files.
add_filter('gk/foundation/secure-download/save-history', function($should_record, $download_info) {
    if (strpos($download_info['file'], '/important/') !== false) {
        return true;
    }

    return $should_record;
}, 10, 2);
```

**Parameters:**
- `$should_record` (bool) - Whether to record (default: `false`).
- `$download_info` (array) - Download information.

##### `gk/foundation/secure-download/visitor-ip`
Customize IP address detection for different server configurations.

```php
add_filter('gk/foundation/secure-download/visitor-ip', function($ip) {
    // Cloudflare support.
    if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
        return $_SERVER['HTTP_CF_CONNECTING_IP'];
    }

    // Load balancer support.
    if (!empty($_SERVER['HTTP_X_REAL_IP'])) {
        return $_SERVER['HTTP_X_REAL_IP'];
    }
    return $ip;
});
```

**Parameters:**
- `$ip` (string) - Detected IP address.


##### `gk/foundation/secure-download/error-response`
Filter the error response when a download fails. Return `null` to indicate the error has been handled.

```php
// Custom error message.
add_filter('gk/foundation/secure-download/error-response', function($response, $exception, $token) {
    if ($exception->getCode() === 403) {
        $response['message'] = 'This download link has expired or is invalid.';
    }
    return $response;
}, 10, 3);

// Redirect to a custom page instead of showing error.
add_filter('gk/foundation/secure-download/error-response', function($response, $exception, $token) {
    if ($exception->getCode() === 404) {
        wp_redirect(home_url('/download-not-found/'));
        return null; // Indicate error has been handled.
    }
    return $response;
}, 10, 3);

// Log errors and show generic message.
add_filter('gk/foundation/secure-download/error-response', function($response, $exception, $token) {
    error_log('Download failed: ' . $exception->getMessage());
    $response['message'] = 'Download temporarily unavailable. Please try again later.';
    return $response;
}, 10, 3);
```

**Parameters:**
- `$response` (array|null) - Error response data with 'code' and 'message' keys, or null if handled
- `$exception` (Exception) - The exception that was thrown
- `$token` (string) - The download token that was provided

##### `gk/foundation/secure-download/history-length`
Control the maximum number of history entries kept per download token. By default, each token stores up to 100 download history entries.

```php
// Increase history limit for important files.
add_filter('gk/foundation/secure-download/history-length', function($max_history, $token_id, $token_data) {
    if (strpos($token_data['file'], '/reports/') !== false) {
        return 500; // Keep more history for report downloads.
    }
    return $max_history;
}, 10, 3);

// Disable history storage completely for specific tokens.
add_filter('gk/foundation/secure-download/history-length', function($max_history, $token_id, $token_data) {
    if (!empty($token_data['meta']['no_history'])) {
        return 0; // Don't keep any history.
    }

    return $max_history;
}, 10, 3);
```

**Parameters:**
- `$max_history` (int) - Maximum number of history entries to keep. Default 100.
- `$token_id` (string) - The token ID for the download.
- `$token_data` (array) - The full token data array.

##### `gk/foundation/secure-download/headers`
Modify HTTP headers sent during file download. This filter allows you to customize response headers including cache control, security headers, and content disposition.

```php
// Add custom headers or modify existing ones.
add_filter('gk/foundation/secure-download/headers', function($headers, $context) {
    // Extract context information
    $file_path = $context['file_path'];
    $file_name = $context['file_name'];
    $mime_type = $context['mime_type'];
    $file_size = $context['file_size'];
    $token_data = $context['token_data'];
    $partial_content = $context['partial_content'];
    $range_start = $context['range_start'];
    $range_end = $context['range_end'];
    
    // Add custom header.
    $headers['X-Custom-Header'] = 'Custom Value';
    
    // Force download for PDFs (override Content-Disposition).
    if (strpos($file_path, '.pdf') !== false) {
        $headers['Content-Disposition'] = 'inline; filename="' . basename($file_path) . '"';
    }
    
    // Custom cache control for specific files.
    if (!empty($token_data['meta']['cache_public'])) {
        $headers['Cache-Control'] = 'public, max-age=2592000'; // 30 days
        unset($headers['Pragma']);
    }
    
    // Add CORS headers for specific domains.
    if (!empty($token_data['meta']['allow_cors'])) {
        $headers['Access-Control-Allow-Origin'] = '*';
    }
    
    return $headers;
}, 10, 2);

// Example: CDN-friendly headers for public files.
add_filter('gk/foundation/secure-download/headers', function($headers, $context) {
    $token_data = $context['token_data'];
    
    // Check if file should be publicly cacheable.
    if (!empty($token_data['meta']['cdn_enabled'])) {
        $headers['Cache-Control'] = 'public, max-age=31536000, immutable'; // 1 year
        $headers['CDN-Cache-Control'] = 'max-age=31536000';
        unset($headers['Pragma']);
        unset($headers['Expires']);
    }
    
    return $headers;
}, 10, 2);

// Example: Add security headers for sensitive downloads.
add_filter('gk/foundation/secure-download/headers', function($headers, $context) {
    $token_data = $context['token_data'];
    
    if (!empty($token_data['meta']['sensitive'])) {
        $headers['X-Frame-Options'] = 'DENY';
        $headers['X-Download-Options'] = 'noopen';
        $headers['Referrer-Policy'] = 'no-referrer';
    }
    
    return $headers;
}, 10, 2);
```

**Parameters:**
- `$headers` (array) - Array of headers to be sent. Default includes Content-Type, Content-Disposition, Content-Length, Accept-Ranges, Cache-Control, and security headers.
- `$context` (array) - Context array containing all relevant information:
  - `file_path` (string) - Full path to the file being downloaded
  - `file_name` (string) - Name of the file being downloaded
  - `mime_type` (string) - MIME type of the file
  - `file_size` (int) - Total size of the file in bytes
  - `range_start` (int) - Start byte position for range requests
  - `range_end` (int) - End byte position for range requests
  - `token_data` (array) - The decrypted token data containing all download parameters
  - `partial_content` (bool) - Whether this is a partial content response (range request)

**Default Headers:**
- `Content-Type` - MIME type of the file
- `Content-Disposition` - Attachment with UTF-8 filename support
- `Content-Length` - Size of content being sent
- `Accept-Ranges` - Indicates support for range requests
- `X-Content-Type-Options` - Set to 'nosniff' for security
- `Content-Security-Policy` - Set to "default-src 'none';" for security
- `Cache-Control` - Auto-detected based on file type (e.g., images: 3 months, PDFs: 1 month, HTML: 1 week)
- `Content-Range` - Only present for partial content responses

#### Actions

##### `gk/foundation/secure-download/token-generated`
Fired after a download URL is generated.

```php
add_action('gk/foundation/secure-download/token-generated', function($result, $file_path, $args) {
    // Log token generation.
    error_log(sprintf('Download token generated for %s with ID %s.', $file_path, $result['id']));
}, 10, 3);
```

**Parameters:**
- `$result` (array) - Array with 'url' and 'id'.
- `$file_path` (string) - The file path.
- `$args` (array) - Arguments used for generation.

##### `gk/foundation/secure-download/before-download`
Fired before a file download starts.

```php
add_action('gk/foundation/secure-download/before-download', function($file_path, $user_id, $token_data) {
    // Custom logging or preparation.
    do_action('my_plugin_download_starting', $file_path, $user_id);
}, 10, 3);
```

**Parameters:**
- `$file_path` (string) - Path to file being downloaded.
- `$user_id` (int) - User ID downloading the file.
- `$token_data` (array) - Complete token data.

##### `gk/foundation/secure-download/after-download`
Fired after a file download completes successfully.

```php
add_action('gk/foundation/secure-download/after-download', function($file_path, $user_id, $token_data) {
    // Cleanup or notifications.
    if (!empty($token_data['meta']['notify_admin'])) {
        wp_mail(get_option('admin_email'), 'File Downloaded', "File downloaded: $file_path");
    }
}, 10, 3);
```

**Parameters:**
- `$file_path` (string) - Path to file that was downloaded.
- `$user_id` (int) - User ID that downloaded the file.
- `$token_data` (array) - Complete token data.

##### `gk/foundation/secure-download/record-download`
Fired when a download should be recorded (regardless of whether history is enabled).

```php
add_action('gk/foundation/secure-download/record-download', function($download_info, $token_data) {
    // Custom download tracking.
    do_action('my_analytics_track_download', [
        'file' => basename($download_info['file']),
        'user' => $download_info['user_id'],
        'time' => $download_info['timestamp'],
    ]);
}, 10, 2);
```

**Parameters:**
- `$download_info` (array) - Download information (may be modified with a filter).
- `$token_data` (array) - Complete token data.

##### `gk/foundation/secure-download/error`
Fired when a download error occurs.

```php
add_action('gk/foundation/secure-download/error', function($exception, $token) {
    // Log to external service.
    if (function_exists('my_error_logger')) {
        my_error_logger('download_failed', [
            'error' => $exception->getMessage(),
            'code' => $exception->getCode(),
            'token_preview' => substr($token, 0, 10) . '...',
        ]);
    }
    
    // Send notification for critical errors.
    if ($exception->getCode() === 500) {
        wp_mail(
            get_option('admin_email'),
            'Critical Download Error',
            'Download system error: ' . $exception->getMessage()
        );
    }
}, 10, 2);
```

**Parameters:**
- `$exception` (Exception) - The exception that was thrown.
- `$token` (string) - The download token that was provided.

### Security Considerations

1. **File Path Security**: The component checks for directory traversal attempts (`..` in paths)
2. **Token Security**: 
   - Tokens are encrypted using the Foundation's Encryption class
   - The entire token payload is encrypted, preventing tampering
3. **Authentication**: Access control via `users` parameter and capability checks
4. **Capability Checks**: Support for WordPress capability-based permissions
5. **IP Restrictions**: Limit downloads to specific IP addresses when needed

---

## NewsletterSignup

WIP.
