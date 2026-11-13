<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $transactions = Transaction::with('wallet', 'category');

        return response()->json([
            $transactions
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = Validator::make($request->all(), [
            'wallet_id' => 'required|exists:money_lover_wallets,id',
            'category_id' => 'required|exists:money_lover_categories,id',
            'amount' => 'required|integer|min:1',
            'date' => 'required|date|format:Ymd',
            'note' => 'nullable|string'
        ]);

        if ($data->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid field',
                'errors' => $data->errors()
            ]);
        }

        $transaction = Transaction::create($data->validated());

        return response()->json([
            'status' => 'Success',
            'message' => 'Transaction added successful',
            'data' => $transaction
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Transaction $transaction)
    {
        $transaction = Transaction::where('user_id', $request->user())->findOrFail($transaction);
        if (!$transaction) {
            return response()->json([
                'status' => 'error',
                'message' => 'Not found'
            ], 404);
        }
        if (!$transaction->where('user_id', $request->user())) {
            return response()->json([
                'status' => 'error',
                'Forbidden access'
            ], 403);
        }

        $transaction->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Transaction deleted successful',
        ]);
    }
}
