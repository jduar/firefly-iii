<?php

declare(strict_types=1);

namespace FireflyIII\Http\Requests;

use FireflyIII\Models\Budget;
use FireflyIII\Models\Category;
use FireflyIII\Models\TransactionTemplate;
use FireflyIII\Repositories\Account\AccountRepositoryInterface;
use FireflyIII\Support\Request\ChecksLogin;
use FireflyIII\Support\Request\ConvertsDataTypes;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * Class TransactionTemplateFormRequest
 */
class TransactionTemplateFormRequest extends FormRequest
{
    use ChecksLogin;
    use ConvertsDataTypes;

    protected array $acceptedRoles = [];

    /**
     * Validated input, normalised for the model.
     *
     * Every referenced id is checked against the current user's own records.
     * An id the user does not own becomes null rather than an error: it can only
     * happen through a tampered form.
     */
    public function getTemplateData(): array
    {
        $data           = $this->validated();

        /** @var AccountRepositoryInterface $repository */
        $repository     = app(AccountRepositoryInterface::class);
        $sourceIds      = $this->accountIds($repository->getAccountsByType(TransactionTemplate::SOURCE_TYPES));
        $destinationIds = $this->accountIds($repository->getAccountsByType(TransactionTemplate::DESTINATION_TYPES));

        return [
            'name'                    => $data['name'],
            'transaction_description' => $this->nullIfBlank($data['transaction_description'] ?? null),
            'source_account_id'       => $this->onlyIfIn($data['source_account_id'] ?? null, $sourceIds),
            'destination_account_id'  => $this->onlyIfIn($data['destination_account_id'] ?? null, $destinationIds),
            'budget_id'               => $this->onlyIfIn(
                $data['budget_id'] ?? null,
                Budget::where('user_id', auth()->id())->pluck('id')->toArray()
            ),
            'category_id'             => $this->onlyIfIn(
                $data['category_id'] ?? null,
                Category::where('user_id', auth()->id())->pluck('id')->toArray()
            ),
            'tags'                    => $this->parseTags($data['tags'] ?? null),
            'notes'                   => $this->nullIfBlank($data['notes'] ?? null),
        ];
    }

    public function rules(): array
    {
        return [
            'name'                    => 'required|min:1|max:255',
            'transaction_description' => 'nullable|max:1000',
            'source_account_id'       => 'nullable|integer',
            'destination_account_id'  => 'nullable|integer',
            'budget_id'               => 'nullable|integer',
            'category_id'             => 'nullable|integer',
            'tags'                    => 'nullable|max:1000',
            'notes'                   => 'nullable|max:65000',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        if ($validator->fails()) {
            Log::channel('audit')->error(sprintf('Validation errors in %s', self::class), $validator->errors()->toArray());
        }
    }

    /**
     * @return array<int, int>
     */
    private function accountIds(Collection $accounts): array
    {
        $return = [];
        foreach ($accounts as $account) {
            $return[] = (int) $account->id;
        }

        return $return;
    }

    private function nullIfBlank(?string $value): ?string
    {
        $value = trim((string) $value);

        return '' === $value ? null : $value;
    }

    private function onlyIfIn(mixed $value, array $allowed): ?int
    {
        $value = (int) $value;
        if (0 === $value) {
            return null;
        }

        return in_array($value, array_map('intval', $allowed), true) ? $value : null;
    }

    /**
     * Comma-separated string to a clean array of tag names.
     */
    private function parseTags(?string $value): ?array
    {
        $parts  = explode(',', (string) $value);
        $return = [];
        foreach ($parts as $part) {
            $part = trim($part);
            if ('' !== $part) {
                $return[] = $part;
            }
        }

        return [] === $return ? null : $return;
    }
}
