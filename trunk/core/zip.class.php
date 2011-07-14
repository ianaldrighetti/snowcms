<?php
////////////////////////////////////////////////////////////////////////////
//                              SnowCMS v2.0                              //
//                           By the SnowCMS Team                          //
//                             www.snowcms.com                            //
//                  Released under the GNU GPL v3 License                 //
//                    www.gnu.org/licenses/gpl-3.0.txt                    //
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
	Class: Zip

	Allows the extraction of zip archives according to the Wikipedia's (don't
	you just love Wikipedia?) zip file format information.
*/
class Zip
{
	// Variable: filename
	private $filename;

	// Variable: fp
	private $fp;

	/*
		Constructor: __construct

		Allows the opening of a zip archive.

		Parameters:
			string $filename - The name of the zip to open.
	*/
	public function __construct($filename = null)
	{
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
						 if the file does not exist, or is not a valid zip archive.
	*/
	public function open($filename)
	{
		// Already opened? Pfft. Can't open it again ;-) Nor can we open a file which doesn't exist.
		if(!empty($this->fp) || !file_exists($filename) || !is_file($filename))
		{
			return false;
		}

		// Now open the zip archive, if it is one, that is :P
		$fp = fopen($filename, 'r');

		// Couldn't open the archive? That really sucks.
		if(empty($fp))
		{
			return false;
		}

		// Check the signature, it should be equal to 0x04034b50.
		list(, $signature) = unpack('V', fread($fp, 4));

		// Not right?
		if($signature != 0x04034b50)
		{
			return false;
		}

		// We are now locking this, it is ours! :P
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
		the currently opened zip archive.

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

		Extracts the currently opened zip archive to the specified location.

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
			$made = mkdir($destination, 0755, true);

			if(empty($made))
			{
				// We tried to make it, but we couldn't! :(
				return false;
			}
		}
		// Is it even a directory if it does happen to exist?
		elseif(file_exists($destination) && !is_dir($destination))
		{
			return false;
		}

		// Get all the files that need to be extracted.
		$files = $this->files();

		if(count($files > 0))
		{
			foreach($files as $file)
			{
				// Prepend the destination to the files name!
				$file['name'] = $destination .'/'. $file['name'];

				// "Safe Mode" on?
				if(!empty($safe_mode))
				{
					$file['name'] = strtr($file['name'], array('../' => '', '/..' => ''));
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
}
?>