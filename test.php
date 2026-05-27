<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$order = App\Models\Order::where('customer_type', 'dealer')->withCount('items')->having('items_count', '>=', 10)->orderBy('id', 'desc')->first();
if($order) {
    $order->load('items');
    echo json_encode($order->toArray(), JSON_PRETTY_PRINT);
} else {
    echo 'No B2B orders found.';
}
