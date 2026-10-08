@if(count($templates ?? []) > 0)
    <template x-if="0 === index">
        <div class="row mb-2">
            <label class="col-sm-1 col-form-label d-none d-sm-block">
                <em title="{{ __('transaction_templates.title') }}" class="bi bi-file-earmark-text"></em>
            </label>
            <div class="col-sm-10">
                <select class="form-select"
                        @change="applyTemplate($event.target.value, formData.transactionTemplates, index); $event.target.value = '';">
                    <option value="">{{ __('transaction_templates.picker_placeholder') }}</option>
                    <template x-for="template in formData.transactionTemplates">
                        <option :value="template.id" x-text="template.name"></option>
                    </template>
                </select>
            </div>
        </div>
    </template>
@endif
