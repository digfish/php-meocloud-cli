<?php 

require_once "vendor/autoload.php";

use GuzzleHttp\Client;
use Dotenv\Dotenv;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Subscriber\Oauth\Oauth1;
use \MirazMac\DotEnv\Writer;

Dotenv::createImmutable('.')->load();

$stack = HandlerStack::create();
$middleware = new Oauth1([
    'consumer_key'    => $_ENV['CONSUMER_KEY'],
    'consumer_secret' => $_ENV['CONSUMER_SECRET']
]);
$stack->push($middleware);

$client = new Client([
    'base_uri' => 'https://meocloud.pt/oauth/',
    'handler' => $stack
]);


// 1. Request token

$r = $client->post('request_token', [
    'auth' => 'oauth',
    'form_params' => [
        'oauth_callback' => 'oob'
    ]
]);


parse_str($r->getBody()->getContents(), $oauth_resp_params);

#print_r($oauth_resp_params);

// 2. Authorize token

$authorize_url = $client->getConfig('base_uri') . 'authorize?'. http_build_query(['oauth_token' =>$oauth_resp_params['oauth_token']]);
print("Visit this URL to authorize the app: $authorize_url\n");
print("Take note of the pin you are given.\n");
$pin = readline("Input the PIN please:");

// 3. Enter PIN and get access token
 $middleware = new Oauth1([
    'consumer_key'    => $_ENV['CONSUMER_KEY'],
    'consumer_secret' => $_ENV['CONSUMER_SECRET'],
    'token'           => $oauth_resp_params['oauth_token'],
    'token_secret'    =>  $oauth_resp_params['oauth_token_secret'],
    'verifier'        => $pin
]);
$stack->push($middleware);
$client = new Client([
    'base_uri' => 'https://meocloud.pt/oauth/',
    'handler' => $stack
]);
 
$resp = $client->post('access_token', [
    'auth' => 'oauth'
]);


parse_str($resp->getBody()->getContents(), $defintive_oauth_tokens);
#print_r($defintive_oauth_tokens);


$dotenvwriter = new Writer('.env');

$dotenvwriter->set('OAUTH_TOKEN', $defintive_oauth_tokens['oauth_token'])
->set('OAUTH_TOKEN_SECRET', $defintive_oauth_tokens['oauth_token_secret'])
->write();

print("New tokens saved to .env file.\n");

