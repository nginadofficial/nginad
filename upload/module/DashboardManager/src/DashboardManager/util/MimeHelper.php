<?php
/**
 * NGINAD Project
 *
 * @link http://www.nginad.com
 * @copyright Copyright (c) 2013-2016 NginAd Foundation. All Rights Reserved
 * @license GPLv3
 */

namespace util;

class MimeHelper {
		
	public static function get_mime_type($file) {
	
		// our list of mime types
		$mime_types = array(
				"pdf"=>"application/pdf"
				,"exe"=>"application/octet-stream"
				,"zip"=>"application/zip"
				,"docx"=>"application/msword"
				,"doc"=>"application/msword"
				,"xls"=>"application/vnd.ms-excel"
				,"ppt"=>"application/vnd.ms-powerpoint"
				,"gif"=>"image/gif"
				,"png"=>"image/png"
				,"jpeg"=>"image/jpg"
				,"jpg"=>"image/jpg"
				,"mp3"=>"audio/mpeg"
				,"wav"=>"audio/x-wav"
				,"mpeg"=>"video/mpeg"
				,"mpg"=>"video/mpeg"
				,"mpe"=>"video/mpeg"
				,"mov"=>"video/quicktime"
				,"avi"=>"video/x-msvideo"
				,"3gp"=>"video/3gpp"
				,"css"=>"text/css"
				,"jsc"=>"application/javascript"
				,"js"=>"application/javascript"
				,"php"=>"text/html"
				,"htm"=>"text/html"
				,"html"=>"text/html"
		);
	
		$file_parts = explode('.', $file);
		
		$extension = strtolower(end($file_parts));
	
		return isset($mime_types[$extension]) ? $mime_types[$extension] : "not/found";
	}

}