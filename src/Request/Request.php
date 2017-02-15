<?php

namespace Alexa\Request;

use RuntimeException;
use InvalidArgumentException;
use DateTime;

use Alexa\Request\Certificate;
use Alexa\Request\Application;

class Request
{

    /**
     * @var string
     */
    public $requestId;

    /**
     * @var string
     */
    public $timestamp;

    /** @var Session */
    public $session;

    /**
     * @var string
     */
    public $data;

    /**
     * @var string
     */
    public $rawData;

    /**
     * @var string
     */
    public $applicationId;

    /**
     * Set up Request with RequestId, timestamp (DateTime) and user (User obj.)
     *
     * @param string $rawData
     * @param string $applicationId
     *
     * @throws InvalidArgumentException
     */
    public function __construct($rawData, $applicationId = null)
    {
        if (!is_string($rawData)) {
            throw new InvalidArgumentException(
                'Alexa Request requires the raw JSON data to validate request signature'
            );
        }

        // Decode the raw data into a JSON array.
        $data = json_decode($rawData, true);
        $this->data = $data;
        $this->rawData = $rawData;

        $this->requestId = $data['request']['requestId'];
        $this->timestamp = new DateTime($data['request']['timestamp']);
        $this->session = new Session($data['session']);

        $this->applicationId = (is_null($applicationId) && isset($data['session']['application']['applicationId']))
            ? $data['session']['application']['applicationId']
            : $applicationId;
    }

    /**
     * Accept the certificate validator dependency in order to allow people
     * to extend it to for example cache their certificates.
     * @param \Alexa\Request\Certificate $certificate
     */
    public function setCertificateDependency(\Alexa\Request\Certificate $certificate)
    {
        $this->certificate = $certificate;
    }

    /**
     * Accept the application validator dependency in order to allow people
     * to extend it.
     * @param \Alexa\Request\Application $application
     */
    public function setApplicationDependency(\Alexa\Request\Application $application)
    {
        $this->application = $application;
    }

    /**
     * Instance the correct type of Request, based on the $json->request->type value.
     *
     * @return \Alexa\Request\Request - the base class
     * @throws RuntimeException
     */
    public function fromData()
    {
        $whitelist = array('127.0.0.1', '::1');

        $localhost = false;
        if(in_array($_SERVER['REMOTE_ADDR'], $whitelist)){
            $localhost = true;
        }
        
        $data = $this->data;

        // Instantiate a new Certificate validator if none is injected
        // as our dependency.
        if (!isset($this->certificate) && !$localhost) {
            $this->certificate = new Certificate($_SERVER['HTTP_SIGNATURECERTCHAINURL'], $_SERVER['HTTP_SIGNATURE']);
        }
        if (!isset($this->application)) {
            $this->application = new Application($this->applicationId);
        }

        // We need to ensure that the request Application ID matches our Application ID.
        $this->application->validateApplicationId($data['session']['application']['applicationId']);

        if (!$localhost) {
            // Validate that the request signature matches the certificate.
            $this->certificate->validateRequest($this->rawData);
        }

        $className = $this->getRequestTypeClassName($data['request']['type']);
        $request = new $className($this->rawData, $this->applicationId);

        return $request;
    }

    /**
     * @param $requestType
     * @return string
     */
    private function getRequestTypeClassName($requestType)
    {
        if (!class_exists('\\Alexa\\Request\\'.$requestType)) {
            throw new RuntimeException('Unknown request type: '.$requestType);
        }

        return '\\Alexa\\Request\\'.$requestType;
    }

}
