<?php /*
	mods:
		9Sept14 zig - add banner_top_cat to page template.
		2Oct14 zig - add breaking news widget
		8Oct2014 zig - add bottom banner with class="ad-container"
		22Oct14 zig - add biz-today widget call
		23Jan15 zig - check custom field pl_post_thumb before displaying thumbnail
    6Nov16 zig - dont show house ad at bottom of home page.
*/
?>
<?php get_header();?>
<div class="prl-container">
    <div class="prl-grid prl-grid-divider">
    	<?php  if (isset($prl_data['banner_top_cat']) && $prl_data['banner_top_cat']!='') echo '<div id="archive-top-ad" class=prl-span-12> <div class="ads_top ad-container">'.stripslashes($prl_data['banner_top_cat']).'</div></div>';  ?>
            <section id="main" class="prl-span-9 zig">
        	<?php if (is_active_sidebar('breaking-news')) {
	    		echo '<div class="eai-breaking-news prl-span-12">';
	    		dynamic_sidebar( 'breaking-news' );
	    		echo '</div>';
	    	} else { /* echo '<!-- no breaking news right now -->'; */ } ?>
		   <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		   <article id="post-<?php the_ID(); ?>" <?php post_class('article-single clearfix'); ?>>
			   <?php if ( has_post_thumbnail() && (get_post_meta($post->ID, 'pl_post_thumb', true)!='disable') ):?>
				<div class="space-bot">
				  <?php the_post_thumbnail(''); ?>
				</div>
				<?php endif; ?>
				<?php if(get_post_meta($post->ID, 'pl_page_title', true)!='disable'){?>
				<h1><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1><?php } ?>

			  <div class="prl-entry-content clearfix">
			  <?php the_content(); ?>
			   <?php wp_link_pages(array('before' => __('Pages','presslayer').': ', 'next_or_number' => 'number')); ?>
			   <?php edit_post_link(__('Edit','presslayer'),'<p>','</p>'); ?>
			   </div>
		   </article>
		   <?php endwhile; endif; ?>
			<?php if( !(is_home() || is_front_page()) && isset($prl_data['banner_after_single_content']) && $prl_data['banner_after_single_content']!='') echo '<div class="hide-mobile"><center class="ad-container ad-in-content">'.stripslashes($prl_data['banner_after_single_content']).'</center></div>';?>
			<?php if (is_active_sidebar('biztoday')) { echo '<div class="biz-today horizontal">'; dynamic_sidebar('biztoday'); echo '</div>'; } ?>
        </section>

        <aside id="sidebar" class="prl-span-3">
            <?php get_sidebar('custom');?>
        </aside>
    </div>
</div>
<?php get_footer();?>
