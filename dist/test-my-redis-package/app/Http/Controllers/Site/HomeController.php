<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\RedisEntities\ProductRedisEntity;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    function index() {
        $productEntity = new ProductRedisEntity();

        $products = $productEntity->getAll();

        foreach ($products as $key => $product) {
            echo $product['title'] . "<br>";
            echo @number_format($product['price']) . "<br>";
            echo "<hr>";
        }
    }
}
