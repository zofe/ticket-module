<?php

namespace App\Modules\Ticket\tests\Feature;

use App\Models\Company;
use App\Models\User;


use App\Modules\Ticket\Models\Ticket;
use App\Modules\Ticket\tests\TestCase;
use Illuminate\Database\Eloquent\Model;
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
    protected Company $company_isp;
    protected User $admin;
    protected User $isp;

    protected function setUp(): void
    {
        $this->markTestIncomplete();
//        parent::setUp();
//
//        $this->company_isp = Company::find('22222222-2222-2222-2222-222222222222');
//        $this->user = User::find('22222222-2222-0000-2222-222222222222');
//        $this->admin = User::find('11111111-1111-0000-1111-111111111111');
    }

    public function test_can_see_livewire_component_on_tickets_page()
    {
        $this->actingAs($this->admin)
            ->withoutExceptionHandling()
            ->get(route_lang('tickets.tickets.table'))
            ->assertSuccessful()
            ->assertSeeLivewire('ticket::tickets-tickets-table');
    }

    public function test_admin_can_reply_to_tickets()
    {
        //isp crea ticket
        Livewire::actingAs($this->isp)
            ->test('ticket::tickets-isp-tickets-table')
            ->set('subject', 'Oggetto ticket')
            ->set('content', 'Contenuto ticket')
            ->set('ticket_category_id', 1)//technical
            ->call('newTicket')
            ->assertRedirect(route_lang('tickets.tickets.table'));

        $ticket = Ticket::where('subject','=','Oggetto ticket')->first();

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
