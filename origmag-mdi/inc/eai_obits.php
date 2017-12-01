<?php
/******* Custom Post type for Obits ***********/
/* 29Nov17 zig - change post type to obituary (instead of ea_obit) */
add_action ('init', 'create_obit_posttype');
if (!function_exists('create_obit_posttype')) {
	function create_obit_posttype() {
		 /* zig xout - old CPT register_post_type( 'ea_obit',
		    array (
		      'labels' => array(
		        'name' => __( 'Obits' ),
		        'singular_name' => __( 'Obit' )
		    ),
		      'taxonomies' => array('category', 'post_tag'),
		      'public' => true,
		      'has_archive' => true,
		      'supports' => array( 'title', 'editor', 'excerpt', 'custom-fields', 'thumbnail' ),
		      'rewrite' => array('slug' => 'obituary'),
		)   ); */
		register_post_type( 'obituary',
			 array (
				 'labels' => array(
					 'name' => __( 'Obituaries' ),
					 'singular_name' => __( 'Obituary' )
			 ),
				 'taxonomies' => array('category', 'post_tag'),
				 'public' => true,
				 'has_archive' => true,
				 'supports' => array( 'title', 'editor', 'excerpt', 'custom-fields', 'thumbnail' ),
				 'rewrite' => array('slug' => 'obituary'),
	 )   );
	}
}
?>
