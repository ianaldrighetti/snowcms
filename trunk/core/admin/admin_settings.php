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

// Title: System Settings

if(!function_exists('admin_settings'))
{
	/*
		Function: admin_settings

		Displays an interface to change some basic (though core!) settings
		for your system

		Parameters:
			none

		Returns:
			void - Nothing is returned by this function.

		Note:
			This function is overloadable.
	*/
	function admin_settings()
	{
		api()->run_hooks('admin_settings');

		// Let's make sure you can manage system settings.
		if(!member()->can('manage_system_settings'))
		{
			admin_access_denied();
		}

		// We have a few different settings forms we can display.
		$form_types = api()->apply_filters('admin_settings_forms', array(
																															   'basic' => array(
																																							l('Basic Settings'),
																																							l('Manage basic settings'),
																																							l('Basic settings include changing the name of your website, along with a website description keywords, and more.'),
																																						),
																															   'date' => array(
																																						 l('Date &amp; Time Settings'),
																																						 l('Manage date and time settings'),
																																						 l('The format of how a date or time is displayed can be modified here.'),
																																					 ),
																															   'mail' => array(
																																						 l('Email Settings'),
																																						 l('Manage email settings'),
																																						 l('In order for activation, password resetting and other email messages to be sent to your users, you must select how these emails are sent. If SMTP is chosen, then you may be required to supply information such as the location of the server, a username, and password.'),
																																					 ),
																																 'security' => array(
																																								 l('Security Settings'),
																																								 l('Manage security related settings'),
																																								 l('There are a couple administrative security options which can be configured, such as disabling administrative security all together and also the timeout period for administrative authentication.'),
																																							 ),
																															   'other' => array(
																																							l('Other Settings'),
																																							l('Manage miscellaneous settings'),
																																							l('Other settings that do not belong to any other category can be found here, such as UTF-8 support, disabling administrative security, and others.'),
																																						),
																															 ));

		// Which one are we going to generate?
		$form_type = !empty($_GET['type']) && isset($form_types[$_GET['type']]) ? $_GET['type'] : 'basic';

		// Just to make sure.
		$GLOBALS['_GET']['type'] = $form_type;

		// This will come in handy.
		api()->context['section_menu'] = array();
		$GLOBALS['settings_identifiers'] = array();
		$is_first = true;
		foreach($form_types as $type_id => $type_info)
		{
			$GLOBALS['settings_identifiers'][$type_id] = $type_info[0];

			api()->context['section_menu'][] = array(
																					 'href' => baseurl. '/index.php?action=admin&amp;sa=settings&amp;type='. $type_id,
																					 'title' => $type_info[1],
																					 'is_first' => $is_first,
																					 'is_selected' => $form_type == $type_id,
																					 'text' => $type_info[0],
																				 );

			// Nothing else will be first.
			$is_first = false;
		}

		admin_settings_generate_form($form_type);
		$form = api()->load_class('Form');

		// Submitting the form? Alright.
		if(!empty($_POST[$form_type. '_settings_form']))
		{
			// We shall process it! But through AJAX?
			if(isset($_GET['ajax']))
			{
				echo $form->json_process($form_type. '_settings_form');
				exit;
			}
			else
			{
				// Just regular ol' submitting ;)
				$form->process($form_type. '_settings_form');
			}
		}

		admin_current_area($form_type. '_system_settings');

		theme()->set_title(htmlchars_decode($form_types[$form_type][0]));

		api()->context['form'] = $form;
		api()->context['form_type'] = $form_type;
		api()->context['settings_title'] = $form_types[$form_type][0];
		api()->context['settings_description'] = $form_types[$form_type][2];

		theme()->render('admin_settings');
	}
}

if(!function_exists('admin_settings_generate_form'))
{
	/*
		Function: admin_settings_generate_form

		Generates the right settings form according to the type requested.

		Parameters:
			string $form_type - The settings form to generate.

		Returns:
			void - Nothing is returned by this function.

		Note:
			This function is overloadable.
	*/
	function admin_settings_generate_form($form_type)
	{
		$form = api()->load_class('Form');

		$form->add($form_type. '_settings_form', array(
																				'action' => baseurl. '/index.php?action=admin&amp;sa=settings&amp;type='. $form_type,
																				'callback' => 'admin_settings_handle',
																				'submit' => l('Update settings'),
																			));

		$form->current($form_type. '_settings_form');

		// Now, which input's do we need to add?
		if($form_type == 'basic')
		{
			// Basic includes things such as website name, sub title, description
			// and so on.
			$form->add_input(array(
												 'name' => 'site_name',
												 'type' => 'string',
												 'label' => l('Website name'),
												 'subtext' => l('The name of your website.'),
												 'default_value' => htmlchars_decode(settings()->get('site_name', 'string')),
											 ));

			// The sub title for the website. Kind of like a slogan.
			$form->add_input(array(
												 'name' => 'site_sub_title',
												 'type' => 'string',
												 'label' => l('Website subtitle'),
												 'subtext' => l('Kind of like a slogan.'),
												 'default_value' => htmlchars_decode(settings()->get('site_sub_title', 'string')),
											 ));

			// Website description.
			$form->add_input(array(
												 'name' => 'site_meta_desc',
												 'type' => 'textarea',
												 'label' => l('Website description'),
												 'subtext' => l('A description of your website which will appear in the &lt;head&gt; of your website.'),
												 'default_value' => htmlchars_decode(settings()->get('site_meta_desc')),
												 'rows' => 4,
											 ));

			// Some keywords, perhaps.
			$form->add_input(array(
												 'name' => 'site_meta_keywords',
												 'type' => 'string',
												 'label' => l('Website keywords'),
												 'subtext' => l('A list of a comma separated keywords which will appear in the &lt;head&gt; of your website.'),
												 'default_value' => htmlchars_decode(settings()->get('site_meta_keywords')),
											 ));

			// Whether or not to display your systems SnowCMS version.
			$form->add_input(array(
												 'name' => 'show_version',
												 'type' => 'checkbox',
												 'label' => l('Display SnowCMS version'),
												 'subtext' => l('When enabled the version of SnowCMS you are running will be displayed.'),
												 'default_value' => htmlchars_decode(settings()->get('show_version', 'int')),
											 ));

		}
		// Anything relating to date and time should go here.
		elseif($form_type == 'date')
		{
			// Time formatting information!
			// This is for when strictly the date (no time) is to be shown.
			$form->add_input(array(
												 'name' => 'date_format',
												 'type' => 'string-html',
												 'label' => l('Date format:'),
												 'subtext' => l('Date only format. See the <a href="http://www.php.net/strftime" title="PHP: strftime function">strftime</a> documentation for more formatting information. <span class="bold">HTML is allowed.</span>'),
												 'default_value' => settings()->get('date_format', 'string'),
											 ));

			// This one is for just when time is to be displayed.
			$form->add_input(array(
												 'name' => 'time_format',
												 'type' => 'string-html',
												 'label' => l('Time format:'),
												 'subtext' => l('Time only format. See the <a href="http://www.php.net/strftime" title="PHP: strftime function">strftime</a> documentation for more formatting information. <span class="bold">HTML is allowed.</span>'),
												 'default_value' => settings()->get('time_format', 'string'),
											 ));

			// As you probably guessed, this is a combination.
			$form->add_input(array(
												 'name' => 'datetime_format',
												 'type' => 'string-html',
												 'label' => l('Date and time format:'),
												 'subtext' => l('Date and time format. See the <a href="http://www.php.net/strftime" title="PHP: strftime function">strftime</a> documentation for more formatting information. <span class="bold">HTML is allowed.</span>'),
												 'default_value' => settings()->get('datetime_format', 'string'),
											 ));

			// SnowCMS sets the timezone to UTC by default, but you can go ahead
			// and change it if you want ;-).
			$form->add_input(array(
												 'name' => 'default_timezone',
												 'type' => 'select',
												 'label' => l('Default timezone:'),
												 'subtext' => l('This setting will change the timezone used when displaying any date and/or times on the website.'),
												 'options' => array(
																				'Africa/Abidjan' => 'Abidjan (Africa)',
																				'Africa/Accra' => 'Accra (Africa)',
																				'America/Adak' => 'Adak (America)',
																				'Africa/Addis_Ababa' => 'Addis Ababa (Africa)',
																				'Australia/Adelaide' => 'Adelaide (Australia)',
																				'Asia/Aden' => 'Aden (Asia)',
																				'Africa/Algiers' => 'Algiers (Africa)',
																				'Asia/Almaty' => 'Almaty (Asia)',
																				'Asia/Amman' => 'Amman (Asia)',
																				'Europe/Amsterdam' => 'Amsterdam (Europe)',
																				'Asia/Anadyr' => 'Anadyr (Asia)',
																				'America/Anchorage' => 'Anchorage (America)',
																				'Europe/Andorra' => 'Andorra (Europe)',
																				'America/Anguilla' => 'Anguilla (America)',
																				'Indian/Antananarivo' => 'Antananarivo (Indian)',
																				'America/Antigua' => 'Antigua (America)',
																				'Pacific/Apia' => 'Apia (Pacific)',
																				'Asia/Aqtau' => 'Aqtau (Asia)',
																				'Asia/Aqtobe' => 'Aqtobe (Asia)',
																				'America/Araguaina' => 'Araguaina (America)',
																				'America/Aruba' => 'Aruba (America)',
																				'Asia/Ashgabat' => 'Ashgabat (Asia)',
																				'Africa/Asmara' => 'Asmara (Africa)',
																				'America/Asuncion' => 'Asuncion (America)',
																				'Europe/Athens' => 'Athens (Europe)',
																				'America/Atikokan' => 'Atikokan (America)',
																				'Pacific/Auckland' => 'Auckland (Pacific)',
																				'Atlantic/Azores' => 'Azores (Atlantic)',
																				'Asia/Baghdad' => 'Baghdad (Asia)',
																				'America/Bahia' => 'Bahia (America)',
																				'America/Bahia_Banderas' => 'Bahia Banderas (America)',
																				'Asia/Bahrain' => 'Bahrain (Asia)',
																				'Asia/Baku' => 'Baku (Asia)',
																				'Africa/Bamako' => 'Bamako (Africa)',
																				'Asia/Bangkok' => 'Bangkok (Asia)',
																				'Africa/Bangui' => 'Bangui (Africa)',
																				'Africa/Banjul' => 'Banjul (Africa)',
																				'America/Barbados' => 'Barbados (America)',
																				'Asia/Beirut' => 'Beirut (Asia)',
																				'America/Belem' => 'Belem (America)',
																				'Europe/Belgrade' => 'Belgrade (Europe)',
																				'America/Belize' => 'Belize (America)',
																				'Europe/Berlin' => 'Berlin (Europe)',
																				'Atlantic/Bermuda' => 'Bermuda (Atlantic)',
																				'America/North_Dakota/Beulah' => 'Beulah (North Dakota, America)',
																				'Asia/Bishkek' => 'Bishkek (Asia)',
																				'Africa/Bissau' => 'Bissau (Africa)',
																				'America/Blanc-Sablon' => 'Blanc-Sablon (America)',
																				'Africa/Blantyre' => 'Blantyre (Africa)',
																				'America/Boa_Vista' => 'Boa Vista (America)',
																				'America/Bogota' => 'Bogota (America)',
																				'America/Boise' => 'Boise (America)',
																				'Europe/Bratislava' => 'Bratislava (Europe)',
																				'Africa/Brazzaville' => 'Brazzaville (Africa)',
																				'Australia/Brisbane' => 'Brisbane (Australia)',
																				'Australia/Broken_Hill' => 'Broken Hill (Australia)',
																				'Asia/Brunei' => 'Brunei (Asia)',
																				'Europe/Brussels' => 'Brussels (Europe)',
																				'Europe/Bucharest' => 'Bucharest (Europe)',
																				'Europe/Budapest' => 'Budapest (Europe)',
																				'America/Argentina/Buenos_Aires' => 'Buenos Aires (Argentina, America)',
																				'Africa/Bujumbura' => 'Bujumbura (Africa)',
																				'Africa/Cairo' => 'Cairo (Africa)',
																				'America/Cambridge_Bay' => 'Cambridge Bay (America)',
																				'America/Campo_Grande' => 'Campo Grande (America)',
																				'Atlantic/Canary' => 'Canary (Atlantic)',
																				'America/Cancun' => 'Cancun (America)',
																				'Atlantic/Cape_Verde' => 'Cape Verde (Atlantic)',
																				'America/Caracas' => 'Caracas (America)',
																				'Africa/Casablanca' => 'Casablanca (Africa)',
																				'Antarctica/Casey' => 'Casey (Antarctica)',
																				'America/Argentina/Catamarca' => 'Catamarca (Argentina, America)',
																				'America/Cayenne' => 'Cayenne (America)',
																				'America/Cayman' => 'Cayman (America)',
																				'America/North_Dakota/Center' => 'Center (North Dakota, America)',
																				'Africa/Ceuta' => 'Ceuta (Africa)',
																				'Indian/Chagos' => 'Chagos (Indian)',
																				'Pacific/Chatham' => 'Chatham (Pacific)',
																				'America/Chicago' => 'Chicago (America)',
																				'America/Chihuahua' => 'Chihuahua (America)',
																				'Europe/Chisinau' => 'Chisinau (Europe)',
																				'Asia/Choibalsan' => 'Choibalsan (Asia)',
																				'Asia/Chongqing' => 'Chongqing (Asia)',
																				'Indian/Christmas' => 'Christmas (Indian)',
																				'Pacific/Chuuk' => 'Chuuk (Pacific)',
																				'Indian/Cocos' => 'Cocos (Indian)',
																				'Asia/Colombo' => 'Colombo (Asia)',
																				'Indian/Comoro' => 'Comoro (Indian)',
																				'Africa/Conakry' => 'Conakry (Africa)',
																				'Europe/Copenhagen' => 'Copenhagen (Europe)',
																				'America/Argentina/Cordoba' => 'Cordoba (Argentina, America)',
																				'America/Costa_Rica' => 'Costa Rica (America)',
																				'America/Cuiaba' => 'Cuiaba (America)',
																				'America/Curacao' => 'Curacao (America)',
																				'Australia/Currie' => 'Currie (Australia)',
																				'Africa/Dakar' => 'Dakar (Africa)',
																				'Asia/Damascus' => 'Damascus (Asia)',
																				'America/Danmarkshavn' => 'Danmarkshavn (America)',
																				'Africa/Dar_es_Salaam' => 'Dar es Salaam (Africa)',
																				'Australia/Darwin' => 'Darwin (Australia)',
																				'Antarctica/Davis' => 'Davis (Antarctica)',
																				'America/Dawson' => 'Dawson (America)',
																				'America/Dawson_Creek' => 'Dawson Creek (America)',
																				'America/Denver' => 'Denver (America)',
																				'America/Detroit' => 'Detroit (America)',
																				'Asia/Dhaka' => 'Dhaka (Asia)',
																				'Asia/Dili' => 'Dili (Asia)',
																				'Africa/Djibouti' => 'Djibouti (Africa)',
																				'America/Dominica' => 'Dominica (America)',
																				'Africa/Douala' => 'Douala (Africa)',
																				'Asia/Dubai' => 'Dubai (Asia)',
																				'Europe/Dublin' => 'Dublin (Europe)',
																				'Antarctica/DumontDUrville' => 'DumontDUrville (Antarctica)',
																				'Asia/Dushanbe' => 'Dushanbe (Asia)',
																				'Pacific/Easter' => 'Easter (Pacific)',
																				'America/Edmonton' => 'Edmonton (America)',
																				'Pacific/Efate' => 'Efate (Pacific)',
																				'America/Eirunepe' => 'Eirunepe (America)',
																				'Africa/El_Aaiun' => 'El Aaiun (Africa)',
																				'America/El_Salvador' => 'El Salvador (America)',
																				'Pacific/Enderbury' => 'Enderbury (Pacific)',
																				'Australia/Eucla' => 'Eucla (Australia)',
																				'Pacific/Fakaofo' => 'Fakaofo (Pacific)',
																				'Atlantic/Faroe' => 'Faroe (Atlantic)',
																				'Pacific/Fiji' => 'Fiji (Pacific)',
																				'America/Fortaleza' => 'Fortaleza (America)',
																				'Africa/Freetown' => 'Freetown (Africa)',
																				'Pacific/Funafuti' => 'Funafuti (Pacific)',
																				'Africa/Gaborone' => 'Gaborone (Africa)',
																				'Pacific/Galapagos' => 'Galapagos (Pacific)',
																				'Pacific/Gambier' => 'Gambier (Pacific)',
																				'Asia/Gaza' => 'Gaza (Asia)',
																				'Europe/Gibraltar' => 'Gibraltar (Europe)',
																				'America/Glace_Bay' => 'Glace Bay (America)',
																				'America/Godthab' => 'Godthab (America)',
																				'America/Goose_Bay' => 'Goose Bay (America)',
																				'America/Grand_Turk' => 'Grand Turk (America)',
																				'America/Grenada' => 'Grenada (America)',
																				'Pacific/Guadalcanal' => 'Guadalcanal (Pacific)',
																				'America/Guadeloupe' => 'Guadeloupe (America)',
																				'Pacific/Guam' => 'Guam (Pacific)',
																				'America/Guatemala' => 'Guatemala (America)',
																				'America/Guayaquil' => 'Guayaquil (America)',
																				'Europe/Guernsey' => 'Guernsey (Europe)',
																				'America/Guyana' => 'Guyana (America)',
																				'America/Halifax' => 'Halifax (America)',
																				'Africa/Harare' => 'Harare (Africa)',
																				'Asia/Harbin' => 'Harbin (Asia)',
																				'America/Havana' => 'Havana (America)',
																				'Asia/Hebron' => 'Hebron (Asia)',
																				'Europe/Helsinki' => 'Helsinki (Europe)',
																				'America/Hermosillo' => 'Hermosillo (America)',
																				'Asia/Ho_Chi_Minh' => 'Ho Chi Minh (Asia)',
																				'Australia/Hobart' => 'Hobart (Australia)',
																				'Asia/Hong_Kong' => 'Hong Kong (Asia)',
																				'Pacific/Honolulu' => 'Honolulu (Pacific)',
																				'Asia/Hovd' => 'Hovd (Asia)',
																				'America/Indiana/Indianapolis' => 'Indianapolis (Indiana, America)',
																				'America/Inuvik' => 'Inuvik (America)',
																				'America/Iqaluit' => 'Iqaluit (America)',
																				'Asia/Irkutsk' => 'Irkutsk (Asia)',
																				'Europe/Isle_of_Man' => 'Isle of Man (Europe)',
																				'Europe/Istanbul' => 'Istanbul (Europe)',
																				'Asia/Jakarta' => 'Jakarta (Asia)',
																				'America/Jamaica' => 'Jamaica (America)',
																				'Asia/Jayapura' => 'Jayapura (Asia)',
																				'Europe/Jersey' => 'Jersey (Europe)',
																				'Asia/Jerusalem' => 'Jerusalem (Asia)',
																				'Africa/Johannesburg' => 'Johannesburg (Africa)',
																				'Pacific/Johnston' => 'Johnston (Pacific)',
																				'Africa/Juba' => 'Juba (Africa)',
																				'America/Argentina/Jujuy' => 'Jujuy (Argentina, America)',
																				'America/Juneau' => 'Juneau (America)',
																				'Asia/Kabul' => 'Kabul (Asia)',
																				'Europe/Kaliningrad' => 'Kaliningrad (Europe)',
																				'Asia/Kamchatka' => 'Kamchatka (Asia)',
																				'Africa/Kampala' => 'Kampala (Africa)',
																				'Asia/Karachi' => 'Karachi (Asia)',
																				'Asia/Kashgar' => 'Kashgar (Asia)',
																				'Asia/Kathmandu' => 'Kathmandu (Asia)',
																				'Indian/Kerguelen' => 'Kerguelen (Indian)',
																				'Africa/Khartoum' => 'Khartoum (Africa)',
																				'Europe/Kiev' => 'Kiev (Europe)',
																				'Africa/Kigali' => 'Kigali (Africa)',
																				'Africa/Kinshasa' => 'Kinshasa (Africa)',
																				'Pacific/Kiritimati' => 'Kiritimati (Pacific)',
																				'America/Indiana/Knox' => 'Knox (Indiana, America)',
																				'Asia/Kolkata' => 'Kolkata (Asia)',
																				'Pacific/Kosrae' => 'Kosrae (Pacific)',
																				'America/Kralendijk' => 'Kralendijk (America)',
																				'Asia/Krasnoyarsk' => 'Krasnoyarsk (Asia)',
																				'Asia/Kuala_Lumpur' => 'Kuala Lumpur (Asia)',
																				'Asia/Kuching' => 'Kuching (Asia)',
																				'Asia/Kuwait' => 'Kuwait (Asia)',
																				'Pacific/Kwajalein' => 'Kwajalein (Pacific)',
																				'America/La_Paz' => 'La Paz (America)',
																				'America/Argentina/La_Rioja' => 'La Rioja (Argentina, America)',
																				'Africa/Lagos' => 'Lagos (Africa)',
																				'Africa/Libreville' => 'Libreville (Africa)',
																				'America/Lima' => 'Lima (America)',
																				'Australia/Lindeman' => 'Lindeman (Australia)',
																				'Europe/Lisbon' => 'Lisbon (Europe)',
																				'Europe/Ljubljana' => 'Ljubljana (Europe)',
																				'Africa/Lome' => 'Lome (Africa)',
																				'Europe/London' => 'London (Europe)',
																				'Arctic/Longyearbyen' => 'Longyearbyen (Arctic)',
																				'Australia/Lord_Howe' => 'Lord Howe (Australia)',
																				'America/Los_Angeles' => 'Los Angeles (America)',
																				'America/Kentucky/Louisville' => 'Louisville (Kentucky, America)',
																				'America/Lower_Princes' => 'Lower Princes (America)',
																				'Africa/Luanda' => 'Luanda (Africa)',
																				'Africa/Lubumbashi' => 'Lubumbashi (Africa)',
																				'Africa/Lusaka' => 'Lusaka (Africa)',
																				'Europe/Luxembourg' => 'Luxembourg (Europe)',
																				'Asia/Macau' => 'Macau (Asia)',
																				'America/Maceio' => 'Maceio (America)',
																				'Antarctica/Macquarie' => 'Macquarie (Antarctica)',
																				'Atlantic/Madeira' => 'Madeira (Atlantic)',
																				'Europe/Madrid' => 'Madrid (Europe)',
																				'Asia/Magadan' => 'Magadan (Asia)',
																				'Indian/Mahe' => 'Mahe (Indian)',
																				'Pacific/Majuro' => 'Majuro (Pacific)',
																				'Asia/Makassar' => 'Makassar (Asia)',
																				'Africa/Malabo' => 'Malabo (Africa)',
																				'Indian/Maldives' => 'Maldives (Indian)',
																				'Europe/Malta' => 'Malta (Europe)',
																				'America/Managua' => 'Managua (America)',
																				'America/Manaus' => 'Manaus (America)',
																				'Asia/Manila' => 'Manila (Asia)',
																				'Africa/Maputo' => 'Maputo (Africa)',
																				'America/Indiana/Marengo' => 'Marengo (Indiana, America)',
																				'Europe/Mariehamn' => 'Mariehamn (Europe)',
																				'America/Marigot' => 'Marigot (America)',
																				'Pacific/Marquesas' => 'Marquesas (Pacific)',
																				'America/Martinique' => 'Martinique (America)',
																				'Africa/Maseru' => 'Maseru (Africa)',
																				'America/Matamoros' => 'Matamoros (America)',
																				'Indian/Mauritius' => 'Mauritius (Indian)',
																				'Antarctica/Mawson' => 'Mawson (Antarctica)',
																				'Indian/Mayotte' => 'Mayotte (Indian)',
																				'America/Mazatlan' => 'Mazatlan (America)',
																				'Africa/Mbabane' => 'Mbabane (Africa)',
																				'Antarctica/McMurdo' => 'McMurdo (Antarctica)',
																				'Australia/Melbourne' => 'Melbourne (Australia)',
																				'America/Argentina/Mendoza' => 'Mendoza (Argentina, America)',
																				'America/Menominee' => 'Menominee (America)',
																				'America/Merida' => 'Merida (America)',
																				'America/Metlakatla' => 'Metlakatla (America)',
																				'America/Mexico_City' => 'Mexico City (America)',
																				'Pacific/Midway' => 'Midway (Pacific)',
																				'Europe/Minsk' => 'Minsk (Europe)',
																				'America/Miquelon' => 'Miquelon (America)',
																				'Africa/Mogadishu' => 'Mogadishu (Africa)',
																				'Europe/Monaco' => 'Monaco (Europe)',
																				'America/Moncton' => 'Moncton (America)',
																				'Africa/Monrovia' => 'Monrovia (Africa)',
																				'America/Monterrey' => 'Monterrey (America)',
																				'America/Montevideo' => 'Montevideo (America)',
																				'America/Kentucky/Monticello' => 'Monticello (Kentucky, America)',
																				'America/Montreal' => 'Montreal (America)',
																				'America/Montserrat' => 'Montserrat (America)',
																				'Europe/Moscow' => 'Moscow (Europe)',
																				'Asia/Muscat' => 'Muscat (Asia)',
																				'Africa/Nairobi' => 'Nairobi (Africa)',
																				'America/Nassau' => 'Nassau (America)',
																				'Pacific/Nauru' => 'Nauru (Pacific)',
																				'Africa/Ndjamena' => 'Ndjamena (Africa)',
																				'America/North_Dakota/New_Salem' => 'New Salem (North Dakota, America)',
																				'America/New_York' => 'New York (America)',
																				'Africa/Niamey' => 'Niamey (Africa)',
																				'Asia/Nicosia' => 'Nicosia (Asia)',
																				'America/Nipigon' => 'Nipigon (America)',
																				'Pacific/Niue' => 'Niue (Pacific)',
																				'America/Nome' => 'Nome (America)',
																				'Pacific/Norfolk' => 'Norfolk (Pacific)',
																				'America/Noronha' => 'Noronha (America)',
																				'Africa/Nouakchott' => 'Nouakchott (Africa)',
																				'Pacific/Noumea' => 'Noumea (Pacific)',
																				'Asia/Novokuznetsk' => 'Novokuznetsk (Asia)',
																				'Asia/Novosibirsk' => 'Novosibirsk (Asia)',
																				'America/Ojinaga' => 'Ojinaga (America)',
																				'Asia/Omsk' => 'Omsk (Asia)',
																				'Asia/Oral' => 'Oral (Asia)',
																				'Europe/Oslo' => 'Oslo (Europe)',
																				'Africa/Ouagadougou' => 'Ouagadougou (Africa)',
																				'Pacific/Pago_Pago' => 'Pago Pago (Pacific)',
																				'Pacific/Palau' => 'Palau (Pacific)',
																				'Antarctica/Palmer' => 'Palmer (Antarctica)',
																				'America/Panama' => 'Panama (America)',
																				'America/Pangnirtung' => 'Pangnirtung (America)',
																				'America/Paramaribo' => 'Paramaribo (America)',
																				'Europe/Paris' => 'Paris (Europe)',
																				'Australia/Perth' => 'Perth (Australia)',
																				'America/Indiana/Petersburg' => 'Petersburg (Indiana, America)',
																				'Asia/Phnom_Penh' => 'Phnom Penh (Asia)',
																				'America/Phoenix' => 'Phoenix (America)',
																				'Pacific/Pitcairn' => 'Pitcairn (Pacific)',
																				'Europe/Podgorica' => 'Podgorica (Europe)',
																				'Pacific/Pohnpei' => 'Pohnpei (Pacific)',
																				'Asia/Pontianak' => 'Pontianak (Asia)',
																				'Pacific/Port_Moresby' => 'Port Moresby (Pacific)',
																				'America/Port_of_Spain' => 'Port of Spain (America)',
																				'America/Port-au-Prince' => 'Port-au-Prince (America)',
																				'America/Porto_Velho' => 'Porto Velho (America)',
																				'Africa/Porto-Novo' => 'Porto-Novo (Africa)',
																				'Europe/Prague' => 'Prague (Europe)',
																				'America/Puerto_Rico' => 'Puerto Rico (America)',
																				'Asia/Pyongyang' => 'Pyongyang (Asia)',
																				'Asia/Qatar' => 'Qatar (Asia)',
																				'Asia/Qyzylorda' => 'Qyzylorda (Asia)',
																				'America/Rainy_River' => 'Rainy River (America)',
																				'Asia/Rangoon' => 'Rangoon (Asia)',
																				'America/Rankin_Inlet' => 'Rankin Inlet (America)',
																				'Pacific/Rarotonga' => 'Rarotonga (Pacific)',
																				'America/Recife' => 'Recife (America)',
																				'America/Regina' => 'Regina (America)',
																				'America/Resolute' => 'Resolute (America)',
																				'Indian/Reunion' => 'Reunion (Indian)',
																				'Atlantic/Reykjavik' => 'Reykjavik (Atlantic)',
																				'Europe/Riga' => 'Riga (Europe)',
																				'America/Rio_Branco' => 'Rio Branco (America)',
																				'America/Argentina/Rio_Gallegos' => 'Rio Gallegos (Argentina, America)',
																				'Asia/Riyadh' => 'Riyadh (Asia)',
																				'Europe/Rome' => 'Rome (Europe)',
																				'Antarctica/Rothera' => 'Rothera (Antarctica)',
																				'Pacific/Saipan' => 'Saipan (Pacific)',
																				'Asia/Sakhalin' => 'Sakhalin (Asia)',
																				'America/Argentina/Salta' => 'Salta (Argentina, America)',
																				'Europe/Samara' => 'Samara (Europe)',
																				'Asia/Samarkand' => 'Samarkand (Asia)',
																				'America/Argentina/San_Juan' => 'San Juan (Argentina, America)',
																				'America/Argentina/San_Luis' => 'San Luis (Argentina, America)',
																				'Europe/San_Marino' => 'San Marino (Europe)',
																				'America/Santa_Isabel' => 'Santa Isabel (America)',
																				'America/Santarem' => 'Santarem (America)',
																				'America/Santiago' => 'Santiago (America)',
																				'America/Santo_Domingo' => 'Santo Domingo (America)',
																				'America/Sao_Paulo' => 'Sao Paulo (America)',
																				'Africa/Sao_Tome' => 'Sao Tome (Africa)',
																				'Europe/Sarajevo' => 'Sarajevo (Europe)',
																				'America/Scoresbysund' => 'Scoresbysund (America)',
																				'Asia/Seoul' => 'Seoul (Asia)',
																				'Asia/Shanghai' => 'Shanghai (Asia)',
																				'America/Shiprock' => 'Shiprock (America)',
																				'Europe/Simferopol' => 'Simferopol (Europe)',
																				'Asia/Singapore' => 'Singapore (Asia)',
																				'America/Sitka' => 'Sitka (America)',
																				'Europe/Skopje' => 'Skopje (Europe)',
																				'Europe/Sofia' => 'Sofia (Europe)',
																				'Atlantic/South_Georgia' => 'South Georgia (Atlantic)',
																				'Antarctica/South_Pole' => 'South Pole (Antarctica)',
																				'America/St_Barthelemy' => 'St Barthelemy (America)',
																				'Atlantic/St_Helena' => 'St Helena (Atlantic)',
																				'America/St_Johns' => 'St Johns (America)',
																				'America/St_Kitts' => 'St Kitts (America)',
																				'America/St_Lucia' => 'St Lucia (America)',
																				'America/St_Thomas' => 'St Thomas (America)',
																				'America/St_Vincent' => 'St Vincent (America)',
																				'Atlantic/Stanley' => 'Stanley (Atlantic)',
																				'Europe/Stockholm' => 'Stockholm (Europe)',
																				'America/Swift_Current' => 'Swift Current (America)',
																				'Australia/Sydney' => 'Sydney (Australia)',
																				'Antarctica/Syowa' => 'Syowa (Antarctica)',
																				'Pacific/Tahiti' => 'Tahiti (Pacific)',
																				'Asia/Taipei' => 'Taipei (Asia)',
																				'Europe/Tallinn' => 'Tallinn (Europe)',
																				'Pacific/Tarawa' => 'Tarawa (Pacific)',
																				'Asia/Tashkent' => 'Tashkent (Asia)',
																				'Asia/Tbilisi' => 'Tbilisi (Asia)',
																				'America/Tegucigalpa' => 'Tegucigalpa (America)',
																				'Asia/Tehran' => 'Tehran (Asia)',
																				'America/Indiana/Tell_City' => 'Tell City (Indiana, America)',
																				'Asia/Thimphu' => 'Thimphu (Asia)',
																				'America/Thule' => 'Thule (America)',
																				'America/Thunder_Bay' => 'Thunder Bay (America)',
																				'America/Tijuana' => 'Tijuana (America)',
																				'Europe/Tirane' => 'Tirane (Europe)',
																				'Asia/Tokyo' => 'Tokyo (Asia)',
																				'Pacific/Tongatapu' => 'Tongatapu (Pacific)',
																				'America/Toronto' => 'Toronto (America)',
																				'America/Tortola' => 'Tortola (America)',
																				'Africa/Tripoli' => 'Tripoli (Africa)',
																				'America/Argentina/Tucuman' => 'Tucuman (Argentina, America)',
																				'Africa/Tunis' => 'Tunis (Africa)',
																				'UTC' => 'UTC',
																				'Asia/Ulaanbaatar' => 'Ulaanbaatar (Asia)',
																				'Asia/Urumqi' => 'Urumqi (Asia)',
																				'America/Argentina/Ushuaia' => 'Ushuaia (Argentina, America)',
																				'Europe/Uzhgorod' => 'Uzhgorod (Europe)',
																				'Europe/Vaduz' => 'Vaduz (Europe)',
																				'America/Vancouver' => 'Vancouver (America)',
																				'Europe/Vatican' => 'Vatican (Europe)',
																				'America/Indiana/Vevay' => 'Vevay (Indiana, America)',
																				'Europe/Vienna' => 'Vienna (Europe)',
																				'Asia/Vientiane' => 'Vientiane (Asia)',
																				'Europe/Vilnius' => 'Vilnius (Europe)',
																				'America/Indiana/Vincennes' => 'Vincennes (Indiana, America)',
																				'Asia/Vladivostok' => 'Vladivostok (Asia)',
																				'Europe/Volgograd' => 'Volgograd (Europe)',
																				'Antarctica/Vostok' => 'Vostok (Antarctica)',
																				'Pacific/Wake' => 'Wake (Pacific)',
																				'Pacific/Wallis' => 'Wallis (Pacific)',
																				'Europe/Warsaw' => 'Warsaw (Europe)',
																				'America/Whitehorse' => 'Whitehorse (America)',
																				'America/Indiana/Winamac' => 'Winamac (Indiana, America)',
																				'Africa/Windhoek' => 'Windhoek (Africa)',
																				'America/Winnipeg' => 'Winnipeg (America)',
																				'America/Yakutat' => 'Yakutat (America)',
																				'Asia/Yakutsk' => 'Yakutsk (Asia)',
																				'Asia/Yekaterinburg' => 'Yekaterinburg (Asia)',
																				'America/Yellowknife' => 'Yellowknife (America)',
																				'Asia/Yerevan' => 'Yerevan (Asia)',
																				'Europe/Zagreb' => 'Zagreb (Europe)',
																				'Europe/Zaporozhye' => 'Zaporozhye (Europe)',
																				'Europe/Zurich' => 'Zurich (Europe)',
																			),
													'default_value' => settings()->get('default_timezone', 'string', 'UTC'),
												));

			// The timeformat function will say Today at ... or Yesterday at ...
			// when it is relevant, but not everyone likes that. Maybe.
			$form->add_input(array(
												 'name' => 'disable_today_yesterday',
												 'type' => 'checkbox',
												 'label' => l('Disable today/yesterday feature'),
												 'subtext' => l('Disable date and times from being displayed as <strong>Today</strong> at <em>[...]</em> and <strong>Yesterday</strong> at <em>[...]</em>.'),
												 'default_value' => settings()->get('disable_today_yesterday', 'string', false),
											 ));
		}
		// Sending email, SMTP and mail settings belong here.
		elseif($form_type == 'mail')
		{
			// The email address to, of course, send any emails from.
			$form->add_input(array(
												 'name' => 'site_email',
												 'type' => 'string',
												 'label' => l('Website email address'),
												 'subtext' => l('The email address from which emails will appear to come from.'),
												 'default_value' => htmlchars_decode(settings()->get('site_email', 'string')),
											 ));

			// What should handle sending emails..?
			$form->add_input(array(
												 'name' => 'mail_handler',
												 'type' => 'select',
												 'label' => l('Mail handler'),
												 'subtext' => l('Allows you to set which protocol (or function) handles sending emails.'),
												 'options' => api()->apply_filters('admin_mail_handler', array(
																																									 'smtp' => 'SMTP',
																																									 'mail' => 'PHP mail()',
																																								 )),
												 'default_value' => htmlchars_decode(settings()->get('mail_handler', 'string')),
											 ));

			// Your SMTP host, quite important, you know?
			$form->add_input(array(
												 'name' => 'smtp_host',
												 'type' => 'string',
												 'label' => l('SMTP host'),
												 'subtext' => l('The host address of the SMTP server.'),
												 'default_value' => htmlchars_decode(settings()->get('smtp_host', 'string')),
											 ));

			// The port of the SMTP server.
			$form->add_input(array(
												 'name' => 'smtp_port',
												 'type' => 'int',
												 'label' => l('SMTP port'),
												 'subtext' => l('The port of the SMTP server, usually 25 or 465 (if it uses SSL).'),
												 'length' => array(
																			 'min' => 1,
																			 'max' => 65535,
																		 ),
												 'default_value' => settings()->get('smtp_port', 'int'),
											 ));

			// SMTP username.
			$form->add_input(array(
												 'name' => 'smtp_user',
												 'type' => 'string',
												 'label' => l('SMTP username'),
												 'default_value' => htmlchars_decode(settings()->get('smtp_user', 'string')),
											 ));

			// SMTP password.
			$form->add_input(array(
												 'name' => 'smtp_pass',
												 'type' => 'password',
												 'label' => l('SMTP password'),
												 'subtext' => l('Your SMTP password will only be updated if this field is set.'),
												 'default_value' => '',
											 ));

			// Does the SMTP host use TLS?
			$form->add_input(array(
												 'name' => 'smtp_is_tls',
												 'type' => 'checkbox',
												 'label' => l('SMTP host uses TLS'),
												 'subtext' => l('Check this box if the SMTP host uses TLS, such as Gmail or Hotmail.'),
												 'default_value' => settings()->get('smtp_is_tls', 'int'),
												));

			// Number of seconds before the SMTP connection attempt is aborted.
			$form->add_input(array(
												 'name' => 'smtp_timeout',
												 'type' => 'int',
												 'label' => l('SMTP timeout'),
												 'subtext' => l('The maximum number, in seconds, that the server will wait for a response from the SMTP host.'),
												 'length' => array(
																			 'min' => 1,
																		 ),
												 'default_value' => settings()->get('smtp_timeout', 'int'),
											 ));

			// Additional mail parameters.
			$form->add_input(array(
												 'name' => 'mail_additional_parameters',
												 'type' => 'string',
												 'label' => l('Additional mail parameters'),
												 'subtext' => l('Any additional PHP mail() function parameters (the $additional_parameters parameter).'),
												 'default_value' => htmlchars_decode(settings()->get('mail_additional_parameters', 'string')),
											 ));

		}
		elseif($form_type == 'security')
		{
			// How long should their authentication last?
			$form->add_input(array(
												 'name' => 'admin_login_timeout',
												 'type' => 'int',
												 'length' => array(
																			 'min' => 1,
																		 ),
												 'label' => l('Authentication timeout'),
												 'subtext' => l('How often should a user have to authenticate themselves by entering their password in order to access the control panel, in minutes. Requires administrative security to be enabled.'),
												 'default_value' => settings()->get('admin_login_timeout', 'int', 15),
											 ));

			// Disable admin security? Not a good idea, but hey, it's your site!!!
			$form->add_input(array(
												 'name' => 'disable_admin_security',
												 'type' => 'checkbox',
												 'label' => l('Disable administrative security'),
												 'subtext' => l('If administrative security is disabled, then users who are allowed to access the control panel will never be prompted for their password. It is <em>not</em> recommended that this be disabled.'),
												 'default_value' => settings()->get('disable_admin_security', 'int'),
											 ));
		}
		// Anything else belongs here.
		elseif($form_type == 'other')
		{
			// Whether or not you want to enable the task system.
			$form->add_input(array(
												 'name' => 'enable_tasks',
												 'type' => 'checkbox',
												 'label' => l('Enable tasks'),
												 'subtext' => l('If enabled, scheduled tasks will be allowed to run, this is not run by a cron, but by people browsing your site.'),
												 'default_value' => settings()->get('enable_tasks', 'int'),
											 ));

			// The maximum number of tasks to run at a time.
			$form->add_input(array(
												 'name' => 'max_tasks',
												 'type' => 'int',
												 'label' => l('Maximum tasks to run at a time'),
												 'subtext' => l('The maximum number of tasks which can be ran at once at any given time.'),
												 'length' => array(
																			 'min' => 0,
																		 ),
												 'default_value' => settings()->get('max_tasks', 'int'),
											 ));

			// Enable even more UTF8 support? You crazy! :P
			$form->add_input(array(
												 'name' => 'enable_utf8',
												 'type' => 'checkbox',
												 'label' => l('Enable UTF-8 support'),
												 'subtext' => l('If enabled (and if the Multibyte PHP extension is enabled), UTF8 capable functions will be used to handle data. Please note that this can, in cases, slow your site down.'),
												 'disabled' => !function_exists('mb_internal_encoding'),
												 'default_value' => settings()->get('enable_utf8', 'int'),
											 ));

			// Log errors in the database?
			$form->add_input(array(
												 'name' => 'errors_log',
												 'type' => 'checkbox',
												 'label' => l('Log errors in database'),
												 'subtext' => l('When enabled, SnowCMS will log any PHP errors (not fatal errors) in the database, instead of the error logging system set in the php.ini.'),
												 'default_value' => settings()->get('errors_log', 'int'),
											 ));
		}

		// You may need to do this yourself.
		api()->run_hooks('admin_settings_generate_form', array($form_type));
	}
}

if(!function_exists('admin_settings_handle'))
{
	/*
		Function: admin_settings_handle

		Handles the admin_settings_form information.

		Parameters:
			array $data
			array &$errors

		Returns:
			bool - Returns true on success, false on failure.

		Note:
			This function is overloadable.

			Even if false is returned, in the case that certain settings
			were invalid, all the valid settings do get saved.
	*/
	function admin_settings_handle($data, &$errors = array())
	{
		// We will need to update the values so we don't have to redirect.
		$form = api()->load_class('Form');

		// Loop through all the settings and save them!
		foreach($form->inputs($_GET['type']. '_settings_form') as $input)
		{
			// Ignore this if it is a CSRF token.
			if(substr($input->name(), -6, 6) == '_token' && $input->type() == 'hidden')
			{
				continue;
			}

			$variable = $input->name();
			$value = $data[$variable];

			// This one is special :P
			if($variable == 'smtp_pass')
			{
				if(empty($value))
				{
					// Don't update it!
					continue;
				}
			}

			// Set it :)
			settings()->set($variable, $value, 'string');
		}

		api()->add_hook($_GET['type']. '_settings_form_messages', create_function('&$value', '
																																$value[] = l(\'Settings have been updated successfully.\');'), 10, 1);

		return true;
	}
}
?>