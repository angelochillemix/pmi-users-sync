<?php

class Path_Utils
{
	/**
	 * No instances allowed since this class is meant to contain only static method
	 */
	private function __construct()
	{
	}

	/**
	 * Return the full path of the file under the resources directory
	 *
	 * @param string $filePath
	 * @return string The full path of the file under the resources directory
	 */
	public static function resource_path($filePath)
	{
		return PMI_USERS_SYNC_PLUGIN_DIR_RESOURCES . $filePath;
	}

	/**
	 * Retieve the full path fo the file with the PMI-ID from PMI
	 *
	 * @param string $file_url The URL of the Excel file set in the plugin settings
	 * @return bool|string The full path fo the file with the PMI-ID from PMI
	 */
	public static function get_file_path($file_url)
	{
		// Return false if the plugin setting is not set
		if (
			!isset($file_url)
			|| empty($file_url)
			|| false === $file_url
		) {
			return false;
		}
		return self::attachment_url_to_path($file_url);
	}

	/**
	 * Get the attachment absolute path from its url
	 *
	 * @param string $url the attachment url to get its absolute path
	 * @param bool $check_file If true it checks that the file exists, else return the resulting file path as string
	 * @return bool|string It returns the absolute path of an attachment, or false if file does not exist
	 */
	public static function attachment_url_to_path($url, $check_file = true)
	{
		$parsed_url = parse_url($url);
		if (empty($parsed_url['path'])) return false;
		//Remove parent directory
		$dir_path = substr(ltrim($parsed_url['path'], '/'), strpos(ltrim($parsed_url['path'], '/'), '/'));
		// Remove one more trailing slash from the full path
		$dir_path = ltrim($dir_path, '/');
		// Append the absolute path of wordpress directory from the ABSPATH variable
		$file = ABSPATH . $dir_path;
		// Check if the resulting file exists and return its full path
		if (!$check_file || file_exists($file)) {
			return $file;
		} 
		return false;
	}

}
