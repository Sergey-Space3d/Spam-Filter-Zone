<?php 
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Implements loading inner file into a script */
class CHtmlInnerFile
{
	const FILE_INCLUDED		= 0x00;
	const FILE_REQUIRED     = 0x01;
	const FILE_ONCE         = 0x02;
	const FILE_DEFAULT      = 0x03;
	 
	static protected $filenames = array();
	protected $filename = null;
	protected $flags = 0;
	
	/** The constructor */
	public function __construct($filename, $flags = CHtmlInnerFile::FILE_DEFAULT) 
	{
		$this->filename = $filename;
		$this->flags = $flags;
	}
	
	/** Create instance. Returns instance on success, null otherwise */
	public static function create($filename, $required = true, $once = true)
	{
	    static $loaded = array();
	    
	    $in_array = in_array($filename, $loaded);
	    if ($once && $in_array) return null;
	    if (!$in_array) $loaded[] = $filename;
	    
	    $flags = 0;
	    if ($required) $flags &= CHtmlInnerFile::FILE_REQUIRED;
	    if ($once) $flags &= CHtmlInnerFile::FILE_ONCE;
	    
	    return new CHtmlInnerFile($filename, $flags);
	}
	
	/** Convert file contents to string */
	public function __toString()
	{
		$str = '';
		
		if ($this->filename)
		{
		    $buffering = CHtmlElement::is_buffering_enabled();
		    
			if (!$buffering)
			{
                ob_start();
			}
			
			if ($this->flags & CHtmlInnerFile::FILE_REQUIRED)
			{
				if ($this->flags & CHtmlInnerFile::FILE_ONCE)
					require_once($this->filename);
				else 
					require($this->filename);
			}
			else
			{
				if ($this->flags & CHtmlInnerFile::FILE_ONCE)
					include_once($this->filename);
				else 
					include($this->filename);
			}
			
			if (!$buffering)
			{
    			$str = ob_get_contents();
    			ob_end_clean();
    		}
		}
		
		return $str;
	}
}
?>