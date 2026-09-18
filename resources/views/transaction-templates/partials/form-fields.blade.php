@php($t = $template ?? null)
<div class="mb-3">
    <label class="form-label" for="name">{{ __('transaction_templates.field_name') }}</label>
    <input type="text" class="form-control" id="name" name="name" required
           value="{{ old('name', $t?->name) }}">
</div>
<div class="mb-3">
    <label class="form-label" for="transaction_description">{{ __('transaction_templates.field_transaction_description') }}</label>
    <input type="text" class="form-control" id="transaction_description" name="transaction_description"
           value="{{ old('transaction_description', $t?->transaction_description) }}">
    <div class="form-text">{{ __('transaction_templates.help_transaction_description') }}</div>
</div>
<div class="mb-3">
    <label class="form-label" for="source_account_id">{{ __('transaction_templates.field_source') }}</label>
    <select class="form-select" id="source_account_id" name="source_account_id">
        <option value="">{{ __('transaction_templates.no_value') }}</option>
        @foreach($sourceAccounts as $groupName => $group)
            <optgroup label="{{ $groupName }}">
                @foreach($group as $id => $accountName)
                    <option value="{{ $id }}" @selected((int) old('source_account_id', $t?->source_account_id) === (int) $id)>{{ $accountName }}</option>
                @endforeach
            </optgroup>
        @endforeach
    </select>
</div>
<div class="mb-3">
    <label class="form-label" for="destination_account_id">{{ __('transaction_templates.field_destination') }}</label>
    <select class="form-select" id="destination_account_id" name="destination_account_id">
        <option value="">{{ __('transaction_templates.no_value') }}</option>
        @foreach($destinationAccounts as $groupName => $group)
            <optgroup label="{{ $groupName }}">
                @foreach($group as $id => $accountName)
                    <option value="{{ $id }}" @selected((int) old('destination_account_id', $t?->destination_account_id) === (int) $id)>{{ $accountName }}</option>
                @endforeach
            </optgroup>
        @endforeach
    </select>
</div>
<div class="mb-3">
    <label class="form-label" for="budget_id">{{ __('transaction_templates.field_budget') }}</label>
    <select class="form-select" id="budget_id" name="budget_id">
        <option value="">{{ __('transaction_templates.no_value') }}</option>
        @foreach($budgets as $id => $budgetName)
            <option value="{{ $id }}" @selected((int) old('budget_id', $t?->budget_id) === (int) $id)>{{ $budgetName }}</option>
        @endforeach
    </select>
</div>
<div class="mb-3">
    <label class="form-label" for="category_id">{{ __('transaction_templates.field_category') }}</label>
    <select class="form-select" id="category_id" name="category_id">
        <option value="">{{ __('transaction_templates.no_value') }}</option>
        @foreach($categories as $id => $categoryName)
            <option value="{{ $id }}" @selected((int) old('category_id', $t?->category_id) === (int) $id)>{{ $categoryName }}</option>
        @endforeach
    </select>
    <div class="form-text">{{ __('transaction_templates.help_category') }}</div>
</div>
<div class="mb-3">
    <label class="form-label" for="tags">{{ __('transaction_templates.field_tags') }}</label>
    <input type="text" class="form-control" id="tags" name="tags"
           value="{{ old('tags', null === $t?->tags ? '' : implode(', ', $t->tags)) }}">
    <div class="form-text">{{ __('transaction_templates.help_tags') }}</div>
</div>
<div class="mb-3">
    <label class="form-label" for="notes">{{ __('transaction_templates.field_notes') }}</label>
    <textarea class="form-control" id="notes" name="notes" rows="4">{{ old('notes', $t?->notes) }}</textarea>
</div>
