<?php /* custom functions for the new homepage layout */

if ( function_exists('register_sidebar') ){
		// topbar
		register_sidebar(array(
			'name' => 'EAIhome_side1',
			'id' => 'eaihome_side1',
			'description' => 'First sidebar on EA HOME (right)',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		));
    register_sidebar(array(
      'name' => 'EAIhome_across1',
      'id' => 'eaihome_across1',
      'description' => 'Under First content area EA HOME (full width)',
      'before_widget' => '<div id="%1$s" class="widget %2$s">',
      'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
    ));

    register_sidebar(array(
      'name' => 'EAIhome_side2',
      'id' => 'eaihome_side2',
      'description' => 'Second sidebar on EA HOME (left)',
      'before_widget' => '<div id="%1$s" class="widget %2$s">',
      'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
    ));
    register_sidebar(array(
      'name' => 'EAIhome_across2',
      'id' => 'eaihome_across2',
      'description' => 'Under Second content area on EA HOME (full width)',
      'before_widget' => '<div id="%1$s" class="widget %2$s">',
      'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
    ));
		register_sidebar(array(
			'name' => 'EAIhome_side3',
			'id' => 'eaihome_side3',
			'description' => 'Third sidebar on EA HOME (left)',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		));
		register_sidebar(array(
			'name' => 'EAIhome_across3',
			'id' => 'eaihome_across3',
			'description' => 'Under Third content acea on EA HOME (full width)',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		));
  }

function eaihome_build_postcol($arg_numcolumns = 3, $arg_coltitle = '', $arg_excerpt = false, $arg_date = false) {
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

    case '8':
      $columns = 8;
      $prl_class = 'prl-span-8';
    break;

    default:
      $columns = 2;
      $prl_class = 'prl-span-6';
    break;

  }
  $outstring = '';
  // $outstring .= '<div class="prl-span-4">';
  $outstring .= '<div id="post-'.get_the_ID().'" class="'.implode(' ', get_post_class($prl_class)).' zig2 ">';
    if ($arg_coltitle){
      $outstring .= '<h5 class="prl-block-title">'.$arg_coltitle.'</h5>';
    }
    $outstring .= eaihome_build_post($arg_excerpt, $arg_date);
  $outstring .= '</div>';
  return $outstring;
}

function eaihome_build_post($arg_excerpt = false, $arg_date = false) {
    $outstring .='';
		$outstring .= '<article class="prl-article">';
  	$outstring .= eaihome_build_postcat();
    $do_excerpt = false;
		if (has_post_thumbnail()) {
			$outstring .= '<div class = "eaihome-img">';
      $outstring .= '<a class="prl-thumbnail" href="'.get_permalink(get_the_ID()).'" title="'.the_title_attribute('echo=0').'">';
			$outstring .= get_the_post_thumbnail(get_the_ID(),'ea_storycrop');
      $outstring .= "</a>";
			$outstring .= '</div>';
		} else {
        $do_excerpt = true;
    }

		$outstring .= '<h3 class="prl-article-title">';
		$outstring .= '<a href="'.get_the_permalink().'" title="'.the_title_attribute('echo=0').'" rel="bookmark" >'.get_the_title().'</a>';
		$outstring .= '</h3>';

    if ($arg_date) {
      		$outstring .= '<span>'.get_the_time('F j, Y').'</span>';
    }
		if ($arg_excerpt || $do_excerpt) {
			$outstring .= '<p>'.text_trim(get_the_excerpt(),25,"...").'</p>';
		}
		$outstring .= '</article>';
		return $outstring;
	}

function eaihome_build_postcat($useCatLink = true) {
  // SHOW YOAST PRIMARY CATEGORY, OR FIRST CATEGORY
  $category = get_the_category();
  $useCatLink = true;
  $outcat_string = "";
  // If post has a category assigned.
  if ($category){
  	$category_display = '';
  	$category_link = '';
  	if ( class_exists('WPSEO_Primary_Term') )
  	{
  		// Show the post's 'Primary' category, if this Yoast feature is available, & one is set
  		$wpseo_primary_term = new WPSEO_Primary_Term( 'category', get_the_id() );
  		$wpseo_primary_term = $wpseo_primary_term->get_primary_term();
  		$term = get_term( $wpseo_primary_term );
  		if (is_wp_error($term)) {
  			// Default to first category (not Yoast) if an error is returned
  			$category_display = $category[0]->name;
  			$category_link = get_category_link( $category[0]->term_id );
  		} else {
  			// Yoast Primary category
  			$category_display = $term->name;
  			$category_link = get_category_link( $term->term_id );
  		}
  	}
  	else {
  		// Default, display the first category in WP's list of assigned categories
  		$category_display = $category[0]->name;
  		$category_link = get_category_link( $category[0]->term_id );
  	}
  	// Display category
  	if ( !empty($category_display) ){
  	    if ( $useCatLink == true && !empty($category_link) ){
          $outcat_string .= '<div class="eaihome-post-cat">';
  		    //$outcat_string .=  '<a href="'.$category_link.'">'.htmlspecialchars($category_display).'</a>';
					$outcat_string .=  '<a href="'.$category_link.'">'.$category_display.'</a>';
  		    $outcat_string .=  '</div>';
  	    } else {
          $outcat_string .=  '<div class="eaihome-post-cat">'.$category_display.'</div>';
  	    }
  	}

  }
  return  $outcat_string;
}
