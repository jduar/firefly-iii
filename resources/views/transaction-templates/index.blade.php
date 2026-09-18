@extends('layout.v3.session')
@section('content')
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="card mb-2">
                <x-elements.card-header-with-menu
                    :cardTitle="__('transaction_templates.title')"
                    :route="route('transaction-templates.create')"
                    :linkTitle="__('transaction_templates.create_new')"/>
                <div class="card-body p-0">
                    @if(0 === count($templates))
                        <div class="d-flex justify-content-center m-3">
                            <p>{{ __('transaction_templates.none_yet') }}</p>
                        </div>
                    @endif
                    @if(count($templates) > 0)
                        <table class="table table-sm table-hover" aria-label="{{ __('transaction_templates.title') }}">
                            <thead>
                            <tr>
                                <th>{{ __('transaction_templates.field_name') }}</th>
                                <th>{{ __('transaction_templates.field_transaction_description') }}</th>
                                <th>{{ __('transaction_templates.field_source') }}</th>
                                <th>{{ __('transaction_templates.field_destination') }}</th>
                                <th>{{ __('transaction_templates.field_category') }}</th>
                                <th>{{ __('transaction_templates.field_budget') }}</th>
                                <th>&nbsp;</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($templates as $template)
                                <tr>
                                    <td><a href="{{ route('transaction-templates.edit', [$template->id]) }}">{{ $template->name }}</a></td>
                                    <td>{{ $template->transaction_description }}</td>
                                    <td>{{ $template->sourceAccount?->name }}</td>
                                    <td>{{ $template->destinationAccount?->name }}</td>
                                    <td>{{ $template->category?->name }}</td>
                                    <td>{{ $template->budget?->name }}</td>
                                    <td class="text-end">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                                    id="tpl_menu_{{ $template->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                                {{ __('firefly.actions') }}
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="tpl_menu_{{ $template->id }}">
                                                <li><a class="dropdown-item" href="{{ route('transaction-templates.edit', [$template->id]) }}">
                                                        <span class="bi bi-pencil"></span> {{ __('firefly.edit') }}</a></li>
                                                <li><a class="dropdown-item" href="{{ route('transaction-templates.delete', [$template->id]) }}">
                                                        <span class="bi bi-trash"></span> {{ __('firefly.delete') }}</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @vite(['js/pages/generic-nodates.js'])
@endsection
