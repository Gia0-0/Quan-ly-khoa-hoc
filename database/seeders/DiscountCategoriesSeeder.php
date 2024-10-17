<?php

namespace Database\Seeders;

use App\Models\Discount;
use App\Models\DiscountCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DiscountCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DiscountCategory::factory(50)->create();
    }
}
