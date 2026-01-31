@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="card card-outline card-warning">
                <div class="card-header">
                    <h3 class="card-title">Report -- General Ledger (Transactions)</h3>
                </div>
                <form action="{{ route($route) }}" method="GET" target="_blank">
                    <input type="hidden" name="generate" value="true">

                    <div class="card-body">

                        <!-- Specific Account -->
                        <fieldset class="border p-2 mb-3">
                            <legend class="w-auto px-2 h6">Account Selection</legend>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Account</label>
                                <div class="col-sm-9">
                                    <select name="vote_id" class="form-control select2">
                                        <option value="">All Accounts</option>
                                        @foreach($votes as $vote)
                                        <option value="{{ $vote->id }}">{{ $vote->vote_code }} - {{ $vote->description }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </fieldset>

                        <!-- Period -->
                        <fieldset class="border p-2 mb-3">
                            <legend class="w-auto px-2 h6">Transaction Dates</legend>
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
                                <label class="col-sm-3 col-form-label">Range</label>
                                <div class="col-sm-4">
                                    <input type="date" name="date_from" id="date_from" class="form-control" placeholder="From">
                                </div>
                                <div class="col-sm-4">
                                    <input type="date" name="date_to" id="date_to" class="form-control" placeholder="To">
                                </div>
                            </div>
                        </fieldset>

                        <!-- Output Format -->
                        <div class="form-group row mt-3">
                            <label class="col-sm-3 col-form-label">Output Format</label>
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
                            <button type="submit" class="btn btn-warning text-white">Print / Generate</button>
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