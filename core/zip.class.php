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

// We need that Extractor interface.
if(!interface_exists('Extractor'))
{
	// Get it!
	require(coredir. '/extractor.interface.php');
}

/*
	Class: Zip

	The Zip class enables the extraction of zip archives according to
	Wikipedia's zip file format information (don't you just love Wikipedia?).

	Note:
		This class implements the <Extractor> interface.
*/
class Zip implements Extractor
{
	// Variable: filename
	private $filename;

	// Variable: fp
	private $fp;

	/*
		Constructor: __construct

		Initializes all attributes to null and can also be used to open a zipped
		archive.

		Parameters:
			string $filename - The name of the zip to open.
	*/
	public function __construct($filename = null)
	{
		// Everything needs to be set to null.
		$this->filename = null;
		$this->fp = null;

		if(!empty($filename))
		{
			$this->open($filename);
		}
	}

	/*
		Method: open

		Opens the specified zip archive for reading.

		Parameters:
			string $filename - The name of the zip archive to open.

		Returns:
			bool - Returns true if the zip archive was opened successfully, false
						 if the file does not exist or is not a valid zip archive.

		Note:
			This method may also return false if extracting zip archives are not
			supported on this system. See <Zip::is_supported> for more details.
	*/
	public function open($filename)
	{
		// Already doing something else? Maybe the file doesn't exist? We will
		// make sure of that. Also make sure we can even extract zip archives.
		if(!$this->is_supported() || !empty($this->fp) || !file_exists($filename) || !is_file($filename))
		{
			return false;
		}

		// Now open the alleged zip archive.
		$fp = fopen($filename, 'r');

		// Couldn't open the file? That really sucks.
		if(empty($fp))
		{
			return false;
		}

		// Check the signature, it should be 0x04034b50.
		list(, $signature) = unpack('V', fread($fp, 4));

		// Not right?
		if($signature != 0x04034b50)
		{
			// Then I guess this isn't a zip archive.
			return false;
		}

		// We are now locking this to prevent any tampering.
		flock($fp, LOCK_SH);
		fseek($fp, 0);

		// Save a couple things, we will need them.
		$this->filename = $filename;
		$this->fp = $fp;

		// Alright, done. For now.
		return true;
	}

	/*
		Method: files

		Returns an array containing all the files and folders contained within
		the current zip archive.

		Parameters:
			none

		Returns:
			array - Returns an array containing all the files and folders in the
							zip archive.
	*/
	public function files()
	{
		// Nothing opened? Then there are no files ;-)!
		if(empty($this->fp))
		{
			return false;
		}

		// This will hold all the files and stuff :-)
		$files = array();
		$length = filesize($this->filename);
		$pos = 0;

		// It's time to get loopy!!!
		while($pos < $length)
		{
			// Is this not a local file header..? :/
			fseek($this->fp, $pos);
			list(, $signature) = unpack('V', fread($this->fp, 4));

			if($signature != 0x04034b50)
			{
				// It is not, so that's a no go.
				break;
			}

			// Skim past all the crap we don't care about...
			fseek($this->fp, $pos + 18);

			// Read out the compressed and uncompressed size.
			$file = unpack('Vcompressed_size/Vuncompressed_size', fread($this->fp, 8));

			// Just so we can follow the Extractor interface guidelines.
			$file['size'] = $file['uncompressed_size'];

			// We will need the file name and extra field length.
			$tmp = unpack('Sfilename_length/Sextra_length', fread($this->fp, 4));

			// Now read the name of the file.
			$file['name'] = fread($this->fp, $tmp['filename_length']);

			// Is it a file or directory? Easy to tell, as the file names have a trailing /.
			$file['is_dir'] = substr($file['name'], -1, 1) == '/';

			// Skim past the extra field, if anything.
			if($tmp['extra_length'] > 0)
			{
				fread($this->fp, $tmp['filename_length']);
			}

			// Now to read the data of the file? Nope. But we will save the position
			// at where the file data starts :-P
			$file['pos'] = ftell($this->fp);

			// Save the files information, otherwise, why would we have done this?
			$files[] = $file;

			// Though we will skim past the data, as we kinda need to :P
			fseek($this->fp, $file['compressed_size'], SEEK_CUR);

			// We might need to skim past the data descriptor as well, maybe.
			list(, $signature) = unpack('V', fread($this->fp, 4));

			if($signature == 0x04034b50)
			{
				// No data descriptor... So set the current position back a bit ;-)
				$pos = ftell($this->fp) - 4;
			}
			else
			{
				// Keep on going, for 8 more bytes.
				fseek($this->fp, 8, SEEK_CUR);
				$pos = ftell($this->fp);
			}
		}

		// You can have it now :-)
		return $files;
	}

	/*
		Method: extract

		Extracts the current zip archive to the specified location.

		Parameters:
			string $destination - Where the contents of the zip archive will be
														extracted to.
			bool $safe_mode - It is, of course, possible for files to have such
												names as ../../someImportantFile.sys which would
												then possibly overwrite a very important file of
												any kind. By setting this to true, the name of
												the file will have ../ and /.. removed.

		Returns:
			bool - Returns true on success, false on failure.
	*/
	public function extract($destination, $safe_mode = true)
	{
		// Can't extract the archive if it isn't opened, now can we?
		if(empty($this->fp))
		{
			return false;
		}

		// Does the destination not exist? Then we shall try to create it!
		if(!file_exists($destination))
		{
			if(!@mkdir($destination, 0755, true))
			{
				// We tried to make it, but we couldn't! :(
				return false;
			}
		}
		// Is it even a directory? That is if it happens to exist.
		elseif(file_exists($destination) && !is_dir($destination))
		{
			return false;
		}

		// Get all the files that need to be extracted.
		$files = $this->files();

		if(count($files) > 0)
		{
			foreach($files as $file)
			{
				// Prepend the destination to the files name!
				$file['name'] = $destination. '/'. $file['name'];

				// "Safe Mode" on?
				if(!empty($safe_mode))
				{
					$file['name'] = strtr($file['name'], array('../' => '', '/..' => '', './' => ''));
				}

				// Is it a directory? Simple.
				if($file['is_dir'])
				{
					if(!file_exists($file['name']))
					{
						@mkdir($file['name'], 0755, true);
					}
				}
				// Nope, a file.
				else
				{
					// Move to where the files data is located.
					fseek($this->fp, $file['pos']);

					// Open the file where it will reside ;-)
					$fp = fopen($file['name'], 'wb');

					if(empty($fp))
					{
						continue;
					}

					// Get the files data out (The compressed size, because, well, you know!)...
					$data = fread($this->fp, $file['compressed_size']);

					// The compressed and not compressed size different? Inflate it!
					if($file['compressed_size'] != $file['uncompressed_size'])
					{
						$data = @gzinflate($data);
					}

					// Now write the data.
					fwrite($fp, $data);

					// And, done!
					fclose($fp);
					unset($data);
				}
			}

			fseek($this->fp, 0);
		}

		return true;
	}

	/*
		Method: read

		Reads the specified file from the current zip archive and returns the
		contents of the file or saves it to the specified file.

		Parameters:
			string $filename - The name of the file in the zip archive to read.
			string $destination - The location where $filename should be saved to,
														if left blank the contents will be returned.

		Returns:
			mixed - Returns a string containing the files contents if $destination
							is left empty but false if the file does not exist. If
							a destination is supplied, true will be returned on success
							and false on failure (e.g. the file does not exist).

		Note:
			File names are case-sensitive!

			This does not work on directories in the archives, only files can be
			retrieved.

			If you want to retrieve a list of all the directories (and files)
			within a zip archive, check out <Zip::files>.
	*/
	public function read($filename, $destination = null)
	{
		// Nothing open?
		if(empty($this->fp))
		{
			return false;
		}

		// Load up the files in the zip archive.
		$files = $this->files();

		// Now, let's see if the file exists. In order to do that, we must
		// search the files array.
		$found = false;
		foreach($files as $file)
		{
			// Make sure it isn't a directory.
			if($file['name'] == $filename && !$file['is_dir'])
			{
				// Found it!
				$found = $file;

				break;
			}
		}

		// Was that search successful?
		if($found === false)
		{
			// No, it wasn't. Sorry.
			return false;
		}

		// Whether or not you want it in a file, we must take out the contents
		// of the file in order to decompress it.
		fseek($this->fp, $file['pos']);

		// Now read the files data.
		$file_data = fread($this->fp, $file['compressed_size']);

		// Move the pointer back to the beginning of the file.
		fseek($this->fp, 0);

		// If the compressed and uncompressed sizes are different, then we
		// should decompress it.
		if($file['compressed_size'] != $file['uncompressed_size'])
		{
			$file_data = @gzinflate($file_data);
		}

		// Now, do you want the data returned or in a file?
		if(empty($destination))
		{
			return $file_data;
		}
		else
		{
			// I guess you want us to save it somewhere. First, let's make sure
			// the directory exists in which you want to save it.
			if(!is_dir(dirname($destination)) || !file_exists(dirname($destination)))
			{
				// Try to make it.
				if(!@mkdir(dirname($destination), 0755, true))
				{
					return false;
				}
			}

			// Now try to make the file.
			$fp = fopen($destination, 'wb');

			if(empty($fp))
			{
				// Shoot! Couldn't do it!
				return false;
			}

			flock($fp, LOCK_EX);
			fwrite($fp, $file_data);
			flock($fp, LOCK_SH);
			fclose($fp);

			return true;
		}
	}

	/*
		Method: close

		Closes the zip archive currently being read.

		Parameters:
			none

		Returns:
			void - Nothing is returned by this method.

		Note:
			This method is automatically called when the object is destroyed.
	*/
	public function close()
	{
		if(!empty($this->fp))
		{
			// Release the lock, captain!
			flock($this->fp, LOCK_UN);

			// Now close it!
			fclose($this->fp);

			// Just forget the file name.
			$this->filename = null;
			$this->fp = null;
		}
	}

	/*
		Destructor: __destruct
	*/
	public function __destruct()
	{
		$this->close();
	}

	/*
		Method: is_supported

		Returns whether or not a zip archive could be extracted on the current
		system.

		Parameters:
			none

		Returns:
			bool - Returns true if a zip archive could be extracted on the current
						 system, false if not.

		Note:
			In order for a zip archive to be extracted the Zip class requires the
			use of the <www.php.net/gzinflate> function which is part of the
			<www.php.net/zlib> extension.
	*/
	public function is_supported()
	{
		return function_exists('gzinflate');
	}
}
?>