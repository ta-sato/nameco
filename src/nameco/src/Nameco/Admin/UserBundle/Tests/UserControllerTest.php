<?php
namespace Nameco\Admin\UserBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
	public function testNew()
	{
		$client     = static::createClient();
		
		//　ログイン
		$crawler    = $client->request('GET', '/login');
		$login_form = $crawler->selectButton('submit')->form();
		$crawler = $client->submit($login_form, array(
				'_username'      => 'admin',
				'_password'      => 'namecoadmin',
		));
		
		$crawler = $client->request('GET', '/user/new');
		$this->assertTrue($crawler->filter('html:contains("名前")')->count() > 0);
		
		$user_form = $crawler->selectButton('submit')->form();
		$user_id   = 'test-mail'. time();
		$mail      = $user_id . '@minori-sol.jp';
		$crawler   = $client->submit($user_form, array(
		    'form[familly_name]'         => 'テスト姓',
		    'form[first_name]'           => 'テスト名',
		    'form[kana_familly]'         => 'テストセイ',
		    'form[kana_first]'           => 'テストメイ',
			'form[email]'                => $mail,
			'form[password][first]'      => 'testpass',
			'form[password][second]'     => 'testpass'
		));
		
		$this->assertTrue($crawler->filter('html:contains("登録しました")')->count() > 0);
		
		$em = $client->getContainer()->get('doctrine.orm.entity_manager');
		$user_repo = $em->getRepository('NamecoAdminUserBundle:User');
		$exsist = $user_repo->findOneBy(array(
				'username' => $user_id,
				'email'    => $mail
				)) != null;
		$this->assertTrue($exsist);
	}
}
