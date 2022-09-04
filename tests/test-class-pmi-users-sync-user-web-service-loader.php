<?php

use Yoast\WPTestUtils\BrainMonkey\TestCase;

class Test_Pmi_Users_Sync_Pmi_User_Web_Service_Loader extends TestCase
{

	public function test_no_username_password_set()
	{
		$mock_web_service = $this->createMock(Pmi_Users_Sync_Members_Web_Service::class);
		$csv_file         = file_get_contents(__DIR__ . '/test-pmi-users.csv');
		$obj              = new stdClass();
		$obj->Success     = true;
		$obj->ExtractFile = $csv_file;
		$obj->MemberCount = 4;
		if (!file_exists(__DIR__ . '/test-pmi-users.csv')) {
			$this->fail('Test CSV file does not exist');
		}
		$mock_web_service->expects($this->once())->method('call')->willReturn($obj);
		$mock_web_service->method('get_csv_extract')->willReturn($csv_file);
		$loader = new Pmi_Users_Sync_Pmi_User_Web_Service_Loader($mock_web_service);
		$result = $loader->load();
		$this->assertNotNull($result);
		$this->assertIsArray($result);
		$this->assertCount(4, $result, 'Differenct count of members found');
	}
}
