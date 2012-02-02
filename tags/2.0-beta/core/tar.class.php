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
	Class: Tar

	The Tar class enables the extraction of tarballed files, along with
	tarballs which are gzipped. The entire package may be extracted into a
	directory or files may be read individually. Tarballs can also be created
	with this class as well.

	Note:
		This class implements the <Extractor> interface.
*/
class Tar implements Extractor
{
	// Variable: filename
	private $filename;

	// Variable: filemtime
	private $filemtime;

	// Variable: fp
	private $fp;

	// Variable: mode
	private $mode;

	// Variable: files
	private $files;

	// Variable: gzipped
	private $is_gzipped;

	// Variable: is_ustar
	private $is_ustar;

	// Variable: is_tmpfile
	private $is_tmpfile;

	/*
		Constructor: __construct

		Initializes all attributes to null, and a tarball may be opened or
		created during this time.

		Parameters:
			string $filename - See <Tar::open>.
			string $mode - See <Tar::open>.
	*/
	public function __construct($filename = null, $mode = 'r')
	{
		// Initialize everything to null.
		$this->filename = null;
		$this->filemtime = null;
		$this->fp = null;
		$this->mode = null;
		$this->files = null;
		$this->is_gzipped = null;
		$this->is_ustar = null;
		$this->is_tmpfile = null;

		// Opening a file yet?
		if(!empty($filename))
		{
			$this->open($filename, $mode);
		}
	}

	/*
		Method: open

		Opens the specified tarball for reading or writing.

		Parameters:
			string $filename - The name of the file to open.
			string $mode - r to open the tarball for reading, w to create a
										 tarball with the specified name.

		Returns:
			bool - Returns true on success, false on failure.

		Note:
			If the mode is set to reading and the file does not exist or is not a
			valid tarball or gzipped tarball, false will be returned. When the
			mode is set to writing the existing file will be overwritten if there
			is sufficient permission to do so, otherwise false will be returned if
			the file could not be created or opened and the contents removed.

			This method could potentially return false if extracting tarballs are
			not supported on the current system. This is very unlikely however,
			because extracting tarballs requires no other extension. But keep in
			mind that extracting gzipped tarballs does require a PHP extension.
			See <Tar::is_supported> for more information.
	*/
	public function open($filename, $mode = 'r')
	{
		// Are we busy with something else? Or maybe the mode is unknown.
		if(!empty($this->mode) || !in_array(strtolower($mode), array('r', 'w')))
		{
			return false;
		}

		if(strtolower($mode) == 'r')
		{
			// Open the file for reading, if it exists!
			if(!file_exists($filename) || (file_exists($filename) && !is_file($filename)))
			{
				return false;
			}

			$filename = realpath($filename);

			// Now open it for reading.
			$fp = fopen($filename, 'rb');

			if(empty($fp))
			{
				return false;
			}

			$this->filename = $filename;
			$this->fp = $fp;
			$this->mode = 'r';

			// It's mine now ;) Sorta.
			flock($this->fp, LOCK_SH);

			// Check to see if the file supplied is gzipped, which we can do by
			// checking the first couple of bytes. If it is gzipped then we need
			// to mark it as such.
			$magic = unpack('H2a/H2b', fread($this->fp, 2));

			$this->is_gzipped = strtolower($magic['a']. $magic['b']) == '1f8b';

			// Gzipped? Then we need to do something special.
			if($this->is_gzipped() && in_array('tar.gz', $this->is_supported()))
			{
				// We need to extract the file out of its gzipped state. We will do
				// so by putting it in a temporary file.
				$fp = tmpfile();

				// That is if we could do it.
				if(empty($fp))
				{
					return false;
				}

				// Which compression method is used? It better be 8!
				fseek($this->fp, 2);
				$info = unpack('Ccm/Cflg', fread($this->fp, 2));

				if($info['cm'] != 8)
				{
					return false;
				}

				// Now move passed the modified time, XFL and OS. I couldn't care
				// less about that stuff.
				fseek($this->fp, 6, SEEK_CUR);

				// Is there a file name? I don't want it, but we need to make our
				// way beyond that.
				if($info['flg'] & 8 || $info['flg'] & 3)
				{
					while(fread($this->fp, 1) != chr(0))
					{
						// Keep going, and going, and going, and going...
						continue;
					}
				}

				// Maybe there is some comment I don't care about.
				if($info['flg'] & 4)
				{
					while(fread($this->fp, 1) != chr(0))
					{
						// You know the drill.
						continue;
					}
				}

				// CRC16 stuff?
				if($info['flg'] & 1)
				{
					fseek($this->fp, 2, SEEK_CUR);
				}

				// Now it is time to decompress it.
				$gztar_data = '';

				// Let's get it!
				while(!feof($this->fp))
				{
					$gztar_data .= fread($this->fp, 8192);
				}

				// Now write everything to the temporary file.
				fwrite($fp, gzinflate($gztar_data));

				// Close the current file pointer so we can switch it with the
				// temporary file.
				flock($this->fp, LOCK_UN);
				fclose($this->fp);

				$this->fp = $fp;
				$this->is_tmpfile = true;
			}
			// Are gzipped tarballs not supported?
			elseif($this->is_gzipped() && !in_array('tar.gz', $this->is_supported()))
			{
				return false;
			}

			// Now to see if the file is a valid tarball. But while there isn't a
			// for sure way to tell if it is a tarball, we can give it a try.
			if(filesize($filename) < 512 && !$this->is_gzipped())
			{
				// The headers in a tarball (for each file/directory) are 512 bytes,
				// so if the file isn't at least 512 bytes then it can't be valid.
				$this->close();

				return false;
			}

			// Move 512 bytes from the end, they should be all NUL-bytes.
			$this->fseek(-512, SEEK_END);
			if($this->fread(512) != str_repeat(chr(0), 512))
			{
				$this->close();

				return false;
			}

			$this->fseek(0);

			// Not gzipped? We can check if it is UStar formatted!
			$this->check_format();

			// Back to 0!
			$this->fseek(0);

			return true;
		}
		else
		{
			// Just try to open it.
			$fp = fopen($filename, 'wb');

			if(empty($fp))
			{
				return false;
			}

			// Alright, we could open it. However, we don't need it now.
			fclose($fp);

			$this->filename = $filename;
			$this->fp = true;
			$this->mode = 'w';
			$this->files = array();
			$this->is_gzipped = false;

			return true;
		}
	}

	/*
		Method: check_format

		Checks to see if the current tarball is setup with the older format, or
		the newer UStar format.

		Parameters:
			none

		Returns:
			void - Nothing is returned by this method.

		Note:
			This is a private method automatically called upon during <Tar::open>.
	*/
	private function check_format()
	{
		// We can only check the format if we are reading a file.
		if($this->mode != 'r')
		{
			return false;
		}

		$this->fseek(257);

		// At position 257, there should be ustar...
		$ustar = strtolower(trim(str_replace(chr(0), '', $this->fread(6))));
		$this->is_ustar = $ustar == 'ustar';

		// We always want to pointer to be at the beginning of the file.
		$this->fseek(0);
	}

	/*
		Method: files

		If the mode is set to read, then all the file information inside the
		current tarball will be returned, otherwise the current files which will
		be added to the tarball will be returned.

		Parameters:
			none

		Returns:
			array - An array containing all the information about the files in the
							tarball being read or created.
	*/
	public function files()
	{
		// If nothing is opened then we can't do anything.
		if(empty($this->mode))
		{
			return false;
		}

		if($this->mode == 'r')
		{
			// If we already did this then we can return what we already found,
			// but make sure the file hasn't been modified since then.
			if($this->filemtime !== null && $this->filemtime >= filemtime($this->filename) && is_array($this->files))
			{
				return $this->files;
			}

			// Get the number of bytes in the file.
			$this->fseek(0, SEEK_END);
			$bytes = $this->ftell();
			$this->fseek(0);

			// Some of the header data needs to be converted from octal to decimal.
			$octal = array('mode', 'uid', 'gid', 'size', 'mtime', 'chksum', 'type');

			// And then the format of what we shall read!
			$format = 'a100name/a8mode/a8uid/a8gid/a12size/a12mtime/a8chksum/a1type/a100linkname';

			// Now, if the tar is in UStar format, we read a bit of extra  info ;)
			if($this->is_ustar())
			{
				$format .= '/a6magic/a2version/a32uname/a32gname/a8devmajor/a8devminor/a155prefix';
			}

			$files = array();
			while($bytes > 0)
			{
				// Read out all 512 bytes.
				$file = unpack($format, ($header = $this->fread(512)));

				// Remove extra spacing, and convert octals to decimals!
				foreach($file as $key => $value)
				{
					$file[$key] = trim($value);

					if(in_array($key, $octal))
					{
						$file[$key] = octdec($file[$key]);
					}
				}

				// Is it a file or directory?
				$file['is_dir'] = substr($file['name'], -1, 1) == '/';

				// Just save the position of the file, for later use!
				$file['pos'] = $this->ftell();

				// Ignore the file data, for now (The file size must be a multiple of 512)...
				$seek = $file['size'] + (($file['size'] % 512) == 0 ? 0 : 512 - ($file['size'] % 512));
				$this->fseek($seek, SEEK_CUR);

				// Remove some bytes.
				$bytes -= 512 + $seek;

				// Is the file name not empty? Then it is a file we should save...
				// (There are a couple empty files, from the NUL-bytes at the end of
				// the tarballs)
				if(!empty($file['name']))
				{
					$files[] = $file;
				}
			}

			// Cache it for now.
			$this->files = $files;
			$this->filemtime = filemtime($this->filename);

			return $this->files;
		}
		elseif($this->mode == 'w')
		{
			// Just return what we got!
			return $this->files;
		}

		return false;
	}

	/*
		Method: extract

		Extracts the files out of the tar file, if the mode is set to read.

		Parameters:
			string $destination - The directory to extract the tarball in.
			bool $safe_mode - It is possible for people to have such file names as
												"../../someImportantFile.sys" and overwrite
												important files, but if this option is set to true
												any ../ will be removed from the file or directory
												name. Defaults to true.

		Returns:
			bool - Returns true on success, false on failure.

		Note:
			False may be returned in the event that the destination directory
			could not be created or if the directory could not be written to.
	*/
	public function extract($destination, $safe_mode = true)
	{
		// Not reading?
		if($this->mode != 'r')
		{
			return false;
		}

		// Does the destination exist?
		if(!file_exists($destination))
		{
			$made = mkdir($destination, 0755, true);

			if(empty($made))
			{
				// We tried, but it failed! :(
				return false;
			}
		}
		// Make sure the destination is a directory and that it is writable.
		elseif((file_exists($destination) && !is_dir($destination)) || !is_writable($destination))
		{
			return false;
		}

		// Turn it into an absolute path.
		$destination = realpath($destination);

		// The files method saves the position of the file, so yeah... Simple
		// enough, really.
		$this->files();

		// Before we get started, are there even any files?
		if(count($this->files) > 0)
		{
			foreach($this->files as $file)
			{
				// Prepend the destination to the file name!
				$file['name'] = $destination. '/'. $file['name'];

				// Safe mode on..?
				if(!empty($safe_mode))
				{
					$file['name'] = strtr($file['name'], array('../' => '', '/..' => ''));
				}

				// Now, is it a directory or a file?
				if(!empty($file['is_dir']))
				{
					// Make that directory, if we need to, and we're done.
					if(!file_exists($file['name']))
					{
						@mkdir($file['name'], 0755, true);
					}
				}
				else
				{
					// It's a file, super fun!
					$this->fseek($file['pos']);

					// Open the file that needs to be created.
					$fp = @fopen($file['name'], 'wb');

					// Couldn't do it?
					if(empty($fp))
					{
						continue;
					}

					// Small enough to do it quickly?
					if($file['size'] <= 8192 && $file['size'] > 0)
					{
						fwrite($fp, $this->fread($file['size']));
					}
					elseif($file['size'] > 8192)
					{
						// Nope... So we will do it in chunks.
						$left = $file['size'];
						while($left > 0)
						{
							fwrite($fp, $this->fread($left >= 8192 ? 8192 : $left));
							$left -= $left >= 8192 ? 8192 : $left;
						}
					}

					fclose($fp);
				}
			}

			$this->fseek(0);
		}

		return true;
	}

	/*
		Method: read

		Reads the specified file from the current tarball and returns the
		contents of the file or saves it to the specified file.

		Parameters:
			string $filename - The name of the file in the tarball to read.
			string $destination - The location where $filename should be saved to,
														if left blank the contents will be returned.

		Returns:
			mixed - Returns a string containing the files contents if $destination
							is left empty but false if the file does not exist. If
							a destination is supplied, true will be returned on success
							and false on failure (e.g. the file does not exist).

		Note:
			File names are case-sensitive!

			This does not work on directories in tarballs, only files can be
			retrieved.

			If you want to retrieve a list of all the directories (and files)
			within a tarball, check out <Tar::files>.
	*/
	public function read($filename, $destination = null)
	{
		// Not in read mode? Sorry.
		if($this->mode != 'r')
		{
			return false;
		}

		// Load up the files in the tarball.
		$this->files();

		// Now, let's see if the file exists. In order to do that, we must
		// search the files array.
		$found = -1;
		foreach($this->files as $index => $file)
		{
			// Make sure it isn't a directory.
			if($file['name'] == $filename && !$file['is_dir'])
			{
				// Found it!
				$found = $index;

				break;
			}
		}

		// Was that search successful?
		if($found == -1)
		{
			// No, it wasn't. Sorry.
			return false;
		}

		// Do you just want the file or do you want it in a file?
		if(empty($destination))
		{
			// Alright, I'll just give it to you.
			// First we need to move to the right position.
			$this->fseek($this->files[$found]['pos']);

			// Now read the contents.
			$file_data = $this->fread($this->files[$found]['size']);

			// Move the pointer back to the beginning.
			$this->fseek(0);

			// And return the data.
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

			// Write the contents to the file.
			$bytes = $this->files[$found]['size'];
			$this->fseek($this->files[$found]['pos']);
			while($bytes > 0)
			{
				$bytes -= fwrite($fp, $this->fread($bytes > 8192 ? 8192 : $bytes));
			}

			// Looks like we are all done.
			flock($fp, LOCK_SH);
			fclose($fp);

			$this->fseek(0);

			return true;
		}
	}

	/*
		Method: add_file

		Adds a file to the tarball that is currently being created.

		Parameters:
			string $filename - The name of the file to add to the tarball.
			string $new_filename - The new name of the file (including the
														 relative path, so just the files name to have
														 it in the root directory of the tarball).

		Returns:
			bool - Returns true on success, false on failure.

		Note:
			If no new file name is supplied, and the file is within the current
			working directory, then the new file name will be created
			automatically, so the parameter is not required, however, if it is not
			 within the current working directory, adding will fail unless you
			 supply the name.

			Also, any ../ references in the new file name will be removed!
	*/
	public function add_file($filename, $new_filename = null)
	{
		// Check the usual, and whether or not the file exists.
		if($this->mode != 'w' || !file_exists($filename) || !is_file($filename))
		{
			return false;
		}

		// Resolve the absolute file path.
		$filename = realpath($filename);

		// No new filename supplied? Alright, I'll try my best!
		if(empty($new_filename) && substr($filename, 0, strlen(getcwd())) == getcwd())
		{
			$new_filename = substr($filename, strlen(getcwd()) + 1, strlen($filename));
		}
		elseif(empty($new_filename))
		{
			return false;
		}

		// Is the new file name a directory? Nuh uh!
		if(substr($new_filename, -1, 1) == '/')
		{
			return false;
		}

		// Remove any ./ or ../
		$new_filename = strtr($new_filename, array('../' => '', '/..' => '', './' => ''));

		// Add it to the files array, and that's it, for now.
		$this->files[$new_filename] = array(
																		'name' => $filename,
																		'stat' => stat($filename),
																	);

		return true;
	}

	/*
		Method: add_from_string

		Adds a file from a string to the tarball that is currently being
		created.

		Parameters:
			string $filename - The name of the file that will be created inside
												 the tarball.
			string $file - The contents of the file.

		Returns:
			bool - Returns true on success, false on failure.

		Note:
			Just as with <Tar::add_file>, any ../ references in the file name will
			be removed.
	*/
	public function add_from_string($filename, $file)
	{
		// Check the usual...
		if($this->mode != 'w' || empty($filename) || substr($filename, -1, 1) == '/')
		{
			return false;
		}

		// No ../ ;)
		$filename = strtr($filename, array('../' => '', '/..' => '', './' => ''));

		// Simply add the file data.
		$this->files[$filename] = array(
																'data' => $file,
																'stat' => array(
																						'dev' => 0,
																						'ino' => 0,
																						'mode' => 755,
																						'nlink' => 0,
																						'uid' => 0,
																						'gid' => 0,
																						'rdev' => 0,
																						'size' => strlen($file),
																						'atime' => 0,
																						'mtime' => 0,
																						'ctime' => 0,
																						'blksize' => 0,
																						'blocks' => 0,
																					),
															);

		return true;
	}

	/*
		Method: add_empty_dir

		Adds an empty directory to the tarball that is currently being created.

		Parameters:
			string $dirname - The directory name to be created inside the tarball.

		Returns:
			bool - Returns true on success, false on failure.

		Note:
			Any ../ references will be removed, just as with <Tar::add_file>.
	*/
	public function add_empty_dir($dirname)
	{
		if($this->mode != 'w' || empty($dirname))
		{
			return false;
		}

		// Don't have a / at the end, it is needed, but I can do it :P
		if(substr($dirname, -1, 1) != '/')
		{
			$dirname .= '/';
		}

		$dirname = strtr($dirname, array('../' => '', '/..' => '', './' => ''));

		// Add it, done!
		$this->files[$dirname] = array(
															 'stat' => array(
																					 'dev' => 0,
																					 'ino' => 0,
																					 'mode' => 755,
																					 'nlink' => 0,
																					 'uid' => 0,
																					 'gid' => 0,
																					 'rdev' => 0,
																					 'size' => 0,
																					 'atime' => 0,
																					 'mtime' => 0,
																					 'ctime' => 0,
																					 'blksize' => 0,
																					 'blocks' => 0,
																				 ),
														 );

		return true;
	}

	/*
		Method: set_gzip

		If the mode is set to write then the tarball being created will be
		gzipped.

		Parameters:
			bool $gzip - Whether or not to gzip the tarball.

		Returns:
			bool - Returns true on success, false on failure.

		Note:
			False will be returned if gzipped tarballs are not supported and the
			$gzip parameter is set to true.
	*/
	public function set_gzip($gzip = true)
	{
		// You can only set whether the file is gzipped if the mode is write.
		// If you are trying to make the tarball you are creating gzipped and
		// the current system doesn't support it, we can't let you change it.
		if($this->mode != 'w' || (!in_array('tar.gz', $this->is_supported()) && !empty($gzip)))
		{
			return false;
		}

		$this->is_gzipped = !empty($gzip);

		return true;
	}

	/*
		Method: save

		Saves the created tarball into a file.

		Parameters:
			none

		Returns:
			bool - Returns true on success, false on failure.

		Note:
			If the file was written successfully, the <Tar::close> method is
			called automatically.

			Also, if you want to have the tarball gzipped,
			check out <Tar::set_gzip>.
	*/
	public function save()
	{
		// No saving if the mode isn't write.
		if($this->mode != 'w')
		{
			return false;
		}

		// Try to open the file we will create.
		$this->fp = $this->is_gzipped() ? gzopen($this->filename, 'wb') : fopen($this->filename, 'wb');

		if(empty($this->fp))
		{
			return false;
		}

		flock($this->fp, LOCK_EX);
		$this->fseek(0);

		// Are there even any files or directories?
		if(count($this->files) > 0)
		{
			// Used later :P
			$format = array(
									'mode' => array(6, ' '. chr(0)),
									'uid' => array(6, ' '. chr(0)),
									'gid' => array(6, ' '. chr(0)),
									'size' => array(11, ' '),
									'mtime' => array(11, ' '),
								);

			foreach($this->files as $filename => $file)
			{
				// Some special stuff needs to be done to certain things ;)
				foreach($format as $key => $f)
				{
					$file['stat'][$key] = str_pad(decoct($file['stat'][$key]), $f[0], ' ', STR_PAD_LEFT). $f[1];
				}

				// Make the generic header...
				$header = str_pad($filename, 100, chr(0)). $file['stat']['mode']. $file['stat']['uid']. $file['stat']['gid']. $file['stat']['size']. $file['stat']['mtime']. '        '. (!isset($file['data']) && !isset($file['name']) ? 5 : 0). str_repeat(chr(0), 100);

				// Calculate the headers checksum by converting it to their decimal
				// value.
				$checksum = 0;
				for($i = 0; $i < 257; $i++)
				{
					$checksum += ord($header[$i]);
				}

				$checksum = decoct($checksum);

				// Make it again, but with extra padding ;)
				$header = str_pad($filename, 100, chr(0)). $file['stat']['mode']. $file['stat']['uid']. $file['stat']['gid']. $file['stat']['size']. $file['stat']['mtime']. str_pad($checksum, 6, ' ', STR_PAD_LEFT). ' '. chr(0). (!isset($file['data']) && !isset($file['name']) ? 5 : 0). str_repeat(chr(0), 355);

				// Write the header to the file now.
				$this->fwrite($header);

				// Now for the file data...
				$data = isset($file['data']) ? $file['data'] : (isset($file['name']) ? file_get_contents($file['name']) : '');

				// We may need to append NUL-bytes in order to make it take up
				// multiples of 512 bytes.
				$length = octdec($file['stat']['size']);
				if($length > 0 && ($length / (double)512) != 0)
				{
					$data .= str_repeat(chr(0), 512 - ($length % 512));
				}

				// And the data!
				$this->fwrite($data);
			}
		}

		// The end of the tar contains at least 2 512 byte blocks of NUL's...
		// Weird.
		$this->fwrite(str_repeat(chr(0), 1024));
		$this->close();

		return true;
	}

	/*
		Method: close

		Closes all opened files and sets all attributes to null.

		Parameters:
			none

		Returns:
			void - Nothing is returned by this method.

		Note:
			This method is automatically called in the objects destructor.
	*/
	public function close()
	{
		if(!empty($this->mode))
		{
			@flock($this->fp, LOCK_UN);

			$this->fclose();
		}

		$this->filename = null;
		$this->filemtime = null;
		$this->fp = null;
		$this->mode = null;
		$this->files = null;
		$this->is_gzipped = null;
		$this->is_ustar = null;
		$this->is_tmpfile = null;
	}

	/*
		Method: filename

		Parameters:
			none

		Returns:
			string - Returns the current file which has been opened with
							 <Tar::open>.
	*/
	public function filename()
	{
		return $this->filename;
	}

	/*
		Method: mode

		Parameters:
			none

		Returns:
			string - Returns the current mode, r for read, w for write, null for
							 nothing.
	*/
	public function mode()
	{
		return $this->mode;
	}

	/*
		Method: is_gzipped

		Parameters:
			none

		Returns:
			bool - Returns true if the current tarball is gzipped, false if not.

		Note:
			The file does not need to be opened in read only in order for this to
			return true, if you set the file to be gzipped when creating a
			tarball, this will return true as well.
	*/
	public function is_gzipped()
	{
		return $this->is_gzipped;
	}

	/*
		Method: is_ustar

		Parameters:
			none

		Returns:
			bool - Returns true if the current tarball is in the UStar format,
						 false if not.
	*/
	public function is_ustar()
	{
		return $this->is_ustar;
	}

	/*
		Method: is_open

		Parameters:
			none

		Returns:
			bool - Returns true if a tarball is open, false if not.
	*/
	public function is_open()
	{
		return !empty($this->mode);
	}

	/*
		Destructor: __destruct
	*/
	public function __destruct()
	{
		$this->close();
	}

	/*
		Method: fread

		Reads the specified bytes from the current file pointer, whether it be
		gzipped or not.

		Parameters:
			int $length - The number of bytes to read.

		Returns:
			string
	*/
	private function fread($length)
	{
		return $this->is_gzipped() && $this->is_tmpfile !== true ? gzread($this->fp, $length) : fread($this->fp, $length);
	}

	/*
		Method: fwrite

		Writes the specified content to the current file pointer, whether it be
		gzipped or not.

		Parameters:
			string $string - The string to write to the file pointer.

		Returns:
			int - Returns the number of bytes written to the file pointer.
	*/
	private function fwrite($string)
	{
		return $this->is_gzipped() && $this->is_tmpfile !== true ? gzwrite($this->fp, $string) : fwrite($this->fp, $string);
	}

	/*
		Method: fseek

		Moves the file pointer to the specified position, whether it be gzipped
		or not.

		Parameters:
			int $offset - The seeked offset.
			int $whence - Such as SEEK_SET, SEEK_CUR or SEEK_END. Defaults to
										SEEK_SET.

		Returns:
			int - Returns 0 upon success and -1 on failure.

		Note:
			The function gzseek does not support SEEK_END.
	*/
	public function fseek($offset, $whence = SEEK_SET)
	{
		// Gzipped and doing SEEK_END? Darn...
		if($this->is_gzipped() && $whence == SEEK_END && $this->is_tmpfile !== true)
		{
			return -1;
		}

		return $this->is_gzipped() && $this->is_tmpfile !== true ? gzseek($this->fp, $offset, $whence) : fseek($this->fp, $offset, $whence);
	}

	/*
		Method: ftell

		Returns the current position of the file pointer, whether it be gzipped
		or not.

		Parameters:
			none

		Returns:
			int - Returns the position of the file pointer or false if an error
						occurs.
	*/
	public function ftell()
	{
		return $this->is_gzipped() && $this->is_tmpfile !== true ? gztell($this->fp) : ftell($this->fp);
	}

	/*
		Method: fclose

		Closes the file pointer, whether it be gzipped or not.

		Parameters:
			none

		Returns:
			bool - Returns true on success, false on failure.
	*/
	public function fclose()
	{
		return $this->is_gzipped() && $this->is_tmpfile !== true ? gzclose($this->fp) : fclose($this->fp);
	}

	/*
		Method: is_supported

		Returns the types of tarballs supported on the current system.

		Parameters:
			none

		Returns:
			array - Returns an array containing tar if tarballs are supported and
							tar.gz if gzipped tarballs are supported.

		Note:
			In order to extract gzipped tarballs the current system must have the
			<www.php.net/zlib> extension enabled.
	*/
	public function is_supported()
	{
		// Tarballs will always be supported.
		$supported = array('tar');

		// But gzipped tarballs may not be.
		if(function_exists('gzopen'))
		{
			$supported[] = 'tar.gz';
		}

		return $supported;
	}
}
?>