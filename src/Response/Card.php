<?php

namespace Alexa\Response;

class Card
{
    /**
     * @var string
     */
    public $type = 'Simple';

    /**
     * @var string
     */
    public $title = '';

    /**
     * @var string
     */
    public $content = '';

    /**
     * @return array
     */
    public function render()
    {
        return array(
            'type' => $this->type,
            'title' => $this->title,
            'content' => $this->content,
        );
    }
}