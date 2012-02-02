<?php
////////////////////////////////////////////////////////////////////////////
//                              SnowCMS v2.0                              //
//                           By the SnowCMS Team                          //
//                             www.snowcms.com                            //
//            Released under the Microsoft Reciprocal License             //
//                 www.opensource.org/licenses/ms-rl.html                 //
////////////////////////////////////////////////////////////////////////////
//                                                                        //
//       SnowCMS originally pawned by soren121 started in early 2008      //
//                                                                        //
////////////////////////////////////////////////////////////////////////////
//                                                                        //
//                  SnowCMS v2.0 began in November 2009                   //
//                                                                        //
////////////////////////////////////////////////////////////////////////////
//                       File version: SnowCMS 2.0                        //
////////////////////////////////////////////////////////////////////////////

if(!defined('INSNOW'))
{
	die('Nice try...');
}

/*
	Interface: Extractor

	The Extractor interface defines the methods which must be defined if a
	class is to be registered as a decompression handler in the <Extraction>
	class.

	Keep in mind that classes which implement the Extractor interface may
	define more (and private) methods other than those laid out here.
*/
interface Extractor
{
	/*
		Method: open

		Opens the specified compressed file.

		Parameters:
			string $filename - The name of the file to open.

		Returns:
			bool - Returns true if the file was opened successfully and is capable
						 of being extracted by the decompression handler.
	*/
	public function open($filename);

	/*
		Method: files

		Returns a list of files and directories which exist within the
		compressed file.

		Parameters:
			none

		Returns:
			array - Should return an array containing files and directories which
							exist within the compressed file.

		Note:
			The child arrays should contain the following indices:

				string name - The name of the file or directory.
				bool is_dir - Whether the object is a directory.
				int size - The size of the file uncompressed, if at all possible.
	*/
	public function files();

	/*
		Method: extract

		Extracts the files from the compressed file to the specified
		destination.

		Parameters:
			string $destination - The directory to extract the compressed file
														into.
			bool $safe_mode - It is possible for compressed files to have such
												file names as "../../someImportantFile.sys" and
												overwrite important files, but if this option is set
												to true any ../ will be removed from the file or
												directory name. Default to true.

		Returns:
			bool - Returns true if the compressed file was successfully extracted
						 to the specified destination, but false if the destination
						 does not exist and could not be created.

		Note:
			If $safe_mode is set to true, the name of the file/directory within
			the compressed file should be ran through this:
				filename = strtr(filename, array('../' => '', '/..' => ''));
	*/
	public function extract($destination, $safe_mode = true);

	/*
		Method: read

		Reads the specified file from the compressed file and returns the
		contents of the file or saves the contents to the specified file.

		Parameters:
			string $filename - The name of the file in the compressed file to
												 read.
			string $destination - The location where $filename should be saved to,
														if left blank the contents will be returned.

		Returns:
			mixed - Returns a string containing the files contents if $destination
							is left empty but false if the file does not exist. If a
							destination is supplied, true will be returned on success and
							false on failure (e.g. the file does not exist).

		Note:
			When searching for the file within the compressed file, file name
			comparisons should be case sensitive.

			This should not work with directories, as only files should be
			retrievable.
	*/
	public function read($filename, $destination = null);

	/*
		Method: close

		Closes all the current file being dealt with and set all necessary
		attributes to blanks.

		Parameters:
			none

		Returns:
			void - Nothing should be returned by this method.

		Note:
			This method should be called upon when the instance of the object is
			destructed.
	*/
	public function close();

	/*
		Method: is_supported

		Returns whether the decompression type is supported on the current
		system.

		Parameters:
			none

		Returns:
			mixed - If the decompression handler only deals with a single type of
							compressed file then true should be returned if the system is
							configured to extract such a compressed file and false if not.
							However, if a decompression handler deals with multiple types
							of compressed files (e.g. the <Tar> class which deals with
							tarballs and gzipped tarballs) then an array containing the
							file extensions supported (like tar and tar.gz), if none are
							supported on the current system an empty array or false should
							be returned.
	*/
	public function is_supported();
}
?>