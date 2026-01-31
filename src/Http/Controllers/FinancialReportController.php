<?php

namespace Nexterp\JasperReports\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Nexterp\JasperReports\JasperReportService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;

class FinancialReportController extends Controller
{
    protected $jasper;

    public function __construct(JasperReportService $jasper)
    {
        $this->jasper = $jasper;
    }

    protected function getReportPath($filename)
    {
        // Single source of truth: application's resources/reports directory
        return resource_path("reports/{$filename}");
    }

    protected function getOutputDetails($name)
    {
        $directory = storage_path('app/reports');
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }
        return $directory . '/' . $name . '_' . time();
    }

    protected function getCommonParams(Request $request)
    {
        $company = \App\Models\CompanyDetail::first();

        $year = $request->query('financial_year');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        // Logic: specific dates take precedence, then financial year, then relative year
        if ($dateFrom && $dateTo) {
            // Dates are set, stick with them
        } elseif ($year) {
            $dateFrom = Carbon::createFromDate($year, 1, 1)->startOfDay()->toDateString();
            $dateTo = Carbon::createFromDate($year, 12, 31)->endOfDay()->toDateString();
        } else {
            // Default to current year
            $dateFrom = Carbon::now()->startOfYear()->toDateString();
            $dateTo = Carbon::now()->endOfDay()->toDateString();
            $year = Carbon::now()->year;
        }

        return [
            'company_name' => $company ? $company->name : config('app.name', 'NextERP'),
            'company_address' => $company ? $company->address : 'Generated Report',
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'financial_year' => (string) $year,
            'logo' => public_path('logo.png'),
        ];
    }

    protected function handleRequest(Request $request, $reportType, $reportName, $routeName)
    {
        // If 'generate' flag is not present, show the selection form
        if (!$request->has('generate')) {
            $votes = ($reportType === 'general_ledger') ? \App\Models\Vote::orderBy('vote_code')->get(['id', 'vote_code', 'description']) : [];
            $financialYears = \App\Models\FinancialYear::orderBy('year', 'desc')->get();

            // Load Menu Data
            $menuJson = json_decode(file_get_contents(public_path('menu-data/financials.json')));

            // Determine View Name: reports.configure.{reportType}
            // Fallback to generic if specific doesn't exist (though we will create them all)
            $viewName = "jasper-reports::reports.configure.{$reportType}";

            return view($viewName, [
                'reportName' => $reportName,
                'route' => $routeName,
                'reportType' => $reportType,
                'votes' => $votes,
                'financialYears' => $financialYears,
                'moduleTitle' => 'Reporting',
                'menuJson' => $menuJson
            ]);
        }

        // Generate Report Logic
        $input = $this->getReportPath($reportType . '.jrxml');
        $output = $this->getOutputDetails($reportType);
        $params = $this->getCommonParams($request);
        $format = $request->input('format', 'pdf');

        // Ensure we rely on user input dates if provided, specifically overriding any defaults
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $params['date_from'] = $request->date_from;
            $params['date_to'] = $request->date_to;
        }

        // Specific parameters for General Ledger
        if ($reportType === 'general_ledger' && $request->filled('vote_id')) {
            $params['vote_id'] = (int) $request->vote_id;
        }

        // Advanced Options
        $params['print_details'] = $request->input('print_details', 'sub_accounts');
        $params['sort_by'] = $request->input('sort_by', 'account_name');
        $params['display_format'] = $request->input('display_format', 'account');
        $params['suppress_zero'] = $request->boolean('suppress_zero') ? 'true' : 'false';
        $params['include_ytd'] = $request->boolean('include_ytd') ? 'true' : 'false';
        $params['include_budget'] = $request->boolean('include_budget') ? 'true' : 'false';
        $params['print_assets_first'] = $request->boolean('print_assets_first') ? 'true' : 'false';

        // Optimization for Excel/CSV: Ignore Pagination
        if (in_array($format, ['xls', 'csv'])) {
            $params['IS_IGNORE_PAGINATION'] = 'true';
        }

        try {
            Log::info("=== JASPER REPORT DEBUG ===");
            Log::info("Input JRXML: {$input}");
            Log::info("Input exists: " . (file_exists($input) ? 'YES' : 'NO'));
            Log::info("Output: {$output}");

            $this->jasper->compile($input);

            $jasperPath = str_replace('.jrxml', '.jasper', $input);
            Log::info("Compiled .jasper: {$jasperPath}");
            Log::info(".jasper exists: " . (file_exists($jasperPath) ? 'YES' : 'NO'));

            $path = $this->jasper->generateReport($input, $output, [$format], $params);
            Log::info("Generated report: {$path}");

            if (!file_exists($path)) {
                return back()->with('error', 'Report generation failed. Output file not found.');
            }

            return Response::download($path);
        } catch (\Exception $e) {
            Log::error("Jasper error: " . $e->getMessage());
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function index()
    {
        $votes = \App\Models\Vote::orderBy('vote_code')->get(['id', 'vote_code', 'description']);
        $financialYears = \App\Models\FinancialYear::orderBy('year', 'desc')->get();
        $menuJson = json_decode(file_get_contents(public_path('menu-data/financials.json')));

        return view('jasper-reports::reports.explorer_v2', [
            'votes' => $votes,
            'financialYears' => $financialYears,
            'moduleTitle' => 'Reporting',
            'menuJson' => $menuJson
        ]);
    }

    public function trialBalance(Request $request)
    {
        return $this->handleRequest($request, 'trial_balance', 'Trial Balance', 'reports.trial_balance');
    }

    public function incomeStatement(Request $request)
    {
        return $this->handleRequest($request, 'income_statement', 'Income Statement', 'reports.income_statement');
    }

    public function balanceSheet(Request $request)
    {
        return $this->handleRequest($request, 'balance_sheet', 'Balance Sheet', 'reports.balance_sheet');
    }

    public function generalLedger(Request $request)
    {
        return $this->handleRequest($request, 'general_ledger', 'General Ledger', 'reports.general_ledger');
    }

    public function cashFlow(Request $request)
    {
        return $this->handleRequest($request, 'cash_flow', 'Statement of Cash Flows', 'reports.cash_flow');
    }

    public function changesInEquity(Request $request)
    {
        return $this->handleRequest($request, 'changes_in_equity', 'Statement of Changes in Equity', 'reports.changes_in_equity');
    }
}
