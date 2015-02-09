<?php
function edsdb_eval_values_range($start,$end,$current_course) {
	$eval_titles = array (
				'Knowledgeable about the subject',            //INST: 
				'Well prepared',                              //INST: 
				'Presents material in a way the helps',       //INST: 
				'Encourages participation',                   //INST: 
				'Answers students questions',                 //INST: 
				'Enthusiastic about teaching',                //INST: 
				'The pace of the course Is just right',       //INST: 
				'I would recommend him to others',            //INST: 
				'Course difficulty suitable',                 //ASSI: 
				'Readings help me learn the material',        //ASSI: 
				'Assignments interest me',                    //ASSI: 
				'Have been about the right length',           //ASSI: 
				'Classroom comfortable and inviting',         //CLRM: 
				'Classroom presents few distractions',        //CLRM: 
				'Desks provide adequate work space',          //CLRM: 
				'I like this course',                         //GENR: 
				'I recommend this course to others');         //GENR:
				
	$eval_items = explode(",", $current_course['eval']);
	$eval_vals = explode(".", $eval_items[0]);
	$eval_num = count($eval_vals); //Take number of evaluations from first item
	
	//Create Participants Numbering (header)
	echo '[\'Participants\',';
		for ($i = 1; $i <= $eval_num ; $i++) {
			echo '\'Participant '.$i.' \',';
		}
		echo '],';
		
	//Create Chart Content, rest of rows
	foreach ($eval_items as $ikey => $eval_item) {
		if($ikey >=$start && $ikey <=$end) { 					//Take instructor's items only
			echo '[\''.$eval_titles[$ikey].'\',';
			
			//$eval_vals = array("strong", "weak","dsd");
			$eval_vals = explode(".", $eval_item); 	//Create array of eval values
			
				for ($i = 0; $i <= $eval_num-1; $i++) {
					if ($eval_vals[$i] <= 0 OR $eval_vals[$i] >= 6) {
						echo '6,' ;					//Print Not Applicable value
					} else {
						echo $eval_vals[$i].',' ;	//Print actual eval values
						}
				}
			echo '],';
		}
	}
}
?>
	<!--Load the AJAX API-->
	<script type="text/javascript" src="http://canvg.googlecode.com/svn/trunk/rgbcolor.js"></script> 
    <script type="text/javascript" src="http://canvg.googlecode.com/svn/trunk/canvg.js"></script>
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">

      // Load the Visualization API and the piechart package.
      google.load('visualization', '1.0', {'packages':['corechart']});

      // Set a callback to run when the Google Visualization API is loaded.
	  google.setOnLoadCallback(drawColumnChart);	  
	  
      // Callback that creates and populates a data table,
      function drawColumnChart() {

        // Create the data table.
		  var data_inst = google.visualization.arrayToDataTable([<?php edsdb_eval_values_range(0,7,$current_course);?>]);
		  var data_assi = google.visualization.arrayToDataTable([<?php edsdb_eval_values_range(8,11,$current_course);?>]);
		  var data_clrm = google.visualization.arrayToDataTable([<?php edsdb_eval_values_range(12,16,$current_course);?>]);

		  var chart_options = {//title:"EDS Course Instructor Evaluation",
			width:900, height:450,
			hAxis: {slantedText: true, slantedTextAngle: 20,textStyle:{fontSize:14}},
			vAxis: {baseline:6,direction:-1, title: 'Score of the Item',maxValue:6,viewWindowMode:'explicit',viewWindow:{max:6, min:1}, gridlines: {count:6}},
			};
						
		  // Create and draw the visualization.
		  chart_options.title = "Instructor Evaluation";
			new google.visualization.ColumnChart(document.getElementById('chart_inst')).draw(data_inst,chart_options);
		  chart_options.title = "Assignments Evaluation";
			new google.visualization.ColumnChart(document.getElementById('chart_assi')).draw(data_assi,chart_options);
		  chart_options.title = "Classrooom and General Evaluation";
			new google.visualization.ColumnChart(document.getElementById('chart_classroom')).draw(data_clrm,chart_options);
      }

	//window.onload = function() {
	function tmpSave(chart_id,image_name) { //Image_name: 10 charachters
	
	var chartContainer = document.getElementById(chart_id);
	   
        var doc = chartContainer.ownerDocument;
        var img = doc.createElement('img');
		
		var chartArea = chartContainer.getElementsByTagName('iframe')[0].contentDocument.getElementById('chartArea');
        var svg = chartArea.innerHTML;
        var doc = chartContainer.ownerDocument;
        var canvas = doc.createElement('canvas');
        canvas.setAttribute('width', chartArea.offsetWidth);
        canvas.setAttribute('height', chartArea.offsetHeight);
		
		
        doc.body.appendChild(canvas);
        canvg(canvas, svg);        
		var imgData = canvas.toDataURL("image/png");
		canvas.parentNode.removeChild(canvas);
		img.src = imgData;
		
		
		//Send image info to saving page 
		var ajax = new XMLHttpRequest();
		ajax.open("POST",'<?php
						bloginfo('wpurl'); echo '/wp-content/plugins/eds-db/tmpSave.php';
						?>',false);
		ajax.setRequestHeader('Content-Type', 'application/upload');
		//ajax.send(imgData);
		ajax.send(image_name+imgData);
		//alert(document.URL); //show current URL
		
	//	   while (imageContainer.firstChild) {
		//	 imageContainer.removeChild(imageContainer.firstChild);
		  // }
		
	}
	
	function edsdb_tmpsave_charts() {
		tmpSave('chart_inst','chart_inst');
		tmpSave('chart_assi','chart_assi');
		tmpSave('chart_classroom','chart_clrm');		
		tmpSave('chart_cost','chart_cost');		
	}
	
    </script>
    <!--Div that will hold the pie chart-->
	<h2>Rating system:</h2>
		<h3>1-Agree Strongly, 2-Agree, 3-Unsure, 4-Dlsagree, 5-Disagree Strongly, 6-Not applicable</h3>
	<div id="chart_inst"></div>
	<div id="chart_assi"></div>
	<div id="chart_classroom"></div>

