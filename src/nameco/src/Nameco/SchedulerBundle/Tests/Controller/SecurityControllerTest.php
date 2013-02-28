<?php
namespace Nameco\SchedulerBundle\Tests\Controller;

class SecurityControllerTest extends BaseSchedulerControllerTest
{
        public function setUp()
	{
            parent::setUp();
            $this->loadData();
        }

	/**
	 * ログインできること
	 */
	public function testLogin()
	{
		$client = static::createClient();
                $this->login($client, 'test4', 'testtest');

                $crawler = $client->followRedirect();
		$this->assertTrue($crawler->filter('html:contains("Test4 Test4")')->count() > 0);

		$this->logout($client);
	}

	/**
	 * ログインできないこと
	 */
	public function testError()
	{
            	$client = static::createClient();
                $this->login($client, time(), time());

                $crawler = $client->followRedirect();
		$this->assertTrue($crawler->filter('html:contains("ユーザ名またはパスワードが違います")')->count() > 0);
	}

}