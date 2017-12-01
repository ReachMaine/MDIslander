<?php
/* mods:
  25Jan16 zig - copy of index for obit custom post type.
  7Mar17 zig - use new obits-ad-contair
*/
get_header();?>
<div class="prl-container archive obituary">
    <div class="prl-grid prl-grid-divider">

    	<?php if (is_active_sidebar('topbanner')) {
			dynamic_sidebar( 'topbanner' );
    	} ?>
		<section id="main" class="prl-span-9">
		<?php if (is_active_sidebar('breaking-news')) {
    		echo '<div class="eai-breaking-news prl-span-12">';
    		dynamic_sidebar( 'breaking-news' );
    		echo '</div>';
    	}  ?>

		<?php
		$archive_style = $prl_data['archive_style'];
		if(is_category()) {
			$queried_object = get_queried_object();
			$term_id = $queried_object->term_id;
			$cat_meta =  get_option( "tax_".$term_id);
			if($cat_meta['cat_style']!='default' && $cat_meta['cat_style']!='') $archive_style = $cat_meta['cat_style'];
			else $archive_style = $prl_data['archive_style'];
		}

		switch($archive_style){
			case 'list':
				$content = 'content-archive';
			break;
			case '2col':
				$content = 'content-archive-2';
			break;
			case '3col':
				$content = 'content-archive-3';
			break;
			case 'liv':
					$content = 'content-archive-liv';
			break;
			case 'subcats':
				$content='content-archive-subcats';
			break;
			default :
				$content = 'loop-entry';
			break;
		}
		get_template_part($content,'index');
 		//assert( "locate_template( array('content-archive-liv-index.php', 'content-archive-liv.php'), true, false )" );
?>
		<?php /* zout if(is_category() && isset($prl_data['banner_bot_cat']) && $prl_data['banner_bot_cat']!='') echo '<div class="ads_bottom prl-panel hide-tablet"><div class="ad-container ad-in-content">'.stripslashes($prl_data['banner_bot_cat']).'</div></div>'; */?>
        <?php if (is_active_sidebar('obit_bottom_ad')) {
            echo '<hr class="prl-grid-divider">' ;
            echo '<div class="hide-mobile"><center class="ad-container ad-in-content">'; dynamic_sidebar('obit_bottom_ad'); echo '</center></div>'; } ?>

		</section>
        <aside id="sidebar" class="prl-span-3">
            <?php get_sidebar();?>
        </aside>
    </div><!--.prl-grid-->
</div>
<?php get_footer();?>
