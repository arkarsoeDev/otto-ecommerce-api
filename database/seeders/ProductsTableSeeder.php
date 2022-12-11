<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Photo;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds. 
     *
     * @return void
     */
    public function run()
    {
        // Laptops
        for ($i = 1; $i <= 30; $i++) {
            $product = Product::create([
                'name' => 'Laptop ' . $i,
                'slug' => 'laptop-' . $i,
                'stock' => rand(100,200),
                'details' => [13, 14, 15][array_rand([13, 14, 15])] . ' inch, ' . [1, 2, 3][array_rand([1, 2, 3])] . ' TB SSD, 32GB RAM',
                'price' => rand(149999, 249999),
                'description' => 'Lorem ' . $i . ' ipsum dolor sit amet, consectetur adipisicing elit. Ipsum temporibus iusto ipsa, asperiores voluptas unde aspernatur praesentium in? Aliquam, dolore!',
                'category_id' => Category::where('slug', 'laptop')->first()->id,
                'brand_id' => Brand::inRandomOrder()->first()->id,
            ]);
            Photo::create([
                "product_id" => $product->id,
                "name" => 'public/uploads/'.$product->slug.'.jpg'
            ]);
        }

        // Desktops
        for ($i = 1; $i <= 9; $i++) {
            $product = Product::create([
                'name' => 'Desktop ' . $i,
                'slug' => 'desktop-' . $i,
                'stock' => rand(100,200),
                'details' => [24, 25, 27][array_rand([24, 25, 27])] . ' inch, ' . [1, 2, 3][array_rand([1, 2, 3])] . ' TB SSD, 32GB RAM',
                'price' => rand(249999, 449999),
                'description' => 'Lorem ' . $i . ' ipsum dolor sit amet, consectetur adipisicing elit. Ipsum temporibus iusto ipsa, asperiores voluptas unde aspernatur praesentium in? Aliquam, dolore!',
                'category_id' => Category::where('slug', 'desktop')->first()->id,
                'brand_id' => Brand::inRandomOrder()->first()->id,
            ]);
            Photo::create([
                "product_id" => $product->id,
                "name" => 'public/uploads/'.$product->slug.'.jpg'
            ]);
        }

        // Phones
        for ($i = 1; $i <= 9; $i++) {
            $product = Product::create([
                'name' => 'Phone ' . $i,
                'slug' => 'phone-' . $i,
                'stock' => rand(100,200),
                'details' => [16, 32, 64][array_rand([16, 32, 64])] . 'GB, 5.' . [7, 8, 9][array_rand([7, 8, 9])] . ' inch screen, 4GHz Quad Core',
                'price' => rand(79999, 149999),
                'description' => 'Lorem ' . $i . ' ipsum dolor sit amet, consectetur adipisicing elit. Ipsum temporibus iusto ipsa, asperiores voluptas unde aspernatur praesentium in? Aliquam, dolore!',
                'category_id' => Category::where('slug', 'phone')->first()->id,
                'brand_id' => Brand::inRandomOrder()->first()->id,
            ]);
            Photo::create([
                "product_id" => $product->id,
                "name" => 'public/uploads/'.$product->slug.'.jpg'
            ]);
        }

        // Tablets
        for ($i = 1; $i <= 9; $i++) {
            $product = Product::create([
                'name' => 'Tablet ' . $i,
                'slug' => 'tablet-' . $i,
                'stock' => rand(100,200),
                'details' => [16, 32, 64][array_rand([16, 32, 64])] . 'GB, 5.' . [10, 11, 12][array_rand([10, 11, 12])] . ' inch screen, 4GHz Quad Core',
                'price' => rand(49999, 149999),
                'description' => 'Lorem ' . $i . ' ipsum dolor sit amet, consectetur adipisicing elit. Ipsum temporibus iusto ipsa, asperiores voluptas unde aspernatur praesentium in? Aliquam, dolore!',
                'category_id' => Category::where('slug', 'tablet')->first()->id,
                'brand_id' => Brand::inRandomOrder()->first()->id,
            ]);
            Photo::create([
                "product_id" => $product->id,
                "name" => 'public/uploads/'.$product->slug.'.jpg'
            ]);
        }

        // TVs
        for ($i = 1; $i <= 9; $i++) {
            $product = Product::create([
                'name' => 'TV ' . $i,
                'slug' => 'tv-' . $i,
                'stock' => rand(100, 200),
                'details' => [46, 50, 60][array_rand([7, 8, 9])] . ' inch screen, Smart TV, 4K',
                'price' => rand(79999, 149999),
                'description' => 'Lorem ' . $i . ' ipsum dolor sit amet, consectetur adipisicing elit. Ipsum temporibus iusto ipsa, asperiores voluptas unde aspernatur praesentium in? Aliquam, dolore!',
                'category_id' => Category::where('slug', 'tv')->first()->id,
                'brand_id' => Brand::inRandomOrder()->first()->id,
            ]);

            Photo::create([
                "product_id" => $product->id,
                "name" => 'public/uploads/'.$product->slug.'.jpg'
            ]);
        }

        // Cameras
        for ($i = 1; $i <= 9; $i++) {
            $product = Product::create([
                'name' => 'Camera ' . $i,
                'slug' => 'camera-' . $i,
                'stock' => rand(100,200),
                'details' => 'Full Frame DSLR, with 18-55mm kit lens.',
                'price' => rand(79999, 249999),
                'description' => 'Lorem ' . $i . ' ipsum dolor sit amet, consectetur adipisicing elit. Ipsum temporibus iusto ipsa, asperiores voluptas unde aspernatur praesentium in? Aliquam, dolore!',
                'category_id' => Category::where('slug','camera')->first()->id,
                'brand_id' => Brand::inRandomOrder()->first()->id,
            ]);
            Photo::create([
                "product_id" => $product->id,
                "name" => 'public/uploads/'.$product->slug.'.jpg'
            ]);
        }
    }
}
