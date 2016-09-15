<?php 
/* functions to support tecnavia paywall. 
	31Jan16 zig 
*/

if (!function_exists('eai_technav_loginmenu')) {
	function eai_technav_loginmenu(){

		$out_string = "";
		
			
		$login_url = "http://www.mdislander.com/";
		$out_string .= '<ul class="ea-technav-login">';
		$out_string .=    '<li>';
		$out_string .=       '<a id="ta_account_button" onclick = "ta_account()" href="'.$login_url.'">My Account</a>';
		$out_string .=    "</li>";
		$out_string .= "</ul>";

		return $out_string;
	}
} 