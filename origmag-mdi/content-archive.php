<?php
/* template mods:
	7Aug2014 zig
		- add check for author and display author name using plugin's function
	8Aug2014 zig:
		- add params for post_meta to now show views
*/
	global $pl_data, $theme_url,  $prl_data;
	global $wp_query;
	if (have_posts()) :?>

	<?php $post = $posts[0]; // Hack. Set $post so that the_date() works. ?>
 	  <?php /* If this is a category archive */ if (is_category()) { ?>
		<h3 class="prl-archive-title"><?php single_cat_title(); ?></h3>
		<?php echo category_description();  ?>
 	  <?php /* If this is a tag archive */ } elseif( is_tag() ) { ?>
		<h3 class="prl-archive-title"><?php _e('Posts Tagged','presslayer');?>: <?php single_tag_title(); ?></h3>
	 <?php /* If this is a author archive */ } elseif( is_author() ) { $curauth = (get_query_var('author_name')) ? get_user_by('slug', get_query_var('author_name')) : get_userdata(get_query_var('author'));?>
		<h3 class="prl-archive-title ">Articles by: <?php echo $curauth->display_name; ?> </h3>
		 <?php echo ts_fab_show_bio('archive', $curauth->ID); ?>
 	  <?php /* If this is a daily archive */ } elseif (is_day()) { ?>
		<h3 class="prl-archive-title"><?php _e('Archive for','presslayer');?> <?php the_time('F jS, Y'); ?></h3>
 	  <?php /* If this is a monthly archive */ } elseif (is_month()) { ?>
		<h3 class="prl-archive-title"><?php _e('Archive for','presslayer');?> <?php the_time('F, Y'); ?></h3>
 	  <?php /* If this is a yearly archive */ } elseif (is_year()) { ?>
		<h3 class="prl-archive-title"><?php _e('Archive for','presslayer');?> <?php the_time('Y'); ?></h3>
 	  <?php /* If this is a paged archive */ } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
		<h3 class="prl-archive-title "><?php _e('Blog Archives','presslayer');?></h3>
 	  <?php } elseif (is_search()){ ?>
	  	<h3 class="prl-archive-title"><?php _e('Search Results','presslayer');?></h3>
	  <?php  } ?>

     <ul class="prl-list-category archive-plain">
	<?php
	$posts_count = $wp_query->post_count;
	if ($posts_count > 0 ) {
			$posts_first_half = floor(($posts_count+1)/2);
	}

	$i=0;
	while (have_posts()) : the_post();
	$i++;
	?>
	<li id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?>>
		<article class="prl-article">
			<?php if( has_post_thumbnail()):?>
			<div class="list-thumbnail"><?php echo post_thumb(get_the_ID(),520, 360, true);?>
				<?php  if ( !(is_category('Obituaries')) && in_category('Obituaries') ) { echo '<div class="archive-cat obits"></div>'; } ?>
			</div>
			<?php endif;?>
			<div class="prl-article-entry">
				<h2 class="prl-article-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a> <?php echo get_label_format(get_the_ID());?> <?php echo get_label_meta(get_the_ID()); ?></h2>
				<?php /* if ( !(is_category('Obituaries')) && in_category('Obituaries') ) { echo '<div class="archive-cat obits">In Memorium</div>';  } */ ?>
				<?php if (is_category('Obituaries') || in_category('Obituaries')) { post_meta(true, false, false, false, false); } else  { post_meta(true, true, false, true, false); } ?>
				<?php the_excerpt();?>
			</div>
		</article>
	</li>
	<?php if ($i == $posts_first_half) {
		//echo "<p>MIDDLE-ish.</p>";
		 if(isset($prl_data['banner_before_single_title']) && $prl_data['banner_before_single_title']!='') {
		 	echo '<div class="eai-ad-container eai-ad-incontent">'.do_shortcode(stripslashes($prl_data['banner_before_single_title'])).'</div>';
		}
	}
 	endwhile; ?>

	</ul>
	<?php if ( function_exists( 'page_navi' ) ) {
		page_navi( 'items=5&amp;show_num=1&amp;num_position=after' ); }
	else {
		echo '<div class="navigation">';
			echo '<div class="alignleft">';
				previous_posts_link( '&laquo; Previous ' );
			echo '</div>';
			echo '<div class="alignright">';
				next_posts_link( 'Next &raquo;', '' );
			echo '</div>';
		echo "</div>";
	}
	?>


<?php else : ?>
	<?php get_search_form(); ?>
<?php endif; ?>
