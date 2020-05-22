<?php

namespace Kampakit\PostalCodesGermany\Console\Commands;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportCommand extends Command
{
    protected $signature = 'postal-codes-germany:import';

    protected $description = 'Import postal code data from source url set in config/postal-codes-germany.php';

    public function handle() {
        $json = $this->downloadData();
//        $features = $json['features'];
//        $processed = $this->processDataForInsert($features);
//        DB::disableQueryLog();
//        DB::beginTransaction();

    }

    private function downloadData() {
        $source_url = config('postal-codes-germany.source_url');
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $source_url);
        $result = curl_exec($curl);
        curl_close($curl);
        return json_decode($result);
    }

    private function processDataForInsert($features) {
        $result = [];
        foreach ($features as $feature) {
            $postal_code = $feature['properties']['plz'];
            $length = strlen($postal_code);
            $city = substr($feature['properties']['note'], $length);
            var_dump($feature['properties']['note']);
            var_dump($city);
            $result[] = [
                'postal_code' => $postal_code,
                'city' => $city,
                'longitude' => $feature['geometry']['coordinates'][0],
                'latitude' => $feature['geometry']['coordinates'][1]
            ];
        }
    }
}