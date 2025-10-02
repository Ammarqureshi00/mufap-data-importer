<?php

namespace App\Services;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\DomCrawler\Crawler;
use App\Models\MufapData;

class MufapService
{
      public function fetchAndStore($date)
      {
            $client = HttpClient::create();

            // Use the $date parameter dynamically
            $url = "https://www.mufap.com.pk/Industry/IndustryStatDaily?tab=3&AMCId=null&fundId=null&datefrom=2025-09-24&datetill=2025-09-24";

            $response = $client->request('GET', $url);
            $html = $response->getContent();

            $crawler = new Crawler($html);

            // Loop through table rows
            $crawler->filter('table tbody tr')->each(function (Crawler $row) {
                  $columns = $row->filter('td')->each(fn($td) => trim($td->text()));

                  // Skip empty rows
                  if (empty($columns) || count($columns) < 12) {
                        return;
                  }

                  // Prepare data array
                  $data = [
                        'fund'           => $columns[0],
                        'validity_date'  => $this->formatDate($columns[6]),
                        'category'       => $columns[1],
                        'inception_date' => $this->formatDate($columns[2]),
                        'offer'          => $this->toFloat($columns[3]),
                        'repurchase'     => $this->toFloat($columns[4]),
                        'nav'            => $this->toFloat($columns[5]),
                        'front_end'      => $this->toFloat($columns[7]),
                        'back_end'       => $this->toFloat($columns[8]),
                        'contingent'     => $this->toFloat($columns[9]),
                        'market'         => $columns[10],
                        'trustee'        => $columns[11],
                  ];

                  // Insert or update database
                  MufapData::updateOrCreate(
                        ['fund' => $data['fund'], 'validity_date' => $data['validity_date']],
                        $data
                  );
            });
      }

      // Helper: convert string to float safely
      private function toFloat($value)
      {
            $cleaned = str_replace(',', '', $value);

            // If the value is empty or not numeric, return null
            return is_numeric($cleaned) ? floatval($cleaned) : null;
      }

      // Helper: format date safely
      private function formatDate($value)
      {
            $timestamp = strtotime($value);
            return $timestamp ? date('Y-m-d', $timestamp) : null;
      }
}
