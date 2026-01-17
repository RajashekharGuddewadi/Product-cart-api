<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'Apple iPhone 15',
                'sku' => 'IPH15-128',
                'price' => 79999.00,
                'stock' => 25,
                'is_active' => true,
            ],
            [
                'name' => 'Samsung Galaxy S24',
                'sku' => 'SGS24-256',
                'price' => 74999.00,
                'stock' => 20,
                'is_active' => true,
            ],
            [
                'name' => 'OnePlus 12',
                'sku' => 'OP12-256',
                'price' => 65999.00,
                'stock' => 18,
                'is_active' => true,
            ],
            [
                'name' => 'Google Pixel 8',
                'sku' => 'PIX8-128',
                'price' => 69999.00,
                'stock' => 15,
                'is_active' => true,
            ],
            [
                'name' => 'Apple MacBook Air M2',
                'sku' => 'MBA-M2',
                'price' => 114999.00,
                'stock' => 10,
                'is_active' => true,
            ],
            [
                'name' => 'Dell XPS 13',
                'sku' => 'DX13-9310',
                'price' => 104999.00,
                'stock' => 8,
                'is_active' => true,
            ],
            [
                'name' => 'HP Pavilion 15',
                'sku' => 'HP-PAV15',
                'price' => 64999.00,
                'stock' => 12,
                'is_active' => true,
            ],
            [
                'name' => 'Lenovo ThinkPad X1',
                'sku' => 'LTP-X1',
                'price' => 124999.00,
                'stock' => 6,
                'is_active' => true,
            ],
            [
                'name' => 'Sony WH-1000XM5 Headphones',
                'sku' => 'SONY-XM5',
                'price' => 29999.00,
                'stock' => 30,
                'is_active' => true,
            ],
            [
                'name' => 'Apple AirPods Pro (2nd Gen)',
                'sku' => 'APP-2GEN',
                'price' => 24999.00,
                'stock' => 40,
                'is_active' => true,
            ],
            [
                'name' => 'Samsung Galaxy Watch 6',
                'sku' => 'SGW-6',
                'price' => 28999.00,
                'stock' => 22,
                'is_active' => true,
            ],
            [
                'name' => 'Apple iPad Air',
                'sku' => 'IPAD-AIR',
                'price' => 58999.00,
                'stock' => 16,
                'is_active' => true,
            ],
            [
                'name' => 'Amazon Echo Dot (5th Gen)',
                'sku' => 'ECHO-DOT5',
                'price' => 4999.00,
                'stock' => 50,
                'is_active' => true,
            ],
            [
                'name' => 'Logitech MX Master 3S Mouse',
                'sku' => 'LOGI-MX3S',
                'price' => 9999.00,
                'stock' => 35,
                'is_active' => true,
            ],
            [
                'name' => 'Samsung 27" Curved Monitor',
                'sku' => 'SAM-CUR27',
                'price' => 21999.00,
                'stock' => 14,
                'is_active' => true,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
