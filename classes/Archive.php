<?php

	class Archive {
	
		private $database;
	
		public function Archive() {
			if(!isset($this->database)) {
				$this->database = new DBConn();
			}
			
			if(empty($_GET["y"]))
				{	$_GET["y"] = date('Y');	}
			
			if(empty($_GET["m"]))
				{	$_GET["m"] = date('m');	}	
		}
		
		public function calGenerate ($month, $year) {
			// User specified month & year - translated to unix time.
			// Find first day of specified month & year.
			$first_of_month = gmmktime(0,0,0, htmlentities($month),1,htmlentities($year));
			
			// Create a list of day names.
			$day_names = array();
			for ($n = 0, $t = 259200; $n < 7; $n++, $t += 86400)  
			{
				// Thurs, Jan 1, 1970 + 3 days = Sunday, Jan 4, 1970
				// Generates accepted order of day names.
				$day_names[$n] = ucfirst(gmstrftime('%A',$t));
			}
			
			// Everything to know about the selected month & year.
			list(	$month, // numerical month
					$year, // numerical year
					$month_name, // name of month
					$weekday // day by name 
				) = explode(',',gmstrftime('%m,%Y,%B,%w',$first_of_month));
			
			// Generate month & year header for calendar.
			$title = ucfirst($month_name)."&nbsp;".$year;
			$calendar = "<table><caption>".$title."</caption>\n<tr>";
			
			// Write up day names row.
			foreach($day_names as $d) {	$calendar .= '<th abbr="'.$d.'">'.substr($d,0,3).'</th>'; }
			$calendar .= "</tr>\n<tr>";
			
			// Fill initial empty days with whitespace.
			if($weekday > 0) { $calendar .= '<td colspan="'.$weekday.'">&nbsp;</td>';  } 
			
			// Fill in all real days.
			$cal_array = $this->database->getArchive($month, $year);	
			for ($day = 1, $days_in_month = gmdate('t', $first_of_month); $day <= $days_in_month; $day++, $weekday++)
			{
				if ($weekday == 7)
				{
					$weekday = 0;
					$calendar .= "</tr>\n<tr>";
				}
				
				// Access database for days comics were posted, attach hyperlinks to those days.
				if(is_null($cal_array[$day]))
				{
					$calendar .= "<td>".$day."</td>";				
				}
				else
				{
					foreach ($cal_array[$day] as $val) {
						$calendar .= "<td><a class=\"cal\" href=\"http://www.lstncomic.com/index.php?id=".$val."\">".$day."</a></td>";
					}
				}
			}
		
			// Fill remaining empty days with whitespace.
			if($weekday != 7) { $calendar .= '<td colspan="'.(7-$weekday).'">&nbsp;</td>'; }	
			$calendar .= "</tr>\n</table>\n";
			
			echo $calendar;
		}	

	function calThumbnails($month, $year) {
		$first_of_month = gmmktime(0,0,0, htmlentities($month),1,htmlentities($year));
				
		echo "<h1 class=\"top\">".gmdate('F', $first_of_month)."&nbsp;".gmdate('Y', $first_of_month)."</h1>\n<hr>";
		
		$calArray = $this->database->getArchive($month, $year);
		
		for ($day = 1, $days_in_month = gmdate('t', $first_of_month); $day <= $days_in_month; $day++)
		{		
			// Access database for days comics were posted, link thumbnails for each day.		
			if(!is_null($calArray[$day]))
			{
				foreach($calArray[$day] as $val) {
					$title = $this->database->getTitle($val);
					echo "<div class=\"NewsHeader\"><img class=\"avatar\" src=\"images/cal/day".$day.".png\" alt=\"".$month."_".$day."_".$year."\"><a class=\"invert\" href=\"index.php?id=".$val."\"><h1>".$title."</h1></a></div>";
				}
			}
		}
	}

	function calNavLeft() {
	
	$beginning_of_time = gmmktime(0,0,0, 8, 1, 2010);
	$listed_date = gmmktime(0,0,0, (intval($_GET['m'])-2),1,intval($_GET['y']));
		
		if($listed_date <= $beginning_of_time) {
			echo "<a title=\"Reverse\" class=\"navLeft\" href=\"#\"></a>";
		}
		else {
			echo "<a title=\"Reverse\" class=\"navLeft\" href=\"archive.php?m=".date("m",gmmktime(0,0,0, (intval($_GET['m'])),1,intval($_GET['y'])))."&y=".date("Y",gmmktime(0,0,0,(intval($_GET['m'])),1,intval($_GET['y'])))."\"></a>";
		}
	}
	
	function calNavRight() {
	
	$current_date = gmmktime(0,0,0, date("m"), 1, date("Y"));
	$listed_date = gmmktime(0,0,0, (intval($_GET['m'])),1,intval($_GET['y']));
		
		if($listed_date >= $current_date) {
			echo "<a title=\"Forward\" class=\"navRight\" href=\"#\"></a>";
		}
		else {
			echo "<a title=\"Forward\" class=\"navRight\" href=\"archive.php?m=".date("m",gmmktime(0,0,0, (intval($_GET['m'])+2),1,intval($_GET['y'])))."&y=".date("Y",gmmktime(0,0,0,(intval($_GET['m'])+2),1,intval($_GET['y'])))."\"></a>";
		}
	}
	
	}