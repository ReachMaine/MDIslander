<?php
/*
	30Dec14 - zig turn off admin bar at top of front end
	28Jan15 - zig move obits custom post type to separate file
	29Jan15 - add ea_text-block pagebuilder block.
	11May15 - zig:  add image size for facebook share.  & filter s.t. Yoast SEO uses it.
	18Sept15 - zig:  add eacat_sport page build block.
	31jan16 - zig:  add technavia paywall login call (take out leaky)
	11Nov16 - zig:  add widget area for house ad on homepage under featured box
*/
// add_image_size( 'ea_featuredimg', 600, 900, false ); // size of single post thumbnail
//add_image_size( 'ea_thumbnail', '150', '600', false );

/*** images/media  ***/
add_image_size( 'facebook_share', 1200, 630, true );
add_image_size( 'ea_featuredimg', 1200, 800, false );
add_image_size( 'ea_featuredcrop', 1200, 800, true );
add_image_size( 'ea_story', 600, 400, false );
add_image_size( 'ea_storycrop', 600, 400, true );
add_image_size( 'ea_verticalcrop', 400, 600, true);
add_image_size( 'ea_vertical', 400, 600, false);

function ea_custom_image_sizes($sizes) {
	return array_merge( array(
        'ea_featuredimg' => __( 'EA Featured' ),
        'ea_featuredcrop' => __( 'EA Featured cropped' ),
        'ea_story' => __( 'EA in story' ),
        'ea_storycrop' => __( 'EA in story cropped' ),
        'ea_vertical' => __( 'EA vertical' ),
        'ea_verticalcrop' => __( 'EA veritcal cropped' ),
        'facebook_share' => __('Facebook Share'),
    ), $sizes );
}

add_filter('wpseo_opengraph_image_size', 'mysite_opengraph_image_size');
function mysite_opengraph_image_size($val) {
	return 'facebook_share';
}

add_action('after_setup_theme', ea_setup);
require_once(get_stylesheet_directory().'/inc/eai_custom_functions.php');
require_once(get_stylesheet_directory().'/inc/eai_election_results.php');
require_once(get_stylesheet_directory().'/inc/eai_leaky.php');
require_once(get_stylesheet_directory().'/inc/eai_obits.php');
require_once(get_stylesheet_directory().'/inc/eai_technav.php');

if ( function_exists('register_sidebar') ){
		// Banner Ad
		 register_sidebar(array(
			'name' => 'Breaking News',
			'id' => 'breaking-news',
			'description' => 'Place for Breaking News above content',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3><div class="tx-div small"></div>',
		));

		/* add top banner ad widget */
		 register_sidebar(array(
			'name' => 'Top Banner Ad',
			'id' => 'topbanner',
			'description' => 'Widget for a targetted banner ad.',
			'before_widget' => '<div class="prl-span-12"><div id="%1$s" class=" %2$s ad-container">',
			'after_widget'  => '</div></div>'

		));

		register_sidebar(array(
			'name' => 'Biz Today Ads',
			'id' => 'biztoday',
			'description' => 'Widget for Business today.',
			'before_widget' => '<div class="prl-span-12"><div id="%1$s" class=" %2$s biztoday-container">',
			'after_widget'  => '</div></div>'

		));
		register_sidebar(array(
			'name' => 'Home Page House ad',
			'id' => 'eai-house-ad',
			'description' => 'House ad space under featured box.',
			'before_widget' => '<div class="prl-span-12"><div id="%1$s" class=" %2$s ad-container">',
			'after_widget'  => '</div></div>'
		));
	}

	function zig_remove_columns( $columns ) {

	// remove the Yoast SEO columns
	unset( $columns['wpseo-score'] );
	unset( $columns['wpseo-title'] );
	unset( $columns['wpseo-metadesc'] );
	unset( $columns['wpseo-focuskw'] );
	unset( $columns['3wp_broadcast']);
	unset( $columns['EPVC']);
	unset( $columns['expirationdate']);
	unset( $columns['woosidebars_enable']);

	return $columns;

}
/* ea_setup function
*  init stuff that we have to init after the main theme is setup.
*
*/
function ea_setup() {

	add_filter ( 'manage_edit-post_columns', 'zig_remove_columns' ); //remove colunms from all posts page.
	/* add favicons for admin */
	add_action('login_head', 'add_favicon');
	add_action('admin_head', 'add_favicon');

	/* add the topnav menu block */
	register_nav_menu('top_nav', 'Top Nav');
	/* register the featured block  */

	/*require_once ( get_stylesheet_directory() . '/page-builder/blocks/aq-eafeature-block.php');
	 aq_register_block('AQ_eafeature_Block');  */

	 if(class_exists('AQ_Block')) {

	    define('AQPB_CUSTOM_DIR', get_stylesheet_directory() . '/page-builder/');
	    define('AQPB_CUSTOM_URI', get_stylesheet_directory()  . '/page-builder/');

	    //include the block files
	    require_once(AQPB_CUSTOM_DIR . 'blocks/eafeature-block.php');
	   	require_once(AQPB_CUSTOM_DIR . 'blocks/eacat_landing.php');
		require_once(AQPB_CUSTOM_DIR . 'blocks/ea_tag_columns.php');
		require_once(AQPB_CUSTOM_DIR . 'blocks/ea_multitag_columns.php');
		require_once(AQPB_CUSTOM_DIR . 'blocks/ea_home2-block.php');
		require_once(AQPB_CUSTOM_DIR . 'blocks/eacat_landingplus.php');
		require_once(AQPB_CUSTOM_DIR . 'blocks/ea_calendar.php');
		require_once(AQPB_CUSTOM_DIR . 'blocks/ea_text-block.php');
		require_once(AQPB_CUSTOM_DIR . 'blocks/eacat_sports.php');

	    //register the blocks
	    aq_register_block('eafeature_Block');
	    aq_register_block('eacat_landing');
	    aq_register_block('ea_tag_cols');
	    aq_register_block('ea_multitag_cols');
	    aq_register_block('ea_home2');
	    aq_register_block('eacat_landingplus');
	    aq_register_block('ea_calendar');
	    aq_register_block('ea_text_block');
	    aq_register_block('eacat_sports');
	}
}
/* returns array of all tags - used in page-builder block ea_tag_columns*/
	function get_array_tags(){
		$tags		= array();
		$tags [0] = 'All Tags';
		$tags_obj 	= get_tags();
		$i = 1;
		foreach ($tags_obj as $tag) {
			$tags[$tag->slug] = $tag->name;
			$i++;
		}
		return $tags;
	}

 /* returns true if current post is the subcategory (one level) of the given parent category
 	ex:  post is in baseball cat which is a subcat of sports, returns true for 'sports' as parent_cat arg */
 function in_subcategory($parent_cat) {
 	$parent_catobj = get_category_by_slug($parent_cat);
 	$in_sub = false;
 	$post_catsarray = get_the_category();

 	foreach ($post_catsarray as $postcat) {
 		//echo '<!-- '.$postcat->name.'-->';
 		if ($postcat->category_parent == $parent_catobj->term_id) {
 			$in_sub = true;
 		}
 	}
 	return $in_sub;
 }

/* returns the category slug
*/
 function category_nomiker ($postid=0) {
 	if (!$postid) {
 		$postid = $post->ID;
 	}
 	$retval = "";
 	$cats = get_the_category($postid);
 	foreach ($cats as $cat) {
 		switch($cat->name) {
 			case 'Obituaries':
 				$retval ='obits';
 				break;

 		}
 	}
 	return $retval;

 } /* end of function category_image */

/* replacing social_share in original theme /inc/custom_functions.php */
function ea_social_share(){
global $theme_url, $image;
?>

<ul class="prl-list prl-list-sharing">
	<li><a href="http://www.facebook.com/share.php?u=<?php the_permalink();?>" target="_blank"><i class="fa fa-facebook-square"></i> </a></li>
	<li><a href="http://twitter.com/home?status=<?php the_title_attribute();?> - <?php the_permalink();?>" target="_blank"><i class="fa fa-twitter-square"></i> </a></li>
	<li><a href="https://plus.google.com/share?url=<?php the_permalink();?>" onClick="javascript:window.open(this.href,&#39;&#39;, &#39;menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=320,width=620&#39;);return false;"><i class="fa fa-google-plus-square"></i></a></li>
	<li><a href="http://pinterest.com/pin/create/button/?url=<?php the_permalink();?>&media=<?php echo $image[0];?>" class="pin-it-button" count-layout="horizontal" onClick="javascript:window.open(this.href,&#39;&#39;, &#39;menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=320,width=620&#39;);return false;"><i class="fa fa-pinterest-square"></i></a></li>
	<li><a href="http://www.linkedin.com/shareArticle?mini=true&url=<?php the_permalink();?>&title=<?php the_title_attribute();?>" target="_blank"><i class="fa fa-linkedin-square"></i></a></li>
	<li><a href="mailto:?subject=<?php the_title_attribute();?>&body=<?php the_permalink();?>" target="_blank"><i class="fa fa-envelope"></i></a></li>
	<li><a href="#" onclick="window.print();" id="print-page" ><i class="fa fa-print"></i></a></li>
</ul>

<?php
}


/* find the nth occurance of needle in haystack */
 function strnpos($haystack, $needle, $occurance, $pos = 0) {

        for ($i = 1; $i <= $occurance; $i++) {
            $pos = strpos($haystack, $needle, $pos) + 1;
        }
        return $pos - 1;

    }


function add_favicon() {
  	$favicon_url = get_stylesheet_directory_uri() . '/images/admin-favicon.ico';
	echo '<link rel="shortcut icon" href="' . $favicon_url . '" />';
}

/* add_filter('show_admin_bar', '__return_false'); /* 30Dec14 - zig turn off admin bar at top of front end  */
add_action('after_setup_theme', 'remove_admin_bar');
function remove_admin_bar() {
	if (!current_user_can('webmaster') && !is_admin()) {
	  show_admin_bar(false);
	}
}

//* add a Lost password link to the login form */
add_action( 'login_form_bottom', 'add_lost_password_link' );
function add_lost_password_link() {
	return '<a class="eai-lost-password" href="http://mdislander.com/wp-login.php?action=lostpassword">Lost Password?</a>';
}

function eai_lost_password_logolink() {
    // Check if have submitted
    $confirm = ( isset($_GET['action'] ) && $_GET['action'] == resetpass );
    if( $confirm ) {
        wp_redirect( 'http://www.mdislander.com/membership/login/' );
        exit;
    }

    $lostpass = ( isset($_GET['action'] ) && $_GET['action'] == lostpassword );
    if ($lostpass) {

    }
    return get_bloginfo( 'url' );
}
 add_action('login_headerurl', 'eai_lost_password_logolink'); // change the logo link

/* change the logo on the login page */
 function my_login_logo() { ?>
    <style type="text/css">
        body.login div#login h1 a {
            background-image: url(<?php echo get_stylesheet_directory_uri(); ?>/images/islander_teal.jpg);
            padding-bottom: 30px;
        }
    </style>
<?php }
add_action( 'login_enqueue_scripts', 'my_login_logo' );

/* this didnt work.... trying to links to bottom of login screen
function eai_login_links() {
	$new_links = '';
	$new_links .= '<a href="http://www.mdislander.com/membership/login/">Back to MDIslander.</a>';
	return $new_links;
}

add_action('login_footer', 'eai_login_links'); */
function eai_login_message($message) {
 if ( empty($message) ){
        return "<p class='message'>Welcome to the MDIslander.</p>";
    } else {
        return $message;
    }
}
add_filter( 'login_message', 'eai_login_message' );

// allow Shayla to edit users, I think
function mc_admin_users_caps( $caps, $cap, $user_id, $args ){

    foreach( $caps as $key => $capability ){

        if( $capability != 'do_not_allow' )
            continue;

        switch( $cap ) {
            case 'edit_user':
            case 'edit_users':
                $caps[$key] = 'edit_users';
                break;
            case 'delete_user':
            case 'delete_users':
                $caps[$key] = 'delete_users';
                break;
            case 'create_users':
                $caps[$key] = $cap;
                break;
        }
    }

    return $caps;
}
add_filter( 'map_meta_cap', 'mc_admin_users_caps', 10, 4 );

// filter to wrap embedded videos (youtube) in div ss.t. we can style it
add_filter('embed_oembed_html', 'wrap_embed_with_div', 10, 3);
function wrap_embed_with_div($html, $url, $attr) {
        return '<div class="ea-responsive-container">'.$html.'</div>';
}

/* EOF */
?>
