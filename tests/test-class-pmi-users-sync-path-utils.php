<?php

/**
 * Path utility functions
 *
 * @link       http://angelochillemi.com/pmi-users-sync
 * @since      1.0.0
 *
 * @package    Pmi_Users_Sync
 * @subpackage Pmi_Users_Sync/includes
 */

use Yoast\WPTestUtils\BrainMonkey\TestCase;

/**
 * Test case for {@see Pmi_Users_Sync_Path_Util}
 *
 * @package    Pmi_Users_Sync
 * @subpackage Pmi_Users_Sync/includes
 * @author     Angelo Chillemi <info@angelochillemi.com>
 */
class Test_Path_Utils extends TestCase
{

	private const TEST_URL            = 'https://localhost/wordpress/wp-content/uploads/2021/11/MemberDetail.xls';
	private const TEMP_WORDPRESS_PATH = '/tmp/wordpress';

	private const TEST_TEMP_URL = 'https://localhost/wordpress/wp-content/path_to_media/test.file';

	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public function test_get_file_path()
	{
		$this->assertFalse(Pmi_Users_Sync_Path_Utils::get_file_path(null));
		$this->assertFalse(Pmi_Users_Sync_Path_Utils::get_file_path(false));
		$this->assertFalse(Pmi_Users_Sync_Path_Utils::get_file_path(''));
	}

	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public function test_attachment_url_to_path_with_file_check()
	{
		$this->assertFalse(Pmi_Users_Sync_Path_Utils::attachment_url_to_path(self::TEST_URL));
	}

	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public function test_attachment_url_to_path()
	{
		$this->assertIsString(Pmi_Users_Sync_Path_Utils::attachment_url_to_path(self::TEST_URL, false), 'It is not a string!');
		$this->assertEquals(self::TEMP_WORDPRESS_PATH . '/wp-content/uploads/2021/11/MemberDetail.xls', Pmi_Users_Sync_Path_Utils::attachment_url_to_path(self::TEST_URL, false));
		$this->assertEquals(self::TEMP_WORDPRESS_PATH . '/wp-content/path_to_media/test.file', Pmi_Users_Sync_Path_Utils::attachment_url_to_path(self::TEST_TEMP_URL, false));
	}
}
