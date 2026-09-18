<?php

declare(strict_types=1);

namespace Tests\integration\TransactionTemplate;

use FireflyIII\Models\TransactionTemplate;
use FireflyIII\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Override;
use Tests\integration\TestCase;

/**
 * @internal
 *
 * @covers \FireflyIII\Http\Controllers\TransactionTemplate\TransactionTemplateController
 */
final class TransactionTemplateControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    public function testDestroyRemovesTemplate(): void
    {
        $template = TransactionTemplate::create(['user_id' => $this->user->id, 'name' => 'Gone soon']);

        $this->post(route('transaction-templates.destroy', [$template->id]))
            ->assertRedirect(route('transaction-templates.index'))
        ;

        $this->assertNull(TransactionTemplate::find($template->id));
    }

    public function testEditRejectsTemplateOfOtherUser(): void
    {
        $other    = User::create(['email' => 'other@firefly', 'password' => 'secret']);
        $template = TransactionTemplate::create(['user_id' => $other->id, 'name' => 'Not yours']);

        $this->get(route('transaction-templates.edit', [$template->id]))->assertNotFound();
    }

    public function testIndexShowsOnlyOwnTemplates(): void
    {
        $other = User::create(['email' => 'other@firefly', 'password' => 'secret']);
        TransactionTemplate::create(['user_id' => $this->user->id, 'name' => 'Mine']);
        TransactionTemplate::create(['user_id' => $other->id, 'name' => 'Theirs']);

        $this->get(route('transaction-templates.index'))
            ->assertOk()
            ->assertSee('Mine')
            ->assertDontSee('Theirs')
        ;
    }

    public function testStoreParsesTagsAndIgnoresBlankFields(): void
    {
        $this->post(route('transaction-templates.store'), [
            'name'                    => 'Weekly groceries',
            'transaction_description' => 'Lidl',
            'tags'                    => 'food, weekly ,, ',
            'notes'                   => '',
        ])->assertRedirect(route('transaction-templates.index'));

        $template = TransactionTemplate::where('name', 'Weekly groceries')->first();

        $this->assertNotNull($template);
        $this->assertSame(['food', 'weekly'], $template->tags);
        $this->assertNull($template->notes);
        $this->assertNull($template->source_account_id);
    }

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createAuthenticatedUser();
        $this->actingAs($this->user);
    }
}
