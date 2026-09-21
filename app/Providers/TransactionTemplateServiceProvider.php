<?php

declare(strict_types=1);

namespace FireflyIII\Providers;

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator;
use FireflyIII\Models\Account;
use FireflyIII\Repositories\Account\AccountRepositoryInterface;
use FireflyIII\Repositories\TransactionTemplate\TransactionTemplateRepository;
use FireflyIII\Repositories\TransactionTemplate\TransactionTemplateRepositoryInterface;
use FireflyIII\Support\Facades\Amount;
use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class TransactionTemplateServiceProvider extends ServiceProvider
{
    /**
     * The only transaction type templates apply to.
     */
    public const APPLIES_TO = 'withdrawal';

    public function boot(): void
    {
        Route::middleware('web')->group(base_path('routes/transaction-templates.php'));

        $this->registerBreadcrumbs();

        View::composer('partials.form.transaction.transaction-template', function (ViewContract $view): void {
            $view->with('templates', $this->pickerPayload());
        });
    }

    public function register(): void
    {
        // reference to auth is not understood by phpstan.
        $this->app->bind(static function (Application $app): TransactionTemplateRepositoryInterface {
            /** @var TransactionTemplateRepositoryInterface $repository */
            $repository = app(TransactionTemplateRepository::class);
            if ($app->auth->check()) {
                $repository->setUser(auth()->user());
            }

            return $repository;
        });
    }

    // Kept out of routes/breadcrumbs.php to facilitate upstream merges to fork given the feature likely won't be upstreamed.
    private function registerBreadcrumbs(): void
    {
        Breadcrumbs::for(
            'transaction-templates.index',
            static function (Generator $breadcrumbs): void {
                $breadcrumbs->parent('index');
                $breadcrumbs->push(trans('transaction_templates.breadcrumb'), route('transaction-templates.index'));
            }
        );
        Breadcrumbs::for(
            'transaction-templates.create',
            static function (Generator $breadcrumbs): void {
                $breadcrumbs->parent('transaction-templates.index');
                $breadcrumbs->push(trans('transaction_templates.breadcrumb_create'), route('transaction-templates.create'));
            }
        );
        Breadcrumbs::for(
            'transaction-templates.edit',
            static function (Generator $breadcrumbs, int|string $id): void {
                $breadcrumbs->parent('transaction-templates.index');
                $breadcrumbs->push(trans('transaction_templates.breadcrumb_edit'), route('transaction-templates.edit', [$id]));
            }
        );
        Breadcrumbs::for(
            'transaction-templates.delete',
            static function (Generator $breadcrumbs, int|string $id): void {
                $breadcrumbs->parent('transaction-templates.index');
                $breadcrumbs->push(trans('transaction_templates.breadcrumb_delete'), route('transaction-templates.delete', [$id]));
            }
        );
    }

    /**
     * The currency the create form should lock the amount to.
     */
    private function accountCurrencyCode(?Account $account): ?string
    {
        if (null === $account) {
            return null;
        }

        return (app(AccountRepositoryInterface::class)->getAccountCurrency($account) ?? Amount::getPrimaryCurrency())->code;
    }

    /**
     * Data for the picker on the transaction create form.
     *
     * Returns an empty array unless the current request is the withdrawal
     * create form. That single check keeps the picker off the edit form (which
     * shares the same Blade component) and off deposit/transfer create forms.
     */
    private function pickerPayload(): array
    {
        $route = request()->route();
        if (null === $route) {
            return [];
        }
        if ('transactions.create' !== $route->getName()) {
            return [];
        }
        if (self::APPLIES_TO !== $route->parameter('objectType')) {
            return [];
        }
        if (!auth()->check()) {
            return [];
        }

        $templates = app(TransactionTemplateRepositoryInterface::class)->getTemplates();

        $return    = [];
        foreach ($templates as $template) {
            $return[] = [
                'id'                                => (string) $template->id,
                'name'                              => $template->name,
                'transaction_description'           => $template->transaction_description,
                'source_account_id'                 => null === $template->source_account_id ? null : (string) $template->source_account_id,
                'source_account_name'               => $template->sourceAccount?->name,
                'source_account_type'               => $template->sourceAccount?->accountType->type,
                'source_account_currency_code'      => $this->accountCurrencyCode($template->sourceAccount),
                'destination_account_id'            => null === $template->destination_account_id ? null : (string) $template->destination_account_id,
                'destination_account_name'          => $template->destinationAccount?->name,
                'destination_account_type'          => $template->destinationAccount?->accountType->type,
                'destination_account_currency_code' => $this->accountCurrencyCode($template->destinationAccount),
                'budget_id'                         => null === $template->budget_id ? null : (string) $template->budget_id,
                'category_name'                     => $template->category?->name,
                'tags'                              => $template->tags ?? [],
                'notes'                             => $template->notes,
            ];
        }

        return $return;
    }
}
