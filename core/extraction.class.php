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

// No Extractor interface defined? Get it.
if(!interface_exists('Extractor'))
{
	require(coredir. '/extractor.interface.php');
}

/*
	Class: Extraction

	The Extraction class does just what one might think: extract files.
	Instead of having to figure out the method of decompression yourself the
	Extraction class can determine the compression type and then deal with
	each different method of compression appropriately.

	Plugins may register additional decompression handlers other than the
	built in handlers which are zip (See <Zip>), tar and tar.gz (See <Tar>).
*/
class Extraction
{
	// Variable: types
	// An array containing registered types of decompression handlers.
	private $types;

	// Variable: cache
	// Holds a cache of the appropriate object which has implemented the
	// Extraction interface for the specified file.
	private $cache;

	/*
		Constructor: __construct

		Initializes all attributes to nulls.

		Parameters:
			none
	*/
	public function __construct()
	{
		$this->types = array();
		$this->cache = array();

		api()->run_hooks('extraction_construct', array(&$this));

		// Register our types.
		$this->add_type('Zip', coredir. '/zip.class.php');
		$this->add_type('Tar', coredir. '/tar.class.php');
	}

	/*
		Method: add_type

		Registers a decompression type handler.

		Parameters:
			string $class_name - The name of the class which will handle the
													 compressed type.
			string $filename - The name of the file which contains the specified
												 class. This parameter may be left blank.

		Returns:
			bool - Returns true if the handler was added successfully, false if
						 the file or class does not exist, or if the decompression
						 handler does not implement the <Extraction> interface.

		Note:
			In order for a handler to be registered the class must implement the
			<Extractor> interface.

			It is also recommended that the name of the class be that of the
			typical file extension used to identify the compressed type (like Zip
			or Tar).
	*/
	public function add_type($class_name, $filename = null)
	{
		// Just to make sure.
		if(isset($this->types[strtolower($class_name)]))
		{
			return false;
		}

		// Is this class not defined? Let's see if we can fix that.
		if(!class_exists($class_name) && file_exists($filename))
		{
			require($filename);

			// We will check once more.
			if(!class_exists($class_name))
			{
				return false;
			}
		}
		else
		{
			// Sorry, it won't work.
			return false;
		}

		// In order for this class to be registered as a decompression handler
		//  it must implement the Extractor interface. This can be done through
		// the ReflectionClass.
		$reflect = new ReflectionClass($class_name);

		// The implementsInterface method will tell us what we want to know.
		if(!$reflect->implementsInterface('Extractor'))
		{
			return false;
		}

		// Just add it. Nothing more needs to be done.
		$this->types[strtolower($class_name)] = $class_name;

		return true;
	}

	/*
		Method: remove_type

		Removes the specified decompression handler.

		Parameters:
			string $class_name - The name of the decompression handler class to
													 remove.

		Returns:
			bool - Returns true if the decompression handler was removed
						 successfully, false if it doesn't exist.
	*/
	public function remove_type($class_name)
	{
		// Simple enough.
		if(!isset($this->types[strtolower($class_name)]))
		{
			// It doesn't exist, so we can't remove it.
			return false;
		}

		unset($this->types[strtolower($class_name)]);

		// There, removed it.
		return true;
	}

	/*
		Method: typeof

		Determines the decompression handler required to decompress the
		specified file.

		Parameters:
			string $filename - The name of the file.

		Returns:
			string - Returns a string containing the name of the decompression
							 handlers class name if a suitable handler was found, or false
							 on failure.
	*/
	private function typeof($filename)
	{
		// Make sure the file exists.
		if(!file_exists($filename) || !is_file($filename))
		{
			return false;
		}

		// Now see if we can find the right handler.
		foreach($this->types as $class_name)
		{
			$handler = new $class_name();

			// If we can open the file, then we found it!
			if($handler->open($filename))
			{
				// First close the handler.
				$handler->close();

				// Now return the name of the class which is up to the job.
				return $class_name;
			}
		}

		// Hmm, I guess we found nothing. Sorry.
		return false;
	}

	/*
		Method: supported

		Returns an array containing the supported types of compressed files.

		Parameters:
			none

		Returns:
			array - Returns an array containing the supported types of compressed
							files which may be decompressed.
	*/
	public function supported()
	{
		// Let's build that list!
		$supported = array();

		foreach($this->types as $class_name)
		{
			$handler = new $class_name();

			// Get the types supported.
			$is_supported = $handler->is_supported();

			// Is it a boolean?
			if(!is_array($is_supported))
			{
				// So what'd say?
				if(!empty($is_supported))
				{
					$supported[] = strtolower($class_name);
				}
			}
			else
			{
				// Add all the types the handler says it can, well, handle.
				foreach($is_supported as $type)
				{
					$supported[] = $type;
				}
			}
		}

		return $supported;
	}

	/*
		Method: is_supported

		Determines whether or not the specified file could be extracted using
		the registered extraction handlers.

		Parameters:
			string $filename - The name of the file.

		Returns:
			bool - Returns true if the file could possibly be extracted by one of
						 the registered extraction handlers, false if not.

		Note:
			Please note that it says "could possibly" not it will be able to do
			so for sure, as the extractors do not test whether or not the files
			in the compressed package can be retrieved for sure.
	*/
	public function is_supported($filename)
	{
		// Simple enough.
		return $this->typeof($filename) !== false;
	}

	/*
		Method: get_object

		Obtains the correct object for the specified file from the cache, if the
		cache does not contain an object for the specified file an appropriate
		object will be instantiated.

		Parameters:
			string $filename - The name of the file.

		Returns:
			object - Returns the appropriate instance of an object which has
							 implemented the Extraction interface, but false if the file
							 does not exist or if an appropriate handler does not exist.
	*/
	private function get_object($filename)
	{
		// Is there anything in the cache? If there is we don't need to go
		// through the typeof check as it was already done.
		if(isset($this->cache[realpath($filename)]))
		{
			return $this->cache[realpath($filename)];
		}
		// We don't need to see if the file exists as the typeof method will do
		// that for us.
		elseif(($handler = $this->typeof($filename)) === false)
		{
			return false;
		}

		// Now create an instance of the appropriate extractor.
		$extractor = new $handler();

		// Make sure the extractor will open it. It should, but might as well
		// check again!
		if(!$extractor->open($filename))
		{
			// Well, didn't work. Oh well.
			return false;
		}

		// Save it to the cache.
		$this->cache[realpath($filename)] = $extractor;

		// Now return it.
		return $extractor;
	}

	/*
		Method: files

		Returns a list of files and directories which exist within the specified
		compressed file.

		Parameters:
			string $filename - The name of the compressed file.

		Returns:
			array - Returns an array containing the files and directories which
							exist within the compressed file, or false if the specified
							file does not have a valid extraction handler.

		Note:
			The child arrays will contain at least the following indices:

				string name - The name of the file or directory.
				bool is_dir - Whether the object is a directory.
				int size - The size of the file.
	*/
	public function files($filename)
	{
		// See if we can get an object.
		if(($extractor = $this->get_object($filename)) === false)
		{
			return false;
		}

		// Get that list of files.
		return $extractor->files();
	}

	/*
		Method: extract

		Extracts the files from the specified compressed file to the desired
		destination.

		Parameters:
			string $filename - The name of the compressed file.
			string $destination - The directory to extract the compressed file
														into.
			bool $safe_mode - It is possible for compressed files to have such
												file names as "../../someImportantFile.sys" and
												overwrite important files, but if this option is set
												to true any ../ will be removed from the file or
												directory name. Defaults to true.

		Returns:
			bool - Returns true if the compressed file was successfully extracted
						 to the specified destination, but false if the destination
						 does not exist and could not be created, or if the specified
						 file does not have a valid extraction handler.
	*/
	public function extract($filename, $destination, $safe_mode = true)
	{
		// See if we can get an object.
		if(($extractor = $this->get_object($filename)) === false)
		{
			return false;
		}

		return $extractor->extract($destination, $safe_mode);
	}

	/*
		Method: read

		Reads the specified file from the specified compressed file and returns
		the contents of the file or saves the contents to the desired location.

		Parameters:
			string $filename - The name of the compressed file to extract from.
			string $file - The name of the file within the compressed file.
			string $destination - The location where $file should be saved to, if
														left blank the contents will be returned.

		Returns:
			mixed - Returns a string containing the files contents if $destination
							is left empty but false if the file does not exist. If a
							destination is supplied, true will be returned on success and
							false on failure (e.g. the file does not exist) or if the
							specified compressed file does not have a valid extraction
							handler.

		Note:
			When searching for the file within the compressed file, file name
			comparisons are case sensitive.

			This will not work with directories, as only files can be retrieved.
	*/
	public function read($filename, $file, $destination = null)
	{
		// See if we can get an object.
		if(($extractor = $this->get_object($filename)) === false)
		{
			return false;
		}

		return $extractor->read($file, $destination);
	}

	/*
		Destructor: __destruct

		Closes all open extraction handlers.

		Parameters:
			none

		Returns:
			void - Nothing is returned by this method.
	*/
	public function __destruct()
	{
		foreach($this->cache as $extractor)
		{
			$extractor->close();
		}

		// Make the cache empty.
		$this->cache = array();
	}
}
?>