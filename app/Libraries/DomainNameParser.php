<?php

namespace App\Libraries;

use LayerShifter\TLDExtract\Extract;

class DomainNameParser
{
    protected $url;

    public function __construct($url)
    {
        $this->url = $url;
        $this->extractor = new Extract();
    }

    public function get()
    {
        $result = $this->extractor->parse($this->url);

        return $result->getRegistrableDomain();
    }
}
