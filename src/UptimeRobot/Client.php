<?php

namespace App\UptimeRobot;

class Client
{
    private $status_name_map = [
        0 => [
            'text' => 'Paused',
            'code' => 'paused'
        ],
        1 => [
            'text' => 'Not checked yet',
            'code' => 'not-checked-yet'
        ],
        2 => [
            'text' => 'Up',
            'code' => 'up'
        ],
        8 => [
            'text' => 'Seems down',
            'code' => 'seems-down'
        ],
        9 => [
            'text' => 'Down',
            'code' => 'down'
        ],
    ];

    /**
     * @param string $api_key
     * @return Monitor[]
     */
    public function fetch(string $api_key, array $monitors): array
    {
        if (count($monitors) === 0) {
            return [];
        }

        $curl = curl_init();

        $fields = ["api_key=$api_key"];
        $codes = implode('-', $monitors);
        $fields[] = "monitors=$codes";

        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL            => 'https://api.uptimerobot.com/v2/getMonitors?=',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING       => '',
                CURLOPT_MAXREDIRS      => 10,
                CURLOPT_TIMEOUT        => 30,
                CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST  => 'POST',
                CURLOPT_POST           => count($fields),
                CURLOPT_POSTFIELDS     => implode('&', $fields)
            )
        );

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return [];
        }

        $ur_response = json_decode($response);

        $monitors = [];
        if (isset($ur_response->monitors)) {
            foreach ($ur_response->monitors as $monitor_json) {
                $monitors[] = $this->buildMonitor($monitor_json);
            }
        }

        return $monitors;
    }

    private function buildMonitor(\stdClass $monitor_json): Monitor
    {
        $monitor = new Monitor();

        $monitor->id = $monitor_json->id;
        $monitor->name = $monitor_json->friendly_name;
        $monitor->status_text = $this->status_name_map[$monitor_json->status]['text'];
        $monitor->status_code = $this->status_name_map[$monitor_json->status]['code'];

        return $monitor;
    }
}
