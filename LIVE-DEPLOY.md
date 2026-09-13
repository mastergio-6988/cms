# CampusConnect Live Deployment

This package preserves the **CampusConnect** identity, theme, custom module, public routes, media, icons, and current Drupal database dump.

## Public content routes

- `/campusconnect-home`
- `/announcements`
- `/campus-news`
- `/events`
- `/departments`
- `/faculty-staff`

The CampusConnect shell uses same-page content navigation: public CampusConnect links load the requested content into the main content area while the fixed header/branding remains in place.

## Database

The current dump is:

`database/campusconnect-live.sql.gz`

For a fresh environment, configure `web/sites/default/settings.php` for the target database and import the dump with:

```bash
./scripts/import-live.sh
```

## Local server

For the existing Campus CMS installation, keep the established service on port **8891**.
