<?php

namespace Alexa\Request;

class User
{
    /**
     * @var null|string
     */
    public $userId;

    /**
     * @var null|string
     */
    public $accessToken;

    /**
     * @param array $data
     */
    public function __construct($data)
    {
        $this->userId = isset($data['userId']) ? $data['userId'] : null;
        $this->accessToken = isset($data['accessToken']) ? $data['accessToken'] : null;
    }

}