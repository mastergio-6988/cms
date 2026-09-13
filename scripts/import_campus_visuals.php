<?php

use Drupal\Core\File\FileSystemInterface;
use Drupal\media\Entity\Media;

$source_dir = DRUPAL_ROOT . '/sites/default/files/homepage-visuals';

$visuals = [
  'announcements.jpg' => [
    'name' => 'CampusConnect — Announcements',
    'alt' => 'Students and academic communication on a university campus',
  ],
  'campus-news.jpg' => [
    'name' => 'CampusConnect — Campus News',
    'alt' => 'University students collaborating on campus',
  ],
  'campus-events.jpg' => [
    'name' => 'CampusConnect — Events',
    'alt' => 'University campus event with students and community members',
  ],
  'departments.jpg' => [
    'name' => 'CampusConnect — Departments',
    'alt' => 'Modern university academic building',
  ],
  'faculty-staff.jpg' => [
    'name' => 'CampusConnect — Faculty and Staff',
    'alt' => 'University faculty and staff',
  ],
  'academic-communication.jpg' => [
    'name' => 'CampusConnect — Academic Communication',
    'alt' => 'Professor teaching students in a university lecture hall',
  ],
  'student-experience.jpg' => [
    'name' => 'CampusConnect — Student Experience',
    'alt' => 'University students collaborating together',
  ],
  'people-expertise.jpg' => [
    'name' => 'CampusConnect — People and Expertise',
    'alt' => 'Students and educators working together',
  ],
  'events.jpg' => [
    'name' => 'CampusConnect — Events and Calendar',
    'alt' => 'University event with students and speakers',
  ],
  'find-information.jpg' => [
    'name' => 'CampusConnect — Find Information',
    'alt' => 'Student studying with a laptop in an academic environment',
  ],
  'connected-campus.jpg' => [
    'name' => 'CampusConnect — Connected Campus',
    'alt' => 'Students using technology together on campus',
  ],
];

$file_system = \Drupal::service('file_system');
$file_repository = \Drupal::service('file.repository');
$media_storage = \Drupal::entityTypeManager()->getStorage('media');

$directory = 'public://homepage-visuals';

$file_system->prepareDirectory(
  $directory,
  FileSystemInterface::CREATE_DIRECTORY | FileSystemInterface::MODIFY_PERMISSIONS
);

foreach ($visuals as $filename => $definition) {
  $source = $source_dir . '/' . $filename;

  if (!is_file($source)) {
    echo "[ERROR] Missing file: {$filename}" . PHP_EOL;
    continue;
  }

  $existing = $media_storage->loadByProperties([
    'name' => $definition['name'],
  ]);

  if ($existing) {
    $media = reset($existing);
    echo "[EXISTS] {$definition['name']} (Media ID {$media->id()})" . PHP_EOL;
    continue;
  }

  $data = file_get_contents($source);

  if ($data === FALSE) {
    echo "[ERROR] Cannot read: {$filename}" . PHP_EOL;
    continue;
  }

  $destination = 'public://homepage-visuals/' . $filename;

  $file = $file_repository->writeData(
    $data,
    $destination,
    FileSystemInterface::EXISTS_REPLACE
  );

  $media = Media::create([
    'bundle' => 'image',
    'name' => $definition['name'],
    'status' => 1,
    'field_media_image' => [
      'target_id' => $file->id(),
      'alt' => $definition['alt'],
    ],
  ]);

  $media->save();

  echo "[CREATED] {$definition['name']} — Media ID {$media->id()}" . PHP_EOL;
}

echo PHP_EOL . "Homepage visual import complete." . PHP_EOL;
