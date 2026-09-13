<?php

namespace Drupal\campusconnect_home\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Drupal\node\NodeInterface;

/**
 * Provides the CampusConnect editorial dashboard.
 */
class CampusConnectDashboardController extends ControllerBase {

  /**
   * CampusConnect editorial areas.
   */
  private const AREAS = [
    'announcement' => [
      'label' => 'Announcements',
      'icon' => '📢',
      'color' => '#f0f4ff',
    ],
    'campus_news' => [
      'label' => 'Campus News',
      'icon' => '📰',
      'color' => '#eaf7f0',
    ],
    'campus_event' => [
      'label' => 'Events',
      'icon' => '📅',
      'color' => '#fff5dc',
    ],
    'department' => [
      'label' => 'Departments',
      'icon' => '🏫',
      'color' => '#f1ecff',
    ],
    'faculty_staff' => [
      'label' => 'Faculty & Staff',
      'icon' => '👥',
      'color' => '#e8f5f8',
    ],
  ];

  /**
   * Builds the dashboard.
   */
  public function dashboard(): array {
    $storage = $this->entityTypeManager()->getStorage('node');

    $cards = [];
    $total = 0;

    foreach (self::AREAS as $type => $area) {
      $count = (int) $storage->getQuery()
        ->condition('type', $type)
        ->accessCheck(FALSE)
        ->count()
        ->execute();

      $total += $count;

      $add_url = Url::fromRoute('node.add', [
        'node_type' => $type,
      ])->toString();

      $manage_url = Url::fromRoute('system.admin_content', [
        'type' => $type,
      ])->toString();

      $cards[] = '
        <article class="cc-admin-card" style="--cc-card-tint:' . $area['color'] . '">
          <div class="cc-admin-card-icon" aria-hidden="true">' . $area['icon'] . '</div>

          <div class="cc-admin-card-count">' . $count . '</div>

          <h2>' . $this->t($area['label']) . '</h2>

          <div class="cc-admin-card-actions">
            <a href="' . $add_url . '">+ Add new</a>
            <a href="' . $manage_url . '">Manage</a>
          </div>
        </article>
      ';
    }

    /*
     * Media management.
     *
     * These are native Drupal Media routes. We deliberately use Drupal's
     * routes instead of creating a second custom media system.
     */
    $media_links = [];

    if ($this->currentUser()->hasPermission('access media overview')) {
      $media_links[] = '
        <a href="' . Url::fromRoute('entity.media.collection')->toString() . '">
          <span aria-hidden="true">🖼</span>
          <strong>Media Library</strong>
          <small>Browse, edit and delete uploaded media.</small>
        </a>
      ';
    }

    if ($this->currentUser()->hasPermission('create image media')) {
      $media_links[] = '
        <a href="' . Url::fromRoute('entity.media.add_form', [
          'media_type' => 'image',
        ])->toString() . '">
          <span aria-hidden="true">＋</span>
          <strong>Add Image</strong>
          <small>Upload a new reusable campus image.</small>
        </a>
      ';
    }

    /*
     * Recent content.
     */
    $recent_ids = $storage->getQuery()
      ->accessCheck(TRUE)
      ->sort('created', 'DESC')
      ->range(0, 6)
      ->execute();

    $recent = [];

    foreach ($storage->loadMultiple($recent_ids) as $node) {
      if (!$node instanceof NodeInterface) {
        continue;
      }

      $node_type = $this->entityTypeManager()
        ->getStorage('node_type')
        ->load($node->bundle());

      $type_label = $node_type ? $node_type->label() : $node->bundle();

      $recent[] = '
        <li>
          <span class="cc-admin-dot" aria-hidden="true"></span>
          <div>
            <a href="' . $node->toUrl()->toString() . '">'
              . htmlspecialchars($node->label(), ENT_QUOTES, 'UTF-8') .
            '</a>
            <small>'
              . htmlspecialchars((string) $type_label, ENT_QUOTES, 'UTF-8')
              . ' · '
              . \Drupal::service('date.formatter')->format(
                $node->getCreatedTime(),
                'custom',
                'M j, Y'
              )
            . '</small>
          </div>
        </li>
      ';
    }

    if (!$recent) {
      $recent[] = '
        <li class="cc-admin-recent-empty">
          No content has been created yet.
        </li>
      ';
    }

    /*
     * Website / server actions.
     */
    $website_actions = '
      <a href="/campusconnect-home">
        <span aria-hidden="true">🌐</span>
        <strong>Preview Homepage</strong>
        <small>Open the public CampusConnect experience.</small>
      </a>

      <a href="/admin/campusconnect/server">
        <span aria-hidden="true">🖥</span>
        <strong>Server Control</strong>
        <small>Start or take the CampusConnect site offline.</small>
      </a>
    ';

    return [
      '#attached' => [
        'library' => [
          'campusconnect_home/dashboard',
        ],
      ],

      'body' => [
        '#markup' => '
          <div class="cc-admin-wrap">

            <section class="cc-admin-hero">
              <div>
                <span class="cc-admin-eyebrow">
                  CAMPUSCONNECT / ADMIN
                </span>

                <h1>Campus at a glance.</h1>

                <p>
                  Manage the stories, events, people, places and visual
                  content that keep your campus community connected.
                </p>
              </div>

              <div class="cc-admin-total">
                <strong>' . $total . '</strong>
                <span>Total content items</span>
              </div>
            </section>

            <section class="cc-admin-section">
              <div class="cc-admin-section-heading">
                <div>
                  <span class="cc-admin-section-label">CONTENT</span>
                  <h2>Manage campus information</h2>
                </div>
              </div>

              <div class="cc-admin-grid">
                ' . implode('', $cards) . '
              </div>
            </section>

            <section class="cc-admin-section">
              <div class="cc-admin-section-heading">
                <div>
                  <span class="cc-admin-section-label">MEDIA</span>
                  <h2>Manage campus photography</h2>
                </div>
              </div>

              <div class="cc-admin-action-grid">
                ' . implode('', $media_links) . '
              </div>
            </section>

            <section class="cc-admin-section">
              <div class="cc-admin-section-heading">
                <div>
                  <span class="cc-admin-section-label">WEBSITE</span>
                  <h2>Website controls</h2>
                </div>
              </div>

              <div class="cc-admin-action-grid">
                ' . $website_actions . '
              </div>
            </section>

            <section class="cc-admin-section">
              <div class="cc-admin-section-heading">
                <div>
                  <span class="cc-admin-section-label">QUICK ACTIONS</span>
                  <h2>Create something new</h2>
                </div>
              </div>

              <div class="cc-admin-actions">
                <a href="/node/add/announcement">📢 New announcement</a>
                <a href="/node/add/campus_news">📰 Publish campus news</a>
                <a href="/node/add/campus_event">📅 Create event</a>
                <a href="/node/add/department">🏫 Add department</a>
                <a href="/node/add/faculty_staff">👥 Add faculty / staff</a>
                <a href="/admin/content">📋 Manage all content</a>
              </div>
            </section>

            <section class="cc-admin-section">
              <div class="cc-admin-section-heading">
                <div>
                  <span class="cc-admin-section-label">ACTIVITY</span>
                  <h2>Recently created content</h2>
                </div>
              </div>

              <ul class="cc-admin-recent">
                ' . implode('', $recent) . '
              </ul>
            </section>

          </div>
        ',
      ],
    ];
  }

}
