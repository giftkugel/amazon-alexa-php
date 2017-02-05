<?php

namespace Alexa\Request;

class LaunchRequest extends Request
{
    public $applicationId;

    /**
     * @param string $rawData
     */
    public function __construct($rawData)
    {
        parent::__construct($rawData);
        $data = $this->data;

        $this->applicationId = $data['session']['application']['applicationId'];
    }
}
