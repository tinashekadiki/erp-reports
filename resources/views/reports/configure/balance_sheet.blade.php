@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card card-outline card-info">
                <div class="card-header">
                    <h3 class="card-title">Report -- Balance Sheet</h3>
                </div>
                <!-- Form points to same URL but with generate=true -->
                <form action="{{ route($route) }}" method="GET" target="_blank">
                    <input type="hidden" name="generate" value="true">

                    <div class="card-body">
                        <!-- Period Section -->
                        <fieldset class="border p-2 mb-3">
                            <legend class="w-auto px-2 h6">Balance Sheet Period</legend>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Period</label>
                                <div class="col-sm-9">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <select name="financial_year" id="financial_year" class="form-control select2">
                                                <option value="">Select Financial Year</option>
                                                @foreach($financialYears as $fy)
                                                <option value="{{ $fy->year }}" data-start="{{ $fy->start }}" data-end="{{ $fy->end }}">
                                                    {{ $fy->year }} ({{ date('M', mktime(0, 0, 0, $fy->start, 10)) }} - {{ date('M', mktime(0, 0, 0, $fy->end, 10)) }})
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <!-- Hidden real dates -->
                                            <input type="date" name="date_to" id="date_to" class="form-control" placeholder="End Date">
                                            <small class="text-muted">Report As Of Date</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>

                        <!-- Options Section -->
                        <fieldset class="border p-2">
                            <legend class="w-auto px-2 h6">Options</legend>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Print details up to</label>
                                <div class="col-sm-9">
                                    <select name="print_details" class="form-control">
                                        <option value="master_accounts">Master Accounts</option>
                                        <option value="sub_accounts" selected>Sub Accounts</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-9 offset-sm-3">
                                    <div class="custom-control custom-checkbox mb-2">
                                        <input class="custom-control-input" type="checkbox" id="suppress_zero" name="suppress_zero" value="1" checked>
                                        <label for="suppress_zero" class="custom-control-label">Suppress if Zero</label>
                                    </div>
                                    <div class="custom-control custom-checkbox mb-2">
                                        <input class="custom-control-input" type="checkbox" id="include_budget" name="include_budget" value="1" checked>
                                        <label for="include_budget" class="custom-control-label">Print if there is Budget Value</label>
                                    </div>
                                    <div class="custom-control custom-checkbox mb-2">
                                        <input class="custom-control-input" type="checkbox" id="print_assets_first" name="print_assets_first" value="1" checked>
                                        <label for="print_assets_first" class="custom-control-label">Print Assets First</label>
                                    </div>
                                    <div class="custom-control custom-checkbox">
                                        <input class="custom-control-input" type="checkbox" id="stmt_financial_pos" name="stmt_financial_pos" value="1" checked>
                                        <label for="stmt_financial_pos" class="custom-control-label">Statement of Financial Position</label>
                                    </div>
                                </div>
                            </div>
                        </fieldset>

                        <!-- Universal Format -->
                        <div class="form-group row mt-3">
                            <label class="col-sm-3 col-form-label">Format</label>
                            <div class="col-sm-9">
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
                            <!-- <button type="button" class="btn btn-default mr-2">Preview</button> -->
                            <button type="submit" class="btn btn-info">Print / Generate</button>
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
        const option = this.options[this.selectedIndex];
        const year = this.value;
        const startMonth = option.getAttribute('data-start') || 1;
        const endMonth = option.getAttribute('data-end') || 12;

        if (year) {
            const pad = (n) => n < 10 ? '0' + n : n;
            const lastDay = new Date(year, parseInt(endMonth), 0).getDate();
            const endDate = year + '-' + pad(endMonth) + '-' + pad(lastDay);
            document.getElementById('date_to').value = endDate;
        }
    });
</script>
@endsection