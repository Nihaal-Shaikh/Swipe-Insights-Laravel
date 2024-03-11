<?php

namespace Database\Seeders;

use App\Models\Image;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\File;

class ImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $client = new Client();

        $imagesDirectory = storage_path('app/public/images');
        File::makeDirectory($imagesDirectory, $mode = 0777, true, true);

        foreach (range(1, 50) as $index) {
            $randomSize = $faker->numberBetween(400, 1200); // Adjust the range as needed
            $response = $client->get("https://source.unsplash.com/random/{$randomSize}x{$randomSize}");
            $imageData = $response->getBody()->getContents();

            // Save the image to your storage or public directory
            $imageName = "image_$index.jpg";
            $path = "$imagesDirectory/$imageName";
            file_put_contents($path, $imageData);

            Image::create([
                'customer_id' => $faker->numberBetween(1, 5),
                'image_name' => $imageName,
                'image_status_id' => $faker->numberBetween(1, 4),
                'operator_id' => $faker->numberBetween(1, 5),
                'active' => $faker->boolean(),
                'created_by' => null,
                'updated_by' => null,
            ]);
        }
    }
}
