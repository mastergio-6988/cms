<?php

namespace Drupal\campusconnect_home\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\Process\Process;

/**
 * Campus CMS server control.
 */
class CampusServerControlForm extends FormBase {

  /**
   * Campus CMS service name.
   */
  private const SERVICE = 'campus-cms.service';

  /**
   * Campus CMS port.
   */
  private const PORT = 8891;

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'campus_server_control_form';
  }

  /**
   * Check whether the Campus service is active.
   */
  private function isServerActive(): bool {
    $process = new Process([
      'systemctl',
      '--user',
      'is-active',
      self::SERVICE,
    ]);

    $process->run();

    return trim($process->getOutput()) === 'active';
  }

  /**
   * Check whether Campus is actually answering on port 8891.
   */
  private function isSiteOnline(): bool {
    $process = new Process([
      'curl',
      '-fsS',
      '--max-time',
      '2',
      'http://127.0.0.1:' . self::PORT . '/',
    ]);

    $process->run();

    return $process->isSuccessful();
  }

  /**
   * Run a systemd action.
   */
  private function serviceAction(string $action): bool {
    $process = new Process([
      'systemctl',
      '--user',
      $action,
      self::SERVICE,
    ]);

    $process->setTimeout(10);
    $process->run();

    return $process->isSuccessful();
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {

    $active = $this->isServerActive();
    $online = $active && $this->isSiteOnline();

    $form['status'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => [
          'cc-server-status',
          $online ? 'is-online' : 'is-offline',
        ],
      ],
    ];

    $form['status']['indicator'] = [
      '#markup' => '<span class="cc-server-status-dot" aria-hidden="true"></span>',
    ];

    $form['status']['text'] = [
      '#markup' => '<div class="cc-server-status-text">'
        . '<strong>' . ($online ? 'ONLINE' : 'OFFLINE') . '</strong>'
        . '<span>'
        . ($online
          ? 'CampusConnect is available on port ' . self::PORT . '.'
          : 'CampusConnect server is currently stopped.')
        . '</span>'
        . '</div>',
    ];

    $form['actions'] = [
      '#type' => 'actions',
      '#attributes' => [
        'class' => ['cc-server-actions'],
      ],
    ];

    if ($online) {
      $form['actions']['stop'] = [
        '#type' => 'submit',
        '#value' => $this->t('Turn server OFF'),
        '#submit' => ['::stopServer'],
        '#attributes' => [
          'class' => ['cc-server-button', 'cc-server-button-off'],
        ],
      ];
    }
    else {
      $form['actions']['start'] = [
        '#type' => 'submit',
        '#value' => $this->t('Turn server ON'),
        '#submit' => ['::startServer'],
        '#attributes' => [
          'class' => ['cc-server-button', 'cc-server-button-on'],
        ],
      ];
    }

    $form['address'] = [
      '#markup' => '<div class="cc-server-address">'
        . '<span>Campus URL</span>'
        . '<code>http://127.0.0.1:' . self::PORT . '</code>'
        . '</div>',
    ];

    return $form;
  }

  /**
   * Start Campus CMS.
   */
  public function startServer(array &$form, FormStateInterface $form_state): void {

    if ($this->isServerActive()) {
      $this->messenger()->addStatus(
        $this->t('Campus CMS server is already running.')
      );
      return;
    }

    if (!$this->serviceAction('start')) {
      $this->messenger()->addError(
        $this->t('Unable to start Campus CMS.')
      );
      return;
    }

    $this->messenger()->addStatus(
      $this->t('Campus CMS server started on port @port.', [
        '@port' => self::PORT,
      ])
    );

    $form_state->setRebuild(TRUE);
  }

  /**
   * Stop Campus CMS.
   */
  public function stopServer(array &$form, FormStateInterface $form_state): void {

    if (!$this->isServerActive()) {
      $this->messenger()->addStatus(
        $this->t('Campus CMS server is already stopped.')
      );
      return;
    }

    if (!$this->serviceAction('stop')) {
      $this->messenger()->addError(
        $this->t('Unable to stop Campus CMS.')
      );
      return;
    }

    $this->messenger()->addStatus(
      $this->t('Campus CMS server stopped.')
    );

    $form_state->setRebuild(TRUE);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $form_state->setRebuild(TRUE);
  }

}
