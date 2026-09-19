<?php

declare(strict_types=1);

namespace FireflyIII\Console\Commands\Tools;

use FireflyIII\Console\Commands\ShowsFriendlyMessages;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Escape hatch for the transaction templates feature: leaves the database in the
 * state upstream Firefly III expects, so the fork can be abandoned.
 *
 * "migrate:rollback --path=" is not an alternative: it rolls back the whole last
 * batch, and on a fresh install every upstream migration shares batch 1.
 *
 * Equivalent SQL, should this command no longer exist:
 *   DROP TABLE IF EXISTS transaction_templates;
 *   DELETE FROM migrations WHERE migration = '2026_09_18_120000_create_transaction_templates';
 */
class RemoveTransactionTemplates extends Command
{
    use ShowsFriendlyMessages;

    public const MIGRATION  = '2026_09_18_120000_create_transaction_templates';

    public const TABLE      = 'transaction_templates';

    protected $description  = 'Removes the transaction templates table, to return to an unmodified Firefly III.';

    protected $signature    = 'firefly-iii:remove-transaction-templates {--force : Skip confirmation.}';

    public function handle(): int
    {
        if (!Schema::hasTable(self::TABLE)) {
            $this->friendlyInfo(sprintf('Table "%s" does not exist, nothing to do.', self::TABLE));

            return 0;
        }

        $count = DB::table(self::TABLE)->count();
        if (!$this->option('force') && !$this->confirm(sprintf('Drop table "%s" and its %d row(s)? This cannot be undone.', self::TABLE, $count), false)) {
            $this->friendlyInfo('Cancelled.');

            return 0;
        }

        Schema::drop(self::TABLE);
        $removed = DB::table('migrations')->where('migration', self::MIGRATION)->delete();

        $this->friendlyPositive(sprintf('Dropped "%s" (%d row(s)) and removed %d migration record(s).', self::TABLE, $count, $removed));
        $this->friendlyInfo('The database is now as upstream Firefly III expects it.');

        return 0;
    }
}
