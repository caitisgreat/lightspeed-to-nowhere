<?php

	class DBConn {

		private $dbconn;
		private $num_active_visitors;
		
		// CLASS CONSTRUCTOR
		
		public function DBConn() {
			$this->connect();
		}	

		private function connect()
		{ // attempt to open a connection
			try {
				$this->dbconn = pg_connect("host=localhost dbname=lstncomi_web user=lstncomi_full password=********");
					if(!$this->dbconn) {	throw new Exception('Unable to open connection to database.');	}
			
			} catch (Exception $e) {	exit($e->getMessage());	}
		}
		
		// VISITOR METHODS
		
		public function isActiveVisitor($ip, $sessionId) {
			$visitor = pg_escape_string($ip);
			$id_visitor = pg_escape_string($sessionId);	
			
			try {	// adds or updates a visitor
				$q = "SELECT ID_Visitor FROM Visitors WHERE IP_Address = '$visitor'";
				$res = pg_query($q);
					if(!$res) { throw new Exception("Failed to execute query: '$q'"); }
					
				if (pg_num_rows($res) == 1 && pg_fetch_result($res, 0, 0) == $id_visitor)
				{	return true;	} else {	return false;	}
			
			} catch (Exception $e) {	exit($e->getMessage());	}
		}
		
		public function visitorActivity($ip, $timeLastActive, $sessionId) {
			$visitor = pg_escape_string($ip);
			$id_visitor = pg_escape_string($sessionId);	
			$success = false;
			$timeLastActive = date("m-j-Y H:i:s", $timeLastActive);
			
			try {	// adds or updates a visitor
				$q = "SELECT IP_Address FROM Visitors";
				$res = pg_query($q);
					if(!$res) { throw new Exception("Failed to execute query: '$q'"); }
				
				$this->num_active_visitors = pg_num_rows($res); // updates active visitor count
							
				for($i = 0; $i< pg_num_rows($res); $i++) {
					if(pg_fetch_result($res, $i, 0) == $visitor)	{
						$q = "UPDATE Visitors SET ID_Visitor = '$id_visitor', LastActive = '$timeLastActive' WHERE IP_Address = '$visitor' ";
						$res = pg_query($q);
							if(!$res) { throw new Exception("Failed to execute query: '$q'"); }
						
						$success = true;
						break;					
					}
				}
			
				if(!$success) {
					$q = "INSERT INTO Visitors (IP_Address, LastActive, ID_Visitor) VALUES ('$visitor', '$timeLastActive', '$id_visitor') ";
					$res = pg_query($q);
						if(!$res) { throw new Exception("Failed to execute query: '$q'"); }
					$success = true;
				}		
			
				return $success;
				
			} catch (Exception $e) {	exit($e->getMessage());	}
		}
		
		public function removeDeadVisitors() {
			try {
				$q = "SELECT IP_Address, LastActive FROM Visitors";
				$res = pg_query($q);;
					if(!$res) { throw new Exception("Failed to execute query: '$q'"); }
				
				for($i = 0; $i < pg_num_rows($res); $i++) {
					if ((time() - strtotime(pg_fetch_result($res, $i, 1))) >= 3600)
					{
						$visitor = pg_fetch_result($res, $i, 0);
						$q = "DELETE FROM Visitors WHERE IP_Address = '$visitor' ";
						$rm = pg_query($q);
							if(!$rm) { throw new Exception("Failed to execute query: '$q'"); }	
					}
				}
			} catch (Exception $e) {	exit($e->getMessage());	}
		}
		
		// COMIC METHODS

		public function addComic($filepath, $datecreated, $categoryname, $title, $author) {
			try {
				$q = "INSERT INTO comic_entries (filepath, datecreated, categoryname, title, createdby) VALUES ('$filepath', '$datecreated', '$categoryname', '$title', '$author')";
				$res = pg_query($q);
					if(!$res || (pg_affected_rows($res) == 0)) {	throw new Exception("Failed to execute query: '$q'"); }
			} catch (Exception $e) {	exit($e->getMessage());	}		
		}
		
		public function getComic($cid) {
			$comic_arr = array();
			
			try {
				$q = "SELECT FilePath, CategoryName, Title, DateCreated FROM Comic_Entries WHERE ID_Comic = '$cid'";
				$res = pg_query($q);
					if(!$res) { throw new Exception("Failed to execute query: '$q'"); }
				
				for($i = 0; $i < pg_num_fields($res); $i++)
				{
					$comic_arr[pg_field_name($res, $i)] = pg_fetch_result($res, 0, $i);
				}
		
				return $comic_arr;
					
					
			} catch (Exception $e) {	exit($e->getMessage());	}
		}
		
		public function getLatestInCategory() {
			try {
				$q = "SELECT ID_Comic FROM Comic_Entries";
				
				if (isset($_SESSION['category']) && !empty($_SESSION['category'])) {
					$q .= " WHERE categoryname = '" .$_SESSION['category'] . "'";
				}
				
				$q .= " ORDER BY ID_Comic ASC";
				
				$res = pg_query($q);
					if(!$res) { throw new Exception("Failed to execute query: '$q'"); }
				
				$arrComic = pg_fetch_all($res);
				$i = pg_num_rows($res) - 1;
				
				return $arrComic[$i]['id_comic'];

			} catch (Exception $e) {	exit($e->getMessage());	}
		}
		
		public function getLatestTen() {
			$arr_comics = array();
			
			$latestId = $this->getLatestComicId();
			if($latestId > 10) {	$earlierId = $latestId - 10; } else { $earlierId = 1; }
			
			while($latestId > $earlierId) {
				$arr_comics[$latestId] = "$latestId - " . $this->getTitle($latestId);
				$latestId--;
			}
			
			return $arr_comics;
		}
		
		public function getLatestComicId() { // need category support (need seperate function for categories) 
			try { 
				$q = "SELECT ID_Comic FROM Comic_Entries";
				$res = pg_query($q);
					if(!$res) { throw new Exception("Failed to execute query: '$q'"); }
			
				return pg_num_rows($res);
			} catch (Exception $e) {	exit($e->getMessage());	}
		}
		
		public function setLastViewedComic($ip, $get) {
			$visitor = pg_escape_string($ip);
			$cid = -1;
			
			if(isset($get)) {	$cid = pg_escape_string($get);	} else {	$cid = $this->getLatestInCategory();	}
			
			try {
				$q = "UPDATE Visitors SET LastViewedComic = '$cid' WHERE IP_Address = '$visitor'";
				$res = pg_query($q);
					if(!$res) { throw new Exception("Failed to execute query: '$q'"); }
			} catch (Exception $e) {	exit($e->getMessage());	}
			
			return $cid;
		}

		public function getRandomComic() {
			try {
				$q = "SELECT ID_Comic FROM Comic_Entries ORDER BY ID_Comic ASC";
				
				$res = pg_query($q);
					if(!$res) { throw new Exception("Failed to execute query: '$q'"); }
				
				$arrComic = pg_fetch_all($res);
				$rand = mt_rand(0, (pg_num_rows($res) - 1));
				
				return $arrComic[$rand]['id_comic'];
				
			} catch (Exception $e) {	exit($e->getMessage());	}
		}
		
		
		public function getFirstComic($id) {
			try {
				$q = "SELECT ID_Comic FROM Comic_Entries";
				
				if (isset($_SESSION['category']) && !empty($_SESSION['category'])) {
					$q .= " WHERE categoryname = '" .$_SESSION['category'] . "'";
				}
				
				$q .= " ORDER BY ID_Comic ASC";
				
				$res = pg_query($q);
					if(!$res) { throw new Exception("Failed to execute query: '$q'"); }
				
				$arrComic = pg_fetch_all($res);
				
				if ($arrComic[0]['id_comic'] != $id) {
					$first = $arrComic[0]['id_comic'];
					return $first;
				} else { $first = -1; return $first; }
				
			} catch (Exception $e) {	exit($e->getMessage());	}
		}
		
		public function getNewComic($id) {
			try {
				$q = "SELECT ID_Comic FROM Comic_Entries";
				
				if (isset($_SESSION['category']) && !empty($_SESSION['category'])) {
					$q .= " WHERE categoryname = '" .$_SESSION['category'] . "'";
				}
				
				$q .= " ORDER BY ID_Comic ASC";
				
				$res = pg_query($q);
					if(!$res) { throw new Exception("Failed to execute query: '$q'"); }
				
				$arrComic = pg_fetch_all($res);
				$i = pg_num_rows($res) - 1;
				
				if ($arrComic[$i]['id_comic'] != $id) {
					$new = $arrComic[$i]['id_comic'];
					return $new;
				} else { $new = -1; return $new; }
				
			} catch (Exception $e) {	exit($e->getMessage());	}
		}
		
		public function getLeftComic($id) {
			try {
				$q = "SELECT ID_Comic FROM Comic_Entries";
				
				if (isset($_SESSION['category']) && !empty($_SESSION['category'])) {
					$q .= " WHERE categoryname = '" .$_SESSION['category'] . "'";
				}
				
				$q .= " ORDER BY ID_Comic ASC";
				
				$res = pg_query($q);
					if(!$res) { throw new Exception("Failed to execute query: '$q'"); }
				
				$arrComic = pg_fetch_all($res);
				
				for($i = 0; $i < pg_num_rows($res); $i++) {
					if($arrComic[$i]['id_comic'] == $id) {
						if (($i - 1)  >= 0) {
							$left = $arrComic[$i-1]['id_comic'];
						} else { $left = -1; }
						
						return $left;
					}
				}
			} catch (Exception $e) {	exit($e->getMessage());	}
		}
		
		public function getRightComic($id) {
			try {
				$q = "SELECT ID_Comic FROM Comic_Entries";
				
				if (isset($_SESSION['category']) && !empty($_SESSION['category'])) {
					$q .= " WHERE categoryname = '" .$_SESSION['category'] . "'";
				}
				
				$q .= " ORDER BY ID_Comic ASC";
				
				$res = pg_query($q);
					if(!$res) { throw new Exception("Failed to execute query: '$q'"); }
				
				$arrComic = pg_fetch_all($res);
				
				for($i = 0; $i < pg_num_rows($res); $i++) {
					if($arrComic[$i]['id_comic'] == $id) {
						if (($i + 1) < pg_num_rows($res)) {
							$right = $arrComic[$i+1]['id_comic'];
						} else { $right = -1; }
						
						return $right;
					}
				}
			} catch (Exception $e) {	exit($e->getMessage());	}
		}
		
		// NEWS METHODS
		
		public function addNews($newsbody, $author, $datesubmitted, $cid, $title) {
			try {
				$q = "INSERT INTO news_entries (newsbody, author, datesubmitted, id_comic, title) VALUES ('$newsbody', '$author', '$datesubmitted', $cid, '$title')";
				$res = pg_query($q);
					if(!$res || (pg_affected_rows($res) == 0)) {	throw new Exception("Failed to execute query: '$q'"); }
			} catch (Exception $e) {	exit($e->getMessage());	}
		}
		
		public function getNews($cid) {
			$comic_news_arr = array();
			
			try {
				$q = "SELECT ID_News FROM News_Entries WHERE ID_Comic = '$cid'";
				$res = pg_query($q);
					if(!$res) { throw new Exception("Failed to execute query: '$q'"); }
				
				for($i = 0; $i < pg_num_rows($res); $i++)
				{
					$comic_news_arr[$i] = pg_fetch_result($res, $i, 0);
				}
				
				// print_r($comic_news_arr);
		
				return $comic_news_arr;
				
			} catch (Exception $e) {	exit($e->getMessage());	}
		}
		
		public function getNewsEntry($nid) {
			$news_arr = array();
			
			try {
				$q = "SELECT Management.Email, Management.FirstName, Management.LastName, News_Entries.NewsBody, News_Entries.DateSubmitted, News_Entries.Author, News_Entries.Title FROM News_Entries INNER JOIN Management ON News_Entries.Author = Management.LoginName WHERE ID_News = $nid";
				$res = pg_query($q);
					if(!$res) { throw new Exception("Failed to execute query: '$q'"); }
			
				for($i = 0; $i < pg_num_fields($res); $i++)
				{
					$news_arr[pg_field_name($res, $i)] = pg_fetch_result($res, 0, $i);
				}
		
				return $news_arr;
		
			} catch (Exception $e) {	exit($e->getMessage());	}
		}
		
		// CATEGORY METHODS
		
		public function getCategories() {
			$cat_arr = array();
			
			try {
				$q = "SELECT categoryname FROM categories ORDER BY categoryname ASC";
				$res = pg_query($q);
					if(!$res) {	throw new Exception("Failed to execute query: '$q'"); }
			
				for($i = 0; $i < pg_num_rows($res); $i++)
				{
					$cat_arr[$i] = pg_fetch_result($res, $i, 0);
				}
				
				return $cat_arr;
				
			} catch (Exception $e) {	exit($e->getMessage());	}
		}
		
		// ARCHIVE METHODS
		
		public function getTitle($id) {
			try {
				$q = "SELECT title FROM Comic_Entries WHERE id_comic=$id";
					
				$res = pg_query($q);
					if(!$res) { throw new Exception("Failed to execute query: '$q'"); }
				
				return pg_fetch_result($res,0,0);
			} catch (Exception $e) {	exit($e->getMessage());	}
		}
		
		public function getArchive($month, $year) {
			try {
				$q = "SELECT ID_Comic, extract(day from datecreated) FROM Comic_Entries WHERE extract(month from datecreated)=$month and extract(year from datecreated)=$year";
				
				if (isset($_SESSION['category']) && !empty($_SESSION['category'])) {
					$q .= " and WHERE categoryname = '" .$_SESSION['category'] . "'";
				}
				
				$res = pg_query($q);
					if(!$res) { throw new Exception("Failed to execute query: '$q'"); }
				
				// Store results of query in an array.
				$calArray = array();
				for ($i = 0; $i < pg_num_rows($res); $i++)
				{
					$calArray[pg_fetch_result($res,$i, 1)][] = pg_fetch_result($res,$i,0);
				}
				
				return $calArray;
			} catch (Exception $e) {	exit($e->getMessage());	}
		}
	}
?>