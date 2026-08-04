<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Implements the HTML page */
class CHtmlPage
{
	/** True if in development mode */
	private static $m_development = null;

	/** The JS for statements */
	private static $m_js = null;
	
	/** Display maintenance message and exit */
	private static function do_maintenance($hours)
	{
		if (!isset($_SESSION['bypass']) && $_GET['maint'] == 'bypass')
		{
			$_SESSION['bypass'] = true;
		}

		if (!$_SESSION['bypass'])
		{
		    $text = "Our site is currently undergoing an unscheduled maintenance. Please come back in {$hours} hours";
			$attrs = array('style'=>'color:white;background-color:#FF4400;margin:40px;padding:40px;font-size:26px;text-align:center;');
			echo new CHtmlElement('div', $attrs, $text);
			exit();
		}
	}
	
	/** Output meta keywords */
	private static function do_meta_keywords($keywords, $description)
	{
	    if (!$keywords && !$description)
	    {
	        echo "\n<meta name='robots' content='noindex,nofollow' />";
	        return;
	    }
	    
	    if ($keywords)
		{
		    $meta_keywords = file_get_contents($keywords);
		    
		    if ($meta_keywords)
		    {
		        $meta_keywords = str_replace("\r", "", $meta_keywords);
		        $meta_keywords = explode("\n", $meta_keywords);
		        $meta_keywords = implode(', ', $meta_keywords);
		        echo "\n<meta name='keywords' content='{$meta_keywords}' />";
		    }
		}
		
		if ($description)
		{
		    $meta_description = file_get_contents($description);
		    
		    if ($meta_description)
		    {
		        $meta_description = trim(preg_replace('/\s\s+/', ' ', $meta_description));
		        echo "\n<meta name='description' content='{$meta_description}' />";
		    }
		}
		
		echo "\n<meta name='robots' content='index,all' />";
	}

	// Lifetime *************************************************************
	
	/** Begin HTML page
	Optional arguments:
	maintenance		- the page is closed for maintenance (in hours)
	development		- the page is in development
	locale			- the locale type (defaults to en_US.UTF-8)
	timezone		- the timezone (defaults to UTC)
	charset			- the character set
	language		- the language
	author			- the author
	copyright		- the copyright notice
	viewport        - viewport meta value
	contentoptions  - X-Content-Type-Options
	keywords		- keywords filename
	description		- description filename
	icon			- favorite icon filename
	style			- CSS filename
	onbeforeunload	- the javascript function called before unloading */
	public static function begin($title, $is_homepage = false, array $args = null)
	{
		CHtmlPage::start_session();

		@extract($args);
		
		if ($maintenance > 0)
		{
			self::do_maintenance($maintenance);
		}
		
		self::$m_development = $development;
		
		if (!$locale) $locale = 'en_US.UTF-8';
		@setlocale(LC_ALL, $locale);
		
		if (!$timezone) $timezone = 'UTC';
		@date_default_timezone_set($timezone);
		
		@ob_start();
		@ignore_user_abort(true);
	
		if (!$language) $language = 'en-us';
		
		echo "<!DOCTYPE HTML>";
		echo "\n<html lang='{$language}'>";
		echo "\n<head>";

		if (!$charset) $charset = 'UTF-8';
		echo "\n<meta http-equiv='Content-Type' content='text/html'/>";
		echo "\n<meta charset='{$charset}'/>";
		echo "\n<meta name='language' content='{$language}'/>";
		if ($author) echo "\n<meta name='author' content='{$author}'/>";
		if ($copyright) echo "\n<meta name='copyright' content='{$copyright}'/>";

		self::do_meta_keywords($keywords, $description);
				
		echo "\n<meta http-equiv='pragma' content='no-cache'/>";
		echo "\n<meta http-equiv='cache-control' content='no-cache'/>";
		echo "\n<meta http-equiv='expires' content='-1'/>";
		
		if (!$viewport) $viewport = 'width=device-width, initial-scale=1';
		echo "\n<meta name='viewport' content='{$viewport}'/>";
		if (!$contentoptions) $contentoptions = 'nosniff';
		echo "\n<meta X-Content-Type-Options='{$contentoptions}' http-equiv='Content-Type'/>";
		
		if ($icon)
		{
			echo "\n<link rel='icon' href='", $icon, "' type='image/x-icon'/>";
			echo "\n<link rel='shortcut icon' href='", $icon, "' type='image/x-icon'/>";
		}
		
		if (self::$m_development) $title .= ' (DEVELOPMENT)';
		echo "\n<title>", $title, "</title>";

		if ($style)
		{
		    if (!is_array($style)) $style = array($style);
		    
		    echo "\n<style type='text/css' media='all'>";
		    echo "<!--";
		    
		    foreach ($style as $s)
		    {
                require $s;
		    }
		    
		    echo "-->";
		    echo "</style>";
		}
		
		echo "\n</head>";
		
		if ($is_homepage || !isset($_SESSION['home_page']))
		{
		    // Remember the home page
		    $_SESSION['home_page'] = $_SERVER['PHP_SELF'];
		}

		$onbeforeunload = $onbeforeunload ? 'onunload="'.$onbeforeunload.'"' : null;
		echo "\n<body {$onbeforeunload} style='margin:0px;'>";
	}

	/** Finish HTML page */
	public static function finish()
	{
	    if (self::$m_js)
	    {
	        echo self::$m_js;
	    }
	    
	    echo "\n</body>";
	    echo "\n</html>";

		while (ob_get_level())
		{
			@ob_end_flush();
		}
	}
	
	/** Start session */
	public static function start_session()
	{
		@session_start();
		
		if (isset($_POST['session_encoded']))
		{
			@session_decode($_POST['session_encoded']);
			unset($_POST['session_encoded']);
		}
	}
	
	/** Returns page's JS instance */
	public static function get_js()
	{
	    if (!self::$m_js) self::$m_js = new CHtmlJavaScript();
	    return self::$m_js;
	}

	/** Refresh page on session timeout */
	public static function refresh_on_session_timeout($elid = null, $msg_ts = 15, $url = null)
	{
	    $msec = ini_get('session.gc_maxlifetime') * 1000;
	    
	    if ($msec > 2000)
	    {
	        $msec -= 2000; // make sure that the session is not expired
	        if (!$url) $url = $_SESSION['home_page']; // default is home domain
	        $statement = "setTimeout(function(){window.location.replace('{$url}');},{$msec});";
	        self::get_js()->add_statement($statement);
	        
	        if ($elid && $msg_ts > 0)
	        {
	            $msec -= 1000 * $msg_ts;
	            
	            if ($msec > 0)
	            {
	                $statement = "setTimeout(function(){document.getElementById('{$elid}').style.display='initial';},{$msec});";
	                self::get_js()->add_statement($statement);
	            }
	        }
	    }
	}
	
	/** Return home page */
	public static function get_home_page() { return $_SESSION['home_page']; }

	// Buffers **************************************************************
	
	/** Begin output buffer */
	public static function begin_buffer()
	{
		@ob_start();
	}

	/** Flush output buffer */
	public static function flush_buffer($end = false)
	{
		if ($end) @ob_end_flush();
		@ob_flush();
		@flush();
	}

	/** Clean output buffer */
	public static function clean_buffer($end = false)
	{
		if ($end)
			@ob_end_clean();
		else
			@ob_clean();
	}

	// Cookies **************************************************************
	
	/** Get cookie's value */
	public static function get_cookie($name) { return $_COOKIE[$name]; }

	/** Set cookie. Returns true on success */
	public static function set_cookie($name, $value, $ts_span)
	{
	    $arr = parse_url($_SESSION['home_page']);
	    $arr = pathinfo($arr['path']);
	    $path = $arr['dirname'];
	    
	    $arr = parse_url($_SERVER['HTTP_HOST']);
	    $domain = $arr['host'];

	    return @setcookie($name, $value, time() + $ts_span, $path, $domain);
	}

	/** Delete cookie */
	public static function delete_cookie($name)
	{
		self::set_cookie($name, '', -3600);
		unset($_COOKIE[$name]);
	}
	
	// Last error ***********************************************************
	
	/** Set the last error (may be an array) */
	public static function set_last_error($error)
	{
	    $_SESSION['last_error'] = is_array($error) ? implode('<br/>', $error) : $error;
	}

	/** Inquire if there is the last error */
	public static function is_last_error()
	{
		return isset($_SESSION['last_error']);
	}

	/** Pop the last error */
	public static function pop_last_error()
	{
		$ret = $_SESSION['last_error'];
		unset($_SESSION['last_error']);
		return $ret;
	}

	// Last info ************************************************************
	
	/** Set the last info */
	public static function set_last_info($info)
	{
		$_SESSION['last_info'] = $info;
	}

	/** Inquire if there is the last info */
	public static function is_last_info()
	{
		return isset($_SESSION['last_info']);
	}

	/** Pop the last info */
	public static function pop_last_info()
	{
		$ret = $_SESSION['last_info'];
		unset($_SESSION['last_info']);
		return $ret;
	}
}
?>