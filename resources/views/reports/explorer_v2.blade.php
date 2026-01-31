@extends('layouts.master')

@section('content')

<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h3>Financial Reports</h3>
            <p class="text-muted small">Explore and Generate Professional Enterprise-Grade Financial Reports.</p>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item active">Reporting</li>
            </ol>
        </div>
    </div>
</div>

<div class="container-fluid">
    <!-- Row 1 -->
    <div class="row">
        <!-- Trial Balance -->
        <div class="col-md-4 mb-4">
            <div class="card text-center h-100 shadow-sm">
                <div class="card-body d-flex flex-column">
                    <div class="mb-3 text-primary">
                        <i class="fas fa-list-ol fa-3x"></i>
                    </div>
                    <h5 class="card-title font-weight-bold">Trial Balance</h5>
                    <p class="card-text text-muted flex-grow-1">Consolidated summary of all ledger balances.</p>
                    <a href="{{ route('reports.trial_balance') }}" class="btn btn-primary mt-3 rounded-pill px-4">
                        Configure
                    </a>
                </div>
            </div>
        </div>

        <!-- Income Statement -->
        <div class="col-md-4 mb-4">
            <div class="card text-center h-100 shadow-sm">
                <div class="card-body d-flex flex-column">
                    <div class="mb-3 text-success">
                        <i class="fas fa-chart-line fa-3x"></i>
                    </div>
                    <h5 class="card-title font-weight-bold">Income Statement</h5>
                    <p class="card-text text-muted flex-grow-1">Statement of Profit or Loss and Other Comprehensive Income.</p>
                    <a href="{{ route('reports.income_statement') }}" class="btn btn-success mt-3 rounded-pill px-4">
                        Configure
                    </a>
                </div>
            </div>
        </div>

        <!-- Balance Sheet -->
        <div class="col-md-4 mb-4">
            <div class="card text-center h-100 shadow-sm">
                <div class="card-body d-flex flex-column">
                    <div class="mb-3 text-info">
                        <i class="fas fa-balance-scale fa-3x"></i>
                    </div>
                    <h5 class="card-title font-weight-bold">Balance Sheet</h5>
                    <p class="card-text text-muted flex-grow-1">Statement of Financial Position (Assets, Liabilities & Equity).</p>
                    <a href="{{ route('reports.balance_sheet') }}" class="btn btn-info mt-3 rounded-pill px-4">
                        Configure
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 2 -->
    <div class="row">
        <!-- General Ledger -->
        <div class="col-md-4 mb-4">
            <div class="card text-center h-100 shadow-sm">
                <div class="card-body d-flex flex-column">
                    <div class="mb-3 text-warning">
                        <i class="fas fa-book fa-3x"></i>
                    </div>
                    <h5 class="card-title font-weight-bold">General Ledger</h5>
                    <p class="card-text text-muted flex-grow-1">Detailed transaction history for specific accounts.</p>

                    <a href="{{ route('reports.general_ledger') }}" class="btn btn-warning mt-3 rounded-pill px-4 text-white">
                        Configure
                    </a>
                </div>
            </div>
        </div>

        <!-- Cash Flow Statement (IFRS) -->
        <div class="col-md-4 mb-4">
            <div class="card text-center h-100 shadow-sm">
                <div class="card-body d-flex flex-column">
                    <div class="mb-3 text-secondary">
                        <i class="fas fa-money-bill-wave fa-3x"></i>
                    </div>
                    <h5 class="card-title font-weight-bold">Statement of Cash Flows</h5>
                    <p class="card-text text-muted flex-grow-1">Inflows and outflows of cash and cash equivalents.</p>

                    <a href="{{ route('reports.cash_flow') }}" class="btn btn-secondary mt-3 rounded-pill px-4">
                        Configure
                    </a>
                </div>
            </div>
        </div>

        <!-- Changes in Equity (IFRS) -->
        <div class="col-md-4 mb-4">
            <div class="card text-center h-100 shadow-sm">
                <div class="card-body d-flex flex-column">
                    <div class="mb-3 text-purple" style="color: #6f42c1;">
                        <i class="fas fa-chart-pie fa-3x"></i>
                    </div>
                    <h5 class="card-title font-weight-bold">Changes in Equity</h5>
                    <p class="card-text text-muted flex-grow-1">Statement of Changes in Equity.</p>

                    <a href="{{ route('reports.changes_in_equity') }}" class="btn mt-3 rounded-pill px-4 text-white" style="background-color: #6f42c1;">
                        Configure
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection