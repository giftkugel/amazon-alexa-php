<?php

namespace Alexa\Response;

class Response
{
    /**
     * @var string
     */
    public $version = '1.0';

    /**
     * @var array
     */
    public $sessionAttributes = array();

    /**
     * @var OutputSpeech|null
     */
    public $outputSpeech = null;

    /**
     * @var null|Card
     */
    public $card = null;

    /**
     * @var null|LinkAccount
     */
    public $linkaccount = null;

    /**
     * @var null|Reprompt
     */
    public $reprompt = null;

    /**
     * @var bool
     */
    public $shouldEndSession = false;

    public function __construct()
    {
        $this->outputSpeech = new OutputSpeech;
    }

    /**
     * Set output speech as text.
     *
     * @param string $text
     * @return \Alexa\Response\Response
     */
    public function respond($text)
    {
        $this->outputSpeech = new OutputSpeech;
        $this->outputSpeech->text = $text;

        return $this;
    }

    /**
     * Set up response with SSML.
     * @param string $ssml
     * @return \Alexa\Response\Response
     */
    public function respondSSML($ssml)
    {
        $this->outputSpeech = new OutputSpeech;
        $this->outputSpeech->type = 'SSML';
        $this->outputSpeech->ssml = $ssml;

        return $this;
    }

    /**
     * Set up reprompt with given text
     * @param string $text
     * @return \Alexa\Response\Response
     */
    public function reprompt($text)
    {
        $this->reprompt = new Reprompt;
        $this->reprompt->outputSpeech->text = $text;

        return $this;
    }

    /**
     * Set up reprompt with given ssml
     * @param string $ssml
     * @return \Alexa\Response\Response
     */
    public function repromptSSML($ssml)
    {
        $this->reprompt = new Reprompt;
        $this->reprompt->outputSpeech->type = 'SSML';
        $this->reprompt->outputSpeech->text = $ssml;

        return $this;
    }

    /**
     * Add card information
     * @param string $title
     * @param string $content
     * @return \Alexa\Response\Response
     */
    public function withCard($title, $content = '')
    {
        $this->card = new Card;
        $this->card->title = $title;
        $this->card->content = $content;

        return $this;
    }

    /**
     * Add link account information
     * @return \Alexa\Response\Response
     */
    public function withLinkAccount()
    {
        $this->linkaccount = new LinkAccount;

        return $this;
    }

    /**
     * Set if it should end the session
     * @param bool $shouldEndSession
     * @return \Alexa\Response\Response
     */
    public function endSession($shouldEndSession = true)
    {
        $this->shouldEndSession = $shouldEndSession;

        return $this;
    }

    /**
     * Add a session attribute that will be passed in every requests.
     * @param string $key
     * @param mixed $value
     */
    public function addSessionAttribute($key, $value)
    {
        $this->sessionAttributes[$key] = $value;
    }

    /**
     * Return the response as an array for JSON-ification
     * @return array
     */
    public function render()
    {
        $cardObject = $this->card ? $this->card : $this->linkaccount;

        return array(
            'version' => $this->version,
            'sessionAttributes' => $this->sessionAttributes,
            'response' => array(
                'outputSpeech' => $this->outputSpeech ? $this->outputSpeech->render() : null,
                'card' => $cardObject ? $cardObject->render() : null,
                'reprompt' => $this->reprompt ? $this->reprompt->render() : null,
                'shouldEndSession' => $this->shouldEndSession ? true : false,
            ),
        );
    }
}