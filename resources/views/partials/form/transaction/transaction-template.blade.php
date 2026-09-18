@if(count($templates ?? []) > 0)
    <template x-if="0 === index">
        <div class="row mb-2" x-data="{ fireflyTemplates: {{ \Illuminate\Support\Js::from($templates) }} }">
            <label class="col-sm-1 col-form-label d-none d-sm-block">
                <em title="{{ __('transaction_templates.title') }}" class="bi bi-file-earmark-text"></em>
            </label>
            <div class="col-sm-10">
                <select class="form-select"
                        @change="applyTemplate($event.target.value, fireflyTemplates, index); $event.target.value = '';">
                    <option value="">{{ __('transaction_templates.picker_placeholder') }}</option>
                    @foreach($templates as $template)
                        <option value="{{ $template['id'] }}">{{ $template['name'] }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </template>
@endif
