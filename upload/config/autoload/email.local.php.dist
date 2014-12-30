<?php 
return array(
'mail' => array(
	'admin-email' => array(
			'email' => 'admin@localhost',
			'name'	=> 'NginAd Admin'
			),
	'reply-to' => array(
				'email' => 'admin@localhost',
				'name'	=> 'NginAd Ad Server'
			),
	/*
	 * Here you can elect to subscribe to get
	 * emails about a certain kind of event
	 * such as user sign-ups, ad zone changes,
	 * website domain additions, and
	 * ad campaign changes
	 */
	'subscribe' => array(
		'zones' 			=> true,
		'campaigns'			=> true,
		'signups'			=> true,
		'websites'			=> true,
		// dashboard user emails settings
		'user_zones'		=> true,
		'user_domains'		=> true,
		'user_ad_campaigns'	=> true,
		),
    'transport' => array(
    	// if SMTP is false sendmail will be used
    	'smtp'	  => true,
        'options' => array(
            'name'              => 'smtp.mandrillapp.com',
            'host'              => 'smtp.mandrillapp.com',
            'port'              => 587,
            'connection_class'  => 'plain',
            'connection_config' => array(
                'username' => 'username@domain.com',
                'password' => '_my_mandrill_api_key',
                'ssl' => 'tls'
            ),
        ),  
    ),
),
);