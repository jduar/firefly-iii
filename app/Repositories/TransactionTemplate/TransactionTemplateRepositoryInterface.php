<?php

declare(strict_types=1);

namespace FireflyIII\Repositories\TransactionTemplate;

use FireflyIII\Enums\UserRoleEnum;
use FireflyIII\Models\TransactionTemplate;
use FireflyIII\Models\UserGroup;
use FireflyIII\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Collection;

/**
 * Interface TransactionTemplateRepositoryInterface
 *
 * @method setUserGroup(UserGroup $group)
 * @method getUserGroup()
 * @method getUser()
 * @method checkUserGroupAccess(UserRoleEnum $role)
 * @method setUser(null|Authenticatable|User $user)
 * @method setUserGroupById(int $userGroupId)
 */
interface TransactionTemplateRepositoryInterface
{
    public function destroy(TransactionTemplate $template): void;

    public function find(int $id): ?TransactionTemplate;

    /**
     * All templates of the current user, ordered by name.
     */
    public function getTemplates(): Collection;

    public function store(array $data): TransactionTemplate;

    public function update(TransactionTemplate $template, array $data): TransactionTemplate;
}
