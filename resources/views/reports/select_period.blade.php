@extends('layouts.master')

@section('content')

<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h3>Financial Reports</h3>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item active">{{ $reportName }}</li>
            </ol>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">Generate {{ $reportName }}</h3>
                </div>
                <form action="{{ route($route) }}" method="GET" target="_blank">
                    <div class="card-body">
                        @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        <div class="form-group">
                            <label>Financial Year</label>
                            <select name="financial_year" id="financial_year" class="form-control select2">
                                <option value="">Select Financial Year</option>
                                {{-- Use Database Financial Years if available --}}
                                @foreach($financialYears as $fy)
                                <option value="{{ $fy->year }}" {{ request('financial_year') == $fy->year ? 'selected' : '' }}>
                                    {{ $fy->year }} ({{ date('M', mktime(0, 0, 0, $fy->start, 10)) }} - {{ date('M', mktime(0, 0, 0, $fy->end, 10)) }})
                                </option>
                                @endforeach
                                {{-- Fallback to simple range if no DB data --}}
                                @if($financialYears->isEmpty())
                                @foreach(range(date('Y'), date('Y')-5) as $year)
                                <option value="{{ $year }}" {{ request('financial_year') == $year ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                                @endforeach
                                @endif
                            </select>
                            <small class="text-muted">Select a year to automatically set start and end dates</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Date From</label>
                                    <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Date To</label>
                                    <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Report Format</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="format" id="format_pdf" value="pdf" checked>
                                <label class="form-check-label" for="format_pdf">PDF (Document)</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="format" id="format_xls" value="xls">
                                <label class="form-check-label" for="format_xls">Excel (Spreadsheet)</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="format" id="format_csv" value="csv">
                                <label class="form-check-label" for="format_csv">CSV (Raw Data)</label>
                            </div>
                        </div>

                        @if($reportType === 'general_ledger')
                        <div class="form-group">
                            <label>Account (Optional)</label>
                            <select name="vote_id" class="form-control select2">
                                <option value="">All Accounts</option>
                                @foreach($votes as $vote)
                                <option value="{{ $vote->id }}">{{ $vote->vote_code }} - {{ $vote->description }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        <input type="hidden" name="generate" value="true">
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary float-right">
                            <i class="fas fa-file-pdf"></i> Generate Report
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-js')
<script>
    document.getElementById('financial_year').addEventListener('change', function() {
        const year = this.value;
        if (year) {
            document.getElementById('date_from').value = year + '-01-01';
            document.getElementById('date_to').value = year + '-12-31';
        }
    });

    // Initialize Select2 if available
    $(document).ready(function() {
        if ($.fn.select2) {
            $('.select2').select2({
                theme: 'bootstrap4'
            });
        }
    });
</script>
@endsection