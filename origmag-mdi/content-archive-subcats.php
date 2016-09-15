<?php 
/* template mods:
special archive display for the living category s.t. we can
8Oct2014 zig - add divider between 'row's of subcats.
*/
	global $pl_data, $theme_url;
	$displayed = array();
	if (have_posts()) :?>	 	
		<h3 class="prl-archive-title"><?php single_cat_title(); ?></h3>

		<?php 
		$queried_object = get_queried_object();
		$current_cat = $queried_object->term_id;
		//echo 'current catid = '.$current_cat.<br>;
		$subcats = get_categories(array('child_of' => $current_cat));
		
		// get current page we are on. If not set we can assume we are on page 1.
		$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
		// are we on page one?
		if ( (1 == $paged) && ($subcats) ) {
			/* on the first page use special styling for subcategories */
			 echo '<div class="prl-grid prl-grid-divider">';
			$displayed = array();
			$colcount=0;

			foreach ($subcats as $subcat) {
				$recent_post1 = new WP_Query(array('post_type' => 'post','showposts' => 1,'post__not_in' => get_option('sticky_posts'),'cat' => $subcat->term_id));
				if ($recent_post1->have_posts()) {
					while($recent_post1->have_posts()): $recent_post1->the_post(); 
						$colcount++;
						// debugging echo '<!-- $colcount = '.$colcount.'-->';
						$displayed[] = get_the_ID();
						echo '<div class="prl-span-4">';
						echo '<h2 class="sub-category-title"><a href="'.get_category_link($subcat).'" >'.$subcat->name.'</a></h2>';
						?>
						<article class="prl-article">
							<?php echo post_thumb(get_the_ID(),520, 360, true);?>
							<h3 class="prl-article-title"><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" rel="bookmark" ><?php the_title();?></a> <?php echo get_label_format(get_the_ID());?> <?php echo get_label_meta(get_the_ID());?></h3>
							 
							<?php post_meta(true,false,false,false,false);?>     
							<p><?php echo text_trim(get_the_excerpt(),15,'...');?></p>
							<span class="pr1-block-title-link right"><a href="<?php echo get_category_link($subcat) ?>">See all in <?php echo $subcat->name; ?></a><span>
						</article>
						<?php echo '</div>';  /* end span4 */

						if ($colcount == 3 ) {
							$colcount = 0;
							echo '<hr class="prl-grid-divider">';
						}
					endwhile;
				}	
			}
			echo '</div>'; /* end top grid */
 			if ($colcount > 0) { echo '<hr>';}
 			?>	<h3 class="archive-more-title">More from our <?php single_cat_title(); ?></h3><hr> <?php
		}    ?>
	     <ul class="prl-list-category">
			<?php 
			$i=0;
			while (have_posts()) : the_post();
				if ( !in_array(get_the_ID(), $displayed) )   { 
					$i++;
					?>
					<li id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?>>
						<article class="prl-article">
							<?php if( has_post_thumbnail()):?>
							<div class="list-thumbnail"><?php echo post_thumb(get_the_ID(),520, 360, true);?></div>
							<?php endif;?>
							<div class="prl-article-entry">
								<h2 class="prl-article-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a> <?php echo get_label_format(get_the_ID());?> <?php echo get_label_meta(get_the_ID()); ?></h2> 
								<?php if (is_category('Obituaries')) { post_meta(true, false, false, false, false); } else  { post_meta(true, true, true, true, false); } ?>
								<?php the_excerpt();?>
							</div>
						</article>
					</li>
				<?php } 
			endwhile; ?>
			
		</ul>
		<?php if ( function_exists( 'page_navi' ) ) page_navi( 'items=5&amp;show_num=1&amp;num_position=after' ); ?>

	<?php else : /* dont have posts */ ?> 
	<?php get_search_form(); ?>
<?php endif; ?>  
