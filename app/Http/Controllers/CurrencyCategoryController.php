<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Currency;
use Illuminate\Http\Request;

class CurrencyCategoryController extends Controller
{
    public function currency()
    {
        $currency = Currency::get();

        return response()->json([
            'status' => 'success',
            'message' => 'Get all currencies successful',
            'data' => ['currencies' => $currency]
        ]);
    }

    public function category()
    {
        $category = Category::get();

        return response()->json([
            'status' => 'success',
            'message' => 'Get all categories successful',
            'data' => ['categories' => $category]
        ]);
    }
}
