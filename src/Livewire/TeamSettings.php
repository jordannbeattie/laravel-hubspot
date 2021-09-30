<?php

namespace Jordanbeattie\Hubspot\Livewire;

use App\Jobs\PullHubspotListsForTeam;
use App\Models\Team;
use Livewire\Component;

class TeamSettings extends Component
{

    public $team, $connected, $confirmDisconnect;

    public function mount(Team $team)
    {
          $this->team = $team;
          $this->connected = $team->isConnectedToHubspot();
          $this->confirmDisconnect = false;
    }

    public function render()
    {
        return view('hubspot::livewire.team-settings');
    }

    public function disconnect()
    {
        foreach( $this->team->lists as $list )
        {
            $list->delete();
        }
        $this->team->disconnectFromHubspot();
        $this->mount($this->team->fresh());
    }

}
