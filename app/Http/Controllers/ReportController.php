<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function expense(Request $request)
    {
        return $this->summaryByCategory($request, 'EXPENSE');
    }
    public function income(Request $request)
    {
        return $this->summaryByCategory($request, 'INCOME');
    }
    public function summaryByCategory(Request $request, $type)
    {
        $data = $request->validate([
            'month' => 'sometimes|integer|min:1|max:12',
            'year' => 'sometimes|integer|min:1',
        ]);
        $user = $request->user();
        $query = Transaction::with('category')
            ->whereHas('wallet', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->whereHas('category', function ($query) use ($type) {
                $query->where('type', $type);
            });
        if (isset($data['month'])) {
            $query->whereMonth('date', $data['month']);
        }
        if (isset($data['year'])) {
            $query->whereYear('date', $data['year']);
        }
        $summary = $query
            ->selectRaw('category_id, SUM(amount) as amount')
            ->groupBy('category_id')
            ->get();
        return response()->json([
            'status' => 'success',
            'message' => "Get summary by {$type} category successful",
            'data' => [
                'summary' => $summary->map(function ($item) {
                    return [
                        'category' => $item->category,
                        'amount' => (float) $item->amount,
                    ];
                }),
            ],
        ], 200);
    }
}
