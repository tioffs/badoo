# Unofficial Api Social network Badoo [![License][packagist-license]][license-url]

[![Downloads][packagist-downloads]][packagist-url]
[![Telegram][Telegram-image]][Telegram-url]


- [Installation](#Installation)
- [Example](#Example)
- Method Api
    - [User Auth](#userAuth)
    - [Load Session](#Load-Session)
    - [Get User](#Get-User)
    - [Search User](#Search-User)
    - [Get City](#Get-City)
    - [Get Visitors](#Get-Visitors)
    - [Like User](#Like-User)
    - [Send Message](#Send-Message)
- [Use Proxy](#Use-Proxy)

## Installation
**Using Composer:**
```
composer require tioffs/badoo
```
## Example
```php
require_once __DIR__ . '/vendor/autoload.php';
$badoo = new UnofficialApi\Badoo('login', 'password');
```
# Method Api
## userAuth
Log in and get the session
```php
$auth = $badoo->userAuth();
#success
['user' => 0123456789]
#error
['error' => 'Authorization false']
```
## Load Session
Load authorized session
```php
$sessionLoad = $badoo->loadSession();
#success
true
#error
false
```
## Get User
Get user profile information
```php
#default return current user profile
$dataUser = $badoo->getUser();
#return user profile
$dataUser = $badoo->getUser(123456789);
#data user array
[
    'age' => 23,  //user age
    'albums' => [], //albums photo array
    'displayed_about_me' => [], //profile about text array
    'dob' => '1996-02-02' // date of birth
    'gender': 1, // 1 - men, 2 - women
    'is_blocked': false,
    'is_deleted': false,
    'is_favourite': false,
    'name' => 'Nikolas', // user name
    'online_status' => 3, // 1 - online, 2,3 - was online time
    'online_status_text' => 'Was online 8 hours ago',
    'photo_count' => 2, //count photo
    'profile_fields' => [], //the information is filled
    'profile_photo' => [], //current user photo
    'user_id' => 123345644 // user id

]
```
## Search User
Search for users with a filter
```php
$gender = 1; // 1 - men, 2 - women
$AgeStart = 18; // filter start age
$AgeEnd = 60; // filter end age
$count = 150; // count users
$offset = 1; //offset count
$country = ''; //default all city ''; use method getCity
$user = $badoo->searchUser($gender, $AgeStart, $AgeEnd, $count, $offset, $country);
#result data array users
```
## Get City
Obtaining the data for the subsequent search filter
```php
$city = $badoo->getCity('Moscow');
#success
countryId_regionId_cityId
#error
null
```
## Get Visitors
This method returns the list of visitors for the last week
```php
$data = $badoo->getVisitors();
#result data array users
```
## Like User
Liking user profile
```php
$userId = 123345644;
$badoo->likeUser($userId);
#success
true
#error
false
```
## Send Message
Send private message to user
```php
$userId = 123345644;
$message = 'test message';
$badoo->sendMessage($userId, $message);
#success
uid message
#error
false
```
## Use Proxy
```php
#use proxy http/https
$badoo->setProxy('127.0.0.1', '8080')
#use proxy authorization
$badoo->setProxy('127.0.0.1', '8080', 'login', 'password')
```

----

Made with &#9829; from the [@tioffs][tioffs-url]

[tioffs-url]: https://timlab.ru/
[license-url]: https://github.com/tioffs/badoo/blob/master/LICENSE

[telegram-url]: https://t.me/joinchat/C9JmzQ-fc3SKXI0D-9h-uw
[telegram-image]: https://img.shields.io/badge/Telegram-Join%20Chat-blue.svg?style=flat

[packagist-url]: https://packagist.org/packages/tioffs/badoo
[packagist-license]: https://img.shields.io/github/license/tioffs/badoo
[packagist-downloads]: https://img.shields.io/packagist/dm/tioffs/badoo