<?php

namespace digfish\meocloudclient;

include_once "vendor/autoload.php";


use GuzzleHttp\Client;
use Dotenv\Dotenv;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Subscriber\Oauth\Oauth1;
use GuzzleHttp\Exception\ClientException;


class MeocloudClient {
 
    private $client;
    public $lastStatus;
    const MEOCLOUD_CONTENT_ENDPOINT = 'https://api-content.meocloud.pt/1';

    public function __construct() {
        $dotenv = Dotenv::createImmutable('.');
        $dotenv->load();
        if (!empty($_ENV['HTTP_PROXY'])) {
            putenv("HTTP_PROXY={$_ENV['HTTP_PROXY']}");
        }
        $stack = HandlerStack::create();
        $middleware = new Oauth1([
            'consumer_key'    => $_ENV['CONSUMER_KEY'],
            'consumer_secret' => $_ENV['CONSUMER_SECRET'],
            'token'           => $_ENV['OAUTH_TOKEN'],
            'token_secret'    => $_ENV['OAUTH_TOKEN_SECRET']
        ]);
        $stack->push($middleware);
        $this->client = new Client([
            'base_uri' => 'https://api.meocloud.pt/1/',
            'handler' => $stack
        ]);
    }

    protected function _invoke($uri, $http_met = 'GET', $args=[])
    {
        $resp = null;
        if (!empty($args['body']) && $args['encode_body'] == TRUE) {
            $args['body'] = json_encode($args['body']);
        }
        $args = array_merge($args,['auth' => 'oauth']);
        try {
            $resp = $this->client->request($http_met, $uri,$args);
        } catch (ClientException $e) {
            //echo ($e->getMessage());
            $this->lastStatus = $e->getResponse()->getStatusCode();
            throw new \Exception($e->getMessage(),$this->lastStatus,$e);
        }
        $this->lastStatus = $resp->getStatusCode();
        return $resp;
    }

    protected function _invoke_json($uri, $http_met = 'GET', $args=[],$decode_resp=TRUE,$encode_body=TRUE)
    {
        $args['encode_body'] = $encode_body;
        $resp = $this->_invoke($uri, $http_met, $args);
        $body = $resp->getBody();
        $body = $body->getContents();
        if ($decode_resp) {
            $body = json_decode($body);
        }
        return $body;
    }




    public function get_metadata($path='',$params=[]) {
        $args['params'] = $params;
        return $this->_invoke_json('Metadata/meocloud' . $path,'GET',$args);
    }
 
    public function get_file($filename) {
        $complete_uri = self::MEOCLOUD_CONTENT_ENDPOINT . '/Files/meocloud/' . $filename;
        $resp = $this->_invoke( $complete_uri,'GET',['stream' => true]);
        
        $content = $resp->getBody();
        return $content;
        #return file_put_contents($filename,$resp->getBody()->getContents());
    }

    public function send_file($filename) {
    	return $this->send_data($filename,file_get_contents($filename));
   }

   public function send_data($newfilename,$data) {
	   $complete_uri = self::MEOCLOUD_CONTENT_ENDPOINT . '/Files/meocloud/' . $newfilename;
	   return $this->_invoke_json($complete_uri,'PUT',['body'=>$data, 'stream'=>TRUE],TRUE,FALSE);
   }

    public function delete_file($filename) {
        $args['form_params'] = ['root' => 'meocloud', 'path' => '/' . $filename];
        return $this->_invoke_json("Fileops/Delete",'POST',$args);

    }

    public function create_folder($foldername)
    {
        $args['form_params'] = ['root' => 'meocloud', 'path' => '/' . $foldername];
        $response = null;
        try {
            $response = $this->_invoke_json("Fileops/CreateFolder", 'POST', $args);
        } catch (\Exception $e) {
            if ($this->lastStatus == 403) {
                $response = $e->getPrevious()->getResponse();
            } else {
                throw $e;
            }
        }
        return $response; 
    }


    public function account_info() {
        return $this->_invoke_json('Account/Info');
    }

    public function search($path,$query,$mime_type,$file_limit=1000,$include_deleted=FALSE) {
        //$dirname = substr($query,0,strrchr($query, '/')); 
        //$basename = substr($query,strrchr($query,'/'),strlen($query));
        $args['params'] = [
            'search' => $query,
            'file_limit' => $file_limit,
            'include_deleted' => $include_deleted,
            'mime_type' => $mime_type
        ];
        $args['debug'] = true;
        $response = null;
        try {
            $response = $this->_invoke_json("Search/meocloud/$path", 'GET', $args);
        } catch (\Exception $e) {
            //echo ($e->getMessage());
            if ($e->getCode() == 404) {
                $response = $e->getPrevious()->getResponse();
            } else {
                throw $e;
            }
        }
        return $response;
    }
}