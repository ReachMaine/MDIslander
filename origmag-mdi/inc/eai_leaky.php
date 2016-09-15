<?php 

/* functions & setting for adding leakypay wall to */

/*
	mods:
		- remove custom post type for e-edition.
		23Jan15 zig - add welcome shortcode;
		19Jun15 zig - remove expired date from message if not validated.
		31july15 zig - change eai_leaky_loginmenu() s.t. only have one link in header (cache-ing problem).
					- add logoutbutton shortcode & make links on membership pages look like buttons (add class="prl-button")
*/

/*  shortcode for leaky_payway_yourein  using function do_eai_paywall_yourein

	Displayed when a user logs in on the membership/welcome page.
*/
if ( !function_exists( 'do_eai_paywall_welcome') ) {
	function do_eai_paywall_welcome() {
		global $current_user;
		$eai_this_paper = "MDIslander";
		$eai_circ_phone = "207 667-2576";
		$eai_circ_email = "membership@mdislander.com";
		$eai_with_subscript_get = '<h3 class="eai-with-head">With your subscription, you get...</h3>[template id="7835"]';
		$result = "";
		$eai_user = get_current_user_id();
		if ($eai_user != 0) {
			get_currentuserinfo();
			$result .= '<div class="prl-grid eai-leaky-welcome-box">';
			$result .= '<div class="prl-span-12 eai-leaky-welcome-user">';
			$display_name = $current_user->user_login;
			if ($current_user->user_firstname != "") {
				$display_name = $current_user->user_firstname;
			} else {
				if ($current_user->display_name != "") {
					$display_name = $current_user->display_name;
				}
			}
			$result .= '<h2 class="eai-welcome-text">Welcome <span class="eai-login-name">'.$display_name.'</span>.</h2>';
			$result .= '<p>You are logged in!</p>';
			$result .= '<p>You may change your password at any time.  <a class="prl-button" href="'.get_bloginfo('url').'/membership/my-account">My Account</a></p>';
			$logout_url = "";
			if (function_exists('get_leaky_paywall_settings')){
				$lk_settings = get_leaky_paywall_settings();
				$logout_url = get_page_link( $lk_settings['page_for_login'] );
			}
			$result .= '<p><a class="prl-button" href="'.wp_logout_url( $logout_url ).'">Log Out</a>';
			$result .= '</div>'; // end eai-leaky-welcome-user
			$result .= '</div>'; // end eai-leaky-welcomebox

			if (function_exists( 'is_issuem_leaky_subscriber_logged_in') ) {
				 if (is_issuem_leaky_subscriber_logged_in()) {
				 	$result .= '<h2 class="eai-leaky-welcome-where">Where to?<h2>';
				 	$result .= '<div class="prl-grid eai-leaky-welcome-subscribedbox">';
				 	$last_read = $_SESSION["eai_last"];

				 	if ($last_read != '') {
				 		$result .= '<div class="prl-span-4 eai-leaky-welcome-home">';
				 	} else {
				 		$result .= '<div class="prl-span-6 eai-leaky-welcome-home">';
				 	}
 					$result .= '<a href="'.get_home_url().'">'.'<h3 class="eai-where-to">Take Me to the Homepage</h3></a>';
				 	$result .= '<a href="'.get_home_url().'">'.get_the_post_thumbnail().'</a>';
				 	$result .= '</div>'; // end home
					
					if ($last_read != '') {
				 		$result .= '<div class="prl-span-4 eai-leaky-welcome-edig">';
				 	} else {
				 		$result .= '<div class="prl-span-6 eai-leaky-welcome-edig">';
				 	}
					$eedition = get_page_by_path('digital-edition',"OBJECT",'page');
 				 	$result .= '<a href="'.get_permalink($eedition->ID).'"><h3 class="eai-where-to" >Access My Digital Edition</h3></a>';	
				 	$result .= '<a href="'.get_permalink($eedition->ID).'">'.get_the_post_thumbnail($eedition->ID).'</a>';

				 	$result .= '</div>'; // end eedition


				 	if ($last_read != '') {
						$refer_url = get_permalink($last_read);
						$refer_title = get_the_title($last_read);
						/*$result .= '<p>Click <a href="'.$refer_url.'">here</a> to continue reading...'.$refer_title."</p>";*/

						$result .= '<div class="prl-span-4 eai-leaky-welcome-goback">';
							$result .= '<h3 class="eai-leaky-continue"><a  href="'.$refer_url.'">Continue reading...'.$refer_title.'</a></h3>';
							$result .= '<a href="'.$refer_url.'" title="'.$refer_title.'">';
							$result .= get_the_post_thumbnail($last_read, 'ea_featuredcrop');
							$result .= '</a>';
						$result .= '</div>'; // end of goback
						//unset($_SESSION['eai_last']);// clear out session var if get here.

					}


					$result .= '</div>'; // end welcome-subscribedbox
				 } else { // logged into WP, but not valid leaky subscription....
				 	$result .= '<div class="eai-leak-ngbox">';
				 	$exp = eai_get_leaky_expires($current_user->ID); // get the expiration date
					if ( !empty($exp )) {
				 		$result .= '<h2 class="eai-leaky-expired">Subscription Expired</h2>';
				 		$result .= '<p class="eai-ng-explain"> According to our records, your subscription to <i>'.$eai_this_paper.'</i> expired or is not longer valid. ';
				 		$result .= '<br>If you feel this is an error and your subscription is current, try clicking on the "Log Out" button toward the top of the page. <br>Logging out and logging back in may resolve the issue.';
				 		$result .= '.</p>';
				 	 	$result .= '<p class="eai-ng-fixit">If the problem continues please contact us at <a href="tel:'.$eai_circ_phone.'">'.$eai_circ_phone.'</a> or ';
				 		$result .= $eai_circ_email.'</p>';
						$result .= '</div>'; // end of ngbox.
				 	} else {
				 		$result .= '<p class="eai-ng-explain"> According to our records, you do not have a subscription with <i>'.$eai_this_paper.'</i>.</p>';			 		
				 		$result .= '<p class="eai-ng-fixit">If you feel this is an error, please contact our circulation department at <a href="tel:'.$eai_circ_phone.'">'.$eai_circ_phone.'</a> or ';
					 	$result .= $eai_circ_email.'</p>';
						$result .= '</div>'; // end of ngbox.
				 	}
					$result .= '<div class="eai-with-subscript">'.do_shortcode($eai_with_subscript_get).'</div>';
				 }
			} 
			/* some stuff for testing */
			/* $result .= '<!--'; 
			$result .= ' user id: '. $current_user->ID.'<br />';
			$result .= 'user email: '.$current_user->user_email.'</br>';
		 	$result .= 'session email: '.$_SESSION['issuem_lp_email'];	
		 	$result .= '<br /> user_hash = '.get_user_meta($current_user->ID, '_issuem_leaky_paywall_live_hash', true);
		 	$result .= '<br /> cookie_hash = '.$_COOKIE['issuem_lp_subscriber'];
		 	$result .= '<br /> session_hash: '.$_SESSION['issuem_lp_subscriber'];
		 	$result .= '<br /> meta_expires = '.get_user_meta($current_user->ID, '_issuem_leaky_paywall_live_expires', true);
		 	$result .= '<br /> leaky_expires via hash = '.leaky_paywall_has_user_paid( $_SESSION['issuem_lp_email'] );
			 $result .= '-->'; */
		} else { // not logged to wordpress, but got here.....
			// redirect user to login page
			$redir = get_page_by_path('membership/login');
			$redir_link = get_permalink($redir->ID);
			wp_redirect($redir_link);
			$result .= "<a href=".$redir_link.">Please login in.</a>";
		}
		
		return $result;
	}
	add_shortcode( 'ea_leaky_paywall_welcome', 'do_eai_paywall_welcome' );
} // end leaky_payway_welcome
/*
 * function to get the leaky subscription expiration date
 * returns: string containing date, empty string if not there. 
 */
function eai_get_leaky_expires($in_user_id) {
	$out_expire = "";
	if ( function_exists( 'get_leaky_paywall_settings') )  {
		$lk_settings = get_leaky_paywall_settings();
		$mode = 'off' === $lk_settings ['test_mode'] ? 'live' : 'test';

		$str_expire = get_user_meta($in_user_id,'_issuem_leaky_paywall_' . $mode . '_expires', true);
		if (!empty($str_expire)) {
			$out_expire  = date('M d, Y', strtotime($str_expire));
		}
	}
	return $out_expire;
}
/*  leaky_paywall_continue_reading

	if hit the nag and login or pay, be able to go back to what you are reading.

*/
if ( !function_exists( 'do_eai_paywall_thanks') ) {
	function do_eai_paywall_thanks() {
		global $current_user;
		$result = "";

		$result .= '<div class="prl-grid eai-leaky-thanksbox">';

		if (function_exists( 'is_issuem_leaky_subscriber_logged_in') ) {
			$query_str = $_SERVER['QUERY_STRING'];
			if ( is_issuem_leaky_subscriber_logged_in() ) {
				/* user is logged in  */

				get_currentuserinfo();
				$result .= '<div class="prl-span-6 eai-leaky-thanks">';
				if ( $query_str == 'issuem-leaky-paywall-stripe-return=1' ) {
					/* user is just paid - on the stripe success */
					$result .= '<h3>Thank you for Subscribing!</h3>';
					$result .= '<p>Your username is <span class="eai-login-name">'.$current_user->user_login.'</span>.</p>';
					$result .= '<p>Please Note:</br>You should recieve an email containing your password shortly.  You may change your password at any time.  <a class="prl-button" href="'.get_bloginfo('url').'/membership/my-account">My Account</a></p>';
					$result .= '<p>For your convenience, we suggest you save your username and password in your browser when prompted.</p>';

				} else {
					$display_name = $current_user->user_login;
					if ($current_user->user_firstname != "") {
						$display_name = $current_user->user_firstname;
					} 
					$result .= '<p>Welcome <span class="eai-login-name">'.$display_name.'</span>.</p>';
					$result .= '<p>You may change your password at any time.  <a class="prl-button" href="'.get_bloginfo('url').'/membership/my-account">My Account</a> </p>';
				}
				$result .= '</div>';

				$last_read = $_SESSION["eai_last"];
				if ($last_read != '') {
					$refer_url = get_permalink($last_read);
					$refer_title = get_the_title($last_read);
					/*$result .= '<p>Click <a href="'.$refer_url.'">here</a> to continue reading...'.$refer_title."</p>";*/
					$result .= '<div class="prl-span-6">';
						$result .= '<h3 class="eai-leaky-continue"><a  href="'.$refer_url.'">Click here to continue reading...</a></h3>';
						$result .= build_referer($last_read);
					$result .= '</div>';
					unset($_SESSION['eai_last']);// clear out session var if get here.
				}
			
			} else {
				if (is_user_logged_in()) {
					/* $result .= '<p>Your subscription has expired. Please sign up below to renew.</p>'; */
					$result .= '<p>Please Note:  Subscriptions to <i>The Ellsworth American</i> and the <i>MDIslander</i> are separate.  To subscribe to ';
					$result .= $eai_this_paper;
					$result .= ' sign up below. ';
				}
			}
		} else {
$result .= '<div class="is_issuem_leaky_subscriber_logged_in_not_defined"> </div>';
		}
		$result .= '</div>'; // end row/panel.
		return $result;
	}
	add_shortcode( 'leaky_paywall_thanks', 'do_eai_paywall_thanks' );
}
function build_referer($in_post_id) {
	$refer_title = get_the_title($in_post_id);
	$refer_link = get_permalink($in_post_id);
	$results .= '<div class="prl-span-8">';
        $results .= '<div class="prl-grid">';
			$results.=  '<div class="prl-span-6">';
        		$results.=  '<article class="prl-article">';
        			$results .= '<a href="'.$refer_link.'" title="'.$refer_title.'">';
						$results .= get_the_post_thumbnail($in_post_id, 'ea_featuredcrop');
					$results .= '</a>';
				$results.=  '</article>';
        	$results .= '</div>';
        	$results.=  '<div class="prl-span-6">';
        		$results.=  '<article class="prl-article">';
        			$results.=  '<h3 class="prl-article-title"><a href="'.$refer_link.'" title="'.$refer_title.'">'.$refer_title.'</a></h3>';
					$results .= '<p>'.get_excerpt_by_id($in_post_id).'</p>';
        		$results.=  '</article>';
         	$results.=  '</div>';    
        $results .= '</div>';
    $results .= '</div>';
    return $results;
}
function build_thanks() {
	global $current_user;
	get_currentuserinfo();
	$result_html = "";
	$result_html .= '<p> Your username is <span class="eai-login-name">'.$current_user->user_login.'</span>.</p>';
	$result_html .= '<p>You should recieve and email containing your password shortly.  You may change your password at any time.  Just visit the site and click <a href="'.get_bloginfo('url').'/membership/my-account">My Account</a> in the main menu.</p>';
	$result_html .= '<p>For your convenience, we suggest you save your username and password in your browser when prompted.</p>';

	return $result_html;
}
if ( !function_exists( 'eai_logout_process' ) ) {
	/**
	 * Removes session variables for EA Leaky Paywall subscriber
	 *
	 */
	function eai_logout_process() {
		unset( $_SESSION['eai_last'] );
	}
	add_action( 'wp_logout', 'eai_logout_process' ); //hook into the WP logout process
}


if (!function_exists('get_excerpt_by_id')) {
	function get_excerpt_by_id($post_id){
	    $the_post = get_post($post_id); //Gets post ID
	    $the_excerpt = $the_post->post_content; //Gets post_content to be used as a basis for the excerpt
	    $excerpt_length = 15; //Sets excerpt length by word count
	    $the_excerpt = strip_tags(strip_shortcodes($the_excerpt)); //Strips tags and images
	    $words = explode(' ', $the_excerpt, $excerpt_length + 1);

	    if(count($words) > $excerpt_length) :
	        array_pop($words);
	        array_push($words, '…');
	        $the_excerpt = implode(' ', $words);
	    endif;

	    return $the_excerpt;
	}
}

/*
	functino to display the  my account in header (not login/subscribe or hello/logout  anymore).
*/

if (!function_exists('eai_leaky_loginmenu')) {
	function eai_leaky_loginmenu(){

		$out_string = "";
		if (function_exists( 'get_leaky_paywall_settings') ) {
			$lp_settings = get_leaky_paywall_settings();
			$login_url = get_page_link( $lp_settings['page_for_login'] );
			$out_string .= '<ul class="ea-leaky-login">';
			$out_string .=    '<li>';
			$out_string .=       '<a href="'.$login_url.'">My Account</a>';
			$out_string .=    "</li>";
			$out_string .= "</ul>";
		}
		return $out_string;
	}
} 
if (!function_exists('cimy_update_ExtraFields')) {
	add_action('profile_update','cimy_update_ExtraFields'); // zig
}

add_action('profile_update','eia_update_cimy'); // zig
if (!function_exists('eia_update_cimy')) {
	function eia_update_cimy() {
		
	}
}
add_action( 'wp_login_failed', 'custom_login_fail', 10, 1 );  // hook failed login
function custom_login_fail( $username ) {
	$referrer = $_SERVER['HTTP_REFERER'];  // where did the post submission come from?
   // if there's a valid referrer, and it's not the default log-in screen
	wp_redirect( $referrer . '?login=failed' ); 
   if ( !empty($referrer) && !strstr($referrer,'wp-login') && !strstr($referrer,'wp-admin') ) {
   	 $pos = strpos($referrer, '?login=failed');
        if($pos === false) {
            // add the failed
            wp_redirect( $referrer . '?login=failed' );  // let's append some     information (login=failed) to the URL for the theme to use
        }
        else {
            // already has the failed don't appened it again
            wp_redirect( $referrer );  // already appeneded redirect back
        }   
 
      exit;
   }
}
/* still have issue when no username or password.  
see http://wordpress.stackexchange.com/questions/95614/login-failed-only-attached-to-url-under-certain-circumstances
add_action( 'wp_authenticate', 'my_front_end_login_fail', 1, 2 );

function my_front_end_login_fail( $user, $pwd ) { 
	 if ( ! empty( $user ) && ! empty( $pwd ) && ! is_wp_error( $user ) )
        return false;
} */
add_shortcode('logout_button', 'ea_logout_link');
function ea_logout_link() {
	$logout_url = home_url();
	$out_html = '<a href="'.wp_logout_url($logout_url).'" class="prl-button">Logout</a>';
	return $out_html;
}


?>