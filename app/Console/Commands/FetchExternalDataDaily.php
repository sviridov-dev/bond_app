<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;
use App\Models\Bonds; // Assuming you have an Bonds model for the database

class FetchExternalDataDaily extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-external-data-daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $httpClient = HttpClient::create();
        $page = 1;

        Bonds::truncate(); // Clear the table before inserting new data

        while (true) {
            $url = "https://smart-lab.ru/q/bonds/order_by_val_to_day/desc/page{$page}";
            $response = $httpClient->request('GET', $url);

            $html = $response->getContent();

            $crawler = new Crawler($html);

            $rows = $crawler->filter('div.main__table table > tbody > tr');

            if ($rows->count() <= 2) {
                $this->info("No data.");
                break; // no more data
            }

            $rows->each(function (Crawler $row, $i) {
                if ($i === 0) return; // skip header

                $columns = $row->filter('td');
                
                $link = $columns->eq(1)->filter('a');
                $code = $link->attr('href'); // href link of a tag
                $ticker = $link->text(); // value of a tag
                $ISIN = ""; // last parameter in href link of a tag
                $issuer_information = $link->attr('title'); // title of a tag

                //? $currency = trim($columns->eq(5)->text()); // related to ISIN
                $rating = trim($columns->eq(7)->text());
                $price = trim($columns->eq(11)->text());
                //? $yield_maturity = trim($columns->eq()->text());
                $coupon_rate = trim($columns->eq(12)->text());
                $volume = trim($columns->eq(9)->text());
                $duration = trim($columns->eq(3)->text());
                $maturity_date = trim($columns->eq(15)->text());
                $next_offer_date = trim($columns->eq(16)->text());
                //? $additional_info = trim($columns->eq()->text());

                Bonds::updateOrCreate(
                    ['code' => $code],
                    [
                        'ticker' => $ticker,
                        'ISIN' => $ISIN,
                        'issuer_information' => $issuer_information,
                        // 'currency' => $currency,
                        'rating' => $rating,
                        'price' => floatval($price),
                        'maturity_date' => format_date($maturity_date),
                        'next_offer_date' => format_date($next_offer_date),
                        // 'additional_info' => $additional_info,
                        // 'yield_maturity' => $yield_maturity,
                        'coupon_rate' => floatval($coupon_rate),
                        'volume' => floatval($volume),
                        'duration' => floatval($duration),
                    ]
                );
            });

            $this->info("Scraped page {$page}");
            $page++;
        }

        $this->info("Scraping complete.");
    }
}
