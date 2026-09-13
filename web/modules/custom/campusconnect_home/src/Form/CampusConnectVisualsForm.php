<?php

namespace Drupal\campusconnect_home\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\media\MediaInterface;

/**
 * Homepage visual management form.
 */
class CampusConnectVisualsForm extends ConfigFormBase {

  /**
   * Homepage visual configuration keys.
   */
  private const CATEGORY_IMAGES = [
    'announcement_fallback' => [
      'label' => 'Announcements fallback',
      'description' => 'Used when an Announcement does not have its own image.',
    ],
    'campus_news_fallback' => [
      'label' => 'Campus News fallback',
      'description' => 'Used when Campus News does not have its own image.',
    ],
    'campus_event_fallback' => [
      'label' => 'Events fallback',
      'description' => 'Used when an Event does not have its own image.',
    ],
    'department_fallback' => [
      'label' => 'Departments fallback',
      'description' => 'Used when a Department does not have its own image.',
    ],
    'faculty_staff_fallback' => [
      'label' => 'Faculty & Staff fallback',
      'description' => 'Used when a Faculty & Staff item does not have its own photo.',
    ],
  ];

  /**
   * Platform feature images.
   */
  private const FEATURE_IMAGES = [
    'academic_communication' => [
      'label' => 'Academic communication',
      'description' => 'Lecture, teaching, notices and academic communication.',
    ],
    'student_experience' => [
      'label' => 'Student experience',
      'description' => 'Student life, interaction and campus experience.',
    ],
    'people_expertise' => [
      'label' => 'People & expertise',
      'description' => 'Faculty, staff, teaching and university expertise.',
    ],
    'events_calendar' => [
      'label' => 'Events & calendar',
      'description' => 'Events, activities and things happening on campus.',
    ],
    'find_information' => [
      'label' => 'Find information',
      'description' => 'Library, research, discovery and information access.',
    ],
    'connected_campus' => [
      'label' => 'Connected campus',
      'description' => 'Technology, digital learning and campus connectivity.',
    ],
  ];

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'campusconnect_visuals_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return [
      'campusconnect_home.visuals',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $config = $this->config('campusconnect_home.visuals');

    $form['intro'] = [
      '#type' => 'markup',
      '#markup' => '
        <div class="cc-visuals-intro">
          <h2>CampusConnect visual content</h2>
          <p>
            Choose reusable images from the CampusConnect Media Library.
            Content-specific images still take priority over these fallback
            images.
          </p>
        </div>
      ',
    ];

    $form['category_images'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Content category fallback images'),
      '#description' => $this->t(
        'These images are used only when the individual content item does not have its own image.'
      ),
    ];

    foreach (self::CATEGORY_IMAGES as $key => $definition) {
      $form['category_images'][$key] = $this->buildMediaSelector(
        $config,
        $key,
        $definition['label'],
        $definition['description']
      );
    }

    $form['feature_images'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Platform feature images'),
      '#description' => $this->t(
        'These six images appear in the CampusConnect platform feature section on the homepage.'
      ),
    ];

    foreach (self::FEATURE_IMAGES as $key => $definition) {
      $form['feature_images'][$key] = $this->buildMediaSelector(
        $config,
        $key,
        $definition['label'],
        $definition['description']
      );
    }

    $form['actions'] = [
      '#type' => 'actions',
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save visual settings'),
      '#button_type' => 'primary',
    ];

    $form['#attached']['library'][] = 'campusconnect_home/dashboard';

    return parent::buildForm($form, $form_state);
  }

  /**
   * Builds an Image Media selector.
   */
  private function buildMediaSelector(
    $config,
    string $key,
    string $label,
    string $description
  ): array {
    $default = NULL;
    $media_id = $config->get($key);

    if ($media_id) {
      $media = $this->entityTypeManager()
        ->getStorage('media')
        ->load($media_id);

      if ($media instanceof MediaInterface && $media->bundle() === 'image') {
        $default = $media;
      }
    }

    return [
      '#type' => 'entity_autocomplete',
      '#title' => $this->t($label),
      '#description' => $this->t($description),
      '#target_type' => 'media',
      '#default_value' => $default,
      '#selection_handler' => 'default',
      '#selection_settings' => [
        'target_bundles' => [
          'image' => 'image',
        ],
      ],
      '#placeholder' => $this->t('Start typing an image name...'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $config = $this->configFactory()
      ->getEditable('campusconnect_home.visuals');

    $keys = array_merge(
      array_keys(self::CATEGORY_IMAGES),
      array_keys(self::FEATURE_IMAGES)
    );

    foreach ($keys as $key) {
      $value = $form_state->getValue($key);

      if (is_numeric($value) && (int) $value > 0) {
        $config->set($key, (int) $value);
      }
      else {
        $config->clear($key);
      }
    }

    $config->save();

    parent::submitForm($form, $form_state);
  }

}
