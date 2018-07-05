<?php
/**
 * Created by IntelliJ IDEA.
 * User: rmiles
 * Date: 6/26/2018
 * Time: 3:21 PM
 */

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Table\carbon_users as Users;

final class UserTest extends TestCase
{
    public $user;

    public function setUp()/* The :void return type declaration that should be here would cause a BC issue */
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        define('TEST', true);

        $_SERVER = [
            "DOCUMENT_ROOT" => "C:\Users\rmiles\Documents\GitHub\Stats.Coach",
            "REMOTE_ADDR" => "::1",
            "REMOTE_PORT" => "53950",
            "SERVER_SOFTWARE" => "PHP 7.2.3 Development Server",
            "SERVER_PROTOCOL" => "HTTP/1.1",
            "SERVER_NAME" => "localhost",
            "SERVER_PORT" => "88",
            "REQUEST_URI" => "/login/",
            "REQUEST_METHOD" => "GET",
            "SCRIPT_NAME" => "/index.php",
            "SCRIPT_FILENAME" => "C:\Users\rmiles\Documents\GitHub\Stats.Coach\index.php",
            "PATH_INFO" => "/login/",
            "PHP_SELF" => "/index.php/login/",
            "HTTP_HOST" => "localhost:88",
            "HTTP_CONNECTION" => "keep-alive",
            "HTTP_CACHE_CONTROL" => "max-age=0",
            "HTTP_UPGRADE_INSECURE_REQUESTS" => "1",
            "HTTP_USER_AGENT" => "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36",
            "HTTP_ACCEPT" => "text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8",
            "HTTP_REFERER" => "http://localhost:88/",
            "HTTP_ACCEPT_ENCODING" => "gzip, deflate, br",
            "HTTP_ACCEPT_LANGUAGE" => "en-US,en;q=0.9",
            "HTTP_COOKIE" => "PHPSESSID=gn4amaq3el5giekaboa29q27gp; _gid=GA1.1.1938536140.1530039320",
            "REQUEST_TIME_FLOAT" => 1530054388.652,
            "REQUEST_TIME" => 1530054388,
        ];

        include_once __DIR__ . './../index.php';

        $_POST = [
            'username' => 'Admin',
            'password' => 'goldteamrules',
            'password2' => 'goldteamrules',
            'email' => 'Tmiles199@gmail.com',
            'firstname' => 'Dick',
            'lastname' => 'Miles',
            'gender' => 'Male',
            'Terms' => '1'
        ];

        $this->user = [];
        Users::Get($this->user, null, [
            'user_username' => 'Admin'
        ]);
    }

    /**
     * @runInSeparateProcess
     */
    public function testUserCanBeCreated(): void
    {

        $this->user = [];
        Users::Get($this->user, null, [
            'user_username' => 'Admin'
        ]);

        if (!empty($this->user)){
            $this->testUserCanBeDeleted();
        }


        $this->assertTrue(Users::Post([
            'user_type' => 'Athlete',
            'user_ip' => '127.0.0.1',
            'user_sport' => 'GOLF',
            'user_email_confirmed' => 1,
            'user_username' => 'admin',
            'user_password' => 'goldteam',
            'user_email' => 'richard@miles.systems',
            'user_first_name' => 'Richard',
            'user_last_name' => 'Miles',
            'user_gender' => 'Male'
        ]));



        $this->assertTrue(false);

        //$method = 'register';

        //$this->assertFalse(CM('User', $method)());        // This route redirects to home, thus ending in false

//        $this->assertEquals('Welcome to Stats Coach. Please check your email to finish your registration.',
//            $GLOBALS['json']['alert']['success']);

    }


    /**
     * @depends testUserCanBeCreated
     * @runInSeparateProcess
     */
    public function testUserCanBeRetrieved(): void
    {
        $this->user = [];
        $this->assertTrue(
            Users::Get($this->user, null, ['user_username' => 'Admin']
            ));

        $this->assertInternalType('array', $this->user);

        var_dump($this->user);

        $this->assertArrayHasKey('user_email', $this->user);

        // This route redirects to home, thus ending in false
    }

    /**
     * @depends testUserCanBeRetrieved
     * @runInSeparateProcess
     */
    public function testUserCanBeUpdated(): void
    {
        $this->assertTrue(
            Users::Put($this->user, $this->user['user_id'], [
                'user_first_name' => 'lil\'Rich'
            ]));

        $this->assertEquals('lil\'Rich', $this->user['user_first_name']);
    }


    /**
     * @depends testUserCanBeRetrieved
     * @runInSeparateProcess
     */
    public function testUserCanBeDeleted(): void
    {
        $this->assertTrue(
            Users::Delete(
                $this->user, $this->user['user_id'], [
                    'email' => 'Tmiles199@gmail.com'
                ]
            )
        );

        $this->assertNull($this->user);
    }


}