<?php

/**
 * The file that defines the PMI DEPService web service to retrieve the list of PMI Chapter members directly from PMI
 *
 * @link       http://angelochillemi.com/pmi-users-sync
 * @since      1.1.0
 *
 * @package    Pmi_Users_Sync
 * @subpackage Pmi_Users_Sync/includes
 */


/**
 * Represents the PMI DEPService web service called to retrieve the list of members of the PMI Chapter in CSV format
 *
 * @package    Pmi_Users_Sync
 * @subpackage Pmi_Users_Sync/includes
 * @author     Angelo Chillemi <info@angelochillemi.com>
 */
class Pmi_Users_Sync_User_Loader_Factory
{
    private const OPTION_DEP_SERVICE_USERNAME = PMI_USERS_SYNC_PREFIX . 'depservice_username';
    private const OPTION_DEP_SERVICE_PASSWORD = PMI_USERS_SYNC_PREFIX . 'depservice_password';

    private const OPTION_USER_LOADER = PMI_USERS_SYNC_PREFIX . 'depservice_password';

    private function __construct()
    {
    }

    /**
     * Undocumented function
     *
     * @return Pmi_Users_Sync_User_Loader A loader based on the option selected by the user. 
     * Returns an Excel file loader by default.
     */
    public function create_user_loader()
    {
        $user_loader_option = get_option(self::OPTION_USER_LOADER);
        switch ($user_loader_option) {
            case 'option_web_service':
                $username = get_option(self::OPTION_DEP_SERVICE_USERNAME);
                $password = get_option(self::OPTION_DEP_SERVICE_PASSWORD);
                $loader = new Pmi_Users_Sync_Pmi_User_Web_Service_Loader($username, $password);
                break;

            case 'option_excel':

            default: // default Excel file
                $pmi_file_url = get_option(PMI_USERS_SYNC_PREFIX . 'pmi_file_field_id');
                $file_path = Path_Utils::get_file_path($pmi_file_url);
                $loader = new Pmi_Users_Sync_Pmi_User_Excel_File_Loader($file_path);
                break;
        }
        return $loader;
    }
}
