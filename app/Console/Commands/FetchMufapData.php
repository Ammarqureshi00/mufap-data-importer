<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MufapService;

class FetchMufapData extends Command
{
    protected $signature = 'mufap:fetch {date?}';
    protected $description = 'Fetch daily Mufap data';

    public function handle(MufapService $service)
    {
        $date = $this->argument('date') ?? date('Y-m-d');
        $service->fetchAndStore($date);
        $this->info("Mufap data for {$date} fetched and stored successfully.");
    }
}
