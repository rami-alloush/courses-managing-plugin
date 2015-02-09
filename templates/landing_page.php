<div class="wrap">
    <?php screen_icon( 'plugins' ); ?>
	<h2>EDS - Home</h2>
	
	<hr/>
    <form method="post" action="options.php"> 
        <table class="form-table">  
            <tr valign="top">
                <p>This section will be updated with instructions and handy short-cuts.</p>
            </tr>
			<tr valign="top">
                <?php
				if(appthemes_check_user_role('eds_training_sales')) {
					echo "This user is Sales Team"."<br>";
				} else {
					echo "This user is NOT Sales Team"."<br>";
				};				
				
				if(appthemes_check_user_role('eds_purchasing')) {
					echo "This user is Purchasing Team"."<br>";
				} else {
					echo "This user is NOT Purchasing Team"."<br>";
				};
				
				if(current_user_can('eds_training_sales')) {
					echo "This user has Sales Capability"."<br>";
				} else {
					echo "This user does NOT has Sales Capability"."<br>";
				};
				?>
            </tr>
			<tr valign="top">
			<script type="text/javascript" >
				jQuery(document).ready(function($) {

					var data = {
						action: 'my_action',
						whatever: 1234
					};

					// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
					$.post(ajaxurl, data, function(response) {
					//	alert('Got this from the server: ' + response);
					});
				});
			</script>
			    <div class="section panel">
				<h1>Custom Theme Options</h1>
				<?php
				$instructors = get_posts( array( 'post_type' => 'instructor', 'post_status' => 'publish' ) );
					foreach ( $instructors as $instructor ) {
					$course_title = get_the_title( $instructor );
					$course_code = get_field('product_code', $instructor);
					echo '<option value="'. $course_code . ':' . $course_title .'">'. $course_code .' | '. $course_title .'</option>';
				}
				 global $wpdb;
				$blog_ids = $wpdb->get_row("select * from $wpdb->blogs order by blog_id asc");
				echo "<pre>";
					// var_dump(cmp_return_companys());
					// var_dump(get_field('visit_actions',94));
				echo "</pre>";
				// var_dump(cmp_return_companys());
				
				// check if the repeater field has rows of data
				if( have_rows('visit_actions',94) ):

					// loop through the rows of data
					while ( have_rows('visit_actions',94) ) : the_row();

						// display a sub field value
						$user = get_sub_field('visit_action_assign_to');
						echo $user['user_email'];
							// foreach(get_sub_field("visit_action_assign_to") as $user):
								print_r($user);
								// the_sub_field($user);
							// endforeach;
					endwhile;
				else :
					// no rows found
				endif;
				?>
			  </div>
        </table>
    </form>
</div>