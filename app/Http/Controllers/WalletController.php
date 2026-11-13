<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WalletController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $wallet = Wallet::where('user_id', $request->user())->with('currency:currency_code')->get();
        if (!$wallet) {
            return response()->json([
                'status' => 'error',
                'message' => 'Not found'
            ], 404);
        }
        if (!$wallet->where('user_id', $request->user())) {
            return response()->json([
                'status' => 'error',
                'Forbidden access'
            ], 403);
        }

        $balance = $wallet;

        return response()->json([
            'status' => 'success',
            'message' => 'Get all wallets successful',
            'data' => array_merge($wallet->toArray(), ['balance' => $balance])
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'currency_code' => 'required|string'
        ]);

        if ($data->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid field',
                'errors' => $data->errors()
            ]);
        }

        $wallet = Wallet::create([
            'user_id' => $request->user(),
            'currency_id' => Currency::where('code', $request->currency_code),
            'name' => $request->name,
        ]);
        $dataWallet = $wallet->with('currency:currency_code');

        return response()->json([
            'status' => 'success',
            'message' => 'Wallet added successfully',
            'data' => $dataWallet
        ], 201);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Wallet $wallet)
    {
        $data = $request->validate(['name' => 'required|string|max:255']);

        $wallet = Wallet::where('user_id', $request->user())->findOrFail($wallet);
        if (!$wallet) {
            return response()->json([
                'status' => 'error',
                'message' => 'Not found'
            ], 404);
        }
        if (!$wallet->where('user_id', $request->user())) {
            return response()->json([
                'status' => 'error',
                'Forbidden access'
            ], 403);
        }

        $wallet->update($data);
        $wallet->refresh();

        return response()->json([
            'status' => 'success',
            'message' => 'Wallet updated successful',
            'data' => $wallet
        ]);
    }

    public function show(Request $request, Wallet $wallet)
    {
        $wallet = Wallet::where('user_id', $request->user())->with('currency:currency_code')->findOrFail($wallet);
        if (!$wallet) {
            return response()->json([
                'status' => 'error',
                'message' => 'Not found'
            ], 404);
        }
        if (!$wallet->where('user_id', $request->user())) {
            return response()->json([
                'status' => 'error',
                'Forbidden access'
            ], 403);
        }

        $balance = $wallet;

        return response()->json([
            'status' => 'success',
            'message' => 'Get detail wallet successful',
            'data' => $wallet
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Wallet $wallet)
    {
        $wallet = Wallet::where('user_id', $request->user())->findOrFail($wallet);
        if (!$wallet) {
            return response()->json([
                'status' => 'error',
                'message' => 'Not found'
            ], 404);
        }
        if (!$wallet->where('user_id', $request->user())) {
            return response()->json([
                'status' => 'error',
                'Forbidden access'
            ], 403);
        }

        $wallet->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Wallet deleted successful',
        ]);
    }
}
