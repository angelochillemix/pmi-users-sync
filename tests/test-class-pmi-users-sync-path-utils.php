<?php

use PHPUnit\Framework\TestCase;

class Test_Path_Utils extends TestCase
{
    private const TEST_URL = 'https://localhost/wordpress/wp-content/uploads/2021/11/MemberDetail.xls';
    private const TEMP_WORDPRESS_PATH = '/tmp/wordpress';

    private const TEST_TEMP_URL = 'https://localhost/wordpress/wp-content/path_to_media/test.file';

    public function test_get_file_path()
    {
        $this->assertFalse(Path_Utils::get_file_path(null));
        $this->assertFalse(Path_Utils::get_file_path(false));
        $this->assertFalse(Path_Utils::get_file_path(''));
    }

    public function test_attachment_url_to_path_with_file_check()
    {
        $this->assertFalse(Path_Utils::attachment_url_to_path(self::TEST_URL));
    }

    public function test_attachment_url_to_path()
    {
        $this->assertIsString(Path_Utils::attachment_url_to_path(self::TEST_URL, false), 'It is not a string!');
        $this->assertEquals(self::TEMP_WORDPRESS_PATH . '/wp-content/uploads/2021/11/MemberDetail.xls', Path_Utils::attachment_url_to_path(self::TEST_URL, false));
        $this->assertEquals(self::TEMP_WORDPRESS_PATH . '/wp-content/path_to_media/test.file', Path_Utils::attachment_url_to_path(self::TEST_TEMP_URL, false));
    }
}
