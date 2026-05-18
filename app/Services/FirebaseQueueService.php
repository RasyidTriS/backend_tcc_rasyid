<?php

namespace App\Services;

use App\Models\Booking;
use Google\Auth\Credentials\ServiceAccountCredentials;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Config;
use RuntimeException;

class FirebaseQueueService
{
    private Client $http;

    public function __construct()
    {
        $this->http = new Client([
            'timeout' => 10,
        ]);
    }

    public function updateActiveQueue(Booking $booking)
    {
        $booking->loadMissing(['patient', 'schedule.poli']);

        $poliId = $booking->schedule->poli_id;
        $data = [
            'poli_id' => $poliId,
            'poli_name' => $booking->schedule->poli->nama_poli ?? null,
            'current_queue_number' => $booking->nomor_antrean,
            'booking_id' => $booking->id,
            'patient_name' => $booking->patient->nama ?? null,
            'status' => $booking->status,
            'called_at' => now()->toIso8601String(),
            'updated_at' => now()->toIso8601String(),
        ];

        $this->setDocument('active_queues', (string) $poliId, $data);
    }

    private function setDocument(string $collection, string $document, array $data): void
    {
        $projectId = Config::get('firebase.project_id', env('FIREBASE_PROJECT_ID'));

        if (! $projectId) {
            throw new RuntimeException('FIREBASE_PROJECT_ID belum dikonfigurasi.');
        }

        $url = sprintf(
            'https://firestore.googleapis.com/v1/projects/%s/databases/(default)/documents/%s/%s',
            rawurlencode($projectId),
            rawurlencode($collection),
            rawurlencode($document)
        );

        $this->http->patch($url, [
            'headers' => [
                'Authorization' => 'Bearer '.$this->accessToken(),
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'fields' => $this->firestoreFields($data),
            ],
        ]);
    }

    private function accessToken(): string
    {
        $credentialsPath = Config::get('firebase.projects.app.credentials', env('FIREBASE_CREDENTIALS'));
        $credentialsPath = $credentialsPath ? $this->absolutePath($credentialsPath) : null;

        if (! $credentialsPath || ! is_file($credentialsPath)) {
            throw new RuntimeException('File kredensial Firebase tidak ditemukan: '.$credentialsPath);
        }

        $credentials = new ServiceAccountCredentials(
            ['https://www.googleapis.com/auth/datastore'],
            $credentialsPath
        );

        $token = $credentials->fetchAuthToken();

        if (empty($token['access_token'])) {
            throw new RuntimeException('Gagal mengambil access token Firebase.');
        }

        return $token['access_token'];
    }

    private function absolutePath(string $path): string
    {
        return str_starts_with($path, DIRECTORY_SEPARATOR) ? $path : base_path($path);
    }

    private function firestoreFields(array $data): array
    {
        return collect($data)
            ->map(fn ($value) => $this->firestoreValue($value))
            ->all();
    }

    private function firestoreValue(mixed $value): array
    {
        return match (true) {
            is_null($value) => ['nullValue' => null],
            is_bool($value) => ['booleanValue' => $value],
            is_int($value) => ['integerValue' => (string) $value],
            is_float($value) => ['doubleValue' => $value],
            default => ['stringValue' => (string) $value],
        };
    }
}
