<?php 
namespace Nameco\User\SchedulerBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
	/**
	 * ログインできること
	 */
	public function testLogin()
	{
		$client = static::createClient();
		$crawler    = $client->request('GET', '/login');
		$login_form = $crawler->selectButton('submit')->form();
		$crawler = $client->submit($login_form, array(
				'_username'      => 'admin',
				'_password'      => 'namecoadmin',
		));
		$crawler    = $client->request('GET', '/admin/user/new');
		$this->assertTrue($crawler->filter('html:contains("名前")')->count() > 0);
		
		$client->request('GET', '/logout');
	}
	
	/**
	 * ログインできないこと
	 */
	public function testError()
	{
		$client     = static::createClient();
		$crawler    = $client->request('GET', '/login');
		$login_form = $crawler->selectButton('submit')->form();
		$crawler = $client->submit($login_form, array(
				'_username'      => time(),
				'_password'      => time()
		));
		
		$crawler = $client->followRedirect();
		$this->assertTrue($crawler->filter('html:contains("ユーザ名またはパスワードが違います")')->count() > 0);
	}
	 
}