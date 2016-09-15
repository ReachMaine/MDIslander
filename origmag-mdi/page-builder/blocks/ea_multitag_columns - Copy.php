<?php
/** 11Aug14 zig - A get a set of posts that has anyone of the given tags
	Not sure how to convert a comma deliminated string of tag names to an array of tag_ids as int.  TBD
 **/
class ea_multitag_cols extends AQ_Block {
	
	//set and create block
	function __construct() {
		$block_options = array(
			'name' => 'Multiple Tag Columns',
			'size' => 'span-12',
			'resizable' => 0
		);
		
		//create the block
		parent::__construct('ea_multitag_cols', $block_options);
	}
	
	function form($instance) {
		
		$defaults = array(
			'title' => 'Multi-Tag Cols',
			'tags' => '',
			'post_count' => '10',
			'column' => '2',
			'post_format' => 'all',
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
				<div class="title">Title (not displayed)</div>
				<div class="input">  
					<?php echo aq_field_input('title', $block_id, $title, $size = 'full') ?>
					</div>
				</label>
			</li>
			<li>
				<label for="<?php echo $this->get_field_id('tags') ?>">
					<div class="title">Tag IDs</div>
					<div class="input">
						<?php echo aq_field_input('tags', $block_id, $tags, $size = 'full') ?>
					</div>
				</label>
			</li>
			<li>
				<label for="<?php echo $this->get_field_id('post_count') ?>">
					<div class="title">Post count</div>
					<div class="input"><?php echo aq_field_input('post_count', $block_id, $post_count, $size = 'small') ?></div>
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
				<label for="<?php echo $this->get_field_id('post_format') ?>">
					<div class="title">Post Format</div>
					<div class="input">
						<?php echo aq_field_select('post_format', $block_id, array('all'=>'All', 'video'=>'Video', 'audio'=>'Audio', 'gallery'=>'Gallery'), $post_format) ?>
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
		extract($instance);
		?>
		<?php /* if($title!='') echo '<h5 class="prl-block-title">'.trim($title).'</h5>'; */ ?>
		<div class="prl-grid prl-grid-divider">
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
			$hloopRow1 = 0; // first row flag
			if(is_front_page()){
				$current_page_num = get_query_var('page') ? get_query_var('page') : 1;
			}else{
				$current_page_num = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
			}
			
			// Post format
			$tax_query=array();
			if($post_format!='all'){
				$tax_query = array(
								array(
									'taxonomy' => 'post_format',
									'field'    => 'slug',
									'terms'    => array( 'post-format-'.$post_format),
									'operator' => 'IN'
								)
						  );
			}
			$tagsarr = explode(',', $tags);
			foreach($tagsarr as $tag) {
				$tagg[] = (int) $tag;
			}

			$tag_posts = new WP_Query(array('post_type' => 'post','showposts' => $post_count, 'post__not_in' => get_option('sticky_posts'),'tag__in' => $tagg,'orderby' => 'date' ));
			//var_dump($tag_posts->request);echo '-- $query request<br>';
			if ($tag_posts->have_posts()) {
				while($tag_posts->have_posts()): $tag_posts->the_post();
				if($endRow == 0  && $hloopRow1++ != 0) echo '<div class="prl-grid prl-grid-divider">';
				 ?>
				 <div id="post-<?php the_ID(); ?>" <?php post_class($prl_class); ?>>
				 			
			
					<article class="prl-article">
						<div class="tag-cat <?php echo category_nomiker(); ?>"></div>
						<div class="tag-thumbnail"><?php echo post_thumb(get_the_ID(),520, 360, true);?></div>
						<?php if($show_title){?><h3 class="prl-article-title"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a> <?php echo get_label_format(get_the_ID());?> <?php echo get_label_meta(get_the_ID());?></h3><?php } ?>
						<?php if($show_meta){ post_meta(); } ?>
						<?php if($show_excerpt){ echo '<p>'.text_trim(get_the_excerpt(),$num_excerpt,'...').'</p>'; } ?>
						
					</article>
				</div>
				<?php 
				$endRow++;
				if($endRow >= $columns) {
					echo '</div> <hr class="prl-grid-divider">';
					$endRow = 0;
				}
				endwhile; 
				
				if($endRow != 0) {
					while ($endRow < $columns) {
						echo('<div class="'.$prl_class.'">&nbsp;</div>');
						$endRow++;
					}
					echo('</div> <hr class="prl-grid-divider">');
				}
				
				?>
			  <?php echo '<a href="#"><h3>All posts</h3></a>';  ?>
			  <?php if ( function_exists( 'page_navi' ) ) {page_navi( 'items=5&amp;show_num=1&amp;num_position=after' );} else {echo"<!-- no pagenav --!>"; } ?>
			<?php } else echo"<!-- no posts --!>"; /* end of have posts */ ?>
		 <?php wp_reset_query();
	}
	
}
