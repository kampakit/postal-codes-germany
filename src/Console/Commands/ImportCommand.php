<?php

namespace Kampakit\PostalCodesGermany\Console\Commands;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportCommand extends Command
{
    protected $signature = 'postal-codes-germany:import';
    protected $description = 'Import postal code data from source url set in config/postal-codes-germany.php';

    public function handle() {
        $postal_codes_cities = $this->read_cities();
        $locations = $this->readLocations();
        $city_count = $this->countCitiesByPostalCode($postal_codes_cities);
        foreach ($postal_codes_cities as &$city) {
            $code = $city['postal_code'];
            $location = $locations[$code];
            $city['latitude'] = $location['latitude'];
            $city['longitude'] = $location['longitude'];
            $city['postal_code_description'] = $location['postal_code_description'];
            $city['inhabitants'] = $location['inhabitants'];
            $city['area_km2'] = $location['area_km2'];
            $city['displayed_city'] = $city_count[$code] === 1 ? $location['postal_code_description']: $city['city'];
        }
        $this->mass_insert('postal_codes_germany', $postal_codes_cities);
    }

    private function read_cities() {
        $file =  file(__DIR__.'/../../../data/zuordnung_plz_ort_landkreis.csv');
        $lines = array_map('str_getcsv', $file);
        array_shift($lines); // remove headers
        $headers = ['osm_id', 'gemeindeschluessel', 'city', 'postal_code', 'landkreis', 'bundesland'];
        $list_of_dicts = [];
        foreach ($lines as $line) {
            $list_of_dicts[] = array_combine($headers, $line);
        }
        return $list_of_dicts;
    }

    private function readLocations() {
        $string = file_get_contents(__DIR__.'/../../../data/plz-5stellig-centroid.geojson');
        $json = json_decode($string, TRUE);
        $features = $json['features'];
        $result = [];
        foreach ($features as $feature) {
            $postal_code = $feature['properties']['plz'];
            $length = strlen($postal_code);
            $postal_code_description = substr($feature['properties']['note'], $length + 1);
            $result[$postal_code] = [
                'postal_code' => $postal_code,
                'postal_code_description' => $postal_code_description,
                'longitude' => $feature['geometry']['coordinates'][0],
                'latitude' => $feature['geometry']['coordinates'][1],
                'inhabitants' => $feature['properties']['einwohner'],
                'area_km2' => $feature['properties']['qkm']
            ];
        }
        return $result;
    }

    private function countCitiesByPostalCode(iterable $postal_codes_cities): array {
        $result = [];
        foreach ($postal_codes_cities as $city) {
            $postal_code = $city['postal_code'];
            if (!array_key_exists($postal_code, $result)) {
                $result[$postal_code] = 1;
            } else {
                $result[$postal_code]++;
            }
        }
        return $result;
    }

    private function mass_insert($table, $data) {
        foreach ($data as &$line) {
            $line['created_at'] = now();
            $line['updated_at'] = now();
        }
        $chunked_data = array_chunk($data, 500);
        DB::disableQueryLog();
        DB::beginTransaction();
        try {
            foreach ($chunked_data as $chunk) {
                DB::table($table)->insert($chunk);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}