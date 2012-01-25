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
	Class: Theme

	This is the Theme system, everything that has pretty much anything to do
	with the theme of a SnowCMS powered website is here, or maybe in the
	theme.php file.
*/
class Theme
{
	// Variable: main_title
	// Contains the sites main title, this title is not independent per page
	// but dependent on the whole sites name itself.
	private $main_title;

	// Variable: title
	// The title specific to the current page.
	private $title;

	// Variable: url
	// The URL of the current themes base directory.
	private $url;

	// Variable: links
	// An array containing all the <link> tags to be added inside the <head>
	// of the document. Each array inside links can contain charset, href,
	// hreflang, media, rel, rev, target, type, class, dir, id, lang, style,
	// title and xml:lang.
	private $links;

	// Variable: js_vars
	// An associative array (variable => value) containing JavaScript
	// variables to be defined before including JavaScript files, if any.
	private $js_vars;

	// Variable: js_files
	// Contains JavaScript files to include in the documents <head> tag. Each
	// array inside can contain type, charset, defer and src.
	private $js_files;

	// Variable: meta
	// An array containing meta data to include in the documents <head> tag.
	// Each array can contain content, http-equiv, name, scheme, dir, lang and
	// xml:lang.
	private $meta;

	// Variable: templates
	// An array containing the recognized templates which can be loaded with
	// the <Theme::render> method.
	private $templates;

	// Variable: themedir
	// A string containing the base directory of the current theme, which is
	// where the header.template.php and footer.template.php files are located.
	private $themedir;

	/*
		Constructor: __construct

		Parameters:
			string $main_title - The value to set the main_title attribute to.
			string $url - The URL to the base directory of the current theme.
	*/
	public function __construct($main_title = null, $url = null, $themedir = null)
	{
		// Initiate the attributes.
		$this->title = null;
		$this->url = null;
		$this->links = array();
		$this->js_vars = array();
		$this->js_files = array();
		$this->meta = array();
		$this->templates = array();
		$this->themedir = null;

		// Setting the main title?
		if(!empty($main_title))
		{
			$this->set_main_title($main_title);
		}

		// How about the URL to the themes folder?
		if(!empty($url))
		{
			$this->set_url($url);
		}

		if(!empty($themedir))
		{
			$this->set_themedir($themedir);
		}

		// Our first meta tag!
		$this->add_meta(array('http-equiv' => 'Content-Type', 'content' => 'text/html; charset=utf-8'));
	}

	/*
		Method: clear

		Clears all current links, JavaScript files, variables, meta tags, etc.

		Parameters:
			none

		Returns:
			void - Nothing is returned by this method.

		Note:
			This method will clear the following attributes: title, url, links,
			js_vars, js_files, meta, and themedir.
	*/
	public function clear()
	{
		$this->title = null;
		$this->url = null;
		$this->links = array();
		$this->js_vars = array();
		$this->js_files = array();
		$this->meta = array();
		$this->themedir = null;
	}

	/*
		Method: set_main_title

		Sets the main title of the page.

		Parameters:
			string $main_title - What to set the sites main title to.

		Returns:
			bool - Returns true on success, false on failure.
	*/
	public function set_main_title($main_title)
	{
		// Make sure the title isn't empty.
		if(!empty($main_title))
		{
			$this->main_title = $main_title;

			return false;
		}
		else
		{
			return false;
		}
	}

	/*
		Method: set_title

		Sets the title of the specific page.

		Parameters:
			string $title - What to set the pages title to.

		Returns:
			bool - Returns true on success, false on failure.
	*/
	public function set_title($title)
	{
		// Don't set it to nothing!
		if(!empty($title))
		{
			$this->title = $title;

			return true;
		}
		else
		{
			return false;
		}
	}

	/*
		Method: set_url

		Sets the URL of the current theme, with no trailing /!

		Parameters:
			string $url - The URL of the current themes base directory (No
										trailing /!)

		Returns:
			bool - Returns true on success, false on failure.
	*/
	public function set_url($url)
	{
		// Can't have an empty URL, well, except if you never set it.
		if(!empty($url))
		{
			$this->url = $url;

			return true;
		}
		else
		{
			return false;
		}
	}

	/*
		Method: set_themedir

		Sets the base directory of the current theme, which is where the
		header.template.php and footer.template.php files are located.

		Parameters:
			string $themedir

		Returns:
			bool - Returns true on success, false on failure.
	*/
	public function set_themedir($themedir)
	{
		// Can't have a non-existent location for a theme!
		if(is_file($themedir. '/header.template.php') && is_file($themedir. '/footer.template.php'))
		{
			// Seems like this'll work.
			$this->themedir = realpath($themedir);

			return true;
		}
		else
		{
			return false;
		}
	}

	/*
		Method: main_title

		Returns the current main title.

		Parameters:
			none

		Returns:
			string - Returns the current main title of the theme.
	*/
	public function main_title()
	{
		return $this->main_title;
	}

	/*
		Method: title

		Returns the current title of the page.

		Parameters:
			none

		Returns:
			string - Returns the current title of the page.
	*/
	public function title()
	{
		return $this->title;
	}

	/*
		Method: url

		Returns the URL of the theme.

		Parameters:
			none

		Returns:
			string - Returns the URL of the theme.
	*/
	public function url()
	{
		return $this->url;
	}

	/*
		Method: themedir

		Returns the currently set theme directory where the header and footer
		templates reside.

		Parameters:
			none

		Returns:
			string - Returns the directory of the current theme.
	*/
	public function themedir()
	{
		return $this->themedir;
	}

	/*
		Method: add_link

		Adds a <link> tag to the header of the theme.

		Parameters:
			array $link - An associative array (attribute => value) containing all
										the attributes of an link tag.

		Returns:
			bool - Returns true on success, false on failure.
	*/
	public function add_link($link)
	{
		// Is href not set, link not an array, that link already exist?!?
		if(count($link) == 0 || empty($link['href']) || !is_array($link) || count($link) > 15)
		{
			return false;
		}

		// Make sure you don't have more attributes than allowed...
		$allowed_attributes = array('charset', 'href', 'hreflang', 'media', 'rel', 'rev', 'target', 'type', 'class', 'dir', 'id', 'lang', 'style', 'title', 'xml:lang');
		foreach($link as $attr => $value)
		{
			// Did we find an attribute which wasn't allowed? Uh oh!
			if(!in_array($attr, $allowed_attributes))
			{
				return false;
			}
		}

		// Still going? That means it's okay! ;-)
		$this->links[] = $link;
		return true;
	}

	/*
		Method: link_exists

		Checks to see if there is already a link in the header to the specified
		URL.

		Parameters:
			string $link_href - The href of the link to check.

		Returns:
			bool - Returns true if the link containing that href already exists,
						 false if not.
	*/
	public function link_exists($link_href)
	{
		// No need to search if the URL is blank or if there are no links!
		if(empty($link_href) || count($this->links) == 0)
		{
			return false;
		}

		// Look through them all.
		foreach($this->links as $key => $link)
		{
			// Did we find it?
			if($link['href'] == $link_href)
			{
				// Yup, we found it, so it exists!
				return true;
			}
		}

		// Still going? Then we didn't find it.
		return false;
	}

	/*
		Method: remove_link

		Removes the specified link from the header of the theme.

		Parameters:
			string $link_href - The href of the link to remove.

		Returns:
			bool - Returns true if the link was removed, false if not.
	*/
	public function remove_link($link_href)
	{
		// Empty URL? No links? Doesn't exist? Then we can't remove it, can we?!
		if(empty($link_href) || count($this->links) == 0 || !$this->link_exists($link_href))
		{
			return false;
		}

		foreach($this->links as $key => $link)
		{
			// I think we found it, if they match!
			if($link['href'] == $link_href)
			{
				unset($this->links[$key]);
				return true;
			}
		}

		// This should never happen o.O
		return false;
	}

	/*
		Method: return_links

		Returns all the links (and their information) currently added to the
		theme.

		Parameters:
			none

		Returns:
			array - Returns an array containing all the links in the theme.
	*/
	public function return_links()
	{
		return $this->links;
	}

	/*
		Method: add_js_var

		Adds a JavaScript variable to the header of the theme.

		Parameters:
			string $variable - The name of the JavaScript variable to define.
			mixed $value - The value of the variable. Can either be a string, int
			or float.

		Returns:
			bool - Returns true on success, false on failure.
	*/
	public function add_js_var($variable, $value)
	{
		// You need a variable name, but if the variable already exists...
		if(empty($variable) || $this->js_var_exists($variable))
		{
			return false;
		}

		// JSON encode the value :D
		$this->js_vars[$variable] = $value;
		return true;
	}

	/*
		Method: js_var_exists

		Checks to see if the specified JavaScript variable exists.

		Parameters:
			string $variable - The name of the JavaScript variable to check.

		Returns:
			bool - Returns true if the variable exists, false if not.
	*/
	public function js_var_exists($variable)
	{
		// A simple check, really.
		return isset($this->js_vars[$variable]);
	}

	/*
		Method: remove_js_var

		Removes the specified JavaScript variable

		Parameters:
			string $variable - The name of the JavaScript variable to remove.

		Returns:
			bool - Returns true on success, false on failure.
	*/
	public function remove_js_var($variable)
	{
		// We cannot remove something that doesn't exist, can we?
		if(!$this->js_var_exists($variable))
		{
			return false;
		}

		// Simple enough, just unset it.
		unset($this->js_vars[$variable]);
		return true;
	}

	/*
		Method: return_js_var

		Returns the specified JavaScript variables value, or all of them.

		Parameters:
			string $variable - The name of the variable to return, if set to null
												 all JavaScript variables will be returned.

		Returns:
			mixed - Returns the set value for the specified variable, if $variable
							is set (false if it does not exist), if $variable is not set,
							then an array containing all the current variables is returned.

		Note:
			Even though false is returned if the a variable is specified does not
			necessarily mean it is not set, since the variables value can also be
			set to false. In which case, you should check with
			<Theme::js_var_exists>.
	*/
	public function return_js_var($variable = null)
	{
		return !empty($variable) ? ($this->js_var_exists($variable) ? $this->js_var[$variable] : false) : $this->js_vars;
	}

	/*
		Method: add_js_file

		Adds a JavaScript file to the header of the theme.

		Parameters:
			array $script - Contains the attributes of the script tag.

		Returns:
			bool - Returns true on success, false on failure.
	*/
	public function add_js_file($script)
	{
		// We require you give us at least the source (src) of the script.
		if(empty($script) || !is_array($script) || empty($script['src']))
		{
			return false;
		}

		// No type? Add it ourselves :P
		if(empty($script['type']))
		{
			$script['type'] = 'text/javascript';
		}

		// No language? JavaScript then... Of course.
		if(empty($script['language']))
		{
			$script['language'] = 'JavaScript';
		}

		// Let's make sure you aren't throwing some random crap in...
		$allowed_attributes = array('type', 'charset', 'defer', 'src', 'language');
		foreach($script as $attr => $value)
		{
			// Did you add an attribute which doesn't exist? Tisk tisk!
			if(!in_array($attr, $allowed_attributes))
			{
				return false;
			}
		}

		// If false was never returned, everything is okay.
		$this->js_files[] = $script;
		return true;
	}

	/*
		Method: js_file_exists

		Checks to see if the specified JavaScript file exists.

		Parameters:
			string $script_src - The script source (The URL of the JS file).

		Returns:
			bool - Returns true if the js file exists, false if not.
	*/
	public function js_file_exists($script_src)
	{
		// No src? No JavaScript files currently? Then it definitely won't exist.
		if(empty($script_src) || count($this->js_files) == 0)
		{
			return false;
		}

		// Try to find the file.
		foreach($this->js_files as $js)
		{
			// Do the src's match?
			if($js['src'] == $script_src)
			{
				return true;
			}
		}

		// Nope, didn't find one.
		return false;
	}

	/*
		Method: remove_js_file

		Removes the specified JavaScript file.

		Parameters:
			string $script_src - The script source (The URL of the JS file).

		Returns:
			bool - Returns true on success, false on failure.
	*/
	public function remove_js_file($script_src)
	{
		// Does the JavaScript file not exist? Then we can't remove it.
		if(!$this->js_file_exists($script_src))
		{
			return false;
		}

		// Hmm, let's looks, as we need the key of the index to delete it.
		foreach($this->js_files as $key => $js)
		{
			if($js['src'] == $script_src)
			{
				// Found it!
				unset($this->js_files[$key]);
				return true;
			}
		}

		// This shouldn't happen o.O
		return false;
	}

	/*
		Method: return_js_files

		Returns all the JavaScript files currently added to the theme.

		Parameters:
			none

		Returns:
			array - Returns an array containing all the JavaScript files.
	*/
	public function return_js_files()
	{
		return $this->js_files;
	}

	/*
		Method: add_meta

		Adds a meta tag to the themes header.

		Parameters:
			array $meta - An array containing the meta tags information.

		Returns:
			bool - Returns true on success, false on failure.
	*/
	public function add_meta($meta)
	{
		// No information given? Or no essential information given? Then sorry,
		// can't add it!
		if(empty($meta) || !is_array($meta) || count($meta) == 0 || ((isset($meta['name']) || isset($meta['http-equiv']))))
		{
			return false;
		}
		// You can only have a name OR http-equiv value, not both!
		elseif(!empty($meta['name']) && !empty($meta['http-equiv']))
		{
			return false;
		}

		$allowed_attributes = array('content', 'http-equiv', 'name', 'scheme', 'dir', 'lang', 'xml:lang');
		foreach($meta as $attr => $value)
		{
			// You trying to add some funky attribute? Sorry.
			if(!in_array($attr, $allowed_attributes))
			{
				return false;
			}
		}

		// Looks like it is okay, add it!
		$this->meta[] = $meta;
		return true;
	}

	/*
		Method: meta_exists

		Checks to see if the specified meta tag exists.

		Parameters:
			string $name - The name of the tag (or http-equiv).

		Returns:
			bool - Returns true if the meta tag exists, false if not.
	*/
	public function meta_exists($name)
	{
		// No name supplied?
		if(empty($name) || count($this->meta) == 0)
		{
			return false;
		}

		foreach($this->meta as $meta)
		{
			// Check both the name and http-equiv value of the meta tag.
			if((isset($meta['name']) && $meta['name'] == $name) || (isset($meta['http-equiv']) && $meta['http-equiv'] == $name))
			{
				return true;
			}
		}

		// Sorry, doesn't exist. Or am I sorry? ;-)
		return false;
	}

	/*
		Method: remove_meta

		Removes the specified meta tag.

		Parameters:
			string $name - The name of the tag (or http-equiv).

		Returns:
			bool - Returns true on success, false on failure.
	*/
	public function remove_meta($name)
	{
		// Can't remove something that doesn't exist, at least, in the real
		// world.
		if(!$this->meta_exists($name))
		{
			return false;
		}

		foreach($this->meta as $key => $meta)
		{
			// Search for it!
			if((isset($meta['name']) && $meta['name'] == $name) || (isset($meta['http-equiv']) && $meta['http-equiv'] == $name))
			{
				// Remove it and we are done.
				unset($this->meta[$key]);
				return true;
			}
		}

		// Should never happen :/
		return false;
	}

	/*
		Method: return_meta

		Returns all the current meta tags that have been added to the theme.

		Parameters:
			none

		Returns:
			array - Returns an array containing all the current meta tags.
	*/
	public function return_meta()
	{
		return $this->meta;
	}

	/*
		Method: template_exists

		Checks to see if there is a template with the specified name defined,
		whether it be a file within the default templates directory or defined
		(or overridden) by a plugin.

		Parameters:
			string $name - The name of the template to check for.

		Returns:
			bool - Returns true if a template with that name is already
						 registered, false if not.
	*/
	public function template_exists($name)
	{
		// Simple enough, really.
		return $this->return_template($name) !== false;
	}


	/*
		Method: return_template

		Returns the location of the template based on the specified name.

		Parameters:
			string $name - The name of the template you wish to obtain the
										 location of.

		Returns:
			mixed - Returns a string containing the path to the specified template
							but if there is no template by that name, false will be
							returned.
	*/
	public function return_template($name)
	{
		// Before we check if there is a default template, we will see if a
		// plugin may have defined a location to a current template (or maybe
		// even a template that is plugin-specific).
		$template_location = null;

		api()->run_hooks('template_location', array(&$name, &$template_location));

		// Did we get anything?
		if($template_location === null || !file_exists($template_location))
		{
			// We will go ahead and check if there is one in the default template
			// directory, then.
			// We will check, but only after we make sure the name is safe...
			$name = basename($name);

			if(file_exists(coredir. '/templates/'. $name. '.template.php'))
			{
				// Good, there is a default template we can use.
				$template_location = coredir. '/templates/'. $name. '.template.php';
			}
			else
			{
				$template_location = null;
			}
		}

		return $template_location !== null ? realpath($template_location) : false;
	}

	/*
		Method: render

		This method is aptly named: it renders the current web page. Simply
		specify the template wished to be loaded at the time you wish it to be
		done, and it does it -- simple, no? This method will also call the
		header and footer methods as well.

		Parameters:
			string $template - The name of the template to be loaded.

		Returns:
			void - Nothing is returned by this method.

		Note:
			The template name is simply the name of the file in the
			coredir/templates directory, without the .template.php part.

			Also, plugins may add their own templates as well, by hooking into
			the template_location hook and override or define a new template
			location.
	*/
	public function render($template)
	{
		// Make sure the required theme files exist.
		if(!file_exists($this->themedir. '/header.template.php'))
		{
			die(l('<strong>Fatal theme error:</strong> Could not locate the header.template.php file.'));
		}
		elseif(!file_exists($this->themedir. '/footer.template.php'))
		{
			die(l('<strong>Fatal theme error:</strong> Could not locate the footer.template.php file.'));
		}

		api()->run_hooks('pre_render_theme');

		// This is pretty simple to do.
		require($this->themedir. '/header.template.php');

		api()->run_hooks('pre_theme_content');

		// We can go ahead and determine the location of the template now, any
		// plugin can hook into template_location to override or add any
		// template they please!
		$template_location = $this->return_template($template);

		if($template_location === false)
		{
			echo '
			<h1>', l('Theme Error'), '</h1>
			<p>', l('Sorry, but the specified theme &ndash; &quot;%s&quot; &ndash; could not be loaded, as it is not registered with the Theme system.', htmlchars($template)), '</p>';
		}
		else
		{
			require($template_location);
		}

		api()->run_hooks('post_theme_content');

		require($this->themedir. '/footer.template.php');

		api()->run_hooks('post_render_theme');
	}

	/*
		Method: generate_tag

		Parameters:
			string $name - The name of the HTML tag, like meta, link, etc.
			array $attributes - Attributes of the HTML tag.
			bool $self_closing - Whether or not the tag is self closing.

		Returns:
			string - Returns the formed tag.
	*/
	public function generate_tag($name, $attributes, $self_closing = true)
	{
		// Kinda need the name of a tag.
		if(empty($name))
		{
			return false;
		}

		// Start to generate the tag.
		$tag = '<'. strtolower($name);

		// Any attributes to add? Maybe, maybe not.
		if(is_array($attributes) && count($attributes) > 0)
		{
			$attr = array();

			foreach($attributes as $name => $value)
			{
				// As long as they aren't empty, add them.
				if(!empty($value))
				{
					$attr[] = $name. '="'. addcslashes($value, '"'). '"';
				}
			}

			// Now add them to the tag.
			$tag .= ' '. implode(' ', $attr);
		}

		// Now finally, return the tag, either self closing, or not!
		// You can finish the rest if you don't have a self closing tag ;-)
		return ($tag. (!empty($self_closing) ? ' />' : '>'));
	}
}

/*
	Function: theme

	Initializes the system theme, if required. It will also return the current
	instance of the <Theme> object as well, which can be used for, well,
	theme purposes.

	Parameters:
		none

	Returns:
		object
*/
function theme()
{
	if(!isset($GLOBALS['theme']))
	{
		// Load up <theme_load> and <theme_list> :-)
		require_once(coredir. '/theme.php');

		// Load the Implemented_Theme class...
		$GLOBALS['theme'] = api()->load_class('Theme', array(settings()->get('site_name', 'string'), themeurl. '/'. settings()->get('theme', 'string', 'default'), themedir. '/'. settings()->get('theme', 'string', 'default')));

		// Does the theme have any initialization stuff? If so, get it!
		if(file_exists(themedir. '/'. settings()->get('theme', 'string', 'default'). '/init.php'))
		{
			require_once(themedir. '/'. settings()->get('theme', 'string', 'default'). '/init.php');
		}

		// Be sure that the snowobj.js file is in all themes.
		theme()->add_js_file(array('src' => themeurl. '/default/js/snowobj.js'));

		// Along with  JavaScript variables containing the base URL and theme URL.
		theme()->add_js_var('base_url', baseurl);
		theme()->add_js_var('theme_url', themeurl);
		theme()->add_js_var('session_id', member()->session_id());

		// Now for some meta tags.
		// But only if there is anything to add.
		if(strlen(settings()->get('site_meta_desc', 'string', '')) > 0)
		{
			theme()->add_meta(array(
													'name' => 'description',
													'content' => substr(settings()->get('site_meta_desc', 'string'), 0, 255),
												));
		}

		if(strlen(settings()->get('site_meta_keywords', 'string', '')) > 0)
		{
			theme()->add_meta(array(
													'name' => 'keywords',
													'content' => substr(settings()->get('site_meta_keywords', 'string'), 0, 255),
												));
		}

		// You can hook into here to add all your theme stuffs (<link>'s,
		// js vars, js files, etc).
		api()->run_hooks('post_init_theme');
	}

	return $GLOBALS['theme'];
}
?>