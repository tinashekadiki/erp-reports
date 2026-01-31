@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Report -- General Ledger Trial Balance</h3>
                </div>
                <form action="{{ route($route) }}" method="GET" target="_blank">
                    <input type="hidden" name="generate" value="true">

                    <div class="card-body">

                        <!-- Ledger Account Range -->
                        <fieldset class="border p-2 mb-3">
                            <legend class="w-auto px-2 h6">Ledger Account</legend>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">From</label>
                                <div class="col-sm-10">
                                    <select name="vote_id_from" class="form-control select2">
                                        <option value="">Start Category / Account</option>
                                        @foreach($votes as $vote)
                                        <option value="{{ $vote->id }}">{{ $vote->vote_code }} - {{ $vote->description }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">To</label>
                                <div class="col-sm-10">
                                    <select name="vote_id_to" class="form-control select2">
                                        <option value="">End Category / Account</option>
                                        @foreach($votes as $vote)
                                        <option value="{{ $vote->id }}">{{ $vote->vote_code }} - {{ $vote->description }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </fieldset>

                        <!-- TB Options (Dates) -->
                        <fieldset class="border p-2 mb-3">
                            <legend class="w-auto px-2 h6">Trial Balance Options</legend>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Financial Year</label>
                                        <div class="col-sm-9">
                                            <select name="financial_year" id="financial_year" class="form-control select2">
                                                <option value="">Select Year</option>
                                                @foreach($financialYears as $fy)
                                                <option value="{{ $fy->year }}" data-start="{{ $fy->start }}" data-end="{{ $fy->end }}">
                                                    {{ $fy->year }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label">Date Range</label>
                                        <div class="col-sm-4">
                                            <input type="date" name="date_from" id="date_from" class="form-control" placeholder="From">
                                        </div>
                                        <div class="col-sm-4">
                                            <input type="date" name="date_to" id="date_to" class="form-control" placeholder="To">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="custom-control custom-checkbox mt-2">
                                        <input class="custom-control-input" type="checkbox" id="include_ytd" name="include_ytd" value="1" checked>
                                        <label for="include_ytd" class="custom-control-label">Include YTD Totals</label>
                                    </div>
                                </div>
                            </div>
                        </fieldset>

                        <!-- Options (Sort/Details) -->
                        <fieldset class="border p-2 mb-3">
                            <legend class="w-auto px-2 h6">Options</legend>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Sort by</label>
                                <div class="col-sm-9">
                                    <select name="sort_by" class="form-control">
                                        <option value="account_name">Account Name</option>
                                        <option value="account_code">Account Code</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Print Details up to</label>
                                <div class="col-sm-9">
                                    <select name="print_details" class="form-control">
                                        <option value="sub_accounts" selected>Sub Accounts</option>
                                        <option value="master_accounts">Master Accounts</option>
                                    </select>
                                </div>
                            </div>
                        </fieldset>

                        <div class="row">
                            <!-- Display -->
                            <div class="col-md-6">
                                <fieldset class="border p-2 h-100">
                                    <legend class="w-auto px-2 h6">Display</legend>
                                    <div class="form-group">
                                        <div class="custom-control custom-radio">
                                            <input class="custom-control-input" type="radio" id="disp_account" name="display_format" value="account" checked>
                                            <label for="disp_account" class="custom-control-label">Print "Account"</label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input class="custom-control-input" type="radio" id="disp_desc" name="display_format" value="description">
                                            <label for="disp_desc" class="custom-control-label">Print "Description"</label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input class="custom-control-input" type="radio" id="disp_both" name="display_format" value="both">
                                            <label for="disp_both" class="custom-control-label">Print "Account (Description)"</label>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>

                            <!-- Print Options -->
                            <div class="col-md-6">
                                <fieldset class="border p-2 h-100">
                                    <legend class="w-auto px-2 h6">Print Options</legend>
                                    <div class="custom-control custom-checkbox">
                                        <input class="custom-control-input" type="checkbox" id="suppress_zero" name="suppress_zero" value="1" checked>
                                        <label for="suppress_zero" class="custom-control-label">Suppress if Zero</label>
                                    </div>
                                    <div class="mt-2">
                                        <div class="custom-control custom-checkbox">
                                            <input class="custom-control-input" type="checkbox" id="group_by_type" name="group_by_type" value="1">
                                            <label for="group_by_type" class="custom-control-label">Group by Financial Account Type</label>
                                        </div>
                                        <div class="ml-4 mt-1">
                                            <div class="custom-control custom-radio">
                                                <input class="custom-control-input" type="radio" id="grp_income_first" name="group_order" value="income_first" checked>
                                                <label for="grp_income_first" class="custom-control-label small">Print Income Statement Accounts First</label>
                                            </div>
                                            <div class="custom-control custom-radio">
                                                <input class="custom-control-input" type="radio" id="grp_balance_first" name="group_order" value="balance_first">
                                                <label for="grp_balance_first" class="custom-control-label small">Print Balance Sheet Accounts First</label>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                        </div>

                        <!-- Universal Format -->
                        <div class="form-group row mt-3">
                            <label class="col-sm-2 col-form-label">Output</label>
                            <div class="col-sm-10">
                                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                    <label class="btn btn-outline-secondary active">
                                        <input type="radio" name="format" value="pdf" checked> PDF
                                    </label>
                                    <label class="btn btn-outline-secondary">
                                        <input type="radio" name="format" value="xls"> Excel
                                    </label>
                                    <label class="btn btn-outline-secondary">
                                        <input type="radio" name="format" value="csv"> CSV
                                    </label>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="card-footer d-flex justify-content-between">
                        <a href="{{ route('reporting.index') }}" class="btn btn-default">Close</a>
                        <div>
                            <button type="submit" class="btn btn-primary">Print / Generate</button>
                        </div>
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
        // Same logic for auto-populating dates
        const option = this.options[this.selectedIndex];
        const year = this.value;
        const startMonth = option.getAttribute('data-start') || 1;
        const endMonth = option.getAttribute('data-end') || 12;

        if (year) {
            const pad = (n) => n < 10 ? '0' + n : n;
            const startDate = year + '-' + pad(startMonth) + '-01';
            const lastDay = new Date(year, parseInt(endMonth), 0).getDate();
            const endDate = year + '-' + pad(endMonth) + '-' + pad(lastDay);
            document.getElementById('date_from').value = startDate;
            document.getElementById('date_to').value = endDate;
        }
    });

    $(document).ready(function() {
        if ($.fn.select2) {
            $('.select2').select2({
                theme: 'bootstrap4'
            });
        }
    });
</script>
@endsection