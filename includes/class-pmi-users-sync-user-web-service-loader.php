<?php

use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * Defines the loader of the PMI members through a call to PMI DEPService web service
 *
 * @link       http://angelochillemi.com/pmi-users-sync
 * @since      1.1.0
 *
 * @package    Pmi_Users_Sync
 * @subpackage Pmi_Users_Sync/includes
 */

/**
 * Loads the PMI User through a call to PMI DEPService and returns an array of {@see Pmi_Users_Sync_Pmi_User} instances
 * 
 *
 * @package    Pmi_Users_Sync
 * @subpackage Pmi_Users_Sync/includes
 * @author     Angelo Chillemi <info@angelochillemi.com>
 * @see        Pmi_Users_Sync_User_Loader
 * 
 * @property string $username The username to use for the web service call
 * @property string $password The password to use for the web service call
 */
class Pmi_Users_Sync_Pmi_User_Web_Service_Loader implements Pmi_Users_Sync_User_Loader
{
    private const TEMP_CSV_FILENAME = 'members.csv';

    private const CACHE_KEY = PMI_USERS_SYNC_PREFIX . 'PMI_MEMBERS_WEB_SERVICE';

    private string $username;
    private string $password;

    /**
     * Cache expire time of 4 days
     */
    private const EXPIRE_TIME = 4 * DAY_IN_SECONDS;

    private Pmi_Users_Sync_Members_Web_Service $web_service;

    public function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * Load the list of PMI users with information from the PMI site using the Web Service
     *
     *
     * @return Pmi_Users_Sync_Pmi_User[] array of user instances
     */
    public function load()
    {
        $pmi_users = [];
        $web_service = $this->get_web_service();
        if ($web_service) {

            $reader = IOFactory::createReader('Csv');
            $file_path = $this->get_temp_csv_file_path($web_service->get_csv_extract());
            $spreadsheet = $reader->load($file_path);
            $worksheet = $spreadsheet->getActiveSheet();
            foreach ($worksheet->getRowIterator(2) as $row) {
                if ($row->getRowIndex() >= $worksheet->getHighestRow())
                    break;

                $cellIterator = $row->getCellIterator();
                foreach ($cellIterator as $cell) {
                    if (!strcmp("A", $cell->getColumn())) {
                        $pmi_id = $cell->getValue();
                    }
                    if (!strcmp("D", $cell->getColumn())) {
                        $first_name = $cell->getValue();
                    }
                    if (!strcmp("E", $cell->getColumn())) {
                        $last_name = $cell->getValue();
                    }
                    if (!strcmp("V", $cell->getColumn())) {
                        $email = $cell->getValue();
                    }
                }
                if (!is_null($pmi_id) && !is_null($email))
                    array_push($pmi_users, new Pmi_Users_Sync_Pmi_User($pmi_id, $first_name, $last_name, $email));
            }
        }
        return $pmi_users;
    }

    /**
     * Get an instance of the Pmi_Users_Sync_Members_Web_Service from the cache or create a new instance. 
     * Cache expiration time is 4 days (@see Pmi_Users_Sync_Members_Web_Service::EXPIRE_TIME).
     *
     * @return Pmi_Users_Sync_Members_Web_Service The web service instance as result of the call to PMI DEP Service
     */
    private function get_web_service()
    {
        $web_service = wp_cache_get(self::CACHE_KEY);
        if (!$web_service) {
            if ($this->username && $this->password) {
                $web_service = new Pmi_Users_Sync_Members_Web_Service($this->username, $this->password);
                wp_cache_set(self::CACHE_KEY, $web_service, 'web_service', intval(self::EXPIRE_TIME));
            }
        }
        return $web_service;
    }

    /**
     * Create the temporary file with the PMI members retrieved through the web service.
     * This file is used by PhpSpreadSheet
     *
     * @param string $csv The CSV from web service to be temporary saved on a file
     * @return string The temporary file path
     */
    private function get_temp_csv_file_path(string $csv)
    {
        $temp_csv_file = get_temp_dir() . '/' . self::TEMP_CSV_FILENAME;
        $file = fopen($temp_csv_file, 'w');
        fwrite($file, $csv);
        fclose($file);
        return $temp_csv_file;
    }
}
