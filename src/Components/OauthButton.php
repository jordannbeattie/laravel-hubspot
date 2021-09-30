<?php

namespace Jordanbeattie\Hubspot\Components;

use Illuminate\View\Component;

class OauthButton extends Component
{
    public $url, $copy, $icon;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct( $copy = "Login with Hubspot", $icon = 'true', $returnUrl = false)
    {
        $this->url = $returnUrl ? route('hubspot.auth.login') . "?returnTo=" . $returnUrl : route('hubspot.auth.login');
        $this->copy = $copy;
        $this->icon = ($icon == "false" ? false : true);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('hubspot::components.oauth-button');
    }
}
