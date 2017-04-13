<?php

	class XMLManager {
	
	private $database;		// database connection

	public function XMLManager() {
		if(!isset($this->database)) {
			$this->database = new DBConn();
		}
		
	} 
	
	function DOMinnerHTML($element) 
	{ 
		$innerHTML = ""; 
		$children = $element->childNodes; 
		
		foreach ($children as $child) 
		{ 
			$tmp_dom = new DOMDocument(); 
			$tmp_dom->appendChild($tmp_dom->importNode($child, true)); 
			$innerHTML.=trim($tmp_dom->saveHTML()); 
		}
		return $innerHTML; 
	}
	
	public function printCategories() {
		$categories = $this->database->getCategories();
		
		foreach($categories as $val) {
			echo "<option value=\"". $val . "\">$val</option>";
		}
	}
	
	function stripSingleTags($tag, $string)
	{
		$string = preg_replace('#</?'.$tag.'[^>]*>#is', '', $string);
		return $string;
	}
	
	function xml2db($id, $category) {
		$xml = "../xml/" .$id.".xml";
		$doc = new DOMDocument('1.0');
		$doc->load($xml, LIBXML_DTDLOAD);
		
		$greg_exists = false;
		$todd_exists = false;
		
		$greg_element= NULL;
		$todd_element= NULL;
		
		// get xml data
		
		$r = $doc->getElementsByTagName("post")->item(0);
		
		$title = $doc->getElementsByTagName("title")->item(0)->nodeValue;
		$date = $doc->getElementsByTagName("date")->item(0)->nodeValue;
		$image = $doc->getElementsByTagName("image")->item(0)->nodeValue;
		$element_list = $doc->getElementsByTagName("news");	
		
		$original_poster = $element_list->item(0)->getAttribute("poster");
		
		foreach($element_list as $element)
		{
			
			
			if($element->getAttribute("poster") == "Greg") {
				$greg_exists = true;
				$greg_element = $element;
			}
			
			if($element->getAttribute("poster") == "Todd") {
				$todd_exists = true;
				$todd_element = $element;
			}
		}
		
		// form querys
		
		// COMIC
			
		if($original_poster == 'Greg') {
			$author = 'minion21g';
		} else if ($original_poster == 'Todd') {
			$author = 'aldurath';
		} else { $author = 'blank'; }
		
		$q = "INSERT INTO comic_entries (filepath, datecreated, categoryname, title,  createdby) VALUES ('$image', '" . date("m-j-Y H:i:s", strtotime($date)) . "', '$category', '". pg_escape_string($title) ."', '$author')";
		
		try {	
			$res = pg_query($q);
				if(!$res || (pg_affected_rows($res) == 0)) {	throw new Exception("Failed to execute query: '$q'"); }
				
		} catch (Exception $e) {	exit($e->getMessage());	}
		
		echo "Comic INSERT Success \n"; 
		
		// NEWS
				
		if($greg_exists) {
			$author = 'minion21g';
			$q = "INSERT INTO news_entries (newsbody, author, datesubmitted, id_comic, title) VALUES ('". pg_escape_string($this->stripSingleTags('span', $this->DOMinnerHTML($greg_element))) . "', '$author', '". date("m-j-Y H:i:s", strtotime($date)) . "', $id, '". pg_escape_string($title) ."')";
			
			// execute greg's news query
			try {	
				$res = pg_query($q);
					if(!$res || (pg_affected_rows($res) == 0)) {	throw new Exception("Failed to execute query: '$q'"); }
				
			} catch (Exception $e) {	exit($e->getMessage());	}
			
			echo "Greg News INSERT Success \n";
		}
		
		if($todd_exists) {		
			$author = 'aldurath';
			$q = "INSERT INTO news_entries (newsbody, author, datesubmitted, id_comic, title) VALUES ('". pg_escape_string($this->stripSingleTags('span', $this->DOMinnerHTML($todd_element))) . "', '$author', '". date("m-j-Y H:i:s", strtotime($date)) . "', $id,'". pg_escape_string($title). "')";
			
			// execute todd's news query
			try {	
				$res = pg_query($q);
					if(!$res || (pg_affected_rows($res) == 0)) {	throw new Exception("Failed to execute query: '$q'"); }
				
			} catch (Exception $e) {	exit($e->getMessage());	}
			
			echo "Todd News INSERT Success \n";
		}
	}
}

?>