<div class="wrap">
    <?php screen_icon( 'plugins' ); ?>
	<h2>EDS CMP Settings</h2>
	<hr/>
	
	<form method="post">
	<?php
	// Write settings to the database
	if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['save']=='Save Options') {
		update_option( 'edsdb_careers_list', split( "\n", $_POST['edsdb_careers_list_in'] ) );
		update_option( 'edsdb_courses_warning', $_POST['edsdb_courses_warning'] );
		update_option( 'edsdb_telfax', $_POST['edsdb_telfax'] );
		update_option( 'edsdb_mobile_1', $_POST['edsdb_mobile_1'] );
		update_option( 'edsdb_mobile_2', $_POST['edsdb_mobile_2'] );
		update_option( 'cbd_print', $_POST['cbd_print'] );
		echo '<div id="message" class="updated">Options updated successfully</div>';
	}
	screen_icon( 'plugins' );
	wp_nonce_field('update-options');
	//$Career = sdsdb_career_options();
	?>
	<table class="form-table">
		<tr valign="top">
			<th scope="row"><label for="edsdb_careers_list_in">Careers List:</label></th>
				<td><textarea style="width: 300px; height: 100px" id="edsdb_careers_list_in" name="edsdb_careers_list_in"><?php
					$careers = get_option('edsdb_careers_list');
					if (!empty($careers)) { echo implode( "\n", $careers );} else {echo 'No Careers Listed';}
				?></textarea></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="select_career">User Career List (Preview)</label></th>
				<td><select id="select_career" name="select_career">
					<option value="" >Select Career</option>
					<?php 
						if (isset($Career)) {print edsdb_career_options();}
					// selected( $profileuser->user_career ,, No updating or value showing here ?>
					</select>
					<span class="description">No values showing or updating takes place here (Preview only)</span>
		</td>
		</tr>
	</table>
	<hr>
	<table class="form-table">
		<tr valign="top"> <!-- To Be Changed for another options !-->
		<th scope="row"><label for="edsdb_courses_warning">Show Warning for courses that expires after:</label></th>
			<td><input type="text" id="edsdb_courses_warning" name="edsdb_courses_warning" value="<?php
			print get_option('edsdb_courses_warning');?>"> Days
			</td>
		</tr>
	</table>
	<hr>
	<table class="form-table">
		<h3>Course Break Down Printing Data:</h3>
		<tr valign="top"> <!-- To Be Changed for another options !-->
		<th scope="row"><label for="edsdb_telfax">Telfax:</label></th>
			<td><input type="text" id="edsdb_telfax" name="edsdb_telfax" value="<?php
			print get_option('edsdb_telfax');?>"></td></tr>
		
		<tr><th scope="row"><label for="edsdb_mobile_1">Mobile-1:</label></th>
			<td><input type="text" id="edsdb_mobile_1" name="edsdb_mobile_1" value="<?php
			print get_option('edsdb_mobile_1');?>"></td></tr>	
			
		<tr><th scope="row"><label for="edsdb_mobile_2">Mobile-2:</label></th>
			<td><input type="text" id="edsdb_mobile_2" name="edsdb_mobile_2" value="<?php
			print get_option('edsdb_mobile_2');?>"></td></tr>
					
		<tr><th scope="row"><label for="edsdb_mobile_2">Printing Style:</label></th>
		<td>
			<input type="radio" name="cbd_print" value="int" <?php checked(get_option('cbd_print'),"int");?>>Full Report (Internal)<br>
			<input type="radio" name="cbd_print" value="ext" <?php checked(get_option('cbd_print'),"ext");?>>Customized Report (External)
		</td></tr>
		
	</table>
	<hr>
	<input type="submit" name="save" value="Save Options" class="button-primary" />
	<input type="submit" name="reset" value="Reset" class="button-secondary" />
	<input type="hidden" name="action" value="update" />	

	</form>
</div>
<div class="clear"></div>