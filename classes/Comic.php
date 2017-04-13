<?php	
	
	class Comic {
		
		private $database;					// database connection
		public $filePath;						// file path to the comic image
		public $category;					// category the comic belongs to
		public $title;							// title of the comic
		public $id;								// comic database identifier
		public $dateCreated;				// date of creation
		
		public function Comic($cid) {
			if(!isset($this->database)) {
				$this->database = new DBConn();
			}
			
			$this->id = $cid;
			$this->setupComic();
		}
		
		private function setupComic() {
			$retval = $this->database->getComic($this->id);
			
			$this->filePath = $retval['filepath'];
			$this->category = $retval['categoryname'];
			$this->title = $retval['title'];
			$this->dateCreated = $retval['datecreated'];
		}		
	}
?>