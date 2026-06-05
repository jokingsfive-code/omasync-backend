<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TransactionRequest;
use App\Services\FinanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FinanceController extends Controller
{
    protected FinanceService $financeService;

    public function __construct(FinanceService $financeService)
    {
        $this->financeService = $financeService;
    }

    public function index(Request $request): JsonResponse
    {
        $transactions = $this->financeService->getAllTransactions($request->all());
        return response()->json($transactions);
    }

    public function store(TransactionRequest $request): JsonResponse
    {
        $transaction = $this->financeService->createTransaction($request->validated());
        return response()->json($transaction, 201);
    }

    public function show(int $id): JsonResponse
    {
        $transaction = $this->financeService->getTransactionById($id);
        return response()->json($transaction);
    }

    public function update(TransactionRequest $request, int $id): JsonResponse
    {
        $transaction = $this->financeService->updateTransaction($id, $request->validated());
        return response()->json($transaction);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->financeService->deleteTransaction($id);
        return response()->json(null, 204);
    }

    public function summary(Request $request): JsonResponse
    {
        $summary = $this->financeService->getFinancialSummary($request->all());
        return response()->json($summary);
    }
}
