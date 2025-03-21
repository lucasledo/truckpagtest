<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Models\Product;
use App\Models\ImportProductHistory;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\ProductSyncFailed;

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

        $currentFile        = '';
        $products_imported  = 0;

        foreach ($files as $key => $file) {
            try {
                $currentFile = $file;

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
                $products_imported = 0;
                foreach ($jsonData as $k => $json) {
                    $products_imported = $k;
                    if ($k == 100) break;

                    $productData = json_decode($json, true);

                    if(is_null($productData)) continue;

                    $productData['code']        = isset($productData['code']) ? str_replace('"', "", $productData['code']) : null;
                    $productData['imported_t']  = Carbon::now();
                    $productData['status']      = 'draft';

                    Product::updateOrCreate(['code' => $productData['code']], $productData);
                }

                $this->info("Import of $k products from file $file has been finished.");

                ImportProductHistory::create([
                    'date'              => Carbon::now(),
                    'status'            => 'completed',
                    'products_imported' => $products_imported,
                    'file'              => $currentFile,
                    'message'           => "Import of $k products from file $file has been finished."
                ]);

            } catch (\Throwable $th) {
                report($th);
                $this->error($th->getMessage());

                // Send email alert
                Mail::to(env('EMAIL_NOTIFICATION'))->send(new ProductSyncFailed($th));

                ImportProductHistory::create([
                    'date'              => Carbon::now(),
                    'status'            => 'failed',
                    'products_imported' => $products_imported,
                    'file'              => $currentFile,
                    'message'           => $th->getMessage()
                ]);

                return;
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
