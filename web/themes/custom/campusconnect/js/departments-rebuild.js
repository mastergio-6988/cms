(function () {
  'use strict';

  const stories = {
    agricultural: {
      title: 'Agricultural Department',
      intro: 'A practical academic community focused on agriculture, sustainability, food systems, and applied learning.',
      body: 'Students explore modern agricultural practices, sustainable development, community-based learning, and opportunities to connect classroom knowledge with real-world challenges.',
      image: '/sites/default/files/campusconnect-library/departments/departments-01-file-computer-centre-laboratory-back-view-at-obafemi-aw.jpg',
      highlights: [
        'Agricultural education and practical learning',
        'Sustainable development and community engagement',
        'Applied projects and academic opportunities'
      ]
    },

    technology: {
      title: 'Information Technology Department',
      intro: 'A technology-focused department preparing students for the rapidly changing digital world.',
      body: 'Programs cover software development, networking, databases, cybersecurity, information systems, and digital innovation, giving students practical skills for modern technology careers.',
      image: '/sites/default/files/campusconnect-library/departments/departments-02-file-digital-computer-laboratory-jpg.jpg',
      highlights: [
        'Software development and programming',
        'Networking, databases, and cybersecurity',
        'Digital innovation and information systems'
      ]
    },

    business: {
      title: 'Business Administration Department',
      intro: 'A business community developing future managers, entrepreneurs, leaders, and professionals.',
      body: 'Students develop knowledge in management, entrepreneurship, marketing, leadership, and business operations while building practical skills for organizations and new ventures.',
      image: '/sites/default/files/campusconnect-library/departments/departments-03-file-former-computer-laboratory-tower-geograph-org-uk-1.jpg',
      highlights: [
        'Management and organizational leadership',
        'Entrepreneurship and innovation',
        'Marketing and business operations'
      ]
    },

    education: {
      title: 'Education Department',
      intro: 'Preparing students to become effective educators, learning leaders, and contributors to their communities.',
      body: 'The department supports learning in teaching practice, educational leadership, curriculum development, and learning development with an emphasis on preparing students for meaningful education careers.',
      image: '/sites/default/files/campusconnect-library/departments/departments-04-file-barbara-mcclintock-1902-1992-shown-in-her-laborato.jpg',
      highlights: [
        'Teaching and educational practice',
        'Educational leadership and curriculum',
        'Learning development and community service'
      ]
    }
  };

  const modal = document.getElementById('department-story');

  if (!modal) {
    return;
  }

  const title = modal.querySelector('#cc-dept-modal-title');
  const intro = modal.querySelector('.cc-dept-modal-intro');
  const body = modal.querySelector('.cc-dept-modal-body');
  const image = modal.querySelector('.cc-dept-modal-image');
  const highlights = modal.querySelector('.cc-dept-modal-highlights');

  function openStory(key) {
    const story = stories[key];

    if (!story) {
      return;
    }

    title.textContent = story.title;
    intro.textContent = story.intro;
    body.textContent = story.body;
    image.src = story.image;
    image.alt = story.title;

    highlights.innerHTML = story.highlights
      .map(function (item) {
        return '<div>✓ &nbsp; ' + item + '</div>';
      })
      .join('');

    modal.classList.add('is-open');
    modal.setAttribute('aria-hidden', 'false');
    document.body.classList.add('cc-dept-modal-open');

    const close = modal.querySelector('.cc-dept-modal-close');
    if (close) {
      close.focus();
    }
  }

  function closeStory() {
    modal.classList.remove('is-open');
    modal.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('cc-dept-modal-open');
  }

  document.querySelectorAll('[data-department]').forEach(function (link) {
    link.addEventListener('click', function (event) {
      event.preventDefault();
      openStory(link.getAttribute('data-department'));
    });
  });

  modal.querySelectorAll('[data-department-close]').forEach(function (element) {
    element.addEventListener('click', closeStory);
  });

  document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape' && modal.classList.contains('is-open')) {
      closeStory();
    }
  });
})();
