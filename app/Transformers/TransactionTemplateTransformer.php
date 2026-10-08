<?php

declare(strict_types=1);

namespace FireflyIII\Transformers;

use FireflyIII\Models\TransactionTemplate;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Class TransactionTemplateTransformer
 *
 * Single source of truth for the flat payload shape of a template, used by
 * both the API v1 endpoint and the create form picker.
 */
class TransactionTemplateTransformer extends AbstractTransformer
{
    /**
     * TransactionTemplateTransformer constructor.
     */
    public function __construct()
    {
        $this->parameters = new ParameterBag();
    }

    /**
     * Transform a template into the flat payload used by the create form picker.
     */
    public function transform(TransactionTemplate $template): array
    {
        return [
            'name'                     => $template->name,
            'transaction_description'  => $template->transaction_description,
            'source_account_id'        => null === $template->source_account_id ? null : (string) $template->source_account_id,
            'source_account_name'      => $template->sourceAccount?->name,
            'destination_account_id'   => null === $template->destination_account_id ? null : (string) $template->destination_account_id,
            'destination_account_name' => $template->destinationAccount?->name,
            'budget_id'                => null === $template->budget_id ? null : (string) $template->budget_id,
            'category_name'            => $template->category?->name,
            'tags'                     => $template->tags ?? [],
            'notes'                    => $template->notes,
        ];
    }
}
