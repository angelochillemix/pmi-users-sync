<?php
/**
 * The PMI Use class as model to manage the related information
 *
 * @link       http://angelochillemi.com
 * @since      1.0.0
 *
 * @package    Pmi_Users_Sync
 * @subpackage Pmi_Users_Sync/includes
 */

use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

/**
 * Logger class uised to log erros, warning and information to the log file
 */
class Pmi_Users_Sync_Logger {

	const LOG_NAME      = 'PMI Users Sync Log';
	const LOG_FILE_NAME = PMI_USERS_SYNC_PREFIX . 'log.log';
	const LOG_DIR_PATH  = PMI_USERS_SYNC_PLUGIN_DIR_ADMIN . 'logs/';
	const LOG_FILE_PATH = self::LOG_DIR_PATH . self::LOG_FILE_NAME;

	/**
	 * The logger instance
	 *
	 * @var Logger
	 */
	private static $log = null;

	/**
	 * Private constructor
	 */
	private function __construct() {
	}

	/**
	 * Create and return an instance of the Logger
	 *
	 * @return Logger The instance of the Logger class
	 */
	private static function get_log() {
		if ( null === self::$log ) {
			// Create a log channel.
			self::$log = new Logger( self::LOG_FILE_NAME );
			self::$log->pushHandler( new RotatingFileHandler( self::LOG_FILE_PATH, 5, Logger::INFO ) );
		}
		return self::$log;
	}

	/**
	 * Log errors in the log file
	 *
	 * @param string $message The message to log.
	 * @param array  $context The context.
	 * @return void
	 */
	public static function log_error( $message, $context = array() ) {
		self::get_log()->error( $message, $context );
	}

	/**
	 * Log information in the log file
	 *
	 * @param string $message The message to log.
	 * @param array  $context The context.
	 * @return void
	 */
	public static function log_information( $message, $context = array() ) {
		self::get_log()->info( $message, $context );
	}

	/**
	 * Log warning in the log file
	 *
	 * @param string $message The message to log.
	 * @param array  $context The context.
	 * @return void
	 */
	public static function log_warning( $message, $context = array() ) {
		self::get_log()->warning( $message, $context );
	}
}
