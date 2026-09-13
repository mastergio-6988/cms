(function (Drupal, once) {
  'use strict';

  Drupal.behaviors.campusConnectUI = {
    attach: function (context) {

      /*
       * Mobile navigation
       */
      once('cc-mobile-menu', '.cc-menu-toggle', context).forEach(function (button) {
        var nav = document.getElementById(button.getAttribute('aria-controls'));

        if (!nav) {
          return;
        }

        button.addEventListener('click', function () {
          var expanded = button.getAttribute('aria-expanded') === 'true';

          button.setAttribute('aria-expanded', String(!expanded));
          nav.classList.toggle('is-open', !expanded);
          document.body.classList.toggle('cc-menu-open', !expanded);
        });

        nav.querySelectorAll('a').forEach(function (link) {
          link.addEventListener('click', function () {
            button.setAttribute('aria-expanded', 'false');
            nav.classList.remove('is-open');
            document.body.classList.remove('cc-menu-open');
          });
        });

        document.addEventListener('keydown', function (event) {
          if (event.key === 'Escape') {
            button.setAttribute('aria-expanded', 'false');
            nav.classList.remove('is-open');
            document.body.classList.remove('cc-menu-open');
            button.focus();
          }
        });
      });


      /*
       * Search panel
       */
      once('cc-search', '.cc-search-button', context).forEach(function (button) {
        var panel = document.getElementById('cc-search-panel');

        if (!panel) {
          return;
        }

        button.addEventListener('click', function () {
          var expanded = button.getAttribute('aria-expanded') === 'true';

          button.setAttribute('aria-expanded', String(!expanded));

          if (expanded) {
            panel.hidden = true;
          }
          else {
            panel.hidden = false;

            var input = panel.querySelector('input');

            if (input) {
              window.setTimeout(function () {
                input.focus();
              }, 50);
            }
          }
        });

        document.addEventListener('keydown', function (event) {
          if (event.key === 'Escape' && !panel.hidden) {
            panel.hidden = true;
            button.setAttribute('aria-expanded', 'false');
            button.focus();
          }
        });
      });


      /*
       * Scroll reveal
       */
      once(
        'cc-reveal',
        '.cc-home-section, .cc-list-card, .cc-home-quicklink, .cc-home-snapshot-card',
        context
      ).forEach(function (element) {

        if (!('IntersectionObserver' in window)) {
          element.classList.add('cc-visible');
          return;
        }

        var observer = new IntersectionObserver(function (entries) {
          entries.forEach(function (entry) {
            if (entry.isIntersecting) {
              entry.target.classList.add('cc-visible');
              observer.unobserve(entry.target);
            }
          });
        }, {
          threshold: 0.12
        });

        observer.observe(element);
      });

    }
  };

})(Drupal, once);


/*
 * CampusConnect premium header.
 *
 * Adds:
 * - scroll state
 * - current navigation state
 * - safe same-origin path comparison
 */
(function (Drupal, once) {
  'use strict';

  Drupal.behaviors.campusConnectHeader = {
    attach: function (context) {

      once('cc-header-enhancement', '[data-cc-header]', context).forEach(function (header) {

        var navLinks = header.querySelectorAll('.cc-main-nav > a');
        var currentPath = window.location.pathname.replace(/\/+$/, '') || '/';

        navLinks.forEach(function (link) {
          try {
            var linkUrl = new URL(link.href, window.location.origin);

            if (linkUrl.origin !== window.location.origin) {
              return;
            }

            var linkPath = linkUrl.pathname.replace(/\/+$/, '') || '/';

            if (linkPath === currentPath) {
              link.classList.add('is-active');
              link.setAttribute('aria-current', 'page');
            }
          }
          catch (error) {
            /* Ignore malformed navigation URLs. */
          }
        });

        function updateScrollState() {
          header.classList.toggle('is-scrolled', window.scrollY > 12);
        }

        updateScrollState();

        window.addEventListener('scroll', updateScrollState, {
          passive: true
        });

      });

    }
  };

})(Drupal, once);


/*
 * CampusConnect editorial hero rotation.
 *
 * Uses only locally stored CampusConnect imagery.
 * No external network requests.
 */
(function (Drupal, once) {
  'use strict';

  Drupal.behaviors.campusConnectEditorial = {
    attach: function (context) {

      once(
        'cc-editorial-hero',
        '.cc-home-hero .cc-hero-image',
        context
      ).forEach(function (hero) {

        var reducedMotion = window.matchMedia &&
          window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        if (reducedMotion) {
          return;
        }

        var images = [
          '/themes/custom/campusconnect/images/campus/campus-aerial.jpg',
          '/themes/custom/campusconnect/images/campus/campus-life.jpg',
          '/themes/custom/campusconnect/images/campus/campus-aerial.jpg'
        ];

        var index = 0;
        var changing = false;

        function showNextImage() {
          if (changing) {
            return;
          }

          changing = true;

          hero.style.opacity = '0';

          window.setTimeout(function () {
            index = (index + 1) % images.length;

            hero.style.backgroundImage =
              'linear-gradient(90deg,' +
              'rgba(10,14,38,.92) 0%,' +
              'rgba(10,14,38,.72) 43%,' +
              'rgba(10,14,38,.24) 100%),' +
              'url("' + images[index] + '")';

            hero.style.opacity = '1';

            window.setTimeout(function () {
              changing = false;
            }, 800);

          }, 750);
        }

        /*
         * Begin after the page has settled.
         * Six seconds gives the visitor enough time to read the hero.
         */
        window.setTimeout(function () {
          window.setInterval(showNextImage, 6500);
        }, 3000);

      });

    }
  };

})(Drupal, once);

/* =========================================================
   CAMPUSCONNECT ANNOUNCEMENTS INTERACTIONS
   ========================================================= */

(function () {
  'use strict';

  function initAnnouncements() {

    if (!document.body.classList.contains('path-announcements') &&
        !window.location.pathname.includes('/announcements')) {
      return;
    }

    const searchInput = document.querySelector(
      '.cc-announcement-search input'
    );

    const filterButton = document.querySelector(
      '.cc-filter-button'
    );

    const filterContainer = document.querySelector(
      '.cc-announcement-filters'
    );

    const filterButtons = filterContainer
      ? filterContainer.querySelectorAll('button, a')
      : [];

    const cardContainer =
      document.querySelector('.cc-announcements-list .view-content') ||
      document.querySelector('.cc-announcements-list');

    if (!cardContainer) {
      return;
    }

    function getCards() {
      return Array.from(
        cardContainer.children
      ).filter(function (item) {
        return item.nodeType === 1 &&
          !item.classList.contains('cc-announcement-no-results');
      });
    }

    let cards = getCards();
    let activeCategory = 'all';

    /* ---------------------------------------------
       Search
       --------------------------------------------- */

    function filterCards() {

      cards = getCards();

      const query = searchInput
        ? searchInput.value.trim().toLowerCase()
        : '';

      let visibleCount = 0;

      cards.forEach(function (card) {

        const text = card.textContent.toLowerCase();

        const categoryMatch =
          activeCategory === 'all' ||
          text.includes(activeCategory);

        const searchMatch =
          !query ||
          text.includes(query);

        const visible =
          categoryMatch && searchMatch;

        card.style.display = visible ? '' : 'none';

        if (visible) {
          visibleCount++;
        }
      });

      let empty = cardContainer.querySelector(
        '.cc-announcement-no-results'
      );

      if (!visibleCount) {

        if (!empty) {
          empty = document.createElement('div');

          empty.className =
            'cc-announcement-no-results';

          empty.innerHTML =
            '<strong>No announcements found</strong>' +
            '<span>Try another keyword or category.</span>';

          cardContainer.appendChild(empty);
        }

        empty.style.display = '';
      }
      else if (empty) {
        empty.style.display = 'none';
      }
    }

    if (searchInput) {
      searchInput.addEventListener(
        'input',
        filterCards
      );
    }

    /* ---------------------------------------------
       Category filters
       --------------------------------------------- */

    filterButtons.forEach(function (button) {

      button.addEventListener('click', function (event) {

        event.preventDefault();

        filterButtons.forEach(function (item) {
          item.classList.remove('is-active');
        });

        button.classList.add('is-active');

        const value =
          button.textContent
            .trim()
            .toLowerCase();

        activeCategory =
          value === 'all'
            ? 'all'
            : value;

        filterCards();
      });
    });

    /* ---------------------------------------------
       Filter toggle
       --------------------------------------------- */

    if (filterButton && filterContainer) {

      filterButton.addEventListener(
        'click',
        function () {

          const hidden =
            filterContainer.classList.toggle(
              'is-hidden'
            );

          filterButton.classList.toggle(
            'is-open',
            !hidden
          );

          filterButton.setAttribute(
            'aria-expanded',
            String(!hidden)
          );
        }
      );

      /*
       * Start with filters visible on desktop.
       * On smaller screens, the button controls them.
       */
      if (window.innerWidth <= 780) {
        filterContainer.classList.add('is-hidden');
        filterButton.setAttribute(
          'aria-expanded',
          'false'
        );
      }
    }

    /* ---------------------------------------------
       Make card links keyboard accessible
       --------------------------------------------- */

    cards.forEach(function (card) {

      const link =
        card.querySelector(
          'h2 a, h3 a, .node__title a'
        );

      if (!link) {
        return;
      }

      card.setAttribute(
        'tabindex',
        '0'
      );

      card.addEventListener(
        'keydown',
        function (event) {

          if (
            event.key === 'Enter' ||
            event.key === ' '
          ) {

            if (
              event.target.tagName !== 'A' &&
              event.target.tagName !== 'BUTTON'
            ) {
              event.preventDefault();
              link.click();
            }
          }
        }
      );
    });

    /* ---------------------------------------------
       Sort support
       --------------------------------------------- */

    const sortSelect =
      document.querySelector(
        '.cc-announcements-tools select'
      );

    if (sortSelect) {

      sortSelect.addEventListener(
        'change',
        function () {

          const direction =
            sortSelect.value.toLowerCase();

          cards = getCards();

          cards.sort(function (a, b) {

            const aText =
              a.textContent.toLowerCase();

            const bText =
              b.textContent.toLowerCase();

            if (
              direction.includes('old')
            ) {
              return aText.localeCompare(bText);
            }

            return bText.localeCompare(aText);
          });

          cards.forEach(function (card) {
            cardContainer.appendChild(card);
          });

          filterCards();
        }
      );
    }

    /* ---------------------------------------------
       Initial state
       --------------------------------------------- */

    if (filterButtons.length) {

      const first =
        Array.from(filterButtons)
          .find(function (button) {
            return button.textContent
              .trim()
              .toLowerCase() === 'all';
          });

      if (first) {
        first.classList.add('is-active');
      }
    }

    filterCards();
  }

  if (document.readyState === 'loading') {
    document.addEventListener(
      'DOMContentLoaded',
      initAnnouncements
    );
  }
  else {
    initAnnouncements();
  }

})();



/* =========================================================
   CAMPUS NEWS SEARCH + FILTERS
   ========================================================= */

(function () {
  'use strict';

  function initCampusNews() {
    const page = document.querySelector('.cc-campus-news-site');

    if (!page) {
      return;
    }

    const cards = Array.from(
      page.querySelectorAll('.cc-campus-news-content .cc-list-card')
    );

    const filterButtons = Array.from(
      page.querySelectorAll('.cc-news-topic')
    );

    const searchInputs = Array.from(
      page.querySelectorAll('#cc-news-search, #cc-news-global-search')
    );

    const status = page.querySelector('#cc-news-results-status');
    const noResults = page.querySelector('#cc-news-no-results');
    const reset = page.querySelector('#cc-news-reset');

    let activeFilter = 'all';
    let searchTerm = '';

    function updateNews() {
      let visible = 0;

      cards.forEach(function (card) {
        const text = card.textContent.toLowerCase();

        const matchesSearch =
          !searchTerm || text.includes(searchTerm);

        let matchesFilter = true;

        if (activeFilter !== 'all') {
          const filterWords = {
            student: [
              'student',
              'achievement',
              'award',
              'club',
              'competition',
              'graduat',
              'scholar'
            ],
            academic: [
              'academic',
              'course',
              'faculty',
              'class',
              'education'
            ],
            research: [
              'research',
              'study',
              'innovation',
              'science',
              'technology',
              'project'
            ],
            community: [
              'community',
              'outreach',
              'service',
              'partnership'
            ],
            events: [
              'event',
              'seminar',
              'workshop',
              'conference',
              'festival'
            ]
          };

          const words = filterWords[activeFilter] || [];

          matchesFilter = words.some(function (word) {
            return text.includes(word);
          });
        }

        const show = matchesSearch && matchesFilter;

        card.style.display = show ? '' : 'none';

        if (show) {
          visible++;
        }
      });

      if (status) {
        if (searchTerm) {
          status.textContent =
            visible +
            ' stor' +
            (visible === 1 ? 'y' : 'ies') +
            ' found for "' +
            searchTerm +
            '"';
        }
        else if (activeFilter !== 'all') {
          status.textContent =
            'Showing ' +
            activeFilter +
            ' campus stories';
        }
        else {
          status.textContent =
            'Showing all campus stories';
        }
      }

      if (noResults) {
        noResults.hidden = visible !== 0;
      }
    }

    filterButtons.forEach(function (button) {
      button.addEventListener('click', function () {
        activeFilter =
          button.getAttribute('data-news-filter') || 'all';

        filterButtons.forEach(function (item) {
          item.classList.remove('is-active');
        });

        button.classList.add('is-active');

        updateNews();
      });
    });

    searchInputs.forEach(function (input) {
      input.addEventListener('input', function () {
        searchTerm = input.value.trim().toLowerCase();

        searchInputs.forEach(function (other) {
          if (other !== input) {
            other.value = input.value;
          }
        });

        updateNews();

        if (searchTerm) {
          document
            .querySelector('#latest-news')
            ?.scrollIntoView({
              behavior: 'smooth',
              block: 'start'
            });
        }
      });
    });

    if (reset) {
      reset.addEventListener('click', function () {
        activeFilter = 'all';
        searchTerm = '';

        searchInputs.forEach(function (input) {
          input.value = '';
        });

        filterButtons.forEach(function (button) {
          button.classList.toggle(
            'is-active',
            button.getAttribute('data-news-filter') === 'all'
          );
        });

        updateNews();
      });
    }

    updateNews();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCampusNews);
  }
  else {
    initCampusNews();
  }

})();


/* Announcement and News: open story details without leaving the listing. */
(function (Drupal, once) {
  "use strict";
  Drupal.behaviors.campusConnectStoryPopup = {
    attach: function (context) {
      var modal = document.getElementById("cc-story-modal");
      if (!modal) {
        modal = document.createElement("div");
        modal.id = "cc-story-modal";
        modal.className = "cc-story-modal";
        modal.hidden = true;
        modal.innerHTML = "<div class=\"cc-story-modal-backdrop\" data-cc-story-close></div><section class=\"cc-story-dialog\" role=\"dialog\" aria-modal=\"true\" aria-labelledby=\"cc-story-modal-title\"><button class=\"cc-story-close\" type=\"button\" aria-label=\"Close story details\" data-cc-story-close>×</button><div class=\"cc-story-modal-label\" id=\"cc-story-modal-type\"></div><h2 id=\"cc-story-modal-title\"></h2><div class=\"cc-story-modal-date\">Official CampusConnect update</div><div class=\"cc-story-modal-divider\"></div><p class=\"cc-story-modal-lead\" id=\"cc-story-modal-summary\"></p><div class=\"cc-story-modal-details\"><span>Official details</span><p id=\"cc-story-modal-details\"></p></div><div class=\"cc-story-modal-foot\"><span>✦ CampusConnect verified update</span><button type=\"button\" data-cc-story-close>Done</button></div></section>";
        document.body.appendChild(modal);
      }
      function closeModal() { modal.hidden = true; document.body.classList.remove("cc-story-modal-open"); }
      once("cc-story-popup-close", "[data-cc-story-close]", modal).forEach(function (button) { button.addEventListener("click", closeModal); });
      once("cc-story-popup-key", "html", context).forEach(function () { document.addEventListener("keydown", function (event) { if (event.key === "Escape" && !modal.hidden) { closeModal(); } }); });
      once("cc-story-popup", ".cc-story-popup-trigger", context).forEach(function (trigger) {
        trigger.addEventListener("click", function (event) {
          event.preventDefault();
          var card = trigger.closest(".cc-list-card");
          var type = card && card.querySelector(".cc-card-type");
          var title = card && card.querySelector("h2");
          var summary = card && card.querySelector(".cc-list-card-content > p");
          modal.querySelector("#cc-story-modal-type").textContent = type ? type.textContent.trim() : "Campus update";
          modal.querySelector("#cc-story-modal-title").textContent = title ? title.textContent.trim() : "CampusConnect story";
          modal.querySelector("#cc-story-modal-summary").textContent = summary ? summary.textContent.trim() : "Read this official CampusConnect update.";
          modal.querySelector("#cc-story-modal-details").textContent = "Loading the complete official details...";
          modal.hidden = false;
          document.body.classList.add("cc-story-modal-open");
          modal.querySelector(".cc-story-close").focus();
          window.fetch(trigger.href, { credentials: "same-origin" }).then(function (response) { return response.text(); }).then(function (html) {
            var documentView = new DOMParser().parseFromString(html, "text/html");
            var detail = documentView.querySelector(".cc-detail-body, .cc-node-body, article .field--name-body");
            modal.querySelector("#cc-story-modal-details").textContent = detail && detail.textContent.trim() ? detail.textContent.trim() : "This CampusConnect update does not have additional published details yet.";
          }).catch(function () { modal.querySelector("#cc-story-modal-details").textContent = "Details could not be loaded right now. Please try again shortly."; });
        });
      });
    }
  };
})(Drupal, once);


/* Faculty and staff: keep profile details on the directory page. */
(function (Drupal, once) {
  "use strict";
  Drupal.behaviors.campusConnectProfilePopup = {
    attach: function (context) {
      var modal = document.getElementById("cc-profile-modal");
      if (!modal) {
        modal = document.createElement("div");
        modal.id = "cc-profile-modal";
        modal.className = "cc-profile-modal";
        modal.hidden = true;
        modal.innerHTML = "<div class=\"cc-profile-modal-backdrop\" data-cc-profile-close></div><section class=\"cc-profile-dialog\" role=\"dialog\" aria-modal=\"true\" aria-labelledby=\"cc-profile-modal-name\"><button class=\"cc-profile-close\" type=\"button\" aria-label=\"Close profile\" data-cc-profile-close>×</button><div class=\"cc-profile-modal-layout\"><img class=\"cc-profile-modal-image\" alt=\"\"><div><span class=\"cc-profile-modal-label\">CampusConnect faculty &amp; staff</span><h2 id=\"cc-profile-modal-name\"></h2><p class=\"cc-profile-modal-position\"></p><div class=\"cc-profile-modal-divider\"></div><p class=\"cc-profile-modal-focus\"></p><dl class=\"cc-profile-modal-details\"><div><dt>Title holder</dt><dd>Faculty &amp; Staff</dd></div><div><dt>Position</dt><dd class=\"cc-profile-detail-position\"></dd></div><div class=\"cc-profile-detail-email-row\"><dt>Email</dt><dd><a class=\"cc-profile-detail-email\"></a></dd></div><div class=\"cc-profile-detail-phone-row\"><dt>Phone</dt><dd class=\"cc-profile-detail-phone\"></dd></div></dl><button class=\"cc-profile-done\" type=\"button\" data-cc-profile-close>Done</button></div></div></section>";
        document.body.appendChild(modal);
      }

      var lastTrigger;
      function closeModal() {
        modal.hidden = true;
        document.body.classList.remove("cc-profile-modal-open");
        if (lastTrigger) lastTrigger.focus();
      }

      once("cc-profile-popup-close", "[data-cc-profile-close]", modal).forEach(function (button) {
        button.addEventListener("click", closeModal);
      });
      once("cc-profile-popup-key", "html", context).forEach(function () {
        document.addEventListener("keydown", function (event) {
          if (event.key === "Escape" && !modal.hidden) closeModal();
        });
      });
      once("cc-profile-popup", ".cc-profile-popup-trigger", context).forEach(function (trigger) {
        trigger.addEventListener("click", function (event) {
          event.preventDefault();
          lastTrigger = trigger;
          var data = trigger.dataset;
          modal.querySelector("#cc-profile-modal-name").textContent = data.profileName || "CampusConnect profile";
          modal.querySelector(".cc-profile-modal-position").textContent = data.profilePosition || "Faculty & Staff";
          modal.querySelector(".cc-profile-modal-focus").textContent = data.profileFocus || "Supporting the CampusConnect community.";
          modal.querySelector(".cc-profile-detail-position").textContent = data.profilePosition || "Faculty & Staff";
          var image = modal.querySelector(".cc-profile-modal-image");
          image.src = data.profileImage || "";
          image.alt = data.profileName ? data.profileName + " portrait" : "";
          var emailRow = modal.querySelector(".cc-profile-detail-email-row");
          var email = modal.querySelector(".cc-profile-detail-email");
          emailRow.hidden = !data.profileEmail;
          email.textContent = data.profileEmail || "";
          email.href = data.profileEmail ? "mailto:" + data.profileEmail : "#";
          var phoneRow = modal.querySelector(".cc-profile-detail-phone-row");
          phoneRow.hidden = !data.profilePhone;
          modal.querySelector(".cc-profile-detail-phone").textContent = data.profilePhone || "";
          modal.hidden = false;
          document.body.classList.add("cc-profile-modal-open");
          modal.querySelector(".cc-profile-close").focus();
        });
      });
    }
  };
})(Drupal, once);


/* Rainbow click feedback. It is visual only and does not intercept controls. */
(function (Drupal, once) {
  "use strict";
  Drupal.behaviors.campusConnectClickFeedback = {
    attach: function (context) {
      once("cc-click-feedback", "body", context).forEach(function (body) {
        var reducedMotion = window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches;
        document.addEventListener("pointerdown", function (event) {
          if (reducedMotion || event.button > 0 || event.target.closest("input, textarea, select, option")) return;
          var burst = document.createElement("span");
          burst.className = "cc-rainbow-burst";
          burst.style.left = event.clientX + "px";
          burst.style.top = event.clientY + "px";
          burst.style.setProperty("--cc-burst-hue", Math.floor(Math.random() * 360) + "deg");
          burst.setAttribute("aria-hidden", "true");
          body.appendChild(burst);
          window.setTimeout(function () { burst.remove(); }, 720);
        }, { passive: true });
      });
    }
  };
})(Drupal, once);
