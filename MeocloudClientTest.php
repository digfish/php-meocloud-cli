<?php

include_once "vendor/autoload.php";

use Dotenv\Dotenv;
use \PHPUnit\Framework\TestCase;
use \Faker\Factory;
use \digfish\meocloudclient\MeocloudClient;

final class MeocloudClientTest extends TestCase {

    var $client;
    static $faker;
    static $filename;

    protected function setUp(): void {
        //parent::setUp();
        $dotenv = Dotenv::createImmutable('.');
        $dotenv->load();
        self::$filename = 'test_file.txt';
        self::$faker = Factory::create();

        $this->client = new MeocloudClient();
    }

    public function testGetMetadata() {
        $metadata = $this->client->get_metadata();
        $this->assertEquals(200, $this->client->lastStatus);
    }

    public function testSendFile() {
		file_put_contents(self::$filename, self::$faker->text);
		$metadata = $this->client->send_file(self::$filename);
		$this->assertEquals(200, $this->client->lastStatus);
	}

    public function testGetFile() {
        $success = $this->client->get_file(self::$filename);
        $this->assertEquals(200, $this->client->lastStatus);
    }


    public function testDeleteFile() {
        $this->client->delete_file(self::$filename);
        $this->assertEquals(200, $this->client->lastStatus);
    }

    public function testAccountInfo() {
        $info = $this->client->account_info();
        $this->assertEquals($this->client->lastStatus,200);
    }

    public function testCreateFolder() {
        $folder = self::$faker->word(8);
        $this->client->create_folder('php');
        $this->assertEquals($this->client->lastStatus,403);
    }

    public function testSearch() {
        $this->client->search('php');
        $this->assertEquals($this->client->lastStatus,200);
    }


}
