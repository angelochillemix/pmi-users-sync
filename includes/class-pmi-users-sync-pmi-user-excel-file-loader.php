<?php
/**
 * Loads the PMI User from an Excel file
 *
 * @link       http://angelochillemi.com
 * @since      1.0.0
 *
 * @package    Pmi_Users_Sync
 * @subpackage Pmi_Users_Sync/includes
 */

use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * Loads the PMI User from an Excel file and returns an array of {@see Pmi_Users_Sync_Pmi_User} instances
 *
 * @package    Pmi_Users_Sync
 * @subpackage Pmi_Users_Sync/includes
 * @author     Angelo Chillemi <info@angelochillemi.com>
 * @see        Pmi_Users_Sync_User_Loader
 */
class Pmi_Users_Sync_Pmi_User_Excel_File_Loader implements Pmi_Users_Sync_User_Loader {

	/**
	 * Default filename
	 */
	const PMI_EXCEL_FILENAME = 'MemberDetail.xls';

	/**
	 * The name of the worksheet with the list of users and related PMI-ID
	 */
	const PMI_EXCEL_WORKSHEET_NAME = 'Details';


	/**
	 * The full file path of the Excel file
	 *
	 * @var string
	 */
	protected string $file_path;

	/**
	 * Constructor of the class
	 *
	 * @param string $file_path The path of the Excel file.
	 */
	public function __construct( $file_path ) {
		$this->file_path = $file_path;
	}

	/**
	 * Loads the users from the Excel file from PMI and return an array of Pmi_Users_Sync_Pmi_User instances
	 *
	 * @return Pmi_Users_Sync_Pmi_User[] array of user instances
	 * @throws \PhpOffice\PhpSpreadsheet\Exception Exception raised by PhpSpreadsheet.
	 */
	public function load() {
		Pmi_Users_Sync_Logger::log_information( __( 'Excel file path is ', 'pmi-users-sync' ) . $this->file_path );
		$spreadsheet = IOFactory::load( $this->file_path );
		$worksheet   = $spreadsheet->setActiveSheetIndex( 0 );
		$pmi_users   = array();
		foreach ( $worksheet->getRowIterator( 2 ) as $row ) {
			if ( $row->getRowIndex() >= $worksheet->getHighestRow() ) {
				break;
			}

			$cell_iterator = $row->getCellIterator();
			foreach ( $cell_iterator as $cell ) {
				if ( ! strcmp( 'A', $cell->getColumn() ) ) {
					$pmi_id = $cell->getValue();
				}
				if ( ! strcmp( 'D', $cell->getColumn() ) ) {
					$first_name = $cell->getValue();
				}
				if ( ! strcmp( 'E', $cell->getColumn() ) ) {
					$last_name = $cell->getValue();
				}
				if ( ! strcmp( 'AV', $cell->getColumn() ) ) {
					$email = $cell->getValue();
				}
			}
			if ( ! is_null( $pmi_id ) && ! is_null( $email ) ) {
				array_push( $pmi_users, new Pmi_Users_Sync_Pmi_User( $pmi_id, $first_name, $last_name, $email ) );
			}
		}
		return $pmi_users;
	}
}
