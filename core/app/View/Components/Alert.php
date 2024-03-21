<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Alert extends Component
{
    public $type = null;
    public $route = null;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($type = 'base', $route = null)
    {
        $this->type = $type;
        $this->route = $route;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.alert');
    }
}
