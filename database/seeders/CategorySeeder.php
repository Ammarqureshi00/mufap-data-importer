<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Mf_Daily_Stats;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $allCategories = Mf_Daily_Stats::select('category')->distinct()->get();

        foreach ($allCategories as $c) {
            if ($c->category) {
                Category::firstOrCreate(['name' => $c->category]);
            }
        }
    }
}
