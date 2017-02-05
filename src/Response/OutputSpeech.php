<?php

namespace Alexa\Response;

class OutputSpeech
{
    /**
     * @var string
     */
    public $type = 'PlainText';

    /**
     * @var string
     */
    public $text = '';

    /**
     * @var string
     */
    public $ssml = '';

    /**
     * @return array
     */
    public function render()
    {
        switch ($this->type) {
            case 'PlainText':
                return array(
                    'type' => $this->type,
                    'text' => $this->text,
                );
            case 'SSML':
                return array(
                    'type' => $this->type,
                    'ssml' => $this->ssml,
                );
        }
    }
}