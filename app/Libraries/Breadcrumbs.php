<?php

namespace App\Libraries;

class Breadcrumbs
{
    public $active = '';

    protected $parents = [];

    public function active($active)
    {
        $this->active = $active;
    }

    public function pushParent($anchorText, $url)
    {
        $this->parents[] = [
            'anchorText'	=> $anchorText,
            'url'			=> $url,
        ];
    }

    public function get()
    {
        return view('user.common.breadcrumbs', [
            'parents'	=> $this->parents,
            'active'	=> $this->active,
        ])->render();
    }
}
