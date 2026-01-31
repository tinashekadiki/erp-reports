@extends('layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="card card-outline card-purple" style="border-top: 3px solid #6f42c1;">
                <div class="card-header">
                    <h3 class="card-title">Report -- {{ $reportName }}</h3>
                </div>
                <form action="{{ route($route) }}" method="GET" target="_blank">
                    <input type="hidden" name="generate" value="true">

                    <div class="card-body">

                        <!-- Report Options -->
                        <fieldset class="border p-2 mb-3">
                            <legend class="w-auto px-2 h6">Report Options</legend>
                            <div class="row">
                                <div class="col-md-12">
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
                            </div>
                        </fieldset>

                        <!-- Format Options -->
                        <fieldset class="border p-2 mb-3">
                            <legend class="w-auto px-2 h6">Output Format</legend>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Format</label>
                                <div class="col-sm-9">
                                    <select name="format" class="form-control">
                                        <option value="pdf">PDF (Printable Document)</option>
                                        <option value="xls">Excel (Spreadsheet)</option>
                                        <option value="csv">CSV (Comma Separated Values)</option>
                                    </select>
                                </div>
                            </div>
                        </fieldset>

                    </div>

                    <div class="card-footer text-right">
                        <a href="{{ route('reporting.index') }}" class="btn btn-default mr-2">
                            <i class="fas fa-times"></i> Close
                        </a>
                        <button type="submit" class="btn text-white" style="background-color: #6f42c1;">
                            <i class="fas fa-file-pdf"></i> Generate Report
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap4',
            width: '100%'
        });

        $('#financial_year').on('change', function() {
            const option = $(this).find(':selected');
            const start = option.data('start');
            const end = option.data('end');

            if (start) $('#date_from').val(start);
            if (end) $('#date_to').val(end);
        });
    });
</script>
@endsection