<?php
/* A ea featured block
	18July2014 zig
		one large featured image and 3 smaller images
		uses a _stickit tag to stick a particular article in the category to the big picture,
			until unstuck or a more recent 'stuck' article
	19Aug19 zig
		add 'no_found_rows' => TRUE to WP query for optimization.
**/
if (!class_exists('eafeature_Block')) {
	class eafeature_Block extends AQ_Block {

		//set and create block
		function __construct() {
			$block_options = array(
				'name' => 'EA Feature',
				'size' => 'span-12',
				'resizable' => 0
			);

			//create the block
			parent::__construct('eafeature_block', $block_options);
		}

		function form($instance) {

			$defaults = array(
				'title' => 'Recent posts',
				'style' => 'prl-homestyle-left',
				'category' => 0,
				'num_excerpt' =>15
			);
			$instance = wp_parse_args($instance, $defaults);
			extract($instance);

			?>
			<ul class="lightbox_form">
	        <li>
				<label for="<?php echo $this->get_field_id('title') ?>">
	            <div class="title">Title</div>
	            <div class="input">
					<?php echo aq_field_input('title', $block_id, $title, $size = 'full') ?>
					</div>
				</label>
			</li>
			<li>
				<label for="<?php echo $this->get_field_id('style') ?>">
	            <div class="title">Style</div>
	            <div class="input">
					<?php echo aq_field_select('style', $block_id, array('prl-homestyle-left' => 'Left', 'prl-homestyle-right' => 'Right'), $style) ?>
					</div>
				</label>
			</li>

			<li>
				<label for="<?php echo $this->get_field_id('category') ?>">
					<div class="title">Category</div>
					<div class="input">
						<?php echo aq_field_select('category', $block_id, get_array_cats(), $category) ?>
	                </div>
				</label>
			</li>

			<li>
				<label for="<?php echo $this->get_field_id('num_excerpt') ?>">
					<div class="title">Length of Excerpt</div>
					<div class="input">
						<?php echo aq_field_input('num_excerpt', $block_id, $num_excerpt, $size = 'full') ?>
					</div>
				</label>
			</li>

	        </ul>

	<?php
		}

		function block($instance) {
			extract($instance);	?>


	   <?php /* if($category > 0){?>
		<h5 class="prl-block-title <?php echo catcolor($category);?>"><a href="<?php echo get_category_link( $category ); ?>"><?php echo get_the_category_by_ID( $category); ?></a> <span class="prl-block-title-link right"><a href="<?php echo get_category_link( $category ); ?>"><?php _e('All posts','presslayer');?> <i class="fa fa-caret-right"></i></a></span></h5>
		<?php } else {?>
		<h5 class="prl-block-title"><?php echo $title;?></h5>
		<?php } */?>

	    <div class="prl-grid prl-grid-divider">

			<?php
			/* get most recent post tagged with 'staytop' in given category */
			$p=0;
			$stay_post = new WP_Query(array('post_type' => 'post','showposts' => 1,'post__not_in' => get_option('sticky_posts'), 'cat' => $category, 'tag' => '_stickit', 'no_found_rows' => TRUE));
			$gotone= false;
			while($stay_post->have_posts()): $stay_post->the_post();
				$featuredid = get_the_ID();
				$gotone=true; ?>
				<div class="prl-span-9">
		           <div class="prl-grid">
					   <div class="prl-span-12">
		               		<article class="prl-article stuck">
								<?php echo post_thumb(get_the_ID(),580, 360, true);?>
							</article>
		               </div>
		           	   <div class="prl-span-12">
		                   <article class="prl-article">
		                    <h3 class="prl-article-title"><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title();?></a> <?php echo get_label_format(get_the_ID());?> <?php echo get_label_meta(get_the_ID());?></h3>
		                    <?php post_meta(true,false,false,false,false);?>
							<p><?php echo text_trim(get_the_excerpt(),$num_excerpt,'...');?></p>
		                    </article>
		               </div>
		           </div>
	          </div>
	          <div class="prl-span-3"><ul class="prl-list prl-list-line" >
			<?php endwhile;  /* */

			wp_reset_postdata();
			/*wp_reset_query(); */

			/* get 4 incase one of the them is the featured post */
			$recent_posts = new WP_Query(array('post_type' => 'post','showposts' => 4,'post__not_in' => get_option('sticky_posts'),'cat' => $category, 'no_found_rows' => TRUE));

			while($recent_posts->have_posts()): $recent_posts->the_post();
			?>
			<?php if ( !$gotone && $p == 0){ ?>
			<!-- !gotone & $p == $p = <?php echo $p; ?> -->
				<div class="prl-span-9">
		           <div class="prl-grid">
					   <div class="prl-span-12">
		               		<article class="prl-article not-stuck">
								<?php echo post_thumb(get_the_ID(),580, 360, true);?>
							</article>
		               </div>
		           	   <div class="prl-span-12">
		                   <article class="prl-article">
		                    <h3 class="prl-article-title"><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title();?></a> <?php echo get_label_format(get_the_ID());?> <?php echo get_label_meta(get_the_ID());?></h3>
		                    <?php post_meta(true,false,false,false,false);?>
							<p><?php echo text_trim(get_the_excerpt(),$num_excerpt,'...');?></p>
		                    </article>
		               </div>
		           </div>
		        </div>
		        <div class="prl-span-3"><ul class="prl-list prl-list-line" >
			 <?php  } else {
			 	if (($p < 3) && (get_the_ID() != $featuredid))  {
			 		$p++; ?>
					<li style="list-style-type:none">
						<!-- $p = <?php echo $p; ?> -->
						<?php echo post_thumb(get_the_ID(),150, 0, true);?>
						<h4>
							<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title();?></a>
						</h4>
						<?php echo get_label_format(get_the_ID());?> <?php echo get_label_meta(get_the_ID());?>
					</li>
			 <?php } } ?>
	        <?php

	        	endwhile;
	        	wp_reset_query(); ?>
	        <?php echo ' </ul></div></div>';?>

		<?php

		}

	}
} /* if not exists */
