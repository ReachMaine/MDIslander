<?php
/** 22Aug2014 zig - a means to output a calendar pull.
	16Oct2014 zig 
		- use excerpt if there is one.
		- dont export 'Unnamed Venue'
	23Oct2014 zig - tweaks 
		- dont display Venue contact info (phone or website - only address, contact info to be embeded in content)

 **/
class ea_calendar extends AQ_Block {
	
	//set and create block
	function __construct() {
		$block_options = array(
			'name' => 'Calendar out',
			'size' => 'span-12',
			'resizable' => 0
		);
		
		//create the block
		parent::__construct('ea_calendar', $block_options);
	}
	
	function form($instance) {
		
		$defaults = array(
			'title' => 'Calendar out',
			'startDate' => '',
			'endDate' => '',
			'desc' => '',
			'cats' => '',
			'towns' => '',
			'post_count' => '10',
			'column' => '2',
			'post_format' => 'all',
			'show_title' => true,
			'show_meta' => true,
			'show_excerpt' => true,
			'num_excerpt' =>15
			
		);
		$instance = wp_parse_args($instance, $defaults);
		extract($instance);
		
		?>
		<ul class="lightbox_form">
			 <li>
				<label for="<?php echo $this->get_field_id('title') ?>">
				<div class="title">Title </div>
				<div class="input">  
					<?php echo aq_field_input('title', $block_id, $title, $size = 'full') ?>
					</div>
				</label>
			</li>
			<li>
				<label for="<?php echo $this->get_field_id('startDate') ?>">
				<div class="title">Start Date (YYYYMMDD) </div>
				<div class="input">  
					<?php echo aq_field_input('startDate', $block_id, $startDate, $size = 'full') ?>
					</div>
				</label>
			</li>
			<li>
				<label for="<?php echo $this->get_field_id('endDate') ?>">
				<div class="title">End Date (YYYYMMDD)</div>
				<div class="input">  
					<?php echo aq_field_input('endDate', $block_id, $endDate, $size = 'full') ?>
					</div>
				</label>
			</li>
			<li>
				<label for="<?php echo $this->get_field_id('desc') ?>">
				<div class="title">Description </div>
				<div class="input">  
					<?php echo aq_field_input('desc', $block_id, $desc, $size = 'full') ?>
					</div>
				</label>
			</li>
			<li>
				<label for="<?php echo $this->get_field_id('cats') ?>">
					<div class="title">Cat IDs (separated by commas) </div>
					<div class="input">
						<?php echo aq_field_input('cats', $block_id, $cats, $size = 'full') ?>
					</div>
				</label>
			</li>
			<li>
				<label for="<?php echo $this->get_field_id('towns') ?>">
					<div class="title">Towns (separated by commas) </div>
					<div class="input">
						<?php echo aq_field_input('towns', $block_id, $towns, $size = 'full') ?>
					</div>
				</label>
			</li>
			<?php /* <li>
				<label for="<?php echo $this->get_field_id('post_count') ?>">
					<div class="title">Post count</div>
					<div class="input"><?php echo aq_field_input('post_count', $block_id, $post_count, $size = 'small') ?></div>
				</label>
				
			</li> 
			
			<li><li>
				<label for="<?php echo $this->get_field_id('column') ?>">
					<div class="title">Columns</div>
					<div class="input">
						<?php echo aq_field_select('column', $block_id, array('2'=>'2 Columns', '3'=>'3 Columns', '4'=>'4 Columns'), $column) ?>
					</div>
				</label>
			</li>
			
			<li>
				<label for="<?php echo $this->get_field_id('post_format') ?>">
					<div class="title">Post Format</div>
					<div class="input">
						<?php echo aq_field_select('post_format', $block_id, array('all'=>'All', 'video'=>'Video', 'audio'=>'Audio', 'gallery'=>'Gallery'), $post_format) ?>
					</div>
				</label>
			</li> */ ?>
			
			<li>
				<div class="title">Show/Hide</div>
				<div class="input">
					<label for="<?php echo $this->get_field_id('show_title') ?>"><?php echo aq_field_checkbox('show_title', $block_id, $show_title) ?> Show Title</label> &nbsp; &nbsp; <label for="<?php echo $this->get_field_id('show_meta') ?>"><?php echo aq_field_checkbox('show_meta', $block_id, $show_meta) ?> Show Town as Group</label> &nbsp; &nbsp; <label for="<?php echo $this->get_field_id('show_excerpt') ?>"><?php echo aq_field_checkbox('show_excerpt', $block_id, $show_excerpt) ?> Show Excerpt</label>
				</div>
			</li>
			
			<?php /* <li>
				<label for="<?php echo $this->get_field_id('num_excerpt') ?>">
					<div class="title">Length of Excerpt</div>
					<div class="input">  
						<?php echo aq_field_input('num_excerpt', $block_id, $num_excerpt, $size = 'full') ?>
					</div>
				</label>
			</li> */ ?>
			
        </ul>
		
<?php
	} 
	/* part that show the block on a page, ie the output of the block */
	function block($instance) {
		extract($instance);
		?>
		<?php  if($title!='') echo '<h5 class="prl-block-title">'.trim($title).'</h5>';  

			$t_startDate = new DateTime($startDate);
			$str_startDate = date_format( $t_startDate, 'l, F j, Y');
			$t_endDate = new DateTime($endDate);
			$str_endDate = date_format( $t_endDate, 'l, F j, Y');
			echo "<h2>Start Date: ".$str_startDate."<br> End Date: ".$str_endDate;
			if ($towns) {
				echo "<br>Filtered by towns: ".$towns;
				$str_towns = str_replace(", ", ",", $towns);
				$str_towns = "'".implode("','", explode(",", $str_towns))."'";
				// echo "<br> str_towns: ".$str_towns;
			}

			if ($cats) {
				echo "<br>Filtered by categories: ".$cats;
			}
			echo "</h2>";
			 /* let's build the query */ 
			global $wpdb;
			$calquery = "SELECT p.ID,p.post_title,";
    
		    $calquery .= " (SELECT  CAST( meta_value AS DATE) FROM `ea_12_postmeta` pm WHERE p.ID=pm.post_id and pm.meta_key='_EventStartDate') as 'startDate',  ";
		    $calquery .= " (SELECT  CAST( meta_value AS TIME) FROM `ea_12_postmeta` pm WHERE p.ID=pm.post_id and pm.meta_key='_EventStartDate') as 'startTime',  ";
		    $calquery .= " (SELECT CAST( meta_value AS DATE) FROM `ea_12_postmeta` pm WHERE p.ID=pm.post_id and pm.meta_key='_EventEndDate') as 'endDate', ";
		    $calquery .= " (SELECT CAST( meta_value AS TIME) FROM `ea_12_postmeta` pm WHERE p.ID=pm.post_id and pm.meta_key='_EventEndDate') as 'endTime', ";
			$calquery .= " (SELECT  meta_value FROM `ea_12_postmeta` pm WHERE p.ID=pm.post_id and pm.meta_key='_EventCost') as 'cost', ";
			$calquery .= " vp.post_title as 'Venue', ";
    		$calquery .= " (SELECT  meta_value FROM `ea_12_postmeta` pm WHERE p.ID=pm.post_id and pm.meta_key='_EventVenueID') as 'VenueID', ";	
    		$calquery .= " (SELECT  meta_value FROM `ea_12_postmeta` pm WHERE vp.ID=pm.post_id and pm.meta_key='_VenueAddress') as 'Address', ";
    		$calquery .= " (SELECT  meta_value FROM `ea_12_postmeta` pm WHERE vp.ID=pm.post_id and pm.meta_key='_VenueCity') as 'City',  ";	
    		$calquery .= " (SELECT  meta_value FROM `ea_12_postmeta` pm WHERE vp.ID=pm.post_id and pm.meta_key='_VenuePhone') as 'Phone', ";	
    		$calquery .= " (SELECT  meta_value FROM `ea_12_postmeta` pm WHERE vp.ID=pm.post_id and pm.meta_key='_VenueURL') as 'website', ";	
    		/* $calquery .= "  ";	 */
    		$calquery .= " p.post_content, p.post_excerpt "; 
    		/* now for the tables & joins */
			$calquery .= " FROM `ea_12_posts` p";
			$calquery .= " JOIN `ea_12_postmeta` pm ON p.ID=pm.post_id and pm.meta_key = '_EventVenueID' ";
			$calquery .= " LEFT JOIN `ea_12_posts` vp ON  pm.meta_value = vp.ID  and pm.meta_key= '_EventVenueID' ";
			if ($cats) {
				$calquery .= "  LEFT JOIN ea_12_term_relationships tr ON (p.ID = tr.object_id) ";
				$calquery .= " LEFT JOIN ea_12_term_taxonomy tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id) ";
			}
			/* the where clause */
			$calquery .= " WHERE p.post_status = 'publish'  AND p.post_type = 'tribe_events'"; 
			if ($startDate AND $endDate) {
			 	 $calquery .=" AND ((select cast(meta_value AS Date) from `ea_12_postmeta` where post_id=p.ID and meta_key= '_EventStartDate') BETWEEN cast('".$startDate."' as Date) AND cast('".$endDate."'  as Date) ";
    			$calquery .= "  OR  (select cast(meta_value AS Date) from `ea_12_postmeta` where post_id=p.ID and meta_key= '_EventEndDate')  BETWEEN cast('".$startDate."' as Date) AND cast('".$endDate."'  as Date) ";
    			$calquery .= "  OR (select cast(meta_value AS Date) from `ea_12_postmeta` where post_id=p.ID and meta_key= '_EventStartDate') < cast('".$startDate."'as Date) ";
        		$calquery .= "  AND (select cast(meta_value AS Date) from `ea_12_postmeta` where post_id=p.ID and meta_key= '_EventEndDate') > cast('".$startDate."' as Date) " ;
        		$calquery .= ")";
			}
			if ($towns) {
				$calquery .= "AND (SELECT  meta_value FROM `ea_12_postmeta` pm WHERE vp.ID=pm.post_id and pm.meta_key='_VenueCity') in (".$str_towns.")";
			}
			if ($cats ) {
				$calquery .= " AND tt.taxonomy = 'tribe_events_cat' ";
   				$calquery .= "AND tt.term_id in (".$cats.')';
			}
			$calquery .= " ORDER BY startDate ASC, City ASC";
			//echo $calquery."<br>-----<br>";
			$calresult = $wpdb->get_results($calquery);
			
			//echo "Array count:".count($calresult);
			/* var_dump($calresult);  */
		?>
		<script>
			function myselecttext(containerid) {
				if (document.selection) {
			        var range = document.body.createTextRange();
			        range.moveToElementText(document.getElementById(containerid));
			        range.select();
			    } else if (window.getSelection()) {
			        var range = document.createRange();
			        range.selectNode(document.getElementById(containerid));
			        window.getSelection().removeAllRanges();
			        window.getSelection().addRange(range);
			    }
			}
		</script>
		<button class="right" type="button" value="" onclick="myselecttext('eai-cal-out')" >Select all</button>
		<div id="eai-cal-out" class="zcal_out"> <!--class="prl-grid prl-grid-divider"> -->
			<?php
			
			$endRow = 0;
			$curdate = $startDate;
			$curcity = ""; 
			$eventcount = count($calresult);
			foreach ($calresult as $event) {

				if ($curdate != $event->startDate) {
					$curdate = $event->startDate;
					$curcity = ""; // start over
					$t_eventStart = new DateTime($event->startDate);
					$str_eventStart = date_format( $t_eventStart, 'l, M. j');
					echo '<h2 class="zcal_date_group">'.$str_eventStart."</h2>";
				}
				if (($curcity != $event->City) && $show_meta) {
					$curcity = $event->City;
					echo '<h4 class="zcal_city_group">'.$event->City.'</h4>';
				}
				?>
				
				<div id="event-<?php echo $event->ID ?>" class="eai-cal-event" >
					<?php /* output the event */
					echo '<b>'.$event->post_title.'</b>';
					$t_eventStartTime = new DateTime($event->startTime);
					$str_eventStartTime = date_format($t_eventStartTime, 'g:i A');
					echo " ".beautify_timestr($str_eventStartTime);
					if ($event->startTime <> $event->endTime) {
						$t_eventendTime = new DateTime($event->endTime);
						$str_eventendTime = date_format($t_eventendTime, 'g:i A');
						echo "-".beautify_timestr($str_eventendTime); 
					}
					if (($event->Venue) && ($event->Venue <> 'Unnamed Venue')) { echo ", ".$event->Venue; }
					if ($event->Address) { echo ", ".$event->Address;}
					if (!$show_meta && $event->City) { echo ','.$event->City; }
					if ($event->post_excerpt) { 
						echo ',<span class="zcal_content">'.$event->post_excerpt.'</span>'; 
					} else {
						if ($event->post_content) { 
							echo ',<span class="zcal_content">'.$event->post_content.'</span>'; 
						} else {
							echo '<!-- no content -->';
						}
					}
					
					/* zig 23Oct2014 if ($event->Phone) { echo ', <span class="nowrap">'.$event->Phone.'</span>';}
					if ($event->website) { echo ", ".$event->website;} */
					echo "<br>";
					?>
				</div> 
				<?php 
			} /* end foreach */
		echo "</div>";
			
	}
	
}
function beautify_timestr($arg_timestr) { 
	/* prettify a time string:
		 remove :00 
		 change AM to a.m. 
	*/
		$newtimestr = $arg_timestr;
		$newtimestr = str_replace("AM", "a.m.", $newtimestr);
		$newtimestr = str_replace("PM", "p.m.", $newtimestr);
		$newtimestr = str_replace(":00", "", $newtimestr);
		return $newtimestr;
	}