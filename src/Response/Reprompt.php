<?php

namespace Alexa\Response;

class Reprompt
{
    /**
     * @var OutputSpeech
     */
    public $outputSpeech;

    public function __construct()
    {
        $this->outputSpeech = new OutputSpeech;
    }

    /**
     * @return array
     */
    public function render()
    {
        return array(
            'outputSpeech' => $this->outputSpeech->render(),
        );
    }
}