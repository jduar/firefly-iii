@extends('layout.v3.session')
@section('content')
    <form method="POST" action="{{ route('transaction-templates.destroy', [$template->id]) }}" accept-charset="UTF-8">
        @csrf
        <div class="row">
            <div class="col-lg-8 col-md-12 col-sm-12">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">{{ __('transaction_templates.delete_title') }}</h3></div>
                    <div class="card-body">
                        <p>{{ __('transaction_templates.delete_confirm', ['name' => $template->name]) }}</p>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-danger">{{ __('firefly.delete_permanently') }}</button>
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
