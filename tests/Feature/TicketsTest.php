<?php

namespace App\Modules\Ticket\tests\Feature;


use App\Models\User;


use App\Modules\Ticket\Database\Seeders\DatabaseSeederTests;
use App\Modules\Ticket\Models\Ticket;
use App\Modules\Ticket\tests\TestCase;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Livewire\Livewire;

/**
 * @group tickets
 *
 * todo more generic tests, own seeders, non related to probject model/components
 *
 * Class TicketsTest
 * @package App\Modules\Tickets\tests\Feature
 */
class TicketsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $user;

    public function getUserByRole($slug)
    {
        return User::whereHas('roles', function ($query) use ($slug) {
            $query->whereName($slug);
        })->first();
    }

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('auth.roles',  ['admin', 'user']);
        Config::set('auth.permissions', [
            'view everything', 'edit everything', 'export everything',
            'view own business', 'edit own business',
            'view own users', 'edit own users',
            'view own tickets', 'edit own tickets',
        ]);
        Config::set('auth.role_permissions', [
            'admin' => [
                'view everything', 'edit everything', 'export everything',
            ],
            'user' => [
                'view own business', 'edit own business',
                'view own tickets', 'edit own tickets',
            ]
        ]);
        Config::set('roles.role_to_component_class',[]);

        $this->seed(DatabaseSeederTests::class);


        $this->admin = $this->getUserByRole('admin');
        $this->user = $this->getUserByRole('user');

    }

    public function test_obvious()
    {
        $this->assertTrue(true);
    }

    public function test_can_see_livewire_component_on_tickets_page()
    {
        $this->actingAs($this->admin)
            ->withoutExceptionHandling()
            ->get(route_lang('tickets.tickets.table'))
            ->assertSuccessful()
            ->assertSeeLivewire('ticket::tickets-tickets-table');
    }

    public function test_user_can_see_livewire_component_on_tickets_page()
    {
        $this->actingAs($this->user)
            ->withoutExceptionHandling()
            ->get(route_lang('tickets.tickets.table'))
            ->assertSuccessful()
            ->assertSeeLivewire('ticket::tickets-user-tickets-table');
    }

    public function test_user_create_and_admin_reply()
    {
        //user crea ticket
        Livewire::actingAs($this->user)
            ->test('ticket::tickets-user-tickets-table')
            ->set('subject', 'Oggetto ticket')
            ->set('content', 'Contenuto ticket')
            ->set('ticket_category_id', 1)//technical
            ->call('newTicket')
            ->assertRedirect(route_lang('tickets.tickets.table'));

        $ticket = Ticket::where('subject', '=', 'Oggetto ticket')->first();

        Model::clearBootedModels();

        //l'admin lo vede nell'elenco
        Livewire::actingAs($this->admin)
            ->test('ticket::tickets-tickets-table')
            ->assertSee('Oggetto ticket')
        ;

        //può assegnarselo e rispondere
        Livewire::actingAs($this->admin)
            ->test('ticket::tickets-tickets-view',['ticket'=> $ticket])
            ->set('agent_id', $this->admin->id)
            ->call('assign')
            ->set('content', 'ticket comment!')
            ->call('comment')
            //->assertEmitted('livewire-on-messages', 'nuova risposta creata')
        ;

    }
}
