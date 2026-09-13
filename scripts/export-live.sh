#!/usr/bin/env bash
set -e

PROJECT="$HOME/Development/Projects/CMS/campus_cms"
DB_NAME="campus_cms"
DB_USER="campus_admin"
DB_HOST="127.0.0.1"
DB_PORT="3306"
OUTPUT="$PROJECT/database/campusconnect-live.sql"

cd "$PROJECT"

echo "======================================"
echo " CampusConnect Database Export"
echo "======================================"
echo
echo "Database : $DB_NAME"
echo "Host     : $DB_HOST:$DB_PORT"
echo "Output   : $OUTPUT"
echo

mkdir -p "$PROJECT/database"

echo "Exporting database..."
echo "You may be asked for the MySQL password."
echo

mysqldump \
  --host="$DB_HOST" \
  --port="$DB_PORT" \
  --user="$DB_USER" \
  --single-transaction \
  --routines \
  --triggers \
  --events \
  --default-character-set=utf8mb4 \
  "$DB_NAME" > "$OUTPUT"

echo
echo "======================================"
echo " Export completed successfully"
echo "======================================"
echo
ls -lh "$OUTPUT"
echo
echo "Database dump:"
echo "$OUTPUT"
