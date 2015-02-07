<?php 
/*
Plugin Name: Mad9Scientist Site Plugin
Plugin URI: http://mad9scientist.com/projects/site-plugin
Description: This plugin provides custom features and functions for Mad9Scientist.com that would normally be in the Theme Functions file.
Author: Chris Holbrook
Version: 0.0.1
Author URI: http://mad9scientist.com/
*/

# Clean up Header

remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'feed_links', 2);
remove_action('wp_head', 'index_rel_link');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'feed_links_extra', 3);
remove_action('wp_head', 'start_post_rel_link', 10, 0);
remove_action('wp_head', 'parent_post_rel_link', 10, 0);
remove_action('wp_head', 'adjacent_posts_rel_link', 10, 0);

# Clean Injected Scripts

# Inject jQuery and Scripts
function jQuery_inject(){
	if(!is_admin()){
		wp_deregister_script( 'jquery' );
	    wp_register_script( 'jquery', '//ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js',false,'2.1.3',true);
	    wp_enqueue_script( 'jquery', '//ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js',false,'2.1.3',true ); 
	}
}

function jQuery_bkp(){
?>
<script>window.jQuery || document.write('<script src="js/vendor/jquery-2.1.3.min.js"><\/script>')</script>
<?php
}

//add_action('wp_footer', 'jQuery_inject', 1);
add_action('wp_enqueue_scripts', 'jQuery_inject', 1);
add_action('wp_footer', 'jQuery_bkp', 20);

function core_m9s_js(){
	echo "<script src='";
	echo bloginfo('template_directory');
	echo "/js/core.mad9scientist.com.js'></script>";
}
add_action( 'wp_footer', 'core_m9s_js', 25);

# Inject Google Analytics to Footer


# Moderation Links for Comments
function m9s_comment_control_links($id) {
	if (current_user_can('edit_post')) {
		echo ' <a class="edit" href="'.get_bloginfo('wpurl').'/wp-admin/comment.php?action=editcomment&c='.$id.'">Edit</a>';

		// echo '<a class="feature-comments feature" title="Feature" data-comment_id="'.$id.'" data-do="feature">Feature</a>
		// <a class="feature-comments unfeature feature" title="Unfeature" data-comment_id="'.$id.'" data-do="unfeature">Unfeature</a>';
		// echo '<a class="feature-comments bury" title="Bury" data-comment_id="'.$id.'" data-do="bury">Bury</a>
		// <a class="feature-comments unbury bury" title="Unbury" data-comment_id="'.$id.'" data-do="unbury">Unbury</a>';

		echo ' <a class="del" href="'.get_bloginfo('wpurl').'/wp-admin/comment.php?action=cdc&c='.$id.'">Delete</a> ';
		echo ' <a class="spam" href="'.get_bloginfo('wpurl').'/wp-admin/comment.php?action=cdc&dt=spam&c='.$id.'">Spam</a>';
	}
} 


# Add Link Post Options

// Enable Post Formats for WP 3.1+
// Options: 'aside','chat','gallery','image','link','quote','status','video','audio'
add_theme_support('post-formats',array('link'));

// filter post title for tumblr links
function sd_link_filter($link, $post) {
     if (has_post_format('link', $post) && get_post_meta($post->ID, 'LinkFormatURL', true)) {
          $link = get_post_meta($post->ID, 'LinkFormatURL', true);
     }
     return $link;
}
add_filter('post_link', 'sd_link_filter', 10, 2);

# Security
// No Announcing of for Fail Login
add_filter('login_errors',create_function('$a', "return null;"));

# Custom Post Types

## Portfolio | Register Custom Post Type
function m9s_portfolio_cpt(){
	 $labels = array(
		'name'               => 'Portfolio',
		'singular_name'      => 'Piece',
		'menu_name'          => 'Portfolio',
		'parent_item_colon'  => 'Parent Item:',
		'all_items'          => 'All Items',
		'view_item'          => 'View Portfolio',
		'add_new_item'       => 'Add New Piece',
		'add_new'            => 'New Piece',
		'edit_item'          => 'Edit Piece',
		'update_item'        => 'Update Piece',
		'search_items'       => 'Search Portfolio',
		'not_found'          => 'No items found',
		'not_found_in_trash' => 'No items found in Trash',
	 );

	 $rewrite = array(
		'slug'       =>	'portfolio',
		'with_front' => true,
		'pages'      => true,
		'feeds'      => false,
	 );

	 $args = array(
		'label'               => 'm9s_portfolio',
		'description'         => 'Manage Portfolio items and pieces.',
		'labels'               => $labels,
		'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'revisions', 'custom-fields',),
		'hierarchical'        => true,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 25,
		'menu_icon'           => plugins_url( 'icons/portfolio.png' , __FILE__ ),
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'rewrite'             => $rewrite,
		'capability_type'     => 'post',
	  );

	 register_post_type( 'm9s_portfolio', $args );
}

### Custom Fields for Projects

add_action('save_post', 'portfolio_Metadata_Save');

function m9s_portfolio_cpt_meta_opt(){
	global $post;
	$custom      = get_post_custom($post->ID);
	$projectType    = $custom['m9s_project_type'][0];
	
	echo "<p><label for='m9s_project_type' >Project Type(s) </label></p>
	<p>
		<textarea id='m9s_project_type' name='m9s_projectType' style='width:100%; height:125px'>$projectType</textarea>
	</p>";
}

function portfolio_Metadata_Save(){
	global $post;
	update_post_meta($post->ID, 'm9s_project_type', $_POST['m9s_projectType']);
}


## Projects | Register Custom Post Type
function m9s_projects_cpt(){
	 $labels = array(
		'name'               => 'Projects',
		'singular_name'      => 'Project',
		'menu_name'          => 'Projects',
		'parent_item_colon'  => 'Parent Project:',
		'all_items'          => 'All Projects',
		'view_item'          => 'View Project',
		'add_new_item'       => 'Add New Project',
		'add_new'            => 'New Project',
		'edit_item'          => 'Edit Project',
		'update_item'        => 'Update Project',
		'search_items'       => 'Search Projects',
		'not_found'          => 'No Projects Found',
		'not_found_in_trash' => 'No Projects found in Trash',
	 );

	 $rewrite = array(
		'slug'       =>	'projects',
		'with_front' => true,
		'pages'      => true,
		'feeds'      => false,
	 );

	 $args = array(
		'label'               => 'm9s_projects',
		'description'         => 'Manage Publicly Sharable Project.',
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'revisions', 'custom-fields', ),
		'hierarchical'        => true,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 25,
		'menu_icon'           => plugins_url( 'icons/projects.png' , __FILE__ ),
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'rewrite'             => $rewrite,
		'capability_type'     => 'post',
	  );

	 register_post_type( 'm9s_projects', $args );
}

### Custom Fields for Projects

add_action('save_post', 'project_Metadata_Save');


function m9s_projects_cpt_meta_opt(){
	global $post;
	$custom      = get_post_custom($post->ID);
	$repoLink    = $custom['m9s_repo_link'][0];
	$licenseType = $custom['m9s_license_type'][0];
	$demoLink    = $custom['m9s_demo_url'][0];
	$projectImg  = $custom['m9s_project_img'][0];

	echo "<p><label for='m9s_repo_link' >Project Code Repository </label></p> <p><input id='m9s_repo_link' name='m9s_repo' type='url' value='$repoLink' /></p>";
	echo "<p><label for='m9s_license_type' >Project License </label></p> <p><input id='m9s_license_type' name='m9s_license'  type='text' value='$licenseType' /></p>";
	echo "<p><label for='m9s_demo_url' >Demo URL </label></p> <p><input id='m9s_demo_url' name='m9s_demo'  type='url' value='$demoLink' /></p>";
	echo "<p><label for='m9s_project_img' >Project Image URL </label></p> <p><input id='m9s_project_img' name='m9s_proj_img'  type='url' value='$projectImg' /></p>";
}

function project_Metadata_Save(){
	global $post;
	update_post_meta($post->ID, 'm9s_repo_link', $_POST['m9s_repo']);
	update_post_meta($post->ID, 'm9s_license_type', $_POST['m9s_license']);
	update_post_meta($post->ID, 'm9s_demo_url', $_POST['m9s_demo']);
	update_post_meta($post->ID, 'm9s_project_img', $_POST['m9s_proj_img']);
}

## URL Redirection | Register Custom Post Type
function m9s_redirect_svc() {
	$labels = array(
		'name'                => 'Addresses',
		'singular_name'       => 'URL',
		'menu_name'           => 'URL Redirect',
		'parent_item_colon'   => 'Parent Address:',
		'all_items'           => 'All Addresses',
		'view_item'           => 'View Address',
		'add_new_item'        => 'Add New URL',
		'add_new'             => 'New URL',
		'edit_item'           => 'Edit URL',
		'update_item'         => 'Update URL',
		'search_items'        => 'Search Addresses',
		'not_found'           => 'No addresses found',
		'not_found_in_trash'  => 'No addresses found in Trash',
	);

	$rewrite = array(
		'slug'                => 'm',
		'with_front'          => false,
		'pages'               => false,
		'feeds'               => false,
	);

	$args = array(
		'label'               => 'm9s_redirector',
		'description'         => 'Create URL Redirections to other sites',
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor',),
		'hierarchical'        => true,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 25,
		'menu_icon'           => plugins_url( 'icons/url.png' , __FILE__ ),
		'can_export'          => true,
		'has_archive'         => false,
		'exclude_from_search' => true,
		'publicly_queryable'  => true,
		'rewrite'             => $rewrite,
		'capability_type'     => 'post',
	);

	register_post_type( 'm9s_redirector', $args );
}

## Link Post | Register Custom Post Type
/*
function m9s_link_cpt(){
	 $labels = array(
		'name'               => 'Link Posts',
		'singular_name'      => 'Link Post',
		'menu_name'          => 'Link Posts',
		'parent_item_colon'  => 'Parent Item:',
		'all_items'          => 'All Items',
		'view_item'          => 'View Link Posts',
		'add_new_item'       => 'Add New Link Post',
		'add_new'            => 'New Link Post',
		'edit_item'          => 'Edit Link Post',
		'update_item'        => 'Update Link Post',
		'search_items'       => 'Search Link Posts',
		'not_found'          => 'No items found',
		'not_found_in_trash' => 'No items found in Trash',
	 );

	 $rewrite = array(
		'slug'       =>	'links',
		'with_front' => true,
		'pages'      => true,
		'feeds'      => false,
	 );

	 $args = array(
		'label'               => 'm9s_link_post',
		'description'         => 'Manage Link Posts.',
		'labels'               => $labels,
		'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'revisions', 'custom-fields',),
		'hierarchical'        => true,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 25,
		'menu_icon'           => '/m9s/wp-content/plugins/m9s-site/icons/linkpost.png',
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'rewrite'             => $rewrite,
		'capability_type'     => 'post',
	  );

	 register_post_type( 'm9s_portfolio', $args );
}
*/
// Hook into the 'init' action
add_action( 'init', 'm9s_redirect_svc', 0 );
add_action( 'init', 'm9s_portfolio_cpt', 0 );
add_action( 'init', 'm9s_projects_cpt', 0 );
##add_action( 'init', 'm9s_link_cpt', 0 );

# Hook custom fields into admin
add_action('admin_init', 'admin_init');

function admin_init(){
	add_meta_box("project-metadata", 
		"Project Attributes",
		 "m9s_projects_cpt_meta_opt",
		 "m9s_projects", 
		 "side", "low");
	add_meta_box("portfolio-metadata", 
		"Portfolio Metadata", 
		"m9s_portfolio_cpt_meta_opt",
		"m9s_portfolio", 
		"normal", "core");
}


# Setup Transients for Footer Links
$M9S_TimeToLive = WEEK_IN_SECONDS * 2;

## Setup Hook to refresh transient for links on link - Update, Creation, Delete
function refreshFooterData(){
	delete_transient('socialmedia-ftlinks');
	delete_transient('shoutout-ftlinks');
	delete_transient('bio-ftlinks');
}
add_action('add_link', 'refreshFooterData');
add_action('delete_link', 'refreshFooterData');
add_action('edit_link', 'refreshFooterData');

## social media
if(false === ($value = get_transient('socialmedia-ftlinks'))){
	set_transient('socialmedia-ftlinks', transientsFooterLinks('social'), $M9S_TimeToLive );
}

## shoutout
if(false === ($value = get_transient('shoutout-ftlinks'))){
	set_transient('shoutout-ftlinks', transientsFooterLinks('shoutout'), $M9S_TimeToLive );
}

## ventures
// if(false === ($value = get_transient('ventures-ftlinks'))){
// 	set_transient('ventures-ftlinks', transientsFooterLinks('ventures'), $M9S_TimeToLive );
// }


function transientsFooterLinks($linkType){
	switch ($linkType) {
		case 'social':
			$social = wp_list_bookmarks('category=3&title_li=&categorize=0&echo=0');
			return $social;
			break;
		case 'shoutout':
			$shoutout = wp_list_bookmarks('category=4&title_li=&categorize=0&echo=0');
			return $shoutout;
			break;
		case 'ventures':
			// $ventures = wp_list_bookmarks('category=5&title_li=&categorize=0&echo=0&before=&after=');
			// return $ventures;
			break;
		case 'bio':
			// This is in the theme function file
			break;
		default:
			// Nothing Match, exit without doing anything
			break;
	}
}




# Theme Development Functions

$themeDev = false;

if($themeDev){

	# Database Queries Echo to Site.
	add_action( 'wp_footer', 'tcb_note_server_side_page_speed' );

	function tcb_note_server_side_page_speed() {
		date_default_timezone_set( get_option( 'timezone_string' ) );
		$stats  = '[ ' . date( 'Y-m-d H:i:s T' ) . ' ] ';
		$stats .= 'Page created in ';
		$stats .= timer_stop( $display = 0, $precision = 2 );
		$stats .= ' seconds from ';
		$stats .= get_num_queries();
		$stats .= ' queries';

		global $wpdb;
		echo "<pre><code class='php-lang'>";
		var_dump($wpdb->queries);
		echo "<hr />$stats";
		echo "</code></pre>";

		if( ! current_user_can( 'administrator' ) ) $content = "\n<!-- $stats -->\n\n";
		echo $content;
	}


}