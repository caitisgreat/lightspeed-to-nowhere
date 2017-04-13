<?php

	class visitSession {
		
		private $ipAddress;						//	visitor's ip address
		private $timeLastActive;				// the time at which the visitor was last active.
		private $lastViewed;						// the most recent comic viewed by the visitor.
		private $database;						// comic database connection
		
		// CLASS CONSTRUCTOR
		
			// setup a connection to the database
			// start the users session if it hasnt been already
			// update their last active time
			// remove inactive visitors
			// what's the last thing they looked at?
		
		public function visitSession() {
			if(!isset($this->database)) {
				$this->database = new DBConn();
			}
			
			$this->timeLastActive = time();
			$this->startSession();
		}
		
		// CLASS PRIVATE FUNCTIONS
		
		private function startSession() {
			session_start();  // Start a new session, if needed.
			
			/* Update or add visitor's information if needed */			
			if($this->isNewVisitor()) {	$this->addNewVisitor();	} 
			else {	$this->database->visitorActivity($this->ipAddress, $this->timeLastActive, $this->sessionId);	}		
			
			/* Remove inactive visitors */
			$this->database->removeDeadVisitors();
			
			if(isset($_GET['category']) && !empty($_GET['category'])){
				$_SESSION['category'] = pg_escape_string(filter_input(INPUT_GET, 'category', FILTER_SANITIZE_STRING));
			} else { unset($_SESSION['category']); }
					
			/* Set last viewed comic */
			$this->lastViewed = $_SESSION['cid'] = $this->database->setLastViewedComic($this->ipAddress, $_GET['id']);
		}
		
		private function addNewVisitor() {
			/* Register new Session */
			$this->ipAddress = $_SESSION['visitor'] = $_SERVER['REMOTE_ADDR'];
			$this->sessionId = $_SESSION['visitor_session'] = md5(mt_rand());
			
			/* Add to active visitors database */
			$this->database->visitorActivity($this->ipAddress, $this->timeLastActive, $this-sessionId);
		}
				
		private function isNewVisitor() {
		
			if(isset($_SESSION['visitor']) && ($_SESSION['visitor_session']))
			{
				if($this->database->isActiveVisitor($_SESSION['visitor'], $_SESSION['visitor_session']))
				{
					$this->ipAddress = $_SESSION['visitor'];
					$this->sessionId = $_SESSION['visitor_session'];
					return false;
				} 
				else
				{
					unset($_SESSION['visitor']);
					unset($_SESSION['visitor_session']);
					return true;					
				}
			} else {	return true;	}	
		}	
	}
?>