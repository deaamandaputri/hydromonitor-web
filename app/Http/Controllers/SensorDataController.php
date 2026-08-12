<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\SensorData;

class SensorDataController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'proximity' => 'nullable|numeric',
            'water_level_cm' => 'nullable|numeric',
            'water_level_percent' => 'nullable|numeric',
            'turbidity_adc' => 'nullable|integer',
            'turbidity_voltage' => 'nullable|numeric',
            'water_status' => 'nullable|string',
            'flow_rate' => 'nullable|numeric',
            'total_litres' => 'nullable|numeric',
            'pump_status' => 'nullable|string',
            'pompa1_status' => 'nullable|string',
            'pompa2_status' => 'nullable|string',
        ]);

        if (empty($data['user_id'])) {
            $data['user_id'] = auth()->id() ?? 1;
        }

        $sensorData = SensorData::create($data);

        return response()->json([
            'status' => 'success',
            'data' => $sensorData
        ], 201);
    }

    public function latest()
    {
        $data = SensorData::orderBy('id', 'desc')->first();
        return response()->json($data);
    }

    public function chartData()
    {
        // Get the maximum total_litres recorded per day over the last 7 days.
        // Assuming MySQL/SQLite where DATE() works.
        $data = \DB::table('sensor_data')
            ->select(\DB::raw('DATE(created_at) as date'), \DB::raw('MAX(total_litres) as total'))
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        $labels = [];
        $values = [];
        foreach ($data as $row) {
            $labels[] = \Carbon\Carbon::parse($row->date)->translatedFormat('l'); // Senin, Selasa, etc.
            $values[] = round($row->total, 2);
        }

        return response()->json([
            'labels' => $labels,
            'values' => $values
        ]);
    }

    public function controlPump(Request $request)
    {
        $request->validate([
            'mode' => 'required|in:auto,manual',
            'pump' => 'nullable|in:ON,OFF',
            'pompa1' => 'nullable|in:ON,OFF',
            'pompa2' => 'nullable|in:ON,OFF'
        ]);

        $server   = env('MQTT_HOST', 'broker.emqx.io');
        $port     = env('MQTT_PORT', 1883);
        $clientId = env('MQTT_CLIENT_ID', 'hydromonitor-web-' . rand(1000, 9999));

        try {
            $mqtt = new \PhpMqtt\Client\MqttClient($server, $port, $clientId);
            $settings = (new \PhpMqtt\Client\ConnectionSettings)->setKeepAliveInterval(60);

            if (env('MQTT_USERNAME')) {
                $settings->setUsername(env('MQTT_USERNAME'))
                         ->setPassword(env('MQTT_PASSWORD'));
            }

            $mqtt->connect($settings, true);

            $payload = json_encode([
                'mode' => $request->mode,
                'pump' => $request->pump ?? 'OFF',
                'pompa1' => $request->pompa1 ?? 'OFF',
                'pompa2' => $request->pompa2 ?? 'OFF'
            ]);

            $mqtt->publish('hydromonitor/control', $payload, 0);
            $mqtt->disconnect();

            return response()->json(['status' => 'success', 'message' => 'Command sent']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
