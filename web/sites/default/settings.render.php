<?php
$app_root = dirname(dirname(__DIR__));
$site_path = 'sites/default';
$default_settings = $app_root . '/' . $site_path . '/default.settings.php';
if (is_readable($default_settings)) {
  require $default_settings;
}

$database_url = getenv('DATABASE_URL');
$database = $database_url ? parse_url($database_url) : [];
$databases['default']['default'] = [
  'database' => $database['path'] ? ltrim($database['path'], '/') : (getenv('DB_DATABASE') ?: getenv('POSTGRES_DB') ?: 'drupal'),
  'username' => $database['user'] ?? (getenv('DB_USERNAME') ?: getenv('POSTGRES_USER') ?: 'drupal'),
  'password' => $database['pass'] ?? (getenv('DB_PASSWORD') ?: getenv('POSTGRES_PASSWORD') ?: ''),
  'host' => $database['host'] ?? (getenv('DB_HOST') ?: getenv('POSTGRES_HOST') ?: '127.0.0.1'),
  'port' => $database['port'] ?? (getenv('DB_PORT') ?: getenv('POSTGRES_PORT') ?: '5432'),
  'namespace' => 'Drupal\\Core\\Database\\Driver\\pgsql',
  'driver' => getenv('DB_DRIVER') ?: 'pgsql',
  'prefix' => '',
];
$settings['hash_salt'] = getenv('DRUPAL_HASH_SALT') ?: hash('sha256', getenv('RENDER_SERVICE_ID') ?: 'campusconnect-render');
$settings['trusted_host_patterns'] = ['^localhost$', '^127\\.0\\.0\\.1$', '^drupal-campusconnect-cms\\.onrender\\.com$'];
$settings['config_sync_directory'] = '../config/sync';
$settings['file_public_path'] = 'sites/default/files';
$settings['file_private_path'] = '/tmp/private';
