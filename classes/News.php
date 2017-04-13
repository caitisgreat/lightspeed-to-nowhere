<?php	
	
	include_once "Markdown.php";
	include_once "Smartypants.php";
	
	class News {
		
		private $id;						// news database identifier
		private $database;			// database connection
		public $newsBody;			// body of the news entry
		public $authorFirstName;	// first name of the author
		public $authorLastName;	// last name of the author
		public $authorLogin;			// username of the author
		public $authorEmail;			// email of the author
		public $dateSubmitted;		// when the news entry was originally submitted
		public $title;						// title for the news post							

		// CLASS CONSTRUCTOR
		
		function News($nid) {
			if(!isset($this->database)) {
				$this->database = new DBConn();
			}
			
			$this->id = $nid;
			$this->setupNews();
		}

		// CLASS PRIVATE FUNCTIONS
		
		private function setupNews() {
			$retval = $this->database->getNewsEntry($this->id);
		
			$this->newsBody = html_entity_decode($retval['newsbody'], ENT_COMPAT, 'UTF-8');
			$this->authorLogin = $retval['author'];
			$this->authorFirstName = $retval['firstname'];
			$this->authorLastName = $retval['lastname'];
			$this->dateSubmitted = $retval['datesubmitted'];
			$this->authorEmail = $retval['email'];
			$this->title = $retval['title'];
			
		}
	}
?>