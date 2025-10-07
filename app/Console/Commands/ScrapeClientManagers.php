<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use App\Models\ClientManager;
use Illuminate\Support\Str;

class ScrapeClientManagers extends Command
{
    protected $signature = 'scrape:client-managers {name=Wong}';
    protected $description = 'Scrape client managers from estation.time.com.my';

    public function handle()
    {
        $searchName = $this->argument('name');
        $url = "https://estation.time.com.my/employee-search-result?name=" . urlencode($searchName) . "&staff_no=&division=";

        $client = new Client(['verify' => false]); 
        $response = $client->get($url); 
        $html = (string) $response->getBody();

        $crawler = new Crawler($html);

        
        $crawler->filter('table tbody tr')->each(function ($row) {
            $columns = $row->filter('td')->each(fn ($td) => trim($td->text()));

            if (count($columns) >= 6) {
                $staffNo = $columns[1];
                $name = $columns[2];
                $division = $columns[3];
                $department = $columns[4];
                $mobile = $columns[5];

                $email = strtolower(str_replace(' ', '.', $name)) . '@time.com.my';

                ClientManager::updateOrCreate(
                    ['staff_no' => $staffNo],
                    [
                        'id' => Str::uuid()->toString(),
                        'name' => $name,
                        'division' => $division,
                        'department' => $department,
                        'email' => $email,
                        'personal_contact' => $mobile,
                    ]
                );

                $this->info("✅ Saved: $name ($staffNo)");
            }
        });

        $this->info('✔️ Done scraping.');
    }
}
