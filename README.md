# Amazon Alexa PHP Library

This is an extension of the amazon-alexa-php library by minicodemonkey/jakubsuchy (https://github.com/MiniCodeMonkey | https://github.com/jakubsuchy)

## Usage



Install via composer: 
```php
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/pixeljunkie/amazon-alexa-php"
        }
    ],
    "require": {
        "jakubsuchy/amazon-alexa-php": "dev-master"
    }
}
```

### Requests
When Amazon Alexa triggers your skill, a HTTP request will be sent to the URL you specified for your app.

You can get the `JSON` body of the request like so:
```php
$applicationId = "your-application-id-from-alexa"; // See developer.amazon.com and your Application. Will start with "amzn1.echo-sdk-ams.app."
$rawRequest = $request->getContent();
$alexa = new \Alexa\Request\Request($rawRequest, $applicationId);
$alexaRequest = $alexa->fromData();
```

The library expect raw request data, not parsed JSON as it needs to validate the request signature.

You can determine the type of the request with `instanceof`, e.g.:
```php
use Alexa\Request\IntentRequest;
...
if ($alexaRequest instanceof IntentRequest) {
	// Handle intent here
}

// or
if ($alexaRequest instanceof \Alexa\Request\IntentRequest) {
	// Handle intent here
}
```

### Certificate validation
By default the system validates the request signature by fetching Amazon's signing certificate and decrypting the signature. You need CURL to be able to get the certificate. No caching is done but you can override the Certificate class easily if you want to implement certificate caching yourself based on what your app provides:

Here is a basic example:
```php
class MyAppCertificate extends \Alexa\Request\Certificate {
  public function getCertificate() {
    $cached_certificate = retrieve_cert_from_myapp_cache();
    if (empty($cached_certificate)) {
      // Certificate is not cached, download it
      $cached_ertificate = $this->fetchCertificate();
      // Cache it now
    }
    return $cached_certificate;
  }
}
```

And then in your app, use the setCertificateDependency function:

```php
$certificate = new MyAppCertificate($_SERVER['HTTP_SIGNATURECERTCHAINURL'], $_SERVER['HTTP_SIGNATURE']);

$alexa = new \Alexa\Request\Request($rawRequest);
$alexa->setCertificateDependency($certificate);

$alexaRequest = $alexa->fromData();
```

### Application Id validation
The library will automatically validate your Application Id matches the one of the incoming request - you don't need to do anything for that. If and only if you wish to change how the validation happens, you might use a similar scenario to the certificate validation - provide your own Application class extending the \Alexa\Request\Application and providing a validateApplicationId() function as part of that. Pass your application to the Request library in a same way as the certificate:
```php

$application = new MyAppApplication($myappId);
$alexa = new \Alexa\Request\Request($rawRequest, $myappId);
$alexa->setApplicationDependency($application);

$alexaRequest = $alexa->fromData();
```


### Response
You can build an Alexa response with the `Response` class. You can optionally set a card or a reprompt too.

Here's a few examples.
```php
$response = new \Alexa\Response\Response;
$response->respond('Cooool. I\'ll lower the temperature a bit for you!')
	->withCard('Temperature decreased by 2 degrees');
```

```php
$response = new \Alexa\Response\Response;
$response->respond('What is your favorite color?')
	->reprompt('Please tell me your favorite color');
```

```php
$response = new \Alexa\Response\Response;
$response->respond('Starting account linking')
	->withLinkAccount();
```

To output the response, simply use the `->render()` function

```php
header('Content-Type: application/json');
echo json_encode($response->render());
exit;
```
