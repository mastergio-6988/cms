<?php
$databases['default']['default'] = [
  'database' => getenv('DB_DATABASE') ?: getenv('POSTGRES_DB') ?: 'drupal',
  'username' => getenv('DB_USERNAME') ?: getenv('POSTGRES_USER') ?: 'drupal',
  'password' => getenv('DB_PASSWORD') ?: getenv('POSTGRES_PASSWORD') ?: '',
  'host' => getenv('DB_HOST') ?: getenv('POSTGRES_HOST') ?: '127.0.0.1',
  'port' => getenv('DB_PORT') ?: getenv('POSTGRES_PORT') ?: '5432',
  'namespace' => 'Drupal\\Core\\Database\\Driver\\pgsql',
  'driver' => getenv('DB_DRIVER') ?: 'pgsql',
  'prefix' => '',
];
$settings['hash_salt'] = getenv('DRUPAL_HASH_SALT') ?: hash('sha256', getenv('RENDER_SERVICE_ID') ?: 'campusconnect-render');
$settings['trusted_host_patterns'] = ['^localhost$', '^127\\.0\\.0\\.1$', '^drupal-campusconnect-cms\\.onrender\\.com$'];
$settings['config_sync_directory'] = '../config/sync';
$settings['file_public_path'] = 'sites/default/files';
$settings['file_private_path'] = '/tmp/private';
