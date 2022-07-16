### php-meocloud-cli ###
PHP API Client implementation for the [Meocloud Storage System](https://meocloud.pt/documentation).


## Install

The package is hosted on [packagist](http://packagist.org). To install run:
```
composer require --dev digfish/php-meocloud-cli
```

## Get credentials
To use this application, you must be a Meo Client, register at MeoCloud with your MeoID, and there [register for a new application](https://meocloud.pt/my_apps). Get your Consumer key and Secret and Choose "Out of Band" has the way to authorize the tokens. Uncheck "sandbox" in order to have total access, then a create a file `.env` in the main directory with this content:
```
CONSUMER_KEY=<YOUR CONSUMER KEY HERE>
CONSUMER_SECRET=<YOUR SECRET HERE>
```
Using this file execute the script `grab_credentials.php` in the main directory, you will prompted to visit an url in your browser, click "AUTORIZAR" and you will be provided with a PIN that you should input at the script. If everything goes well, the `.env` file will be rewritten with the new oauth token and oauth secret. With this data provided, you are ready to use the project.

## Code example
```php
use \digfish\meocloudclient\MeocloudClient;

$meo = new MeocloudClient();
// grab the root directory list
$dirlist = $meo->get_metadata();
// send a file to meocloud
$meo->send_file('README.md');
// get a file from meocloud
$meo->get_file('README.md');
// create directory
$meo->create_folder('ola');
// delete file
$meo->delete_file('README.md');
```
## Unit tests
You have a file with unitary tests: `MeocloudClientTest.php`. Just run `phpunit MeocloudClientTest.php`.

## Environment variables
The variables `CONSUMER_KEY`,`CONSUMER_SECRET`,`OAUTH_TOKEN` and `OAUTH_TOKEN_SECRET` should hold the value of your API key. You can set this via a .`env` file or your own code using `putenv` or for example  `$_ENV['OAUTH_TOKEN_SECRET']`.


## What is implemented ##

|   Method          |    API                   |
|-------------------|--------------------------|
| get_metadata()    | GET Metadata/meocloud/   |
| get_file()        | GET Files/meocloud/:name |
| send_file()       | PUT Files/meocloud/:name |
| delete_file()     | POST Fileops/Delete      |
| create_folder()   | POST Fileops/CreateFolder|
------------------------------------------------
 
