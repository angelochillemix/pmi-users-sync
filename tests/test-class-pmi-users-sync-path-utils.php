<?php

use PHPUnit\Framework\TestCase;

class Test_Path_Utils extends TestCase
{
    public function test_get_file_path()
    {
        $this->assertFalse(Path_Utils::get_file_path(null));
        $this->assertFalse(Path_Utils::get_file_path(false));
        $this->assertFalse(Path_Utils::get_file_path(''));
    }

    public function test_attachment_url_to_path_with_file_check()
    {
        $this->assertFalse(Path_Utils::attachment_url_to_path('https://localhost/wordpress/wp-content/uploads/2021/11/MemberDetail.xls'));
    }

    public function test_attachment_url_to_path()
    {
        $this->assertIsString(Path_Utils::attachment_url_to_path('https://localhost/wordpress/wp-content/uploads/2021/11/MemberDetail.xls', false), 'It is not a string!');
        $this->assertEquals('/tmp/wordpress/wp-content/uploads/2021/11/MemberDetail.xls', Path_Utils::attachment_url_to_path('https://localhost/wordpress/wp-content/uploads/2021/11/MemberDetail.xls', false));
    }
}
