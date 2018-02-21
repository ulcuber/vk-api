`Package for PSR style`

# Vk API
* Uses curl

### Usage
```php
use Vk\Api;

(new Api)->messagesSend($id, $message);
```

### Methods
| Method                        | Description                |
|-------------------------------|----------------------------|
| setAccessToken                |VK_API_ACCESS_TOKEN         |
| messagesSend                  |                            |
| usersGet                      |                            |
| photosGetMessagesUploadServer |                            |
| photosSaveMessagesPhoto       |                            |
| docsGetMessagesUploadServer   |                            |
| docsSave                      |                            |
| apiCall                       | Base methods for API calls |
| upload                        |                            |

You can specify VK_API_ACCESS_TOKEN in your .env, if using Dotenv
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
