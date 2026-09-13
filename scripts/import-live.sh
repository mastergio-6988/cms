#!/usr/bin/env bash
set -e
PROJECT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
DUMP="$PROJECT/database/campusconnect-live.sql.gz"
if [ ! -f "$DUMP" ]; then
  echo "Missing $DUMP"
  exit 1
fi
cd "$PROJECT"
echo "Importing CampusConnect database through Drupal/Drush configuration..."
gzip -cd "$DUMP" | vendor/bin/drush sql:cli
vendor/bin/drush cr
echo "Import complete."
