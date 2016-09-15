<?php
/** EA category landing plus bock - category & subcategories
		featured block from categoey & blocks under neath catplus
		ex: mdi landing page

	1 Oct 2014
		one large featured image and 3 smaller images (similar to ea featured)
		then a blog construct with 3 columsn
		uses a _stickit tag to stick a particular article in the category to the big picture, 
			until unstuck or a more recent 'stuck' article 
**/
if (!class_exists('eacat_landingplus')) {
	class eacat_landingplus extends AQ_Block {
	
		//set and create block
		function __construct() {
			$block_options = array(
				'name' => 'EA Category+ Landing',
				'size' => 'span-12',
				'resizable' => 0
			);
			
			//create the block
			parent::__construct('eacat_landingplus', $block_options);
		}
		
		function form($instance) {
			
			$defaults = array(
				'title' => 'Recent posts',
				'style' => 'prl-homestyle-left',
				'category' => 0,
				'catplus' => 0,
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
				<label for="<?php echo $this->get_field_id('catplus') ?>">
					<div class="title">Columns Category</div>
					<div class="input">
						<?php echo aq_field_select('catplus', $block_id, get_array_cats(), $catplus) ?>
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
			
	        </ul>
			
	<?php
		} 
		
		function block($instance) {
			extract($instance);	?>	
			    

	   <?php
			$catobj = get_category($category);
			
			$cat_children = get_categories(array('child_of'=>  $category));
			$cats = array((int) $category);
			foreach ($cat_children as $ccat) {
				$cats[] = (int) $ccat->term_id;
			}
		?>

	    <div class="prl-grid prl-grid-divider eacat_landing">
	        
			<?php 
			$displayed = array();
			/* get most recent post tagged with '_stickit' in given category */
			$p=0; /* count of post displayed */
			/* $stay_post = new WP_Query(array('post_type' => 'post','showposts' => 1,'post__not_in' => get_option('sticky_posts'), 'cat' => $category, 'tag' => '_stickit')); */	
			$stay_post = new WP_Query(array('post_type' => 'post','showposts' => 1,'post__not_in' => get_option('sticky_posts'), 'category__in' => $cats, 'tag' => '_stickit'));
	
			$gotone= false;
			while($stay_post->have_posts()): $stay_post->the_post();
				$featuredid = get_the_ID();
				$gotone=true; $p++; 
				$displayed[] = $featuredid;
				eai_do_feat(false/*meta*/, $num_excerpt /* excerpt*/);
				 ?> <div class="prl-span-3"><ul class="prl-list prl-list-line" > 
	         
			<?php endwhile;  /* */


			wp_reset_postdata();
			/*wp_reset_query(); */

			/* get 5 incase one of the them is the featured post */
			/*$recent_posts = new WP_Query(array('post_type' => 'post','showposts' => 11,'post__not_in' => get_option('sticky_posts'),'category_name' => $catobj->slug)); */
			$recent_posts = new WP_Query(array('post_type' => 'post','showposts' => 5,'post__not_in' => get_option('sticky_posts'),'category__in' => $cats, 'orderby' => 'date')); /* modified*/
			$done_feat = false;
			if (!$gotone) {
				/* find the first one with a thumnail */
				while ($recent_posts->have_posts()) : $recent_posts->the_post(); 
					if ( (!$gotone) && has_post_thumbnail(get_the_ID()) ) {
						 /* echo '<p>post '.get_the_ID().' has a thumbnail gotit. </p>';  */
						 $gotone = true;
						 eai_do_feat(false/* meta*/, true /* except */);
						 ?> <div class="prl-span-3"><ul class="prl-list prl-list-line" > <?php
						 $featuredid = get_the_ID();
						 $displayed[] = $featuredid;
						$p++; 
					} 
				endwhile;
			} 
			$done_feat = false;
			while ($recent_posts->have_posts() && !$done_feat): $recent_posts->the_post(); 
			?>
			<?php
			 	if (($p < 4) && (get_the_ID() != $featuredid))  { 
			 		$displayed[] = get_the_ID();
			 		$p++; 
			 		?> <li style="list-style-type:none"><h4><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php echo post_thumb(get_the_ID(),520, 360, true);?></a><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title();?></a> <?php echo get_label_format(get_the_ID());?> <?php echo get_label_meta(get_the_ID());?></h4></li> <?php
			   } ?>  
	        <?php 

	        	if ($p == 4) {
	        		$done_feat = true;
	        	}
        	endwhile; 

	        echo ' </ul></div>'; // </div>';
	        wp_reset_query(); 
	    /* end of featured part */
	        echo '<hr/>';
			$catplusobj = get_category($catplus);
			/* get all subchildren of catplus */
			$cat_children = get_categories(array('child_of'=>  $catplus));
			$cats = array((int) $catplus);
			foreach ($cat_children as $ccat) {
				$cats[] = (int) $ccat->term_id;
			}
	        $recent_posts1 = new WP_Query(array('post_type' => 'post','showposts' => 10,'post__not_in' => get_option('sticky_posts'),'category__in' => $cats,));
	       // var_dump($recent_posts1->posts);
			if ($recent_posts1->have_posts()) {
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
				$done_blog = $false;
				while($recent_posts1->have_posts() && !$done_blog): $recent_posts1->the_post();  
					if ( !in_array(get_the_ID(), $displayed) ) {
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

 			$current_site = get_bloginfo('wpurl');
			echo '<span class="prl-block-title-link right"><a href="'.get_bloginfo('wpurl').'/'.$catplusobj->slug.'/page/2/">More '.$catplusobj->cat_name.'<i class="fa fa-caret-right"></i></a></span>';
			?> </div> <!-- end div span12--> <?php
			} /* end of if have posts */?>
	      </div><!-- end of catfeat+ -->      	
		<?php
			 wp_reset_query(); ?>    
		<?php
			
		}
		
	}
	
} /* if not exists */
