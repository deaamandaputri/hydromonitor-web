<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;
use App\Models\SensorData; 
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class MqttListenCommand extends Command
{
    // Nama perintah yang diketikkan di terminal (contoh: php artisan mqtt:listen)
    protected $signature = 'mqtt:listen';
    // Penjelasan singkat tentang apa yang dilakukan perintah ini
    protected $description = 'Listen to MQTT topic and save sensor data';

    // Fungsi utama yang akan otomatis dijalankan ketika perintah ini dipanggil
    public function handle()
    {
        // Mengambil konfigurasi alamat broker MQTT dari file .env (default: test.mosquitto.org)
        $server   = env('MQTT_HOST', 'broker.emqx.io');
        // Mengambil port MQTT dari file .env (default: 1883)
        $port     = env('MQTT_PORT', 1883);
        // Membuat ID Klien secara acak agar tidak bentrok dengan perangkat lain
        $clientId = env('MQTT_CLIENT_ID', 'hydromonitor-laravel-' . rand(1000, 9999));

        // Membuat object client (instansi) untuk proses komunikasi ke MQTT
        $mqtt = new MqttClient($server, $port, $clientId);

        // Mengatur setelan koneksi (contoh: jeda waktu koneksi tetap hidup maksimal 60 detik)
        $settings = (new ConnectionSettings)
            ->setKeepAliveInterval(60);

        // Jika di file .env disediakan username & password untuk MQTT, maka masukkan ke dalam setelan
        if (env('MQTT_USERNAME')) {
            $settings->setUsername(env('MQTT_USERNAME'))
                     ->setPassword(env('MQTT_PASSWORD'));
        }

        // Tampilkan pesan teks berwarna hijau (info) ke layar terminal
        $this->info("Connecting to MQTT Broker at {$server}:{$port}...");

        try {
            // Proses mencoba menghubungi/konek ke broker MQTT
            $mqtt->connect($settings, true);
            $this->info("Connected successfully. Listening to topic: hydromonitor/sensor_data");

            // Berlangganan (subscribe) ke topik tertentu untuk mendengarkan kiriman pesan (data Arduino)
            $mqtt->subscribe('hydromonitor/sensor_data', function (string $topic, string $message) {
                // Jika pesan masuk, cetak pesannya di layar terminal
                $this->info("Received message on topic [{$topic}]: {$message}");
                
                // Pesan dari Arduino berbentuk string JSON, ubah jadi Array PHP agar bisa diolah
                $data = json_decode($message, true);

                // Cek apakah datanya valid (berhasil diubah ke array)
                if ($data && is_array($data)) {
                    // Masukkan (create) seluruh data sensor yang didapat ke dalam database tabel 'sensor_data'
                    SensorData::create([
                        'proximity' => $data['proximity'] ?? null,
                        'water_level_cm' => $data['water_level_cm'] ?? null,
                        'water_level_percent' => $data['water_level_percent'] ?? null,
                        'turbidity_adc' => $data['turbidity_adc'] ?? null,
                        'turbidity_voltage' => $data['turbidity_voltage'] ?? null,
                        'water_status' => $data['water_status'] ?? null,
                        'flow_rate' => $data['flow_rate'] ?? null,
                        'total_litres' => $data['total_litres'] ?? null,
                        'pump_status' => $data['pump_status'] ?? null,
                        'pompa1_status' => $data['pompa1_status'] ?? null,
                        'pompa2_status' => $data['pompa2_status'] ?? null,
                    ]);
                    $this->info("Saved to database.");

                    // ==============================================================================
                    // LOGIKA NOTIFIKASI TELEGRAM (Cek Level Air)
                    // Menggunakan Cache memori agar peringatan tidak dikirim berulang kali (Spamming)
                    // ==============================================================================
                    $proximity = $data['proximity'] ?? null;
                    if ($proximity !== null) {
                        // Mengambil status peringatan terakhir dari memori sistem (Cache), bawaannya 'normal'
                        $lastState = Cache::get('telegram_tank_state', 'normal');
                        $currentState = $lastState;
                        
                        // Menentukan status tangki saat ini berdasarkan jarak air (Proximity)
                        if ($proximity <= 4.0) {
                            $currentState = 'full';  // Jarak terlalu dekat = Tangki Penuh
                        } elseif ($proximity > 8.0 && $proximity <= 20.0) {
                            $currentState = 'empty'; // Jarak mulai menjauh = Tangki Kekurangan Air
                        }
                        
                        // Jika status tangki hari ini BERBEDA dengan status terakhir (Misal: dari kosong tiba-tiba penuh)
                        if ($currentState !== $lastState) {
                            $message = "";
                            // Rangkai kalimat notifikasi berdasarkan status baru
                            if ($currentState === 'empty') {
                                $message = "⚠️ *Peringatan Level Air* ⚠️\n\nAir tangki mulai surut (Jarak ke sensor: {$proximity} cm). Sistem otomatis menyedot air baru.";
                            } elseif ($currentState === 'full') {
                                $totalLitres = $data['total_litres'] ?? 0;
                                $message = "✅ *Informasi Level Air* ✅\n\nTangki air sudah terisi penuh! Sistem pompa dihentikan secara otomatis.\n💧 *Total Air Masuk:* {$totalLitres} Liter.";
                            }
                            
                            // Jika pesan sudah dirangkai, lakukan pengiriman
                            if ($message !== "") {
                                $this->sendTelegramMessage($message); // Panggil fungsi kirim telegram
                                // Simpan status "Penuh/Kosong" saat ini ke memori selama 24 Jam agar pesan tidak diulang
                                Cache::put('telegram_tank_state', $currentState, now()->addHours(24));
                            }
                        }
                    }
                } else {
                    $this->error("Invalid JSON payload.");
                }
            }, 0);

            $mqtt->loop(true);
            $mqtt->disconnect();
        } catch (\Exception $e) {
            $this->error("MQTT Error: " . $e->getMessage());
        }
    }

    // ==============================================================================
    // FUNGSI PENGIRIMAN TELEGRAM
    // Bertugas mengirim request ke API resmi Telegram
    // ==============================================================================
    protected function sendTelegramMessage($message)
    {
        // Mengambil kode rahasia bot dan ID obrolan tujuan dari file .env
        $token = env('TELEGRAM_BOT_TOKEN');
        $chatId = env('TELEGRAM_CHAT_ID');

        // Batalkan (return) eksekusi pengiriman jika token atau ID chat tidak diisi
        if (!$token || !$chatId) {
            return;
        }

        // Alamat lengkap API bot Telegram
        $url = "https://api.telegram.org/bot{$token}/sendMessage";
        
        try {
            // Melakukan request (seakan kita mengakses link) secara POST menggunakan package HTTP Laravel
            Http::post($url, [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'Markdown' // Mengizinkan kita memakai simbol * untuk teks tebal
            ]);
            $this->info("Telegram notification sent!"); // Notifikasi jika sukses dikirim
        } catch (\Exception $e) {
            // Tangkap dan tampilkan error kemerahan di layar terminal jika internet bermasalah/gagal
            $this->error("Failed to send Telegram notification: " . $e->getMessage());
        }
    }
}
