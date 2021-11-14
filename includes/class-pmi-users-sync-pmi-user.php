<?php

/**
 * The PMI User class as model to manage the related information
 *
 * @link       http://angelochillemi.com
 * @since      1.0.0
 *
 * @package    Pmi_Users_Sync
 * @subpackage Pmi_Users_Sync/includes
 */

/**
 * The PMI Use class as model to manage the related information
 *
 * Defines the properties and methods of the PMI User model.
 *
 * @package    Pmi_Users_Sync
 * @subpackage Pmi_Users_Sync/includes
 * @author     Angelo Chillemi <info@angelochillemi.com>
 */
class Pmi_Users_Sync_Pmi_User
{
    private $pmi_id;
    private $email;
    private $first_name;
    private $last_name;

    public function __construct(string $pmi_id, string $first_name, string $last_name, string $email) {
        $this->pmi_id = $pmi_id;
        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->email = $email;
    }

    public function get_pmi_id() {
        return $this->pmi_id;
    }

    public function get_first_name() {
        return $this->first_name;
    }

    public function get_last_name() {
        return $this->last_name;
    }

    public function get_email() {
        return $this->email;
    }
}