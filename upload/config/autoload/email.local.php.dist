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