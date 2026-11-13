<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\User;
use App\Models\Wallet;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class WalletController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $wallets = Wallet::where('user_id', Auth::id())->get();
        if (!$wallets) {
            return response()->json([
                'status' => 'error',
                'message' => 'Forbidden access'
            ], 403);
        }

        $data = $wallets->map(function ($wallet) {
            $balance = $wallet->transaction->sum('amount') ?? null;
            $wallet->unsetRelation('transaction');
            return array_merge($wallet->toArray(), [
                'currency_code' => $wallet->currency->code ?? null,
                'balance' => $balance
            ]);
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Get all wallets successful',
            'data' => ['wallets' => $data]
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'currency_code' => 'required|exists:money_lover_currencies,code'
            ]);

            $currency = Currency::where('code', $request->currency_code)->firstOrFail();

            $wallet = Wallet::create([
                'user_id' => $request->user()->id,
                'currency_id' => $currency->id,
                'name' => $request->name,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Wallet added successfully',
                'data' => array_merge($wallet->toArray(), [
                    'currency_code' => $currency->code
                ])
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid field',
                'errors' => $e->errors()
            ], 422);
        }
    }


    public function update(Request $request, string $wallet)
    {
        try {
            $data = $request->validate(['name' => 'required|string|max:255']);

            $wallet = Wallet::find($wallet);
            if (!$wallet) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Not found'
                ], 404);
            }
            if (!$request->user() || (int) $wallet->user_id !== (int) Auth::id()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Forbidden access'
                ], 403);
            }

            $wallet->update($data);
            $wallet->refresh();
            $currency = $wallet->currency;
            $wallet->unsetRelation('currency');

            return response()->json([
                'status' => 'success',
                'message' => 'Wallet updated successful',
                'data' => array_merge($wallet->toArray(), [
                    'currency_code' => $currency->code ?? null
                ])
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid field',
                'errors' => $e->errors()
            ], 422);
        }
    }

    public function show(Request $request, string $wallet)
    {
        $wallet = Wallet::find($wallet);

        if (!$wallet) {
            return response()->json([
                'status' => 'error',
                'message' => 'Not found'
            ], 404);
        }
        if (!$request->user() || (int) $wallet->user_id !== auth()->id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Forbidden access'
            ], 403);
        }

        $balance = $wallet->transaction->sum('amount') ?? null;
        $wallet->unsetRelation('transaction');

        return response()->json([
            'status' => 'success',
            'message' => 'Get detail wallet successful',
            'data' => array_merge($wallet->toArray(), [
                'currency_code' => $wallet->currency->code ?? null,
                'balance' => $balance
            ])
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $wallet)
    {
        $wallet = Wallet::find($wallet);

        if (!$wallet) {
            return response()->json([
                'status' => 'error',
                'message' => 'Not found'
            ], 404);
        }
        if (!$request->user() || (int) $wallet->user_id !== auth()->id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Forbidden access'
            ], 403);
        }

        $wallet->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Wallet deleted successful',
        ]);
    }
}
