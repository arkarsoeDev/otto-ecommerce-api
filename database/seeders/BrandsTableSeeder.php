<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BrandsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $brands = ["Sony", "Samsung", "Mi"];

        foreach ($brands as $brand) {
            Brand::create([
                'title' => $brand,
                'slug' => Str::slug($brand)
            ]);
        }
    }
}
