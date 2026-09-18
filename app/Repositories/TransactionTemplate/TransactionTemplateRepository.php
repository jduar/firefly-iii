<?php

declare(strict_types=1);

namespace FireflyIII\Repositories\TransactionTemplate;

use FireflyIII\Models\TransactionTemplate;
use FireflyIII\Support\Repositories\UserGroup\UserGroupInterface;
use FireflyIII\Support\Repositories\UserGroup\UserGroupTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Class TransactionTemplateRepository
 */
class TransactionTemplateRepository implements TransactionTemplateRepositoryInterface, UserGroupInterface
{
    use UserGroupTrait;

    public function destroy(TransactionTemplate $template): void
    {
        $template->delete();
    }

    public function find(int $id): ?TransactionTemplate
    {
        return $this->query()->find($id);
    }

    public function getTemplates(): Collection
    {
        return $this->query()
            ->with(['sourceAccount', 'destinationAccount', 'budget', 'category'])
            ->orderBy('name')
            ->get()
        ;
    }

    public function store(array $data): TransactionTemplate
    {
        $data['user_id'] = $this->user->id;

        return TransactionTemplate::create($data);
    }

    public function update(TransactionTemplate $template, array $data): TransactionTemplate
    {
        $template->update($data);

        return $template;
    }

    /**
     * Every read goes through here, so no query can forget to scope to the user.
     */
    private function query(): Builder
    {
        return TransactionTemplate::where('user_id', $this->user->id);
    }
}
