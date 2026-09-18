<?php

declare(strict_types=1);

namespace FireflyIII\Http\Controllers\TransactionTemplate;

use FireflyIII\Http\Controllers\Controller;
use FireflyIII\Http\Requests\TransactionTemplateFormRequest;
use FireflyIII\Models\Budget;
use FireflyIII\Models\Category;
use FireflyIII\Models\TransactionTemplate;
use FireflyIII\Repositories\Account\AccountRepositoryInterface;
use FireflyIII\Repositories\TransactionTemplate\TransactionTemplateRepositoryInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TransactionTemplateController extends Controller
{
    private TransactionTemplateRepositoryInterface $repository;

    public function __construct()
    {
        parent::__construct();

        $this->middleware(function ($request, $next) {
            app('view')->share('title', (string) trans('transaction_templates.title'));
            app('view')->share('mainTitleIcon', 'bi-file-earmark-text');
            $this->repository = app(TransactionTemplateRepositoryInterface::class);

            return $next($request);
        });
    }

    public function index(): View
    {
        return view('transaction-templates.index', ['templates' => $this->repository->getTemplates()]);
    }

    public function create(): View
    {
        return view('transaction-templates.create', $this->formData());
    }

    public function store(TransactionTemplateFormRequest $request): RedirectResponse
    {
        $this->repository->store($request->getTemplateData());

        session()->flash('success', (string) trans('transaction_templates.stored'));

        return redirect(route('transaction-templates.index'));
    }

    public function edit(int $id): View
    {
        $template = $this->findForUser($id);

        return view('transaction-templates.edit', array_merge($this->formData(), ['template' => $template]));
    }

    public function update(TransactionTemplateFormRequest $request, int $id): RedirectResponse
    {
        $this->repository->update($this->findForUser($id), $request->getTemplateData());

        session()->flash('success', (string) trans('transaction_templates.updated'));

        return redirect(route('transaction-templates.index'));
    }

    public function delete(int $id): View
    {
        return view('transaction-templates.delete', ['template' => $this->findForUser($id)]);
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->repository->destroy($this->findForUser($id));

        session()->flash('success', (string) trans('transaction_templates.deleted'));

        return redirect(route('transaction-templates.index'));
    }

    /**
     * Find a template belonging to the current user, or 404.
     *
     * This is the entire authorization story for the feature. Every action that
     * takes an {id} must go through it.
     */
    private function findForUser(int $id): TransactionTemplate
    {
        $template = $this->repository->find($id);
        if (null === $template) {
            throw new NotFoundHttpException();
        }

        return $template;
    }

    /**
     * Group accounts by their type so the select can use optgroups.
     *
     * Account names are unique only per type, so two accounts can share a name.
     * Without grouping they appear as indistinguishable options.
     *
     * @return array<string, array<int, string>>
     */
    private function groupAccounts(Collection $accounts): array
    {
        $return = [];
        foreach ($accounts as $account) {
            $type                        = (string) $account->accountType->type;
            $return[$type] ??= [];
            $return[$type][$account->id] = (string) $account->name;
        }

        return $return;
    }

    private function formData(): array
    {
        /** @var AccountRepositoryInterface $repository */
        $repository = app(AccountRepositoryInterface::class);

        return [
            'sourceAccounts'      => $this->groupAccounts($repository->getAccountsByType(TransactionTemplate::SOURCE_TYPES)),
            'destinationAccounts' => $this->groupAccounts($repository->getAccountsByType(TransactionTemplate::DESTINATION_TYPES)),
            'budgets'             => Budget::where('user_id', auth()->id())->orderBy('name')->pluck('name', 'id')->toArray(),
            'categories'          => Category::where('user_id', auth()->id())->orderBy('name')->pluck('name', 'id')->toArray(),
        ];
    }
}
