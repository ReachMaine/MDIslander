<?php 
/*
YARPP Template: Right Rail
Author: zig (based on Simple by mitcho (Michael Yoshitaka Erlewine) )
Description: A simple example YARPP template.
*/
?>
<?php if (have_posts()):?>
	<h4>Related Posts</h4>
	<ul>
		<?php while (have_posts()) : the_post(); ?>
		<li><a href="<?php the_permalink() ?>" rel="bookmark">
			<span class="yarpp-thumbnail"><?php the_post_thumbnail('thumbnail'); ?></span>
			<p class="yarpp-thumbnail-title"> <?php the_title(); ?></p>
		</a><!-- (<?php the_score(); ?>)--></li>
		<?php endwhile; ?>
	</ul>
<?php else: ?>
	<!-- No related posts.-->
<?php endif; ?>

