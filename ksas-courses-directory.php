<?php
/*
Plugin Name: KSAS Courses Directory
Plugin URI: http://krieger.jhu.edu/
Description: Creates a custom post type for courses.  Courses must be designated as either graduate, undergraduate or both to display properly.  You should also choose the academic department so your courses can be found in the global course directory. To display your courses, create a page titled either Graduate Courses or Undergraduate Courses.  Then choose 'Courses' from the page template drop-down.
Version: 3.0
Author: Cara Peckens
Author URI: mailto:ksasweb@jhu.edu
License: GPL2
*/
// Registration code for courses post type.
function register_courses_posttype() {
	$labels = array(
		'name'               => _x( 'Courses', 'post type general name' ),
		'singular_name'      => _x( 'Course', 'post type singular name' ),
		'add_new'            => _x( 'Add New', 'Course' ),
		'add_new_item'       => __( 'Add New Course ' ),
		'edit_item'          => __( 'Edit Course ' ),
		'new_item'           => __( 'New Course ' ),
		'view_item'          => __( 'View Course ' ),
		'search_items'       => __( 'Search Courses ' ),
		'not_found'          => __( 'No Course found' ),
		'not_found_in_trash' => __( 'No Courses found in Trash' ),
		'parent_item_colon'  => '',
	);

	$taxonomies = array( 'coursetype', 'academicdepartment' );

	$supports = array( 'title', 'editor', 'revisions' );

	$post_type_args = array(
		'labels'             => $labels,
		'singular_label'     => __( 'Course' ),
		'public'             => true,
		'show_ui'            => true,
		'publicly_queryable' => true,
		'query_var'          => true,
		'capability_type'    => 'course',
		'capabilities'       => array(
			'publish_posts'       => 'publish_courses',
			'edit_posts'          => 'edit_courses',
			'edit_others_posts'   => 'edit_others_courses',
			'delete_posts'        => 'delete_courses',
			'delete_others_posts' => 'delete_others_courses',
			'read_private_posts'  => 'read_private_courses',
			'edit_post'           => 'edit_course',
			'delete_post'         => 'delete_course',
			'read_post'           => 'read_course',
		),
		'has_archive'        => false,
		'hierarchical'       => false,
		'rewrite'            => array(
			'slug'       => 'course',
			'with_front' => false,
		),
		'supports'           => $supports,
		'menu_position'      => 5,
		'taxonomies'         => $taxonomies,
		'show_in_rest'       => true,
	);
	register_post_type( 'course', $post_type_args );
}
	add_action( 'init', 'register_courses_posttype' );

// Registration code for coursetype taxonomy.
function register_coursetype_tax() {
	$labels = array(
		'name'               => _x( 'Course Types', 'taxonomy general name' ),
		'singular_name'      => _x( 'Course Type', 'taxonomy singular name' ),
		'add_new'            => _x( 'Add New Course Type', 'Course Type' ),
		'add_new_item'       => __( 'Add New Course Type' ),
		'edit_item'          => __( 'Edit Course Type' ),
		'new_item'           => __( 'New Course Type' ),
		'view_item'          => __( 'View Course Type' ),
		'search_items'       => __( 'Search Course Types' ),
		'not_found'          => __( 'No Course Type found' ),
		'not_found_in_trash' => __( 'No Course Type found in Trash' ),
	);

	$pages = array( 'course' );

	$args = array(
		'labels'            => $labels,
		'singular_label'    => __( 'Course Type' ),
		'public'            => true,
		'show_ui'           => true,
		'hierarchical'      => true,
		'show_tagcloud'     => false,
		'show_in_nav_menus' => false,
		'show_in_rest'      => true,
		'rewrite'           => array(
			'slug'       => 'coursetype',
			'with_front' => false,
		),
	);
	register_taxonomy( 'coursetype', $pages, $args );
}
add_action( 'init', 'register_coursetype_tax' );

function check_coursetype_terms() {

		// See if we already have populated any terms.
	$term = get_terms( 'coursetype', array( 'hide_empty' => false ) );

	// If no terms then lets add our terms.
	if ( empty( $term ) ) {
		$terms = define_coursetype_terms();
		foreach ( $terms as $term ) {
			if ( ! term_exists( $term['name'], 'coursetype' ) ) {
				wp_insert_term( $term['name'], 'coursetype', array( 'slug' => $term['slug'] ) );
			}
		}
	}
}

add_action( 'init', 'check_coursetype_terms' );

function define_coursetype_terms() {

	$terms = array(
		'0' => array(
			'name' => 'graduate',
			'slug' => 'graduate-course',
		),
		'1' => array(
			'name' => 'undergraduate',
			'slug' => 'undergraduate-course',
		),
	);

	return $terms;
}
// Add course details metabox.
$coursedetails_1_metabox = array(
	'id'       => 'coursedetails',
	'title'    => 'Course Details',
	'page'     => array( 'course' ),
	'context'  => 'normal',
	'priority' => 'default',
	'fields'   => array(


		array(
			'name'        => 'Number of Credits',
			'desc'        => '',
			'id'          => 'ecpt_credit',
			'class'       => 'ecpt_credit',
			'type'        => 'text',
			'rich_editor' => 0,
			'max'         => 0,
		),

		array(
			'name'        => 'Instructor Name',
			'desc'        => '',
			'id'          => 'ecpt_instructor',
			'class'       => 'ecpt_instructor',
			'type'        => 'text',
			'rich_editor' => 0,
			'max'         => 0,
		),

		array(
			'name'        => 'Prerequisites',
			'desc'        => '',
			'id'          => 'ecpt_prereqs',
			'class'       => 'ecpt_prereqs',
			'type'        => 'textarea',
			'rich_editor' => 1,
			'max'         => 0,
		),

		array(
			'name'        => 'Course Website',
			'desc'        => '',
			'id'          => 'ecpt_course_website',
			'class'       => 'ecpt_course_website',
			'type'        => 'text',
			'rich_editor' => 0,
			'max'         => 0,
		),

		array(
			'name'        => 'Course Times',
			'desc'        => '',
			'id'          => 'ecpt_course_times',
			'class'       => 'ecpt_course_times',
			'type'        => 'text',
			'rich_editor' => 0,
			'max'         => 0,
		),

		array(
			'name'        => 'Class Limit',
			'desc'        => '',
			'id'          => 'ecpt_class_limit',
			'class'       => 'ecpt_class_limit',
			'type'        => 'text',
			'rich_editor' => 0,
			'max'         => 0,
		),
	),
);

add_action( 'admin_menu', 'ecpt_add_coursedetails_1_meta_box' );
function ecpt_add_coursedetails_1_meta_box() {

	global $coursedetails_1_metabox;

	foreach ( $coursedetails_1_metabox['page'] as $page ) {
		add_meta_box( $coursedetails_1_metabox['id'], $coursedetails_1_metabox['title'], 'ecpt_show_coursedetails_1_box', $page, 'normal', 'default', $coursedetails_1_metabox );
	}
}

// Function to show meta boxes.
function ecpt_show_coursedetails_1_box() {
	global $post;
	global $coursedetails_1_metabox;
	global $ecpt_prefix;
	global $wp_version;

	// Use nonce for verification.
	echo '<input type="hidden" name="ecpt_coursedetails_1_meta_box_nonce" value="', wp_create_nonce( basename( __FILE__ ) ), '" />';

	echo '<table class="form-table">';

	foreach ( $coursedetails_1_metabox['fields'] as $field ) {
		// Get current post meta data.

		$meta = get_post_meta( $post->ID, $field['id'], true );

		echo '<tr>',
				'<th style="width:20%"><label for="', $field['id'], '">', $field['name'], '</label></th>',
				'<td class="ecpt_field_type_' . str_replace( ' ', '_', $field['type'] ) . '">';
		switch ( $field['type'] ) {
			case 'text':
				echo '<input type="text" name="', $field['id'], '" id="', $field['id'], '" value="', $meta ? $meta : $field['std'], '" size="30" style="width:97%" /><br/>', '', $field['desc'];
				break;
			case 'textarea':
				if ( $field['rich_editor'] == 1 ) {
					if ( $wp_version >= 3.3 ) {
						echo wp_editor(
							$meta,
							$field['id'],
							array(
								'textarea_name' => $field['id'],
								'wpautop'       => false,
							)
						);
					} else {
						// Older versions of WP.
						$editor = '';
						if ( ! post_type_supports( $post->post_type, 'editor' ) ) {
							$editor = wp_editor(
								true,
								array(
									'editor_selector'   => $field['class'],
									'remove_linebreaks' => false,
								)
							);
						}
						$field_html = '<div style="width: 97%; border: 1px solid #DFDFDF;"><textarea name="' . $field['id'] . '" class="' . $field['class'] . '" id="' . $field['id'] . '" cols="60" rows="8" style="width:100%">' . $meta . '</textarea></div><br/>' . __( $field['desc'] );
						echo $editor . $field_html;
					}
				} else {
					echo '<div style="width: 100%;"><textarea name="', $field['id'], '" class="', $field['class'], '" id="', $field['id'], '" cols="60" rows="8" style="width:97%">', $meta ? $meta : $field['std'], '</textarea></div>', '', $field['desc'];
				}

				break;
		}
		echo '<td>',
			'</tr>';
	}

	echo '</table>';
}

add_action( 'save_post', 'ecpt_coursedetails_1_save' );

// Save data from meta box.
function ecpt_coursedetails_1_save( $post_id ) {
	global $post;
	global $coursedetails_1_metabox;

	// Verify nonce.
	if ( ! isset( $_POST['ecpt_coursedetails_1_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['ecpt_coursedetails_1_meta_box_nonce'], basename( __FILE__ ) ) ) {
		return $post_id;
	}

	// Check autosave.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return $post_id;
	}

	// Check permissions.
	if ( 'page' == $_POST['post_type'] ) {
		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return $post_id;
		}
	} elseif ( ! current_user_can( 'edit_post', $post_id ) ) {
		return $post_id;
	}

	foreach ( $coursedetails_1_metabox['fields'] as $field ) {

		$old = get_post_meta( $post_id, $field['id'], true );
		$new = $_POST[ $field['id'] ];

		if ( $new && $new != $old ) {
			if ( $field['type'] == 'date' ) {
				$new = ecpt_format_date( $new );
				update_post_meta( $post_id, $field['id'], $new );
			} else {
				update_post_meta( $post_id, $field['id'], $new );

			}
		} elseif ( '' == $new && $old ) {
			delete_post_meta( $post_id, $field['id'], $old );
		}
	}
}
// Configuring Admin Columns - in View all course.
add_action( 'manage_posts_custom_column', 'course_custom_columns' );
add_filter( 'manage_edit-course_columns', 'course_edit_columns' );

function course_edit_columns( $columns ) {
	$columns = array(
		'cb'         => '<input type="checkbox" />',
		'title'      => 'Name',
		'coursetype' => 'Course Type',
		'instructor' => 'Instructor',
	);

	return $columns;
}
function course_custom_columns( $column ) {
	global $post;

	switch ( $column ) {

		case 'coursetype':
			echo get_the_term_list( $post->ID, 'coursetype', '', ', ', '' );
			break;
		case 'instructor':
			$custom = get_post_custom();
			echo $custom['ecpt_instructor'][0];
			break;
	}
}
