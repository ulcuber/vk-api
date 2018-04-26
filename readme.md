`Package for PSR style`

# Vk API
* Uses curl

### Usage
```php
use Vk\Client;

$response = (new Client)->messages_send(['user_id' => $id, 'message' => $message]);
```
``_`` will replaced with ``.``

### Methods
| Method | Description         |
|--------|---------------------|
| token  | VK_API_ACCESS_TOKEN |
| method | 'get', 'post'       |

You can specify __VK_API_ACCESS_TOKEN__ in your _.env_, if using _Dotenv_
(Relies on getenv())

# Callback API
### Usage
```php
use Vk\CallbackApi;

(new CallbackApi(getenv('CALLBACK_API_CONFIRMATION_TOKEN')))
->setMessageHandler(function ($event) {})
->setErrorHandler(function ($error) {});
```
Error handler for script errors, not for API errors
