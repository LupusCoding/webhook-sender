# webhook-sender - Webhook Sender
Simple Sender for webhook requests.

## Contents
 - [Requirements](#requirements)
 - [Install](#install)
 - [Development](#development)
 - [Usage](#usage)
 - [Testing](#testing)

## Requirements <a id="requirements" href="#requirements">#</a>

 - PHP >= 7.4
 
## Install <a id="install" href="#install">#</a>

```shell
composer require lupuscoding/webhook-sender
```

## Usage <a id="usage" href="#usage">#</a>

### Send a message to a webhook
The sender accepts objects, that implement the JsonSerializable interface. Just initialize an object, that implements
the interface and hand it over, to the Sender::send method.
```php
use LupusCoding\Webhooks\Sender\Sender;
// Init your serializable object
/** @var JsonSerializable $mySerializableObject */
$mySerializableObject = new MySerializableObject();
// Setup the hook url
$webhookUrl = 'https://httpbin.org/post';
// Init sender
$sender = new Sender($webhookUrl, false);
// Send object ot webhook
$sender->send($mySerializableObject);
// Get response
$response = $sender->getLastResponse();
```

### Create a valid serializable object
By implementing the JsonSerializable interface and creating the jsonSerialize method, you are able to decide, which 
data will be sent.

```php
use JsonSerializable;

class MySerializableObject implements JsonSerializable
{
    private string $stringToPush;
    private string $stringToProcess;
    private bool $boolToPush;
    private bool $onlyToProcess;
    
    /* Getters and Setters may be here */
    
    public function jsonSerialize(): array
    {
        return [
            'stringToPush' => $this->stringToPush,
            'boolToPush' => $this->boolToPush,
        ];
    }
}
```
This example has two properties that should be pushed / send and two properties that should not be sent.

## Development <a id="development" href="#development">#</a>

* Every contribution should respect PSR-2 and PSR-12.
* Methods must provide argument types and return types.
* Class properties must be typed.
* doc blocks must only contain descriptive information.
* doc blocks may additionally contain a type declaration for arguments or
  return values, if the type declaration is not precise.

For example: ```func(): array``` may not be precise if the method returns
an array of arrays or objects. Consider a doc block entry like
```@return array[]``` or ```@return MyObject[]``` for clarification.

## Testing <a id="testing" href="#testing">#</a>

Webhook test site: https://httpbin.org

First install **phpunit** by executing
```shell
composer install
```
Then start phpunit by executing
```shell
vendor/bin/phpunit
```
**Optional:** Look at the webhook test site, to get more information.