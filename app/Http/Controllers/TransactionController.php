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
        $user = $request->user();
        $data = $request->validate([
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1',
            'month' => 'sometimes|integer|min:1|max:12',
            'year' => 'sometimes|integer|min:1',
        ]);

        $query = Transaction::with(['wallet.currency:id,code', 'category'])->whereHas('wallet', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        });
        if ($request->filled('month')) {
            $query->whereMonth('date', $request->month);
        }
        if ($request->filled('year')) {
            $query->whereYear('date', $request->year);
        }
        $transactions = $query->orderBy('date', 'desc')->paginate($request->input('per_page', 25));
        $transactions->through(function ($transaction) {
            if ($transaction->wallet) {
                $transaction->wallet->setAttribute('currency_code', $transaction->wallet->currency->code ?? null);
                $transaction->wallet->makeHidden('currency_id', 'currency');
                return $transaction;
            }
        });

        return response()->json([
            $transactions
        ], 200);
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
            'date' => 'required|date|date_format:Y-m-d',
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
    public function destroy(Request $request, string $id)
    {
        $transaction = Transaction::find($id);
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
