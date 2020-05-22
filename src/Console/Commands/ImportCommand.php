<?php

namespace Kampakit\PostalCodesGermany\Console\Commands;
use Illuminate\Console\Command;

class ImportCommand extends Command
{
    protected $signature = 'postal-codes-germany:import';

    protected $description = 'Import postal code data from source url set in config/postal-codes-germany.php';

    public function handle() {
        $this->downloadData();
    }

    private function downloadData() {
        $source_url = config('postal-codes-germany.source_url');
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $source_url);
        $result = curl_exec($curl);
        var_dump($result);

    }
}