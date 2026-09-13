<?php

namespace Drupal\campus_event_stories\Controller;

use Drupal\Core\Controller\ControllerBase;

class EventStoryController extends ControllerBase {

  private function page(array $event): array {
    $html = '
      <div class="cc-site cc-event-story-page">

        <header class="cc-header">
          <div class="cc-header-inner">
            <a href="/" class="cc-brand">
              <span class="cc-logo">C</span>
              <span class="cc-brand-text">
                <strong>CampusConnect</strong>
                <small>HIGHER EDUCATION</small>
              </span>
            </a>

            <nav class="cc-nav" aria-label="Main navigation">
              <a href="/">Home</a>
              <a href="/announcements">Announcements</a>
              <a href="/campus-news">News</a>
              <a href="/events" class="active">Events</a>
              <a href="/departments">Departments</a>
              <a href="/faculty-staff">People</a>
            </nav>

            <a class="cc-search" href="/search/node">
              <span>⌕</span>
              <span>Search...</span>
            </a>
          </div>
        </header>

        <main>

          <section class="cc-event-story-hero">

            <div class="cc-event-story-hero-copy">

              <a class="cc-event-back" href="/events">
                ← Back to Events
              </a>

              <div class="cc-event-story-kicker">
                CAMPUSCONNECT / ON CAMPUS
              </div>

              <h1>' . $event['title'] . '</h1>

              <p class="cc-event-story-intro">
                ' . $event['intro'] . '
              </p>

              <div class="cc-event-story-meta">
                <span>' . $event['date'] . '</span>
                <span>✦ ' . $event['location'] . '</span>
                <span>◷ ' . $event['time'] . '</span>
              </div>

            </div>

            <div class="cc-event-story-hero-image">
              <img
                src="' . $event['image'] . '"
                alt="' . $event['title'] . '"
              >
            </div>

          </section>

          <section class="cc-event-story-content">

            <article class="cc-event-story-main">

              <div class="cc-event-story-label">
                EXPLORE THE EVENT
              </div>

              <h2>' . $event['heading'] . '</h2>

              <p>' . $event['paragraph1'] . '</p>

              <p>' . $event['paragraph2'] . '</p>

              <div class="cc-event-story-highlights">
                <h3>What to expect</h3>

                <div class="cc-event-highlight-grid">';

    foreach ($event['highlights'] as $highlight) {
      $html .= '
                  <div class="cc-event-highlight">
                    <span>✓</span>
                    <strong>' . $highlight . '</strong>
                  </div>';
    }

    $html .= '
                </div>
              </div>

            </article>

            <aside class="cc-event-story-sidebar">
              <div class="cc-event-info-card">
                <span class="cc-event-info-label">EVENT DETAILS</span>

                <div class="cc-event-info-row">
                  <span>DATE</span>
                  <strong>' . $event['date'] . '</strong>
                </div>

                <div class="cc-event-info-row">
                  <span>TIME</span>
                  <strong>' . $event['time'] . '</strong>
                </div>

                <div class="cc-event-info-row">
                  <span>LOCATION</span>
                  <strong>' . $event['location'] . '</strong>
                </div>

                <a href="/events" class="cc-event-info-button">
                  View all events →
                </a>
              </div>
            </aside>

          </section>

        </main>

        <footer class="cc-footer">
          <div class="cc-footer-inner">

            <div class="cc-footer-brand">
              <span class="cc-logo">C</span>
              <div>
                <strong>CampusConnect</strong>
                <small>Higher education, connected.</small>
              </div>
            </div>

            <nav>
              <a href="/">Home</a>
              <a href="/announcements">Announcements</a>
              <a href="/campus-news">News</a>
              <a href="/events">Events</a>
              <a href="/departments">Departments</a>
              <a href="/faculty-staff">People</a>
            </nav>

            <span class="cc-copy">© 2026 CampusConnect</span>

          </div>
        </footer>

      </div>';

    return [
      '#markup' => $html,
      '#attached' => [
        'library' => [
          'campusconnect/events',
          'campus_event_stories/story',
        ],
      ],
    ];
  }

  public function capstone(): array {
    return $this->page([
      'title' => 'BSIT Capstone Orientation',
      'intro' => 'A focused orientation designed to help graduating BSIT students understand the capstone journey from project planning through final presentation.',
      'heading' => 'Turning your capstone idea into a working solution.',
      'paragraph1' => 'The BSIT Capstone Orientation gives students a clear starting point for one of the most important projects of their academic journey. Participants will be introduced to project requirements, documentation standards, development expectations, and the process they will follow throughout the capstone.',
      'paragraph2' => 'The session also helps students understand how to organize their project timeline, work effectively with their team, prepare technical documentation, and confidently present the solution they have built.',
      'date' => 'SEP 18, 2026',
      'location' => 'Main Auditorium',
      'time' => '9:00 AM – 4:00 PM',
      'image' => '/themes/custom/campusconnect/images/events/capstone.jpg',
      'highlights' => [
        'Capstone project requirements',
        'Documentation standards',
        'Development guidelines',
        'Presentation preparation',
      ],
    ]);
  }

  public function workshop(): array {
    return $this->page([
      'title' => 'Web Development Workshop',
      'intro' => 'A hands-on learning session where students explore modern web development practices and discover how websites are built with today’s content management tools.',
      'heading' => 'Build, experiment, and understand the modern web.',
      'paragraph1' => 'The Web Development Workshop takes students beyond theory and into practical web development. Participants will explore how websites are structured, developed, maintained, and connected to modern content management systems.',
      'paragraph2' => 'Through practical exercises, students will have opportunities to experiment, troubleshoot problems, and understand how the different parts of a web project work together to create a useful digital experience.',
      'date' => 'SEP 18, 2026',
      'location' => 'Computer Laboratory 2',
      'time' => '1:00 PM – 5:00 PM',
      'image' => '/themes/custom/campusconnect/images/events/workshop.jpg',
      'highlights' => [
        'Hands-on web development',
        'Modern web practices',
        'Content management systems',
        'Practical exercises',
      ],
    ]);
  }

  public function competition(): array {
    return $this->page([
      'title' => 'Student Technology Competition',
      'intro' => 'A friendly campus challenge where students put programming, web development, creativity, and problem-solving skills to the test.',
      'heading' => 'Where technical skills meet creativity and teamwork.',
      'paragraph1' => 'The Student Technology Competition brings students together for a friendly challenge built around programming, web development, and problem-solving. Teams will put their technical knowledge to work while competing in an energetic campus environment.',
      'paragraph2' => 'Beyond the competition itself, the event gives students a chance to demonstrate how they think under pressure, communicate ideas, solve technical problems, and transform their knowledge into working solutions.',
      'date' => 'SEP 25, 2026',
      'location' => 'University Activity Center',
      'time' => '10:00 AM – 12:00 PM',
      'image' => '/themes/custom/campusconnect/images/events/competition.jpg',
      'highlights' => [
        'Programming challenges',
        'Web development',
        'Problem solving',
        'Student teamwork',
      ],
    ]);
  }

}
