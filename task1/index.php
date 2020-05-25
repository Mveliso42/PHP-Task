<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


/** Initiate process */
$directory = __DIR__.'/../dir';
$filename = 'readme.txt';

var_dump(scanUserFile($filename, $directory));


/**
 * Scan through the user's file to ensure all requirements were met
 *
 * @param  string  $filename
 * @param  string  $directory
 * @return bool
 */
function scanUserFile($filename, $directory) {
	if (isFileInDirectory($filename, $directory)) {
		$content = getFileContent($filename, $directory);

		if (! hasKeyword($content, 'Full Name:')) return false;

		if (! hasKeyword($content, 'https://res.cloudinary.com')) return false;

		if (! hasKeyword($content, 'Bio:')) return false;

		if (! isFullNameValid($content)) return false;

		if (! isBioValid($content)) return false;

		/* Promote user or fire any event that should happen on success */
		return true;

	} else {
		return false;
	}
}

/**
 * Check if a file exists in a directory
 *
 * @param  string  $filename
 * @param  string  $directory
 * @return bool
 */
function isFileInDirectory($filename, $directory)
{
	if (is_dir($directory)) {
		$contents = scandir($directory);

		return in_array($filename, $contents) ? true : false;
	}

	return false;
}

/**
 * Get the content of the file
 *
 * @param  string  $filename
 * @param  string  $directory
 * @return string|null
 */
function getFileContent($filename, $directory)
{
	$path = $directory . '/' . $filename;

	if (file_exists($path) && is_readable($path)) {
		return file_get_contents($path);
	}

	return null;
}

/**
 * Check if a certain keyword is provided by the user
 *
 * @param  string|null  $content
 * @param  string|null  $keyword
 * @return bool
 */
function hasKeyword($content = null, $keyword = null)
{
	if (! is_null($content) && ! is_null($keyword)) {
		$split = explode(strtolower($keyword), strtolower( $content));

		if (isset($split[1]) == ' ') return true;
	}

	return false;
}

/**
 * Check if full name was provided. It will return false if only first name
 * or only last name is provided
 *
 * @param  string  $content
 * @return bool
 */
function isFullNameValid($content)
{
	$split = explode('full name:', strtolower($content));
	$fullname = explode('bio:', trim($split[1]));
	$names = explode(' ', trim($fullname[0]));

	return isset($names[0]) && isset($names[1]) ? true : false;
}

/**
 * Checks if the bio content was provided and if it is not more than 150 characters
 *
 * @param  string  $content
 * @return bool
 */
function isBioValid($content)
{
	$split = explode('bio:', strtolower($content));
	$bio = explode('https://res.cloudinary.com', trim($split[1]));

	if (strlen(trim($bio[0])) < 1) return false;

	return (isset($bio[0]) && strlen(trim($bio[0])) <= 150) ? true : false;
}
