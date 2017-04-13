<?php

	class UploadManager {
	
		private $database;
		private $categories;
		private $cid;
		private $imagePath;
		private $image;
		
		// CLASS CONSTRUCTOR
		
		public function UploadManager() {
			if(!isset($this->database)) {
				$this->database = new DBConn();
			}
			
			$this->cid = ($this->database->getLatestComicId() +1);
		}
		
		// CLASS PRIVATE FUNCTIONS
		
		private function updateRSSFeed() {
			$xml = "../feed.xml";
			$doc = new DOMDocument('1.0');
			$doc->load($xml, LIBXML_DTDLOAD);
			
			$r = $doc->getElementsByTagName('feed')->item(0);
			
			$entry = $doc->createElement('entry');

			// title
			$title = $doc->createElement('title', $_POST["title"]);
			// id
			$tag = $doc->createElement('id', "tag:lstncomic.com,".date("Y-m-d").":index.php?id=".$this->cid);
			// updated
			$date = $doc->createElement('updated', date("Y-m-d\TH:m:s\Z"));
			// link
			$link = $doc->createElement('link');
			$href = $doc->createAttribute('href');
			$link->appendChild($href);
			
			$href_val = $doc->createTextNode("http://www.lstncomic.com/index.php?id=".$this->cid);
			$href->appendChild($href_val);
			
			//summary		
			$summary = $doc->createElement('summary', "#".$this->cid." - ".$_POST["title"]);
			
			// image
			$image = $doc->createElement('image');
			$image_url = $doc->createElement('url',"http://www.lstncomic.com/images/comics/".$this->cid.".png");
			$image_title = $doc->createElement('title', $_POST["title"]);
			$image_link = $doc->createElement('link', "http://www.lstncomic.com/");
			$image->appendChild($image_url);
			$image->appendChild($image_title);
			$image->appendChild($image_link);			
			
			$entry->appendChild($title);
			$entry->appendChild($tag);
			$entry->appendChild($date);
			$entry->appendChild($link);
			$entry->appendChild($image);
			$entry->appendChild($summary);
			
			$r->appendChild($entry);
			
			$doc->save($xml);	
		}
		
		private function updateSitemap() {
			$xml = "../sitemap.xml";
			$doc = new DOMDocument('1.0');
			$doc->load($xml, LIBXML_DTDLOAD);
			
			$r = $doc->getElementsByTagName('urlset')->item(0);
			
			$url = $doc->createElement('url');

			// loc
			$loc = $doc->createElement('loc', "http://www.lstncomic.com/index.php?id=".$this->cid);
			// changefreq
			$changefreq = $doc->createElement('changefreq', "never");
			
			$url->appendChild($loc);
			$url->appendChild($changefreq);
			
			$r->appendChild($url);
			
			$doc->save($xml);		
		}
		
		private function errUpload($filename, $error_code) {
			
			$allowedExtensions = array("jpg", "png", "gif");
			
			if (!in_array(end(explode(".", $filename)), $allowedExtensions))
			{
				$error_msg = "The uploaded file is not of the allowed file types (jpg, png, gif).";
			}
			
			switch ($error_code) { 
				case UPLOAD_ERR_INI_SIZE: 
					$error_msg = "The uploaded file exceeds the upload_max_filesize directive in php.ini"; 
					break;
				case UPLOAD_ERR_FORM_SIZE: 
					$error_msg = "The uploaded file exceeds 3 megabytes."; 
					break;
				case UPLOAD_ERR_PARTIAL: 
					$error_msg = "The uploaded file was only partially uploaded"; 
					break;
				case UPLOAD_ERR_NO_FILE:
					$error_msg = "No file was uploaded"; 
					break;
				case UPLOAD_ERR_NO_TMP_DIR:
					$error_msg = "Missing a temporary folder"; 
					break;
				case UPLOAD_ERR_CANT_WRITE: 
					$error_msg = "Failed to write file to disk"; 
					break;
				case UPLOAD_ERR_EXTENSION: 
					$error_msg = "File upload stopped by PHP extension"; 
					break;
				case UPLOAD_ERR_OK:
					break;
			}

			return $error_msg;
		}
		
		private function storeImage() {
			$upload = $_FILES['file'];
			$this->imagePath = "../images/comics/".$this->cid.".".end(explode(".", $upload["name"]));			
			$this->image = "images/comics/" . $this->cid.".".end(explode(".", $upload["name"]));
			
			try {
				if (is_null($this->errUpload($upload['name'], $upload['error']))) 
				{
					if(!file_exists($this->imagePath)) {
						echo "Attempting to upload comic image... ";
						move_uploaded_file($upload["tmp_name"], $this->imagePath);
							echo "Success! Image uploaded to server. <br>";
							echo "Upload: " . $upload["name"] . "<br>";
							echo "Type: " . $upload["type"] . "<br>";
							echo "Size: " . ($upload["size"] / 1024) . " Kb<br>";
							echo "Stored in: " . $this->image." <br>";
					}
				}
				else 
				{	
					$err = $this->errUpload($upload['name'], $upload['error']); throw new Exception($err);	
				}
			} catch (Exception $e) {	exit($e->getMessage());	}
		}
		
		private function generateThumbnail() {
			try {
				echo "Generating thumbnail for archive... ";
					$original = new Imagick($this->imagePath);
					$original->thumbnailImage(400, null);
					file_put_contents("../images/comics/thmb_".$this->cid.".png", $original);
				echo "Success!<br>";					
			}  catch (Exception $e) { throw new Exception ("imagickGenerateThumbnail(): ".$e->getMessage()); }
		}
		
		private function storeNewsEntry() {
			date_default_timezone_set('America/New_York');
			
			// gather & sanitize post data
			if (filter_input(INPUT_POST, 'ckNewsTitle', FILTER_UNSAFE_RAW) != 'checked') {
				$title = pg_escape_string(filter_input(INPUT_POST, 'newsTitle', FILTER_UNSAFE_RAW));
			} else { $title = pg_escape_string(filter_input(INPUT_POST, 'title', FILTER_UNSAFE_RAW)); }
			
			$newsbody = pg_escape_string(filter_input(INPUT_POST, 'news', FILTER_UNSAFE_RAW));
			$datesubmitted = date("m-j-Y H:i:s", time());
			
			if(filter_input(INPUT_POST,'poster', FILTER_UNSAFE_RAW) == 'Greg') {
				$author = 'minion21g';
			} else { $author = 'aldurath'; }
			
			if(isset($_POST['forPost']) & !empty($_POST['forPost'])) {
				$this->cid = pg_escape_string(filter_input(INPUT_POST,'forPost', FILTER_UNSAFE_RAW));
			}
			
			// place on DB
			$this->database->addNews($newsbody, $author, $datesubmitted, $this->cid, $title);
		}
		
		private function storeComicEntry() {
			date_default_timezone_set('America/New_York');
			
			// gather & sanitize post data
			$title = pg_escape_string(filter_input(INPUT_POST, 'title', FILTER_UNSAFE_RAW));
			$categoryname = pg_escape_string(filter_input(INPUT_POST, 'category', FILTER_UNSAFE_RAW));
			$datecreated = date("m-j-Y H:i:s", time());
			
			if(filter_input(INPUT_POST,'poster', FILTER_UNSAFE_RAW) == 'Greg') {
				$author = 'minion21g';
			} else { $author = 'aldurath'; }
			
			// place on DB
			$this->database->addComic($this->image, $datecreated, $categoryname, $title, $author);
		}
		
		// CLASS PUBLIC FUNCTIONS
		
		public function uploadComic() {
			$this->storeImage();
			$this->storeComicEntry();
			$this->storeNewsEntry();
			//$this->generateThumbnail();			
			$this->updateRSSFeed();
			$this->updateSitemap();
		}
		
		public function submitNews() {
			$this->storeNewsEntry();
		}
		
		public function listLatestComics() {
			$arr_comics = $this->database->getLatestTen();
			
			foreach($arr_comics as $key => $val) {
				echo "<option value=\"$key\">$val</option>\n";
			}
		}
		
		public function printCategories() {
			$categories = $this->database->getCategories();
			
			foreach($categories as $val) {
				echo "<option value=\"". $val . "\">$val</option>";
			}		
		}
	}