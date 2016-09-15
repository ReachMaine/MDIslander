/* Embed  related posts into content */
function embed_related_content() {

	$zcontent = apply_filters('the_content', get_the_content()); 
	$zcontent1 = "";
	$zcontent2 = "";
	$ptagcount = substr_count($zcontent, '<p>');
	$direction_vert = true;
	if ($zcontent) { // check for empty content
		$thirdp = strnpos($zcontent,'<p>',3); // find the start of the third <p> tag 
		if ($thirdp) { // check for no third p tag
			$zcontent1 = substr($zcontent, 0, $thirdp-1);
			echo $zcontent1;
			$zcontent2 = substr($zcontent,$thirdp,strlen($zcontent));
		} else { 
			echo '<!-- no third string -->';
			echo $zcontent;
			$direction_vert = false;
		}
		if ($direction_vert) {
			echo '<div class="prl-span-3"> <div class="yarpp-thumbnails-vertical">'; 
			related_posts(); 
			echo '</div></div>';
			echo $zcontent2;
		} else { /* put at end of post  */
			/* changed minds, now dont want realted posts if less than 3 p-tags */
			/* echo '<p>----</p>'; // testing */
			/* echo $zcontent2;
			echo '<div class="prl-span-12"><div class="yarpp-thumbnails-horizontal">'; 
			related_posts();  
			echo '</div></div>'; */
		}
	} else { 
		echo '<!-- no content -->'; 
	}

	/* echo '-----------<br>'; var_dump($zcontent);echo '---------<br>'; */	
}	/* end embed_related_content */