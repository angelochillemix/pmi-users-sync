<?php 

class Path_Utils
{
    /**
     * Return the resources directory path
     *
     * @param string $filePath
     * @return void
     */
    public static function resource_path($filePath) {
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
	 *
	 * @return bool|string It returns the absolute path of an attachment
	 */
	public static function attachment_url_to_path($url)
	{
		$parsed_url = parse_url($url);
		if (empty($parsed_url['path'])) return false;
		//Remove parent directory
		$dir_path = substr(ltrim($parsed_url['path'], '/'), strpos(ltrim($parsed_url['path'], '/'), '/'));
		$file = ABSPATH . $dir_path;
		// Check if the resulting file exists and return its full path
		if (file_exists($file)) return $file;
		return false;
	}

}
