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
class Pmi_Users_Sync_Pmi_User {

	/**
	 * The PMI-ID of the user
	 *
	 * @var string The PMI-ID of the user
	 */
	private $pmi_id;

	/**
	 * Email of the user
	 *
	 * @var string Email of the user
	 */
	private $email;

	/**
	 * The first name of the user
	 *
	 * @var string The first name of the user
	 */
	private $first_name;

	/**
	 * The last name of the user
	 *
	 * @var string The last name of the user
	 */
	private $last_name;

	/**
	 * Create an instance of the PMI User
	 *
	 * @param string $pmi_id The PMI-ID of the user.
	 * @param string $first_name The first name of the user.
	 * @param string $last_name The last name of the user.
	 * @param string $email Email of the user.
	 */
	public function __construct( string $pmi_id, string $first_name, string $last_name, string $email ) {
		$this->pmi_id     = $pmi_id;
		$this->first_name = $first_name;
		$this->last_name  = $last_name;
		$this->email      = $email;
	}

	/**
	 * Returns the PMI ID of the user
	 *
	 * @return string PMI ID of the user
	 */
	public function get_pmi_id() {
		return $this->pmi_id;
	}

	/**
	 * Returns the first name of the user
	 *
	 * @return string first name of the user
	 */
	public function get_first_name() {
		return $this->first_name;
	}

	/**
	 * Returns the last name of the user
	 *
	 * @return string the last name of the user
	 */
	public function get_last_name() {
		return $this->last_name;
	}

	/**
	 * Returns the email of the user
	 *
	 * @return string the email of the user
	 */
	public function get_email() {
		return $this->email;
	}
}
