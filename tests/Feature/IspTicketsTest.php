<?php

namespace App\Modules\Tickets\tests\Feature;

use App\Models\Company;
use App\Models\User;


use App\Modules\Tickets\Models\Ticket;
use App\Modules\Tickets\tests\TestCase;
use Livewire\Livewire;

/**
 * @group tickets
 *
 * Class IspTicketsTest
 * @package App\Modules\Tickets\tests\Feature
 */
class IspTicketsTest extends TestCase
{
    protected Company $company_isp;
    protected User $admin;
    protected User $isp;

    protected function setUp():void
    {
        parent::setUp();

        $this->company_isp = Company::find('22222222-2222-2222-2222-222222222222');
        $this->isp = User::find('22222222-2222-0000-2222-222222222222');
        $this->admin = User::find('11111111-1111-0000-1111-111111111111');
    }

    public function test_can_see_livewire_component_on_companies_page()
    {
        $this->actingAs($this->isp)
            ->withoutExceptionHandling()
            ->get(route_lang('tickets.tickets.table'))
            ->assertSuccessful()
            ->assertSeeLivewire('tickets::tickets-isp-tickets-table')
        ;
    }

    public function test_isp_can_open_tickets()
    {
         Livewire::actingAs($this->isp)
            ->test('tickets::tickets-isp-tickets-table')
            ->set('subject','Oggetto ticket')
            ->set('content','Contenuto ticket')
            ->set('ticket_category_id',1)//technical
            ->call('newTicket')
            ->assertRedirect(route_lang('tickets.tickets.table'))
        ;

        $ticket = Ticket::where('subject','=','Oggetto ticket')->first();
        $this->assertNotNull($ticket);


        Livewire::actingAs($this->isp)
            ->test('tickets::tickets-isp-tickets-table')
            ->assertSee('Oggetto ticket')
        ;

        Livewire::actingAs($this->isp)
            ->test('tickets::tickets-isp-tickets-view',['ticket'=> $ticket])
            ->assertSee('Oggetto ticket')
        ;


    }

}
