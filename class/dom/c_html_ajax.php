<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Implements Http Ajax Request */
class CHtmlAjax extends CHtmlHttpRequest
{
	const MIN_INTERVAL = 50;
	protected $m_requests = null;
	protected $m_file = null;
	protected $m_interval = 0;
	
	/** The constructor */
	public function __construct(array $requests, $file, $interval)
	{
		parent::__construct();
		
		$this->m_requests = $requests;
		$this->m_file = $file;
		$this->m_interval = max($interval, CHtmlAjax::MIN_INTERVAL);
		
		// Update Request Function ------------------------------------------
		
		$lines = array();
		$lines[] = "var fn = function(response)";
		$lines[] = "{";
		$lines[] = "var xmlDoc;";
		$lines[] = "if (window.DOMParser)";		
		$lines[] = "{";
		$lines[] = "var parser = new DOMParser();";
		$lines[] = "xmlDoc = parser.parseFromString(response,'text/html');";
		$lines[] = "process_request(info, xmlDoc);";
		$lines[] = "}";
		$lines[] = "else"; // Internet Explorer
		$lines[] = "{";
		$lines[] = "xmlDoc = new ActiveXObject('Microsoft.XMLDOM');";
		$lines[] = "xmlDoc.async = false;";
		$lines[] = "xmlDoc.loadXML(response);";		
		$lines[] = "process_request(info, xmlDoc);";
		$lines[] = "}";
		$lines[] = "}";
		$lines[] = "var url = file + '?t=' + Math.random();";
		$lines[] = "var data = 'id=' + info.id;";
		$lines[] = "post_request(url, data, fn);";

		$this->add_function("update_request", $lines, array('info', 'file'));
		
		// Process Request Function -----------------------------------------
		
		$lines = array();
		$lines[] = "for (var i = 0; i < info.tags.length; i++)";
		$lines[] = "{";
		$lines[] = "var els = xmlDoc.getElementsByTagName(info.tags[i]);";
		$lines[] = "if (els)";
		$lines[] = "{";
		$lines[] = "var el = els[0];";
		
		$lines[] = "var docEl = document.getElementById(info.elids[i]);";
		$lines[] = "if (docEl)";
		$lines[] = "{";
		$lines[] = "if (el.innerHTML) { docEl.innerHTML = el.innerHTML; }";
		$lines[] = "else if (el.xml) { docEl.innerHTML = el.xml; }";
		$lines[] = "else";
		$lines[] = "{";
		$lines[] = "docEl.removeChild(docEl.childNodes[0]);";
		$lines[] = "docEl.appendChild(el.cloneNode(true));";
		$lines[] = "}";
		$lines[] = "}";

		$lines[] = "}";
		$lines[] = "}";
			
		$this->add_function("process_request", $lines, array('info', 'xmlDoc'));
		
		// Refresh Request Function -----------------------------------------
		
		$lines = array();
		$lines[] = "setInterval(function(){update_request(info, file)}, interval);";
		
		$this->add_function("refresh_request", $lines, array('info', 'file', 'interval'));
		
		// Refresh each request ---------------------------------------------
		
		$is_var = true;
		$interval = $this->m_interval;
		
		foreach ($requests as $request)
		{
			if ($is_var)
			{
				$this->add_statement("var info = {$request->to_js_object()};");
				$is_var = false;
			}
			else
			{
				$this->add_statement("info = {$request->to_js_object()};");
			}
			
			$this->add_statement("update_request(info, \"{$file}\");");
			$this->add_statement("refresh_request(info, \"{$file}\", {$interval});");
			$interval += CHtmlAjax::MIN_INTERVAL; // Spread over time
		}
	}
}
?>