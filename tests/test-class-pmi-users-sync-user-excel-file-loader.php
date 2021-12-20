<?php

use PHPUnit\Framework\TestCase;

class Test_Pmi_Users_Sync_Pmi_User_Excel_File_Loader extends TestCase
{
    private const TEMP_PMI_EXCEL_FILE_PATH = __DIR__ . '/test-pmi-users.xls';
    private const TEMP_PMI_EXCEL_FILE_WITHOUT_DETAILS_WORKSHEET_PATH = __DIR__ . '/test-pmi-users-no-details-worksheet.xls';
    private const TEMP_PMI_EXCEL_FILE_DOES_NOT_EXISTS_PATH = __DIR__ . '/test-pmi-users-inexistent.xls';


    private $excel_loader;
    private $users = [];

    /**
     * Setup the tests by loading the list of users from the Excel test file
     *
     * @return void
     * @beforeClass
     */
    public function setUp()
    {
        $this->excel_loader = new Pmi_Users_Sync_Pmi_User_Excel_File_Loader(self::TEMP_PMI_EXCEL_FILE_PATH);
        $this->users = $this->excel_loader->load();
    }

    /**
     * Unset variables
     *
     * @return void
     * 
     * @afterClass
     */
    public function tearDown()
    {
        unset($excel_loader);
        unset($users);
    }

    /**
     * Check the count of users fromr the test Excel file
     *
     * @return void
     */
    public function test_count_of_users()
    {
        $this->assertCount(4, $this->users, 'Count of users is not 4');
    }

    /**
     * Test the first user loaded from the Excel file
     *
     * @return void
     */
    public function test_user()
    {
        $user = $this->users[0];
        $this->assertSame('1234567', $user->get_pmi_id(), 'PMI-ID does not match');
        $this->assertSame('Ciccio', $user->get_first_name(), 'User name does not match');
        $this->assertSame('Bello', $user->get_last_name(), 'Lst name does not match');
        $this->assertSame('test@email.it', $user->get_email(), 'Email does not match');
    }

    /**
     * Test that missing Details worksheet in the Excel file throws an Exception
     * 
     * @return void
     */
    public function test_missing_details_worksheet()
    {
        $this->expectException(\PhpOffice\PhpSpreadsheet\Exception::class);
        $excel_loader = new Pmi_Users_Sync_Pmi_User_Excel_File_Loader(self::TEMP_PMI_EXCEL_FILE_WITHOUT_DETAILS_WORKSHEET_PATH);
        $users = $excel_loader->load();
    }

    /**
     * Test that inexisten Excel file throws an Excption
     *
     * @return void
     */
    public function test_inexistent_excel_file()
    {
        $this->expectException(\PhpOffice\PhpSpreadsheet\Exception::class);
        $excel_loader = new Pmi_Users_Sync_Pmi_User_Excel_File_Loader(self::TEMP_PMI_EXCEL_FILE_DOES_NOT_EXISTS_PATH);
        $users = $excel_loader->load();
    }

}