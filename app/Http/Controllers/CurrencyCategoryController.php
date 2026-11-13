<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CurrencyCategoryController extends Controller
{
    public function currency()
    {
        if (Auth::user()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthenticated'
            ], 401);
        }
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
