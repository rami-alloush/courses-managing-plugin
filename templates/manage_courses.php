<div class="wrap">
	<h2>CMP - Manage Courses</h2>
	<hr/>
<?php

	//Check URL status ID
	$id = (isset($_GET['id'])) ? (int) $_GET['id'] : NULL; 
	//URL has ID assigned
	if (!empty($id)) { //Start populate options
		edsdb_edit_course_section($id);
	} else {
		edsdb_choose_course_edit_html(); //Show Choosing Tabs
		edsdb_choose_course_details(); // Show Table of courses, After Posting FILTER
	}


//Choose Course to EDIT HTML TABS
function edsdb_choose_course_edit_html () {
 ?>
	<form method="POST" action="">
		<div id="tabs-choose" class="wrap">
			<ul>
				<li><a href="#tabs-1">Choose Course Code</a></li>
				<li><a href="#tabs-2">Choose Instructor</a></li>
				<li><a href="#tabs-3">Date Range</a></li>
			</ul>
		<div id="tabs-1">
			<div class="wrap">
				<table class="form-table"><tr valign="top">
						<td scope="row"><label for="select_course_code">Course Code:</label></td>
						<td><select id="select_course_code" name="select_course_code" STYLE="width: 700px" >
							<option value="" >Select a course</option>
							<?php if(function_exists('edsdb_return_run_courses')) {
									print edsdb_return_run_courses();}?>
						</select>
						<input type="submit" name="submit-code" id="submit" value="Filter" class="button-primary" />
				</td></tr></table>
		</div></div>
		
		<div id="tabs-2">
			<div class="wrap">
				<table class="form-table"><tr valign="top">
						<th scope="row"><label for="select_course_code">Course Instructor: </label></th>
						<td><select id="select_course_inst" name="select_course_inst" STYLE="width: 500px" >
							<option value="" >Select an Instructor </option>
							<?php if(function_exists('edsdb_return_run_courses_instr')) {
									print edsdb_return_run_courses_instr();} ?>
						</select>
						<input type="submit" name="submit-inst" id="submit" value="Filter" class="button-primary" />
						</td></tr></table>
		</div></div>
		
		<div id="tabs-3">
			<div class="wrap">
				<table class="form-table"><tr valign="top">
						<th scope="row"><label for="select_course_code">Range of courses start dates: </label></th>
						<td><input type="text" name="submit_date_start_from" id="submit_date_start_from" value="From" class="mydatepicker"/>
							<input type="text" name="submit_date_start_to" id="submit_date_start_to" value="To" class="mydatepicker"/>
							<input type="submit" name="submit-date" id="submit" value="Filter" class="button-primary" />
						</td></tr></table>
		</div></div>
		</div> <!-- End of TABS !-->
	</form>
	<?php
}

//Details of course choice
function edsdb_choose_course_details() {
	global $wpdb;
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$required_code = $_POST['select_course_code'];
	$required_inst = $_POST['select_course_inst'];
	$required_date_from = $_POST['submit_date_start_from'];
	$required_date_to = $_POST['submit_date_start_to'];
	$courses_table = $wpdb->prefix . "courses";
	?>
		<div class="wrap">
		<table class="widefat">
		<hr><p><h3>Choose a Course to edit:</h3></p>
			<thead><tr><th>Course Code</th><th>Start Date</th><th>End Date</th><th>Course Instructor</th></tr></thead> <!-- Table Header !-->
			<tbody>
			<?php
			if ($_POST['submit-code']=='Filter') {
					$required_courses = $wpdb->get_results('SELECT * FROM ' . $courses_table.' WHERE course_code = "'. $required_code.'"', ARRAY_A);
				} else if ($_POST['submit-inst']=='Filter') {
					$required_courses = $wpdb->get_results('SELECT * FROM ' . $courses_table.' WHERE inst_code = "'. $required_inst.'"', ARRAY_A);
				} else if ($_POST['submit-date']=='Filter') {
					$required_courses = $wpdb->get_results('SELECT * FROM ' . $courses_table.' WHERE course_start > "'. $required_date_from.'" AND course_start < "'.$required_date_to.'"', ARRAY_A);
				}
				
				foreach ($required_courses as $course ) {
					// echo '<tr onclick="document.location = \'admin.php?page=cmp_manage_courses&id='.$course['course_id'].'\'" >
				
					echo '<tr class="clickableRow ui-menu-item" href="admin.php?page=cmp_manage_courses&id='.$course['course_id'].'" >
							<td>'.$course['course_code'].' | '.$course['course_name'].'</td>
							<td>'.$course['course_start'].'</td><td>'.$course['course_end'].'</td><td>'.$course['inst_code'].'</td>
						</tr>';
				}
			}//Close IF POST
			?>
			</tbody>
		</table>
		</div>
<?php
}

// Course Editing Section Function
function edsdb_edit_course_section($id) {
	//Retrive Current Course Details
	global $wpdb;
	$courses_table = $wpdb->prefix . "courses";
	
	//join the post values into comma separated (values from arrays)
	if (!empty($_POST['part_code'])) {$part_code = mysql_real_escape_string(implode(',', $_POST['part_code']));}
	if (!empty($_POST['part_paid'])) {$part_paid = mysql_real_escape_string(implode(',', $_POST['part_paid']));}
	if (!empty($_POST['eval'])) {$eval = mysql_real_escape_string(implode(',', $_POST['eval']));}
	if (!empty($_POST['task_cost'])) {$task_cost = mysql_real_escape_string(implode(',', $_POST['task_cost']));}
	if (!empty($_POST['task_done'])) {$task_done = mysql_real_escape_string(implode(',', $_POST['task_done']));}
			
	// Delete The Course
	if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['submit-delete']=='DELETE Course') {
		// Delete from gCal
		// connect to service .. gCal delete
			$gcal = edsdb_gcal(); //Connect and Authenticate		  
			$current_course = $wpdb->get_row('SELECT * FROM ' . $courses_table.' WHERE course_id = "'. $id.'"', ARRAY_A);
			
			try {     
				  $event = $gcal->getCalendarEventEntry($current_course['gcal_link']);
				  $event->delete();
			  } catch (Zend_Gdata_App_Exception $e) {
				  echo "Error: " . $e->getResponse();
			  }        
			  echo '<div id="message" class="updated below-h2">GCal Event successfully deleted!</div>';
		
		//Delete from Courses DB
		$wpdb->query( "DELETE FROM ".$courses_table." WHERE course_id = ".$id);
	}
	
	//Update The Course
	if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['submit-update']=='Update Course') {
		if (strtotime($_POST['course_end']) >= strtotime($_POST['course_start'])) {
			
			//if(isset($current_course['gcal_link'])) {
			if(function_exists('edsdb_gcal')) {
				// Update gCal event details
					// connect to service
					$gcal = edsdb_gcal(); //Connect and Authenticate
					$current_course = $wpdb->get_row('SELECT * FROM ' . $courses_table.' WHERE course_id = "'. $id.'"', ARRAY_A);
					$event->title = $gcal->newTitle(edsdb_get_post_title($_POST['course_code']));
					
					// construct event object
					// save to server      
					try {
					$event = $gcal->getCalendarEventEntry($current_course['gcal_link']);      
					
					$when = $gcal->newWhen();				
					$startDate = $_POST['course_start'];
					$startTime = "09:00";
					$endDate = $_POST['course_end'];
					$endTime = "17:00";
					$tzOffset = "+02";
					$when->startTime = $startDate."T".$startTime.":00.000".$tzOffset.":00";
					$when->endTime = $endDate."T".$endTime.":00.000".$tzOffset.":00";
					$event->when = array($when);    
					
					// Update the event
					$event->save();
					echo '<div id="message" class="updated below-h2">Event Successfully Modified!</div>'; 
					$gcal_link = $current_course['gcal_link']; //To prevent erasing the gcal value while updating Courses DB
					
					} catch (Zend_Gdata_App_Exception $e) {
							echo '<div id="message" class="error below-h2">No Valid GCal Event!: New event will be created</div>' . $e->getResponse();
					//}
					
				//} else if (empty($current_course['gcal_link'])) { //$current_course['gcal_link'] has no value
						
						//Add Course to Google Calendar
						// connect to service
						$gcal = edsdb_gcal(); //Connect and Authenticate

						// construct event object
						// save to server      
						try {
						$event = $gcal->newEventEntry();        
						$event->title = $gcal->newTitle(edsdb_get_post_title($_POST['course_code']));        
						$when = $gcal->newWhen();
						
						$startDate = $_POST['course_start'];
						$startTime = "09:00";
						$endDate = $_POST['course_end'];
						$endTime = "17:00";
						$tzOffset = "+02";
						$when->startTime = $startDate."T".$startTime.":00.000".$tzOffset.":00";
						$when->endTime = $endDate."T".$endTime.":00.000".$tzOffset.":00";
					
						//$when->startTime = $_POST['course_start'];
						//$when->endTime = $_POST['course_end'];
						$event->when = array($when);    
						$event->where = array($gcal->newWhere("Egyptian Drilling Skills (EDS)"));
								$post_id=edsdb_get_post_id($_POST['course_code']);
								$url=get_bloginfo('home');
						$event->content = $gcal->newContent("<a href=".$url."?p=".$post_id.">Go to course page on the website</a>");
						// Create the event and save the recorded values in $results 
						$results = $gcal->insertEvent($event);   
						
						// retrive the created ID of the event to save it to Courses DB
						$gcal_link = $results->id->gettext(); //because written as "_text:protected" on the "Zend_Gdata_App_Extension_Id Object"
						echo '<div id="message" class="updated below-h2"> New Event successfully added! </div>'; 
						
						} catch (Zend_Gdata_App_Exception $e) {
						echo "Error: " . $e->getResponse();
						}
					}
				} // Close Calendar Work
				
				// Update Courses DB record
				$new_values = array(
					'course_start'=> $_POST['course_start'],
					'course_end'=> $_POST['course_end'],
					'course_days'=> $_POST['course_days'],
					'location'=> $_POST['location'],
					'inst_code'=> $_POST['inst_code'],
					'part_code'=> $part_code,
					'part_paid'=> $part_paid,
					'status'=> $_POST['course_status'],
					'eval'=> $eval,
					'task_cost' => $task_cost,
					'task_done' => $task_done,
					'gcal_link' => $gcal_link
				);
				
				$where = array( 'course_id' => $id );
				$insert_state = $wpdb->update( $courses_table, $new_values, $where );
				
				if ($insert_state) {
					echo '<div id="message" class="updated"><p>Successfully Updated ^_^</p></div>';
				} else {
					echo '<div id="message" class="error"><p>'.$wpdb->last_error.' - Nothing was updated!</p></div>';
				}
				
		} else {
			echo'<div class="error"><p>Nothing was updated<br/>
				Please verify the dates and ensure data integrity</p></div>';
				}
	}
	
	//Retrive Course Data
	$current_course = $wpdb->get_row('SELECT * FROM ' . $courses_table.' WHERE course_id = "'. $id.'"', ARRAY_A);
		if (!empty($current_course)) { //Course exits
			edsdb_course_edit_section_html($current_course); //Start show the course details and edit options
			//$_SESSION['c_course'] = $current_course;
		} else if (!empty($id)) {
		echo'<div class="error">
			<p>The course has been deleted!<br/>
			<a href="'. admin_url("admin.php?page=cmp_manage_courses").'"><<< Go Back To Course Selection</a>
			</p></div>';
		}
}

// HTML for Choose Course to edit TABS
function edsdb_course_edit_section_html ($current_course) {
	//print_r ($current_course['gcal_link']); //for debugging
	if(function_exists('edsdb_gcal')) {
		$gcal = edsdb_gcal();
		try {
			  $event = $gcal->getCalendarEventEntry($current_course['gcal_link']);
			  $gcal_link = $event->getAlternateLink()->getHref();
			  $target = "_blank";
		} catch (Zend_Gdata_App_Exception $e) {
			  echo '<div id="message" class="error below-h2">Error: No Event Associated with this course</div>' ; //. $e->getResponse();
			  echo  $e->getResponse();
			  $gcal_link = "#"; $target = "_self";
		}
	}
	
	echo '<h3>Edit Course No.'. $current_course['course_id']. ' - '. $current_course['course_name'] . ' - '. $current_course['course_code'] .'</h3>
			<hr/>
			<a target="<', $target .'" href="' .$gcal_link .'">Goto course event on Google Calendar (if applicable)</a>';
?>
	
		<form method="POST" action="">
			<div class="wrap" id="tabs-edit">
				<ul>
					<li><a href="#tabs-1">Course Basic Info</a></li>
					<li><a href="#tabs-2">Course Participants</a></li>
					<li><a href="#tabs-3">Course Evaluation</a></li>
					<li><a href="#tabs-4">Operating</a></li>
					<li><a href="#tabs-6">Performance Chart</a></li>
					<li><a href="#tabs-5">Delete Course</a></li>
				</ul>
				
			<div id="tabs-1">
				<div class="wrap">
					<table class="form-table"><tr valign="top">
						<th scope="row"><label for="course_code">Course Code:</label></th>
						<td><input id="course_code" name="course_code" value="<?php echo $current_course['course_code']?>" readonly="true" STYLE="width: 150px"></td></tr>

						<tr><th scope="row"><label for="inst_code">Course Instructor:</label></th>
						<td><select id="inst_code" name="inst_code">
							<option value="TBA" disabled="disabled" selected="selected">TBA | To Be Assigned</option>
							<?php if(function_exists('cmp_return_instructors')) {
									print cmp_return_instructors();} ?>
						</select></td></tr>
						
						<tr><td scope="row"><label for="location">Course Location:</label></td>
						<td>
							<select name="location" id="location">
							<?php $location = ''; 
							if(function_exists('get_countries_list')) {
							get_countries_list();} ?>
							</select>
						</td></tr>						
						
						<tr><th scope="row"><label for="course_start">Start Date:</label></th>
						<td><input type="text" name="course_start" id="course_start" value="<?php echo $current_course['course_start']?>" class="mydatepicker"/></td></tr>
						<tr><th scope="row"><label for="course_end">End Date:</label></th>
						<td><input type="text" name="course_end" id="course_end" value="<?php echo $current_course['course_end']?>" class="mydatepicker"/></td></tr>
						<tr><th scope="row"><label for="course_days">Number of Active Days:</label></th>
						<td><input type="text" name="course_days" id="course_days" value="<?php echo $current_course['course_days']?>" onkeypress='validate(event)'/></td></tr>

						<tr><th scope="row"><label for="course_status">Current Course Status:</label></th><td>
						<input type="radio" name="course_status" value="1" <?php checked( $current_course['status'], 1);?>>Planned<br/>
						<input type="radio" name="course_status" value="2" <?php checked( $current_course['status'], 2);?>>Running<br/>
						<input type="radio" name="course_status" value="3" <?php checked( $current_course['status'], 3);?>>Done<br/>
						<input type="radio" name="course_status" value="4" <?php checked( $current_course['status'], 4);?>>Postponed<br/>
						<input type="radio" name="course_status" value="5" <?php checked( $current_course['status'], 5);?>>Cancelled</td>
					</table>
				</div>
			
			</div>
			
			<div id="tabs-2">
				<?php cmp_course_attendees_html($current_course);?>
			</div>
			
			<div id="tabs-3">
				<div class="wrap">
				<table class="widefat">
				Rating system:<br/>
				1-Agree Strongly,     2-Agree,    3-Unsure,     4-Dlsagree,   5-Disagree Strongly,   6-Not applicable.<br/><hr>
					<thead><tr><th>Evaluation Item</th><th>Evaluation Scores</th></tr></thead>
					<tbody>
					<tr><th><span class="description">Enter values from 1-6 seperated by dots. (e.g. "4.5.3.2.1" represents 5 evaluations for one item)</span></th><th></th></tr>
					<?php
					$eval_items = array (
					'INST: Knowledgeable about the subject',
					'INST: Well prepared',
					'INST: Presents material in a way the helps',
					'INST: Encourages participation',
					'INST: Answers students questions',
					'INST: Enthusiastic about teaching',
					'INST: The pace of the course Is just right',
					'INST: I would recommend him to others',
					'ASSI: Course difficulty suitable',
					'ASSI: Readings help me learn the material',
					'ASSI: Assignments interest me',
					'ASSI: Have been about the right length',
					'CLRM: Classroom comfortable and inviting',
					'CLRM: Classroom presents few distractions',
					'CLRM: Desks provide adequate work space',
					'GENR: I like this course',
					'GENR: I recommend this course to others');
					
					$eval_values = explode(',',$current_course['eval']);
					foreach ( $eval_values as $key => $eval_value) {
					echo '<tr><td>'.$key.'-'.$eval_items[$key].'</td><td><input type="text" id="eval'.$key.'" name="eval[]" value="'.$eval_value.'" size="30" onfocus="clearMe(this)" onBlur="NoEmpty(this.id)" onkeypress="validate(event)"/></td></tr>';
						}
					?>
					<!--  !-->
					</tbody>
					</table>
			</div></div>
			
			<div id="tabs-4" class="wrap">
				<table class="widefat">
					<thead><tr><th>Item</th><th>Cost (EGP)</th><th>Done</th></tr></thead>
					<tbody>
					<?php
					$tasks_items = array('Material','Breackfast','Launch','Gifts','Instructor');
					$tasks_costs = explode(',',$current_course['task_cost']);
					$tasks_status = explode(',',$current_course['task_done']);
					foreach ($tasks_items as $key => $tasks_item) {
						echo '<tr><td>'.$tasks_item.'</td><td><input type="text" id="task_cost'.$key.'" name="task_cost[]" value="'.$tasks_costs[$key].'" size="30" onfocus="clearMe(this)" onBlur="NoEmpty(this.id)" onkeypress="validate(event)"/></td><td><input type="checkbox" id="task_done[]" name="task_done[]"'; if(in_array($tasks_item, $tasks_status)) {echo 'checked="CHECKED"';}; echo 'value="'.$tasks_item.'"></td></tr>';
					}
					?>
					</tbody>                                                                                                                                                   
				</table>
			<script type="text/javascript" src="https://www.google.com/jsapi"></script>
			<script type="text/javascript">
			  google.load("visualization", "1", {packages:["corechart"]});
			  google.setOnLoadCallback(drawChart);
			  function drawChart() {
				var data = google.visualization.arrayToDataTable([
				  ['Task', 'Hours per Day'],
				  <?php 
				  foreach ($tasks_costs as $key => $tasks_cost) {
					echo '["'.$tasks_items[$key].'- EGP '.$tasks_cost.'",'.$tasks_cost.'],';
				  }
				  ?>
				]);

				var options = {
				  title: 'Course Operating Costs',width:900, height:450, pieSliceText:'both'
				};

				var chart = new google.visualization.PieChart(document.getElementById('chart_cost'));
				chart.draw(data, options);
			  }
			</script>
			<div id="chart_cost"></div>
			</div>
			
			<div id="tabs-5">
				<h2 style="font-weight: bold;color: red">WARNING!</h2>
				<span style="font-weight: bold;font-size: 15pt;color: red">You can't undo this action, the course and all the data associated to it will be removed !!!
				<input type="submit" name="submit-delete" id="submit" value="DELETE Course" class="button" style="color: red;"/>			
			</div>
			
			<div id="tabs-6">
				<?php require_once(dirname(__FILE__) .'/eval-chart.inc.php'); ?>
			</div>
			
			
			</div> <!-- End of TABS-EDIT !-->
			
			<!-- Buttons !-->
			<div class="clear"></div>
			<hr>
			<input type="submit" name="submit-update" id="submit" value="Update Course" class="button-primary"/>
			<a type="submit" name="submit-print" id="submit-print"  target="_blank" href="<?php
				bloginfo('wpurl'); echo '/wp-content/plugins/eds-db/cbd-'.get_option('cbd_print').'.php?id='. $current_course['course_id'];
				?>" class="button-secondary" onclick="edsdb_tmpsave_charts()"/>Print Course Breackdown</a>
		</form>	
		<hr><a href="<?php echo admin_url('admin.php?page=cmp_manage_courses')?>"><-- Back To Course Selection</a>
<?php
}
?>
</div>

<?php
function edsdb_return_run_courses() {
	global $wpdb;
	$courses_table = $wpdb->prefix . "courses";
	$cmp_run_courses = $wpdb->get_results('SELECT DISTINCT course_code,course_name FROM ' . $courses_table.' ORDER BY course_code', ARRAY_A);
	
	//print_r($edsdb_courses_codes);
	
	// foreach ($edsdb_courses_codes as $edsdb_courses_code) {$courses_codes[] = $edsdb_courses_code['course_code'];} //Array of Run courses codes
	// $edsdb_courses = $wpdb->get_results('SELECT post_id, meta_value FROM ' . $wpdb->postmeta. ' WHERE meta_key = "_edsdb_ccmb_cc"', ARRAY_N);
	
	// foreach ($edsdb_courses as $edsdb_user) {
		// if ($edsdb_user[1]!="") //No user output if code is empty
		// {$edsdb_user_login= $wpdb->get_results('SELECT post_title FROM ' . $wpdb->posts . ' WHERE ID ='.$edsdb_user[0], ARRAY_N);
		// $edsdb_user_login=ucwords($edsdb_user_login[0][0]); //Strip userlogin value from inside the output array making first letter capital
		// array_push($edsdb_user,$edsdb_user_login); //Add the stripped user name to the array having ID and Code [0]=> ID, [1]=> Code, [2]=> Username
		// $eds_return_users[]=$edsdb_user; //Collect all user data
			// }
		// }
		
	//foreach ($eds_return_users as $key => $row) {$userlogin[$key] = $row[1];} //Create Array to sort with accordance
	//	array_multisort($userlogin, SORT_ASC, $eds_return_users); //Sort
	
	//Create HTML options
	if ($cmp_run_courses) {
		foreach ($cmp_run_courses as $cmp_course) {
			//if(in_array($eds_return_user[1],$courses_codes)) {
			$cmp_run_courses_list .= '<option value="' . $cmp_course['course_code'] . '">' . $cmp_course['course_code'] .' | '.  $cmp_course['course_name'] . '</option>';
			// }
		}
	}

	return $cmp_run_courses_list;
}

cmp_autocomplete_script();

?>