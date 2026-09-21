import Tags from "bootstrap5-tags";

/**
 * The tags field is not a plain input: bootstrap5-tags keeps its own DOM state.
 */
function syncTagWidget(index, tags) {
    const element = document.getElementById("tags_" + index);
    if (null === element) {
        return;
    }
    const instance = Tags.getInstance(element);
    if (null === instance || undefined === instance) {
        return;
    }
    instance.removeAll();
    for (let i = 0; i < tags.length; i++) {
        instance.addItem(tags[i], tags[i]);
    }
}

function applyAccount(account, id, name, type, currencyCode) {
    if (null === id) {
        return;
    }
    account.id = id;
    account.name = name;
    account.alpine_name = name;
    account.type = type;
    account.currency_code = currencyCode;
}

/**
 * Fill split `index` from the template with id `templateId`.
 *
 * Fields the template defines overwrite the form. Fields the template leaves
 * blank are left untouched.
 */
export function applyTemplate(templateId, templates, index) {
    const id = parseInt(templateId);
    if (isNaN(id) || 0 === id) {
        return;
    }

    const template = templates.find((current) => parseInt(current.id) === id);
    if (undefined === template) {
        return;
    }

    const entry = this.entries[index];
    if (undefined === entry) {
        return;
    }

    if (null !== template.transaction_description) {
        entry.description = template.transaction_description;
    }

    applyAccount(
        entry.source_account,
        template.source_account_id,
        template.source_account_name,
        template.source_account_type,
        template.source_account_currency_code,
    );
    applyAccount(
        entry.destination_account,
        template.destination_account_id,
        template.destination_account_name,
        template.destination_account_type,
        template.destination_account_currency_code,
    );

    // budget_id arrives as a string because the budget select's options come
    // from the API as strings. Do not parseInt this.
    if (null !== template.budget_id) {
        entry.budget_id = template.budget_id;
    }
    if (null !== template.category_name) {
        entry.category_name = template.category_name;
    }
    if (null !== template.notes) {
        entry.notes = template.notes;
    }
    if (template.tags.length > 0) {
        entry.tags = template.tags.slice();
        syncTagWidget(index, template.tags);
    }

    // Same call changedSourceAccount() would make after an account changes.
    this.detectTransactionType();
}
