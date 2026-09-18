@extends('layout.v3.session')
@section('content')
    <form method="POST" action="{{ route('transaction-templates.store') }}" accept-charset="UTF-8">
        @csrf
        <div class="row">
            <div class="col-lg-8 col-md-12 col-sm-12">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">{{ __('transaction_templates.create_new') }}</h3></div>
                    <div class="card-body">
                        @include('transaction-templates.partials.form-fields', ['template' => null])
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-success">{{ __('firefly.submit') }}</button>
                        <a href="{{ route('transaction-templates.index') }}" class="btn btn-outline-secondary">{{ __('firefly.cancel') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
@section('scripts')
    @vite(['js/pages/generic-nodates.js'])
@endsection
