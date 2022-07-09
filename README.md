### php-meocloud-cli ###
PHP API Client implementation for the [Meocloud Storage System](https://meocloud.pt/documentation).


## Install

The package is hosted on [packagist](http://packagist.org). To install run:
```
composer install digfish/php-meocloud-cli
```

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
 
