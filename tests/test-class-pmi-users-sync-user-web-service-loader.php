<?php

use PHPUnit\Framework\TestCase;

class Test_Pmi_Users_Sync_Pmi_User_Web_Service_Loader extends TestCase
{
    public function test_load_from_web_service()
    {

        try {
            $username = get_option(Pmi_Users_Sync_Admin::OPTION_DEP_SERVICE_USERNAME);
            $password = get_option(Pmi_Users_Sync_Admin::OPTION_DEP_SERVICE_PASSWORD);
            $loader = new Pmi_Users_Sync_Pmi_User_Web_Service_Loader($username, $password);
            $result = $loader->load();
            $this->assertNotNull($result);
            $this->assertIsArray($result);
        } catch (SoapFault $fault) {
            print("$fault->faultcode : $fault->faultstring");
        }
    }
}
