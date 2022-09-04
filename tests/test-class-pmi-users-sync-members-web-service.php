<?php

/**
 * Unit test for {@see Pmi_Users_Sync_Members_Web_Service}
 *
 * @link       http://angelochillemi.com/pmi-users-sync
 * @since      1.2.0
 *
 * @package    Pmi_Users_Sync
 * @subpackage Pmi_Users_Sync/tests
 */

use Yoast\WPTestUtils\BrainMonkey\TestCase;

/**
 * Undocumented class
 */
class Test_Pmi_Users_Sync_Members_Web_Service extends TestCase
{

	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public function test_web_service_exception()
	{
		$this->expectException('SoapFault');
		$web_service = new Pmi_Users_Sync_Members_Web_Service('', '');
		$web_service->call();

		$this->expectException('SoapFault');
		$web_service = new Pmi_Users_Sync_Members_Web_Service('test_username', '');
		$web_service->call();

		$this->expectException('SoapFault');
		$web_service = new Pmi_Users_Sync_Members_Web_Service('test_username', 'test_password');
		$web_service->call();

		$this->expectException('SoapFault');
		$web_service = new Pmi_Users_Sync_Members_Web_Service('', 'test_password');
		$web_service->call();
	}

	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public function test_loader_with_web_service_exception()
	{
		$this->expectException('SoapFault');
		$web_service = new Pmi_Users_Sync_Members_Web_Service('test_username', 'test_password');
		$loader      = new Pmi_Users_Sync_Pmi_User_Web_Service_Loader($web_service);
		$loader->load();
	}
}
