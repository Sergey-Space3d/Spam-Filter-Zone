<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Implements Http Request */
class CHtmlHttpRequest extends CHtmlJavaScript
{
    /** The constructor */
    public function __construct()
    {
        parent::__construct();
        
        if (!in_array("post_request", self::$functions))
        {
        	$lines = array();
	        $lines[] = "var xmlhttp;";
	        $lines[] = "if (window.XMLHttpRequest)"; // Code for IE7+, Firefox, Chrome, Opera, Safari
	        $lines[] = "{";
	        $lines[] = "xmlhttp = new XMLHttpRequest();";
	        $lines[] = "}";
	        $lines[] = "else";
	        $lines[] = "{";
	        $lines[] = "xmlhttp = new ActiveXObject('Microsoft.XMLHTTP');"; // Code for IE6, IE5
	        $lines[] = "}";
	        $lines[] = "xmlhttp.open('POST', url, true);";
	        $lines[] = "xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');";
	        $lines[] = "xmlhttp.send(data);";
	        $lines[] = "xmlhttp.onreadystatechange = function()";
	        $lines[] = "{";
	        $lines[] = "if (xmlhttp.readyState == 4 && fn)";
	        $lines[] = "{";
	        $lines[] = "if (xmlhttp.status == 200)";
	        $lines[] = "{";
	        $lines[] = "fn = (typeof(fn) == 'function') ? fn : window[fn];";
	        $lines[] = "fn.apply(this, [xmlhttp.responseText]);";
	        $lines[] = "}";
	        $lines[] = "}";
	        $lines[] = "}";
	        
	        $this->add_function("post_request", $lines, array('url', 'data', 'fn'));
        }
    }
    
    /** Returns the post data */
    public static function make_post_data(array $posts)
    {
        $arr = array();
        
        foreach ($posts as $key=>$val)
        {
            $arr[] = "{$key}={$val}";
        }
        
        return implode('&', $arr);
    }
    
    /** Returns JS statement for posting the request */
    public static function make_post_request($url, array $posts, $fn=null)
    {
        $data = self::make_post_data($posts);
        return "post_request(\"{$url}\", \"{$data}\", \"{$fn}\");";
    }
}
?>