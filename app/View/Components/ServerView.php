<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ServerView extends Component
{
    public $server, $url;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($server, $url)
    {
        $this->server = $server;
        $this->url = $url;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.server-view');
    }
}
