<?php
namespace Nameco\UserBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
	/**
	 * 登録できること
	 */
	public function testNew()
	{
		$client = static::createClient();
		$this->login($client);

		$mail = time(). 'test-mail@minori-sol.jp';
		$crawler = $this->add($client, $mail);
		$this->assertTrue($crawler->filter('html:contains("登録しました")')->count() > 0);

		$this->remove($mail);
		$this->logout($client);
	}

	/**
	 * 重複したメールが登録できないこと
	 */
	public function testDuplicate()
	{
		$client = static::createClient();
		$this->login($client);

		$mail = time(). 'test-mail@minori-sol.jp';
		$crawler = $this->add($client, $mail);
		$this->assertTrue($crawler->filter('html:contains("登録しました")')->count() > 0);

		$crawler = $this->add($client, $mail);
		$this->assertTrue($crawler->filter('html:contains("既に登録されています")')->count() > 0);

		$this->remove($mail);
		$this->logout($client);
	}

	/**
	 * 遷移できないこと
	 */
	public function testAuth()
	{
		$client = static::createClient();
		$client->request('GET', '/admin/user/new');
		$crawler = $client->followRedirect();
		$this->assertTrue(
				$crawler->filter('html:contains("名前")')->count() == 0
				&& $crawler->filter('html:contains("ログイン")')->count() > 0
		);
	}

	private function add($client, $mail)
	{
		$crawler = $client->request('GET', '/admin/user/new');
		$this->assertTrue($crawler->filter('html:contains("名前")')->count() > 0);

		$user_form = $crawler->selectButton('submit')->form();
		$crawler   = $client->submit($user_form, array(
				'form[family_name]'         => 'テスト姓',
				'form[first_name]'           => 'テスト名',
				'form[kana_family]'         => 'テストセイ',
				'form[kana_first]'           => 'テストメイ',
				'form[email]'                => $mail,
				'form[password][first]'      => 'testpass',
				'form[password][second]'     => 'testpass'
		));

		return $crawler;
	}

	private function remove($mail)
	{
		$client    = static::createClient();
		$em        = $client->getContainer()->get('doctrine.orm.entity_manager');
		$user_repo = $em->getRepository('NamecoUserBundle:User');
		$user      = $user_repo->findOneByEmail($mail);
		$this->assertTrue($user != null);
		$em->remove($user);
		$em->flush();
	}

	private function login($client)
	{
		$crawler    = $client->request('GET', '/login');
		$login_form = $crawler->selectButton('submit')->form();
		$crawler = $client->submit($login_form, array(
				'_username'      => 'admin',
				'_password'      => 'namecoadmin',
		));
	}
	private function logout($client)
	{
		$client->request('GET', '/logout');
	}
}
