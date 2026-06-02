# Deploy Backend Laravel ke Cloud Run

Panduan ini untuk folder `backend_tcc_rasyid`.

## 1. Set variabel lokal

```bash
PROJECT_ID="isi-project-id-gcp"
REGION="asia-southeast2"
SERVICE="backend-antrean"
DB_INSTANCE="antrean-db"
DB_NAME="antrean"
DB_USER="laravel"
DB_PASS="ganti-password-kuat"
```

## 2. Aktifkan API

```bash
gcloud config set project "$PROJECT_ID"

gcloud services enable \
  run.googleapis.com \
  cloudbuild.googleapis.com \
  artifactregistry.googleapis.com \
  sqladmin.googleapis.com
```

## 3. Buat atau pakai Cloud SQL MySQL

```bash
gcloud sql instances create "$DB_INSTANCE" \
  --database-version=MYSQL_8_0 \
  --tier=db-f1-micro \
  --region="$REGION"

gcloud sql databases create "$DB_NAME" \
  --instance="$DB_INSTANCE"

gcloud sql users create "$DB_USER" \
  --instance="$DB_INSTANCE" \
  --password="$DB_PASS"
```

Ambil connection name:

```bash
INSTANCE_CONNECTION_NAME="$(gcloud sql instances describe "$DB_INSTANCE" --format='value(connectionName)')"
echo "$INSTANCE_CONNECTION_NAME"
```

## 4. Generate APP_KEY Laravel

```bash
php artisan key:generate --show
APP_KEY="base64:hasil-generate-key"
```

## 5. Deploy backend ke Cloud Run

Jalankan dari folder `backend_tcc_rasyid`.

```bash
gcloud run deploy "$SERVICE" \
  --source . \
  --region="$REGION" \
  --allow-unauthenticated \
  --add-cloudsql-instances="$INSTANCE_CONNECTION_NAME" \
  --set-env-vars="APP_NAME=AntreanPuskesmas,APP_ENV=production,APP_KEY=$APP_KEY,APP_DEBUG=false,APP_URL=https://TEMP_URL,LOG_CHANNEL=stderr,LOG_LEVEL=info,DB_CONNECTION=mysql,DB_HOST=localhost,DB_PORT=3306,DB_DATABASE=$DB_NAME,DB_USERNAME=$DB_USER,DB_PASSWORD=$DB_PASS,DB_SOCKET=/cloudsql/$INSTANCE_CONNECTION_NAME,SESSION_DRIVER=database,CACHE_STORE=database,QUEUE_CONNECTION=database"
```

Setelah deploy selesai, update `APP_URL`:

```bash
BACKEND_URL="https://url-cloud-run-kamu"

gcloud run services update "$SERVICE" \
  --region="$REGION" \
  --update-env-vars="APP_URL=$BACKEND_URL"
```

## 6. Jalankan migration

```bash
php artisan migrate --force
php artisan db:seed --force
```

Untuk production Cloud Run, migration dapat dijalankan melalui Cloud Run Job atau shell sementara.

## 7. Hubungkan frontend

Mobile Flutter:

```dart
static const String baseUrl = 'https://url-cloud-run-kamu/api';
```

Web-admin React:

```env
VITE_API_BASE_URL=https://url-cloud-run-kamu/api
```
