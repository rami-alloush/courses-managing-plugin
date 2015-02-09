<?php
if(function_exists("register_field_group"))
{
	register_field_group(array (
		'id' => 'acf_attendees',
		'title' => 'attendees',
		'fields' => array (
			array (
				'key' => 'field_544b7b35f7decatt',
				'label' => 'Mobile/Phone',
				'name' => 'att_mobile',
				'type' => 'text',
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'formatting' => 'html',
				'maxlength' => '',
			),
			array (
				'key' => 'field_54cf4714142a5att',
				'label' => 'Old Code',
				'name' => 'att_old_code',
				'type' => 'text',
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'formatting' => 'html',
				'maxlength' => '',
			),			
			array (
				'key' => 'field_544b819a99b77att',
				'label' => 'Email',
				'name' => 'att_email',
				'type' => 'email',
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
			),
			array (
				'key' => 'field_544baa35d7fafatt',
				'label' => 'Y.O.E',
				'name' => 'att_experience',
				'type' => 'number',
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'min' => '0.5',
				'max' => '',
				'step' => '0.5',
			),
			array (
				'key' => 'field_544baa740ff2aatt',
				'label' => 'Education',
				'name' => 'att_education',
				'type' => 'text',
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'formatting' => 'html',
				'maxlength' => '',
			),
			array (
				'key' => 'field_544baa9f0ff2batt',
				'label' => 'Comments/Notes',
				'name' => 'att_comments',
				'type' => 'textarea',
				'default_value' => '',
				'placeholder' => '',
				'maxlength' => '',
				'rows' => '',
				'formatting' => 'br',
			),
		),
		'location' => array (
			array (
				array (
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'attendee',
					'order_no' => 0,
					'group_no' => 0,
				),
			),
		),
		'options' => array (
			'position' => 'acf_after_title',
			'layout' => 'no_box',
			'hide_on_screen' => array (
				0 => 'permalink',
				1 => 'the_content',
				2 => 'excerpt',
				3 => 'custom_fields',
				4 => 'discussion',
				5 => 'comments',
				6 => 'revisions',
				7 => 'slug',
				8 => 'author',
				9 => 'format',
				10 => 'featured_image',
				11 => 'categories',
				12 => 'tags',
				13 => 'send-trackbacks',
			),
		),
		'menu_order' => 0,
	));
}
?>