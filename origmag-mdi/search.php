<?php  /* template mods:  
	13Nov14 zig - make search.php from copy of page.php, use Google custom search results
 */
?>
<?php get_header();?>    
<div class="prl-container">
    <div class="prl-grid prl-grid-divider">
    	<?php /* if(isset($prl_data['banner_before_single_title']) && $prl_data['banner_before_single_title']!='') echo '<div id="single-top-ad" class="prl-span-12"> <div class="ads_top ad-container">'.stripslashes($prl_data['banner_before_single_title']).'</div></div>'; */ ?> 
    	<?php if (is_active_sidebar('topbanner')) {
			dynamic_sidebar( 'topbanner' );
    	} ?>
        <section id="main" class="prl-span-9"> <!-- single -->
        	<?php if (is_active_sidebar('breaking-news')) { 
    			echo '<div class="eai-breaking-news prl-span-12">';
    			dynamic_sidebar( 'breaking-news' );
    			echo '</div>';
    		} else { /*  echo '<!-- no breaking news right now -->'; */ } ?>
    		<h3 class="prl-archive-title">Search Results for: <?php echo get_search_query();?></h3>
		
			<?php /* google custom search results */ ?>
			<script data-cfasync="false">
			  (function() {
			    var cx = '004781325057155505268:ushh3sbawxu';
			    var gcse = document.createElement('script');
			    gcse.type = 'text/javascript';
			    gcse.async = true;
			    gcse.src = (document.location.protocol == 'https:' ? 'https:' : 'http:') +
			        '//www.google.com/cse/cse.js?cx=' + cx;
			    var s = document.getElementsByTagName('script')[0];
			    s.parentNode.insertBefore(gcse, s);
			  })();
			</script>
			<gcse:searchresults-only></gcse:searchresults-only>
        </section>

		<?php if (is_active_sidebar('biztoday')) { echo '<div class="biz-today horizontal">'; dynamic_sidebar('biztoday'); echo '</div>'; } ?>
        <aside id="sidebar" class="prl-span-3">

            <?php get_sidebar('single');?>
        </aside>
    </div>
</div>


<?php get_footer();?>       
        