<?php
$state = 2;
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['submit-add']=='Add Course') {

	if (!empty($_POST['course_data']) && strtotime($_POST['course_end']) >= strtotime($_POST['course_start'])) {
			global $wpdb;
			$tablename = $wpdb->prefix . "courses";
			// Prepare data info variables
				//join the post values into comma separated (values from arrays)
				if (!empty($_POST['part_code'])) {$part_code = mysql_real_escape_string(implode(',', $_POST['part_code']));}
				if (!empty($_POST['part_paid'])) {$part_paid = mysql_real_escape_string(implode(',', $_POST['part_paid']));}
				if (!empty($_POST['part_comp'])) {$part_comp = mysql_real_escape_string(implode(',', $_POST['part_comp']));}
				if (!empty($_POST['part_cert'])) {$part_cert = mysql_real_escape_string(implode(',', $_POST['part_cert']));}
				if (!empty($_POST['eval'])) {$eval = mysql_real_escape_string(implode(',', $_POST['eval']));}
				if (!empty($_POST['task_cost'])) {$task_cost = mysql_real_escape_string(implode(',', $_POST['task_cost']));}
				if (!empty($_POST['task_done'])) {$task_done = mysql_real_escape_string(implode(',', $_POST['task_done']));}
				
				//Extract Course Code and Name
				$course_data = explode(':', $_POST['course_data']);
				$course_code = $course_data[0];
				$course_name = $course_data[1];
				//print_r($course_data);

				//Instructor Data
				$inst_code = $_POST['inst_code'];
				// $inst_name = $_POST['inst_name'];
				if(empty($inst_code)) {
					$inst_code = 'TBA'; //make sure no ID of another user is selected
				}
				
			//Add Course to Google Calendar
				// check for option to save to gCal
				if (get_option('save_to_gCal')) {
					// connect to service
					$gcal = edsdb_gcal(); //Connect and Authenticate

					// construct event object
					// save to server      
					try {
					$event = $gcal->newEventEntry();
					//Modify title detection ###
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
							$id=edsdb_get_post_id($_POST['course_code']); // ###
							$url=get_bloginfo('home');
					$event->content = $gcal->newContent("<a href=".$url."?p=".$id.">Go to course page on the website</a>");
					// Create the event and save the recorded values in $results 
					$results = $gcal->insertEvent($event);   
					
					// retrive the created ID of the event to save it to Courses DB
					$gcal_link = $results->id->gettext(); //because written as "_text:protected" on the "Zend_Gdata_App_Extension_Id Object"
					
					} catch (Zend_Gdata_App_Exception $e) {
					echo "Error: " . $e->getResponse();
					}
					echo '<div id="message" class="updated below-h2">GCal Event successfully added!</div>'; 
				
				}
							
			//Add Course to Courses DB
				$newdata = array(
					'course_cat'=> $_POST['course_cat'],
					'course_code'=> $course_code,
					'course_name' => $course_name,
					'inst_code'=> $inst_code,
					'inst_name'=> $inst_name,
					'location' => $_POST['country'],
					'course_start'=> $_POST['course_start'],
					'course_end'=> $_POST['course_end'],
					'course_days'=> $_POST['course_days'],
					'status'=> $_POST['course_status'],
					'part_code'=> $part_code,
					'part_paid'=> $part_paid,
					'part_comp'=> $part_comp,
					'part_cert'=> $part_cert,
					'eval'=> $eval,
					'task_cost' => $task_cost,
					'task_done' => $task_done,
					'gcal_link' => $gcal_link
				);
				$insert_state = $wpdb-> insert($tablename, $newdata); //Insert record and return the state of the query

				if ($insert_state === FALSE) {
					$state = 0; //Failure
				} else {
					$state = 1; //Success
				}
				
		} else { //The date has error
			$state = 0; //Failure
		}
	}

// Debugging
//global $wpdb;
//print_r($wpdb);
//print_r($wpdb->last_error);

edsdb_course_add_html($state);

// HTML for Choose Course to edit TABS
function edsdb_course_add_html($state=2) {
	global $wpdb;
	if ($state == 1 ) { //Success
			echo '<div id="message" style="color: #4F8A10; background-color: #DFF2BF;border: 1px solid;margin: 5px 15px 5px 5px;padding:5px 10px 5px 10px;border-radius: 5px;">
			Course was successfully added with the following data:<br/>
			- Course Data > '.$_POST['course_data'].'<br/>
			- Instructor Code > '. $_POST['inst_code'].'<br/>
			- Course start date > '. $_POST['course_start'].'<br/>
			- Course end date > '. $_POST['course_end'].'<br/>
			- Course runnig days > '. $_POST['course_days'].'<br/>
			</div>';
	} else if ($state == 0) { //Failure
			echo "<div id='message' style='color: #D8000C; background-color:#FFBABA;border: 1px solid;margin: 5px 15px 5px 5px;padding:5px 10px 5px 10px;border-radius: 5px;'>
			Nothing was Added!<br/>
			Please make sure that you select the Course Code and check the dates.<br/>
			".$wpdb->last_error."</div>";
			echo $course_code.$course_name;
	}
?>

<script type="text/javascript" >
		jQuery(document).ready(function($) {
		
			$('#wait_1').hide();
			$('#hide_course_name').hide();
			
			$('#course_cat').change(function(){
				
				$('#wait_1').show();
				$('#course_data').hide();
				
				var data = {
					action: 'cmp_course_cat',
					course_cat: $('#course_cat').val()
				};

				// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
				$.post(ajaxurl, data, function(response) {
					// alert('Got this from the server: ' + response);
					$('#course_data').fadeOut();
					setTimeout("finishAjax('course_data', '"+escape(response)+"')", 400);
				});
				
				$('#hide_course_name').show();
				
			});
		});
		
		function finishAjax(id, response) {
			jQuery('#wait_1').hide();
			jQuery('#'+id).html(unescape(response));
			jQuery('#'+id).fadeIn();
		}
		
	</script>	

<div class="wrap">	
	<h2>Run New Course </h2>
	<hr/>
	<form method="POST" action="">
		<div class="wrap" id="tabs">
			<ul>
				<li><a href="#tabs-1">Course Basic Info</a></li>
				<li><a href="#tabs-2">Course Attendees</a></li>
				<li><a href="#tabs-3">Course Evaluation</a></li>
				<li><a href="#tabs-4">Operating Costs</a></li>
			</ul>
		<div id="tabs-1">
			<div class="wrap">
				<table class="form-table">
					<tr>	
						<td scope="row"><label for="course_cat">Course Category:</label></td>
						<td><select id="course_cat" name="course_cat">
								<option value="" disabled="disabled" selected="selected">Select a Course Category</option>
								<?php if(function_exists('cmp_return_courses_cats')) {
										print cmp_return_courses_cats();} ?>
						</select></td>
					</tr>
					
					<tr id="hide_course_name">
						<td scope="row"><label for="course_code">Course Code:</label></td>
						<td>
							<select id="course_data" name="course_data" style="display: none;">
								<!-- Options tags data retrieved from AJAX call here !--> 
							</select>
							<span id="wait_1" style="display: none;">
								<img alt="Please Wait" src="<?php echo plugins_url( "ajax-loader.gif" , __FILE__ ); ?>"/>
							</span>
						</td>
					</tr>

					<tr><td scope="row"><label for="inst_code">Course Instructor:</label></td>
					<td><select id="inst_code" name="inst_code">
							<option value="TBA" disabled="disabled" selected="selected">TBA | To Be Assigned</option>
							<?php if(function_exists('cmp_return_instructors')) {
									print cmp_return_instructors();} ?>
						</select>
						<!-- Or <input id="inst_name" name="inst_name" STYLE="width: 250px" placeholder="Type the name if Not on list ..."><td> !-->
					</tr>
					
					<tr><td scope="row"><label for="country">Course Location:</label></td>
					<td>
						<select name="country" id="country">
						<?php $country = ''; 
						if(function_exists('get_countries_list')) {
						get_countries_list();} ?>
						</select>
					</td></tr>
					
					<tr><td scope="row"><label for="course_start">Start Date:</label></td>
					<td><input type="text" name="course_start" id="course_start" value="<?php echo date("Y-m-d")?>" class="mydatepicker"/>
					<span class="description">Date Format is: yyyy-mm-dd</span></td></tr>
					<tr><td scope="row"><label for="course_end">End Date:</label></td>
					<td><input type="text" name="course_end" id="course_end" value="<?php echo date("Y-m-d")?>" class="mydatepicker"/>
					<span class="description"></span></td></tr>
					<tr><td scope="row"><label for="course_days">Number of Active Days:</label></td>
					<td><input type="text" name="course_days" id="course_days" value="1" onkeypress='validate(event)'/></td></tr>

					<td scope="row"><label for="course_status">Current Course Status:</label></td>
					<td>
						<input type="radio" name="course_status" checked="checked" value="1"> Planned<br/>
						<input type="radio" name="course_status" value="2"> Running<br/>
						<input type="radio" name="course_status" value="3"> Done<br/>
						<input type="radio" name="course_status" value="4"> Postponed<br/>
						<input type="radio" name="course_status" value="5"> Cancelled
					</td>
				</table>
			</div></div>
		
		<div id="tabs-2">
			<?php cmp_course_attendees_html($empty);?>
		</div>
		
		<div id="tabs-3">
			<div class="wrap">
			<table class="widefat">
			Rating system:<br/>
			1-Agree Strongly,     2-Agree,    3-Unsure,     4-Dlsagree,   5-Disagree Strongly,   6-Not applicable.<br/><hr>
				<thead><tr><td>Evaluation Item</td><td>Evaluation Scores</td></tr></thead>
				<tbody>
				<tr><td><span class="description">Enter values from 1-6 seperated by dots. (e.g. "4.5.3.2.1" represents 5 evaluations for one item)</span></td><td></td></tr>
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
				
				foreach ($eval_items as $key => $eval_item) {
				echo '<tr><td>'.$key.'-'.$eval_item.'</td><td><input type="text" id="eval'.$key.'" name="eval[]" value="0" size="30" onfocus="clearMe(this)" onBlur="NoEmpty(this.id)" onkeypress="validate(event)"/></td></tr>';
					}
				?>
				<!--  !-->
				</tbody>
				</table>
		</div></div>
		
		<div id="tabs-4">
			<div class="wrap">
			<table class="widefat">
				<thead><tr><td>Item</td><td>Cost (EGP)</td><td>Done</td></tr></thead>
				<tbody>
				<?php
				$tasks = array('Material','Breackfast','Launch','Gifts','Instructor','Certificates','Practical','Classroom','Other');
				foreach ($tasks as $key => $task) {
					echo '<tr><td>'.$task.'</td><td><input type="text" id="task_cost'.$key.'" name="task_cost[]" value="0" size="30"
						  onfocus="clearMe(this)" onBlur="NoEmpty(this.id)" onkeypress="validate(event)"/></td><td>
						  <input type="checkbox" id="task_done[]" name="task_done[]" value="'.$task.'"></td></tr>';
				}
				?>
					</tbody>                                                                                                                                                   
				</table>
			</div>
		</div>
		<div class="wrap">
			<table class="form-table">
			<tr valign="top">
				<td scope="row"><label for="submit-add"><input type="submit" name="submit-add" id="submit-add" value="Add Course" class="button-primary"/></label></td>
			</tr>
			</table>
		</div>
		</div> <!-- End of TABS !-->
	</form>
</div>
	<?php
cmp_autocomplete_script();
}
?>