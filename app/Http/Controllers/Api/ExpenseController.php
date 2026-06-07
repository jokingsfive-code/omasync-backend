<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index()
    {
        return Expense::with('property')
            ->latest()
            ->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'nullable|exists:properties,id',
            'category' => 'required',
            'description' => 'required',
            'amount' => 'required|numeric',
            'expense_date' => 'required|date',
        ]);

        return Expense::create($validated);
    }

    public function update(Request $request, $id)
    {
        $expense = Expense::findOrFail($id);

        $expense->update($request->all());

        return $expense;
    }

    public function destroy($id)
    {
        Expense::findOrFail($id)->delete();

        return response()->json([
            'message' => 'Deleted'
        ]);
    }
}