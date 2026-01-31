<?php

use Illuminate\Support\Facades\Route;
use Nexterp\JasperReports\Http\Controllers\FinancialReportController;

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/reporting', [FinancialReportController::class, 'index'])->name('reporting.index');

    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('trial-balance', [FinancialReportController::class, 'trialBalance'])->name('trial_balance');
        Route::get('income-statement', [FinancialReportController::class, 'incomeStatement'])->name('income_statement');
        Route::get('balance-sheet', [FinancialReportController::class, 'balanceSheet'])->name('balance_sheet');
        Route::get('general-ledger', [FinancialReportController::class, 'generalLedger'])->name('general_ledger');
        Route::get('cash-flow', [FinancialReportController::class, 'cashFlow'])->name('cash_flow');
        Route::get('changes-in-equity', [FinancialReportController::class, 'changesInEquity'])->name('changes_in_equity');
    });
});
