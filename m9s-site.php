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
	    wp_register_script( 'jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js',false,'1.9.1',true);
	    wp_enqueue_script( 'jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js',false,'1.9.1',true ); 
	}
}

function jQuery_bkp(){
?>
<script>window.jQuery || document.write('<script src="js/vendor/jquery-1.9.0.min.js"><\/script>')</script>
<?php
}

add_action('wp_footer', 'jQuery_inject', 1);
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

		echo '<a class="feature-comments feature" title="Feature" data-comment_id="'.$id.'" data-do="feature">Feature</a>
		<a class="feature-comments unfeature feature" title="Unfeature" data-comment_id="'.$id.'" data-do="unfeature">Unfeature</a>';
		echo '<a class="feature-comments bury" title="Bury" data-comment_id="'.$id.'" data-do="bury">Bury</a>
		<a class="feature-comments unbury bury" title="Unbury" data-comment_id="'.$id.'" data-do="unbury">Unbury</a>';

		echo ' <a class="del" href="'.get_bloginfo('wpurl').'/wp-admin/comment.php?action=cdc&c='.$id.'">Delete</a> ';
		echo ' <a class="spam" href="'.get_bloginfo('wpurl').'/wp-admin/comment.php?action=cdc&dt=spam&c='.$id.'">Spam</a>';
	}
} 

# Security
// No Announcing of for Fail Login
add_filter('login_errors',create_function('$a', "return null;"));

# Custom Post Types

## Portfolio | Register Custom Post Type
function m9s_portfolio_cpt(){
	 $labels = array(
		'name'               => 'Portfolio',
		'singular_name'      => 'Peice',
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
		'menu_icon'           => '/m9s/wp-content/plugins/m9s-site/icons/portfolio.png',
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'rewrite'             => $rewrite,
		'capability_type'     => 'post',
	  );

	 register_post_type( 'm9s_portfolio', $args );
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
		'menu_icon'           => '/m9s/wp-content/plugins/m9s-site/icons/projects.png',
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'rewrite'             => $rewrite,
		'capability_type'     => 'post',
	  );

	 register_post_type( 'm9s_projects', $args );
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
		'menu_icon'           => '/m9s/wp-content/plugins/m9s-site/icons/url.png',
		'can_export'          => true,
		'has_archive'         => false,
		'exclude_from_search' => true,
		'publicly_queryable'  => true,
		'rewrite'             => $rewrite,
		'capability_type'     => 'post',
	);

	register_post_type( 'm9s_redirector', $args );
}

// Hook into the 'init' action
add_action( 'init', 'm9s_redirect_svc', 0 );
add_action( 'init', 'm9s_portfolio_cpt', 0 );
add_action( 'init', 'm9s_projects_cpt', 0 );