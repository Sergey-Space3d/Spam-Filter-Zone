<?php
// Copyright 2005-2026 Sergey Shustov.
// License: GPL v2.  See License.txt.

/** Implements Ajax Request */
class CHtmlAjaxRequest
{
    protected $m_id = 0;
    protected $m_map = array();
    
    /** The constructor */
    public function __construct($id)
    {
        $this->m_id = $id;
    }
    
    /** Map element's id to tag */
    public function map($elid, $tag)
    {
        $this->m_map[$elid] = $tag;
    }
    
    /** Convert array of mapped ids to JS object */
    public function to_js_object()
    {
        $tags = "[";
        $elids = "[";
        
        foreach ($this->m_map as $elid=>$tag)
        {
            if ($tags != "[") $tags .= ",";
            if ($elids != "[") $elids .= ",";
            
            $tags .= '"'.$tag.'"';
            $elids .= '"'.$elid.'"';
        }
        
        $tags .= "]";
        $elids .= "]";
        
        return sprintf("{id:\"%s\", tags:%s, elids:%s}", $this->m_id, $tags, $elids);
    }
}
?>