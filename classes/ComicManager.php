<?php
	
	class ComicManager {
		
		private $database;		// database connection
		public $comic;			// comic object
		public $news;				// array of news objects
		public $id;
		
		public function ComicManager($id) {
			if(!isset($this->database)) {
				$this->database = new DBConn();
			}
			
			if(!isset($this->comic)) {
				$this->comic = new Comic($id);
			}
			
			$this->setupNews($id);
			$this->id = $id;
		}
		
		// COMIC CONTROLS
		
		public function comicCreationDate() {
			$date = date("Y-m-j",strtotime($this->comic->dateCreated));
			echo $date;
		}
		
		public function navLeft() {
			$val = $this->database->getLeftComic($this->id);
			
			if($val != -1) {
				$html = "<a class=\"nav\" id=\"Back\" href=\"index.php?";
				
				if (isset($_SESSION['category']) && !empty($_SESSION['category'])) {
					$html .= "category=" . $_SESSION['category'];
					$html .= "&id=" . $val . "\">";
				} else { $html .= "id=" . $val . "\">"; }
				
				$html .= "</a></li>\n";
				
			} else {
				$html = "<a class=\"nav\" id=\"Back\" href=\"#\"></a></li>\n";
			}
			
			echo $html;
		}
		
		public function navRandom() {
			$val = $this->database->getRandomComic();
			
			$html = "<a class=\"nav\" id=\"Random\" href=\"index.php?id=$val\"></a></li>\n";
				
			echo $html;
		}	
		
		public function navRight() {
			$val = $this->database->getRightComic($this->id);
			
			if($val != -1) {
				$html = "<a class=\"nav\" id=\"Next\" href=\"index.php?";
				
				if (isset($_SESSION['category']) && !empty($_SESSION['category'])) {
					$html .= "category=" . $_SESSION['category'];
					$html .= "&id=" . $val . "\">";
				} else { $html .= "id=" . $val . "\">";}
				
				$html .= "</a></li>\n";
				
			} else {
				$html = "<a class=\"nav\" id=\"Next\" href=\"#\"></a></li>\n";
			}
			
			echo $html;
		}
		
		public function navFirst() {
			$val = $this->database->getFirstComic($this->id);
			
			if($val != -1) {
				$html = "<a class=\"nav\" id=\"First\" href=\"index.php?";
				
				if (isset($_SESSION['category']) && !empty($_SESSION['category'])) {
					$html .= "category=" . $_SESSION['category'];
					$html .= "&id=" . $val . "\">";
				} else { $html .= "id=" . $val . "\">"; }
				
				$html .= "</a></li>\n";
				
			} else {
				$html = "<a class=\"nav\" id=\"First\" href=\"#\"></a></li>\n";
			}
			
			echo $html;
			
		}
		
		public function navNew() {
			$val = $this->database->getNewComic($this->id);
			
			if($val != -1) {
				$html = "<a class=\"nav\" id=\"New\" href=\"index.php?";
				
				if (isset($_SESSION['category']) && !empty($_SESSION['category'])) {
					$html .= "category=" . $_SESSION['category'];
					$html .= "&id=" . $val . "\">";
				} else { $html .= "id=" . $val . "\">"; }
				
				$html .= "</a></li>\n";
				
			} else {
				$html = "<a class=\"nav\" id=\"New\" href=\"#\"></a></li>\n";
			}
			
			echo $html;
		}
		
		// NEWS CONTROLS
		
		private function setupNews($cid) {
			$arrNews = $this->database->getNews($cid); // array of news id's
			
			if(!isset($this->news)){
				$this->news = array();
			}
			
			foreach($arrNews as $key => $val) 
			{
				$this->news[] = new News($val);
			}
		}
		
		public function printNews() {
			if(isset($this->news)){
				foreach($this->news as $obj) {
					$email = $obj->authorEmail;
					$firstname = $obj->authorFirstName;
					$lastname = $obj->authorLastName;
					$nickname = $obj->authorLogin;
					$fullname = $firstname . " '" . $nickname . "' " . $lastname;
										
					$title = $obj->title;
					$date = date("F j, Y h:iA T",strtotime($obj->dateSubmitted));
					$body = $obj->newsBody;
					
					$avatar = "images/avatars/" . strtolower($firstname) . ".png";				
					
					$html = "<div class=\"NewsHeader\">
					<img src=\"$avatar\" alt=\"$firstname\" class=\"avatar\">
					<h1>$title</h1>
					<p><a class=\"invert\" href=\"mailto:$email\">$fullname</a> - $date</p></div>";
					$html .= "<div class=\"NewsBody\">$body</div>";
				
					echo $html;
				}
			}
		}
		
		// CATEGORY CONTROLS
		
		public function setupCategories() {
			$categories = $this->database->getCategories();
			
			foreach($categories as $val)
			{
				echo "<li><a href=\"index.php?category=$val\">$val</a></li>";
			}
		}
			
		public function categoryBanner() {
			if (!empty($_GET['category']) && isset($_GET['category'])) {
				echo "<div class=\"banner\"><p>Showing comics from the " .$_SESSION['category'] . " category.  Click <a href=\"index.php\">here</a> to return to normal view.</p></div>";
			}
		}		
	}