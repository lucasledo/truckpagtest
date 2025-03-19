<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Models\Product;
use Carbon\Carbon;

class ImportProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to import products from Open Food Facts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $url = 'https://challenges.coode.sh/food/data/json/index.txt';

        $response = Http::get($url);
        $files    = explode("\n", $response->body());

        foreach ($files as $key => $file) {
            try {
                if (empty($file)) {
                    continue;
                }

                $this->info("Processing file: $file");

                $zipUrl = "https://challenges.coode.sh/food/data/json/$file";
                $zipContents = Http::get($zipUrl)->body();

                $tempZipPath = storage_path("app/products.gz");
                file_put_contents($tempZipPath, $zipContents);

                $this->info('Descompacting');

                $this->extractGzFile($tempZipPath);
                $jsonFilePath   = storage_path('app/products.json');
                $jsonData       = explode("\n", file_get_contents($jsonFilePath));

                $this->info('Importing...');
                foreach ($jsonData as $k => $json) {
                    if ($k == 100) break;

                    $productData = json_decode($json, true);

                    if(is_null($productData)) continue;

                    $productData['imported_t']  = Carbon::now();
                    $productData['status']      = 'draft';

                    Product::updateOrCreate(['code' => $productData['code']], $productData);
                }

                $this->info("Import of $k products from file $file has been finished.");
            } catch (\Throwable $th) {
                report($th);
                $this->error($th->getMessage());
                //TODO NOtification
                continue;
            }
        }

        $this->info('All imports have been finished.');
    }

    /**
     * Function to descompact .gz.
     *
     * @param string $gzFilePath
     * @return void
     */
    private function extractGzFile($gzFilePath)
    {
        $destinationFile = storage_path('app/products.json');

        $gz = gzopen($gzFilePath, 'rb');
        if (!$gz) {
            $this->error("Error to open file .gz: {$gzFilePath}");
            throw new Exception("Error to open file .gz: {$gzFilePath}", 500);
            return;
        }

        $destination = fopen($destinationFile, 'wb');
        if (!$destination) {
            $this->error("Error to create destination file: {$destinationFile}");
            throw new Exception("Error to create destination file: {$destinationFile}", 500);
            gzclose($gz);
            return;
        }

        while (!gzeof($gz)) {
            fwrite($destination, gzread($gz, 1024));
        }

        gzclose($gz);
        fclose($destination);
    }
}
