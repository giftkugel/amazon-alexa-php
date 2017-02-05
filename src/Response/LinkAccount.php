<?php

namespace Alexa\Response;

class LinkAccount
{
    /**
     * @var string
     */
    public $type = 'LinkAccount';

    /**
     * @return array
     */
    public function render()
    {
        return array(
            'type' => $this->type,
        );
    }
}