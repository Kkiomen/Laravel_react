<?php

namespace Database\Seeders;

use App\Models\Avatar;
use App\Models\Client;
use App\Models\Order;
use App\Models\OrderPosition;
use App\Models\Product;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(ProductSeeder::class);

        $products = Product::get();
        $clients = Client::get();

        for($i = 0; $i < 60; $i++) {
            $order = new Order();
            $client = $clients[rand(0, count($clients) - 1)];
            $order->client_id = $client->id;
            $order->invoice_symbol = 'FV/' . rand(1000, 9999) . '/' . rand(1000, 9999) . '/' . rand(1000, 9999);
            $order->invoice_place = 'Warszawa';
            $order->invoice_date = now()->subDays(rand(1, 460));
            $order->payment_date = now()->addDays(rand(1, 43));
            $order->delivery_address = fake()->address;
            $order->delivery_city = fake()->city;
            $order->delivery_postal_code = fake()->postcode;
            $order->delivery_country = $client->country;
            $order->delivery_phone = fake()->phoneNumber;
            $order->delivery_email = fake()->email;
            $order->delivery_first_name = fake()->firstName;
            $order->delivery_last_name = fake()->lastName;
            $order->positions_count = 0;
            $order->positions_price_net_total = 0;
            $order->positions_price_gross_total = 0;

            $orderPriceNet = 0;
            $orderPriceGross = 0;

            $order->save();
            $countOrderPositions = rand(2,17);
            for($p = 0; $p < $countOrderPositions; $p++) {
                $randomProduct = $products[rand(0, count($products) - 1)];
                $orderPosition = new OrderPosition();
                $orderPosition->order_id = $order->id;
                $orderPosition->product_id = $randomProduct->id;
                $orderPosition->quantity = rand(1, 15);
                $orderPosition->price_net = $randomProduct->price_net;
                $orderPosition->price_gross = $randomProduct->price_gross;
                $orderPosition->tax = 23;
                $orderPosition->price_net_total = $orderPosition->price_net * $orderPosition->quantity;
                $orderPosition->price_gross_total = $orderPosition->price_gross * $orderPosition->quantity;
                $orderPosition->tax_value_total = $orderPosition->price_gross_total - $orderPosition->price_net_total;
                $orderPosition->save();

                $orderPriceNet += $orderPosition->price_net_total;
                $orderPriceGross += $orderPosition->price_gross_total;
            }

            $order->positions_count = $countOrderPositions;
            $order->positions_price_net_total = $orderPriceNet;
            $order->positions_price_gross_total = $orderPriceGross;
            $order->save();
        }

//        $this->call(ClientSeeder::class);
//        $this->call(ComboOrderSeeder::class);
//        $this->call(AssistantSeeder::class);
//        $this->call(LongTermMemorySeeder::class);
//        $this->call(LongTermMemorySeeder::class);
    }
}
