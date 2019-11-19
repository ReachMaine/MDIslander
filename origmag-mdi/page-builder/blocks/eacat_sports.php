<?php

/* EA category sport bock - category & subcategories

	25Feb15 zig - copy eacat_landing for sport only.
		Add sports scores block, 1st block after featured/heading block
		-> use most recent post from the sport-> scores category
	6April add link to scores archive
	15Sept16 zig - apply filters to sports scores box.
	23Aug19 - zig add , 'no_found_rows' => TRUE for query  optimization.
  19Nov19 - zig, not using stickit anymore...
**/

if (!class_exists('eacat_sports')) {

	class eacat_sports extends AQ_Block {

		//set and create block
		function __construct() {

			$block_options = array(
				'name' => 'EA Sports',
				'size' => 'span-12',
				'resizable' => 0
			);

			//create the block

			parent::__construct('eacat_sports', $block_options);

		} /* end __construct */

		function form($instance) {
			$defaults = array(
				'title' => 'Recent posts',
				'style' => 'prl-homestyle-left',
				'category' => 0,
				'scores_category' => 0,
				'column' => '3',
				'show_title' => true,
				'show_meta' => true,
				'show_excerpt' => true,
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
						<label for="<?php echo $this->get_field_id('column') ?>">
							<div class="title">Columns</div>
							<div class="input">
								<?php echo aq_field_select('column', $block_id, array('2'=>'2 Columns', '3'=>'3 Columns', '4'=>'4 Columns'), $column) ?>
							</div>
						</label>
					</li>
					<li>
						<div class="title">Show/Hide</div>
						<div class="input">
							<label for="<?php echo $this->get_field_id('show_title') ?>"><?php echo aq_field_checkbox('show_title', $block_id, $show_title) ?> Show Title</label> &nbsp; &nbsp; <label for="<?php echo $this->get_field_id('show_meta') ?>"><?php echo aq_field_checkbox('show_meta', $block_id, $show_meta) ?> Show Meta</label> &nbsp; &nbsp; <label for="<?php echo $this->get_field_id('show_excerpt') ?>"><?php echo aq_field_checkbox('show_excerpt', $block_id, $show_excerpt) ?> Show Excerpt</label>
						</div>
					</li>

					<li>
						<label for="<?php echo $this->get_field_id('num_excerpt') ?>">
							<div class="title">Length of Excerpt</div>
							<div class="input">
								<?php echo aq_field_input('num_excerpt', $block_id, $num_excerpt, $size = 'full') ?>
							</div>
						</label>
					</li>

					<li>
						<label for="<?php echo $this->get_field_id('scores_category') ?>">
							<div class="title">Scores Category</div>
							<div class="input">
								<?php echo aq_field_select('scores_category', $block_id, get_array_cats(), $scores_category) ?>
			                </div>
						</label>
					</li>

	    </ul>
		<?php
		} /* end form */

		function block($instance) {
			extract($instance);	?>
	   <?php

			$catobj = get_category($category);
			//$cat_children = get_categories(array('child_of'=>  $category));
			$cats = array((int) $category);
			/* foreach ($cat_children as $ccat) {
				if ($ccat != $sports_category) { //  dont include scores category in flow of posts.
					$cats[] = (int) $ccat->term_id;
				}
			}*/
			$do_scores = get_post_meta(get_the_ID(), 'show_sports_scores', true) == 'true';
		?>

		<?php if ($title) {
			echo '<h1 class="ea-catlanding-title"><a href="'.home_url().'?cat='.$catobj->term_id.'">'.$title.'</a></h1>';
		}

		?>

	    <div class="prl-grid prl-grid-divider eacat_landing">

			<?php
			if ($do_scores) {
				/* first get the scores post id, etc */
				$scores_post = new WP_Query(array('post_type' => 'post', 'post_status=publish' ,'showposts' => 1, 'cat' => $scores_category , 'orderby' => 'date', 'no_found_rows' => TRUE));
				/* if ($scores_post->have_posts()) {
					 echo "<p>got a post id = ".$scores_post->post->ID."</p>";
				} else {
					 echo "<p>no scores posts<p>";
				} */
				wp_reset_postdata(); // may not need this, but....
			}
			/* get most recent post tagged with '_stickit' in given category */

			$p=0; /* count of post displayed */
			$gotone= false;

	/* 		$stay_post = new WP_Query(array('post_type' => 'post','showposts' => 1,'post__not_in' => get_option('sticky_posts'), 'category__in' => $cats, 'tag' => '_stickit','no_found_rows' => TRUE));
			while($stay_post->have_posts()): $stay_post->the_post();
				$featuredid = get_the_ID();
				$gotone=true; $p++;
				eai_do_feat(false, 40);
				 ?> <div class="prl-span-3"><ul class="prl-list prl-list-line stuckit" >
			<?php endwhile;
*/
			wp_reset_postdata();

			/* get 11 incase one of the them is the featured post */
			$recent_posts = new WP_Query(array('post_type' => 'post','showposts' => 11,'category__in' => $cats, 'orderby' => 'date','no_found_rows' => TRUE)); /* modified*/
			$done_feat = false;
			if (!$gotone) {

				/* find the first one with a thumnail */
				while ($recent_posts->have_posts() && (!$gotone) ) : $recent_posts->the_post();
					if ( has_post_thumbnail(get_the_ID()) ) {
						 /* echo '<p>post '.get_the_ID().' has a thumbnail gotit. </p>';  */
						 $gotone = true;
						 $featuredid = get_the_ID();
						 eai_do_feat(false/* meta*/, true /* except */);
						 ?> <div class="prl-span-3"><ul class="prl-list prl-list-line" > <?php
						 $p++;
					}
				endwhile;
			} // not got one.

	 	$done_feat = false;
			wp_reset_postdata();
			while ($recent_posts->have_posts() && !$done_feat): $recent_posts->the_post();
			 	if (($p < 4) && (get_the_ID() != $featuredid))  { ?>
					<li style="list-style-type:none" class="<?php echo get_the_ID(); echo 'feat:'.$featuredid; ?>" >
							<?php /* echo post_thumb(get_the_ID(),150, 0, false); */ ?>
							<?php echo post_thumb(get_the_ID(),520, 0, false);?>
					  	<h4 class="eai-featured-2nd prl-article-title"><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title();?></a> <?php echo get_label_format(get_the_ID());?> <?php echo get_label_meta(get_the_ID());?></h4>
					</li>

			 <?php $p++;
		 		}
	        	if ($p == 4) {
	        		$done_feat = true;
	        	}
        	endwhile;
	        echo ' </ul></div>'; // </div>';

	    /* end of featured part */
	        echo '<hr/>';
			if ($recent_posts->have_posts() || $scores_post->have_posts()) {
				?>

				<div class="prl-span-12">
				<?php
				switch($column){
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

				$endRow = 0;
				/* first do the scores block */
				if ($do_scores && $scores_post->have_posts() ) {
					while ($scores_post->have_posts()) : $scores_post->the_post();
						if ($endRow == 0) echo '<div class="prl-grid prl-grid-divider">';
						/* echo '<div class="eai-scoresblock" >'; */

						echo '<div id="post-'.get_the_ID().'" class="'.$prl_class.' eai-scores" >';
							echo '<div class="eai-scores-titlebox">';
								echo category_description($scores_category);
							echo '</div>';
							echo '<article class="prl-article">';
								echo '<h3 class="prl-article-title">'.get_the_title().'</h3>';
								echo apply_filters('the_content',get_the_content());
							echo '</article>';
							echo '<a class="scores-more-link" href="'.get_category_link( $scores_category ).'">Past Scores</a>';
						echo '</div>';
					endwhile;
					$endRow++; $p++;
					wp_reset_postdata();
				}


				$done_blog = $false;

				while($recent_posts->have_posts() && !$done_blog): $recent_posts->the_post();

					if (get_the_ID() != $featuredid) {

						if ($endRow == 0) echo '<div class="prl-grid prl-grid-divider">'; ?>
							<div id="post-<?php the_ID(); ?>" <?php post_class($prl_class); ?> >
								<article class="prl-article">
									<div class="cat-thumbnail"><?php echo post_thumb(get_the_ID(),520, 360, true);?></div>

									<?php if($show_title){?><h3 class="prl-article-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a> <?php echo get_label_format(get_the_ID());?> <?php echo get_label_meta(get_the_ID());?></h3><?php } ?>
									<?php if($show_meta){ post_meta(); } ?>
									<?php if($show_excerpt){ echo '<p>'.text_trim(get_the_excerpt(),$num_excerpt,'...').'</p>'; } ?>
								</article>
							</div>

							<?php $endRow++; $p++;
							if($endRow >= $columns) {
								echo '</div> <hr class="prl-grid-divider">';
								$endRow = 0;
							}
							if ($p == 10) {
								$done_blog = true;
							}
					}
				endwhile;
 			?> </div>  <?php /* end of span12 */

			} /* end of if have posts */

			 wp_reset_query();
		}
	}

} /* if not exists */
