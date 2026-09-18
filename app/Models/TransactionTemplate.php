<?php

declare(strict_types=1);

namespace FireflyIII\Models;

use FireflyIII\Enums\AccountTypeEnum;
use FireflyIII\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransactionTemplate extends Model
{
    use SoftDeletes;

    public const SOURCE_TYPES      = [
        AccountTypeEnum::ASSET->value,
        AccountTypeEnum::LOAN->value,
        AccountTypeEnum::DEBT->value,
        AccountTypeEnum::MORTGAGE->value,
        AccountTypeEnum::REVENUE->value,
    ];

    public const DESTINATION_TYPES = [
        AccountTypeEnum::EXPENSE->value,
        AccountTypeEnum::LOAN->value,
        AccountTypeEnum::DEBT->value,
        AccountTypeEnum::MORTGAGE->value,
        AccountTypeEnum::ASSET->value,
    ];

    protected $fillable = [
        'user_id',
        'name',
        'transaction_description',
        'source_account_id',
        'destination_account_id',
        'budget_id',
        'category_id',
        'tags',
        'notes',
    ];

    public function budget(): BelongsTo
    {
        return $this->belongsTo(Budget::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function destinationAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'destination_account_id');
    }

    public function sourceAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'source_account_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'tags'    => 'array',
        ];
    }
}
