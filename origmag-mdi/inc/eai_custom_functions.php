<?php
/* mods
 8jan19 zig -add embed_ad_signle function
*/
/* custom functions for Ellsworth American, Inc */
/* these functions should be consistent accross EA & MDIslander sties, at least that's the theory */
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


/* replaces post_meta function that original mag uses */
/* differences:
	Add $updated arg - show updated
	The time/date stamp rules:
		1. Home & category pages
			- no time stamp if older than today.
			- if posted today
				show time
			if updated today (within tolerance)
				show "updated" time
*/
function eai_post_meta($date = true, $author=true, $comment=true, $cat=true, $view=true, $updated=false){?>
	<div class="prl-article-meta">
		<?php if($date == true) { echo '<span>'; the_time('F j, Y');  echo '</span>'; } ?>
		<?php if($author==true){?><span><?php _e('by', 'presslayer');?> <a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" rel="author"><?php echo get_the_author(); ?></a></span><?php } ?>
		<?php if($comment==true){?><span><?php comments_popup_link( __('0 comments','presslayer'), __('1 comment','presslayer'), __('% comments','presslayer'), 'comments-link', __('Comments are off','presslayer'));?></span> <?php } ?>
		<?php
		if ( $view == true) {
			$count = get_post_meta(get_the_ID(), 'EasyPostViewCounter', true);
			if(!$count or $count == '') $count = 0;
			if($count == 1) $view_label = __('view','presslayer');
			else $view_label = __('views','presslayer');

			echo '<span>'.$count.' '. $view_label .'</span>';
		}
		if ( $cat == true) :
			$categories_list = get_the_category_list( __( ', ', 'presslayer' ) );
			if ( $categories_list) :
				printf( __( 'on %1$s', 'presslayer' ) , $categories_list );
			endif;
		endif;
		if ($updated) {
			$show_updated = true;
			$str_pubdate = get_the_date();
			$str_pubtime = get_the_time();
			$str_moddate = get_the_modified_date();
			$str_modtime = get_the_modified_time();
			$str_today = date('F j, Y');

			if ($str_pubdate == $str_moddate)  {
				if ($str_pubtime==$str_modtime) {
					$show_updated = false;
				} else {
					$dt_pub = strtotime($str_pubdate.$str_pubtime);
					$dt_mod = strtotime($str_moddate.$str_modtime);
					$dt_tol = $dt_pub + 900; // add 15 minutes.
					echo '<!-- checking for tolerance between '.$dt_pub.' and '.$dt_mod.' and tol is: '.$dt_tol.' -->';
					if ($dt_mod < $dt_tol) {
						echo '<!-- nope -->';
						$show_updated = false;
					}
				}
			}
			if ($show_updated) {
				echo '<span class="eai_updated"> Updated';
				if ($str_pubdate != $str_moddate) {
					echo ' on '.$str_moddate;
				}
				echo' at '.$str_modtime.'</span>';
			}

		} /* end pdated */
		edit_post_link(__('Edit','presslayer'),'&nbsp;','');

		?>
	</div>
<?php
}


	function eai_do_feat($arg_meta = false, $arg_excerpt=false) {
		$num_excerpt = 15;
		?>
		<div class="prl-span-9 ea_feat">
           <div class="prl-grid">
			   <div class="prl-span-12">
               		<article class="prl-article">
						<?php echo post_thumb(get_the_ID(),580, 360, true);?>
					</article>
               </div>
           	   <div class="prl-span-12">
                   <article class="prl-article">
	                    <h3 class="prl-article-title"><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title();?></a> <?php echo get_label_format(get_the_ID());?> <?php echo get_label_meta(get_the_ID());?></h3>
	                    <?php eai_post_meta(true,false,false,false,false);  ?>
	                    <?php if ( ($arg_excerpt) || ($arg_excerpt > 0) ){
	                    	echo '<p>'.text_trim(get_the_excerpt(),$arg_excerpt,'...').'</p>';
	                    } ?>
                    </article>
               </div>
           </div>
        </div>

        <?php
	} /* end do_feat */


	function eai_build_postcol($arg_numcolumns = 3, $arg_coltitle = '', $arg_exerpt = false, $arg_meta=true, $arg_date_only = false) {
		switch($arg_numcolumns){
			case '2':
				$columns = 2;
				$prl_class = 'prl-span-6';
			break;
			case '3':
				$columns = 3;
				$prl_class = 'prl-span-4';
			break;

			case '4':
				$columns = 4;
				$prl_class = 'prl-span-3';
			break;

			default:
				$columns = 2;
				$prl_class = 'prl-span-6';
			break;

		}
		$outstring = '';
		// $outstring .= '<div class="prl-span-4">';
		$outstring .= '<div id="post-'.get_the_ID().'" class="'.implode(' ', get_post_class($prl_class)).'" ">';
		if ($arg_coltitle){
			$outstring .= '<h5 class="prl-block-title">'.$arg_coltitle.'</h5>';
		}
		$outstring .= eai_build_post($arg_exerpt, '', $arg_meta, $arg_date_only);
		/* $outstring .= '<article class="prl-article">';
		$outstring .= post_thumb(get_the_ID(),520, 360, true);
		$outstring .= '<h3 class="prl-article-title">';
		$outstring .= '<a href="'.get_the_permalink().'" title="'.the_title_attribute('echo=0').'" rel="bookmark" >'.get_the_title().'</a>';
		$outstring .= '</h3>';
		//$outstring .= post_meta(true,false,true,false,false); // dont want to output meta here.  need new function -
		if ($arg_exerpt) {
			$outstring .= '<p>'.text_trim(get_the_excerpt(),$num_excerpt,"...").'</p>';
		}
		$outstring .= '</article>'; */
		//$outstring .= '</div>';
		$outstring .= '</div>';
		return $outstring;
	}

	function eai_build_postli( $arg_exerpt = false, $arg_date_only = true ) {
		$outstring = '';
		$outstring .='<li id="post-'.get_the_ID().'" class="'.implode(' ', get_post_class('clearfix')). '" >';
		$outstring .= eai_build_post($arg_exerpt, 'list-thumbnail', true, $arg_date_only);
		$outstring .= '</li>';
		return $outstring;
	}
	function eai_build_post( $arg_exerpt=false, $arg_imageclass = '', $arg_meta = true, $arg_date_only = false) {
		$outstring .='';
		$outstring .= '<article class="prl-article">';

		if (has_post_thumbnail()) {
			if ($arg_imageclass) {
				$outstring .= '<div class = '.$arg_imageclass.'>';
			}
			$outstring .= post_thumb(get_the_ID(),520, 360, true);
			if ($arg_imageclass) {
				$outstring .= '</div>';
			}
		}

		$outstring .= '<h3 class="prl-article-title">';
		$outstring .= '<a href="'.get_the_permalink().'" title="'.the_title_attribute('echo=0').'" rel="bookmark" >'.get_the_title().'</a>';
		$outstring .= '</h3>';
		if ($arg_meta) {
			if (is_category('Obituaries') || $arg_date_only ) {
				$outstring .= eai_build_post_meta(true/*date*/,false/*author*/,false/*comments*/,false/*cat */,false/* views */);
			} else {
				$outstring .= eai_build_post_meta(true/*date*/,true/*author*/,false/*comments*/,false/*cat */,false/* views */);
			}
		}
		if ($arg_exerpt) {
			$outstring .= '<p>'.text_trim(get_the_excerpt(),25,"...").'</p>';
		}
		$outstring .= '</article>';
		return $outstring;
	}
	function eai_build_post_meta($date = true,$author=true,$comment=true,$cat=true,$view = true, $updated = false) {
		$outstring .=""; /* TBD */
		$outstring .= '<div class="prl-article-meta">';
		if ($date == true) {
			$outstring .= '<span>'.get_the_time('F j, Y').'</span>';
		}
		if ($author==true){
			$outstring .= '<span>'. __('by', 'presslayer').' <a href="'.esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ).'" rel="author">'.get_the_author().'</a></span>';
		}
		/* if ( ($comment==true) && (comments_open()) ){
			$outstring .= '<span>'.get_comments_popup_link( __('0 comments','presslayer'), __('1 comment','presslayer'), __('% comments','presslayer'), 'comments-link', __('','presslayer')).'</span>';
		} */
		if ( $view == true) {
			$count = get_post_meta(get_the_ID(), 'EasyPostViewCounter', true);
			if(!$count or $count == '') $count = 0;
			if($count == 1) $view_label = __('view','presslayer'); 	else $view_label = __('views','presslayer');
			$outstring .= '<span>'.$count.' '. $view_label .'</span>';
		}
		if ( $cat == true) :
			$categories_list = get_the_category_list( __( ', ', 'presslayer' ) );
			if ( $categories_list) :
				$outstring .= sprintf( __( 'in %1$s', 'presslayer' ) , $categories_list );
			endif;
		endif;
		$outstring .= '</div>';
		return $outstring;
	}

	if (!function_exists('embed_ad_single'))  {
		function embed_ad_single($in_ad_str, $in_p_tag=5) {
			$zcontent = apply_filters('the_content', get_the_content());
			$zcontent1 = "";
			$zad =  '<div class="eai-ad-container eai-ad-incontent">'.do_shortcode($in_ad_str).'</div>';
			$zcontent2 = "";
			if ( empty($in_ad_str) || empty($zcontent) ) { // disabled or no content....
				$zcontent1 = $zcontent;
				$zad = "";
			} else {
					$ptagcount = substr_count($zcontent, '<p');
					 //echo '<p> ptagcount = '.$ptagcount.'</p>';
					if ( ($ptagcount < $in_p_tag)  ) { /* run at end if less than 5 p-tags */
						$zcontent1 = $zcontent;
					} else {
						$targetp = strnpos($zcontent,'<p',$in_p_tag); // find the start of the target <p> tag
						/* echo '<!--- thirdp = '.$thirdp.'--->'; */
						if ($targetp) { // check for no third p tag
							$zcontent1 = substr($zcontent, 0, $targetp);
							$zcontent2 = substr($zcontent,$targetp,strlen($zcontent));
						}
					}
			}
			echo $zcontent1.$zad.$zcontent2;
		}	/* end embed_ad_single */
	}
/* EOF */
?>
