<?php 
/**
* Finds custom fields meta data in search results
*/

function cf_search_join( $join ) {
    global $wpdb;   
    $join .=' LEFT JOIN '.$wpdb->postmeta. ' ON '. $wpdb->posts . '.ID = ' . $wpdb->postmeta . '.post_id ';
    return $join;
}
add_filter('posts_join', 'cf_search_join' );

function cf_search_where( $where ) {
    global $wpdb;
    $where = preg_replace(
            "/\(\s*".$wpdb->posts.".post_title\s+LIKE\s*(\'[^\']+\')\s*\)/",
            "(".$wpdb->posts.".post_title LIKE $1) OR (".$wpdb->postmeta.".meta_value LIKE $1 AND meta_key NOT IN ('link') )", $where );
    return $where;
}
add_filter( 'posts_where', 'cf_search_where' );

function cf_search_distinct( $where ) {
    global $wpdb;
    return "DISTINCT";
    return $where;
}
add_filter( 'posts_distinct', 'cf_search_distinct' );

/**
* Adding ajax search functionality to the theme
* @return 
*/
function search_ajax_search() { 
    $args_post = array( 
        'post_type'      => array('post'),
        'post_status'    => 'publish',
		'suppress_filters' => false,
        's'              => $_POST['term'],
        'posts_per_page' => -1
    );
	$args_docs = array( 
        'post_type'      => array('documents' ),
        'post_status'    => 'publish',
		'suppress_filters' => false,
        's'              => $_POST['term'],
        'posts_per_page' => -1
    );
	$args_link = array( 
        'post_type'      => array('links'),
        'post_status'    => 'publish',
		'suppress_filters' => false,
        's'              => $_POST['term'],
        'posts_per_page' => -1
    );
    $query_post = new WP_Query($args_post);
	$query_docs = new WP_Query($args_docs);
	$query_links = new WP_Query($args_link);


	?>
	<div class="container-fluid">
	<div class="row header__search-pages active">
		<div class="col">
	<?php
	if($query_post->have_posts() || $query_docs->have_posts() || $query_links->have_posts() ) {
    if($query_post->have_posts()) { 
		?>
		<div class="header__search-pages_caption">
	
		<?php the_field( 'search_pages_caption', 'option' ); ?>
		</div>
		<div class="header__search-pages_result">
		<?php
        while ($query_post->have_posts()) { $query_post->the_post(); ?>
		<a href="<?php echo esc_url( post_permalink() ); ?>?search=<?php echo $_POST['term']; ?>" class="header__result-item">
					<div class="header__result-item-img"
						style="background-image: url(<?php the_post_thumbnail_url( $item->object_id, 'thumbnail'); ?>); ">
						<span>
							<?php the_title();?>
						</span>
					</div>
					<div class="header__result-item-content">
						<div class="header__result-item-caption">
							<?php the_title();?>
						</div>
						<div class="header__result-item-descr">
											<?php echo $_POST['term']; ?>
										</div> 
						<div class="header__result-item-link">
						<?php the_field( 'search_result_link', 'option' ); ?>
						</div>
					</div>
				</a>
	<?php }
?>
</div>
<?php
    } 
	?>
	</div>
	</div>
	<div class="row header__search-files">
		<div class="col">
			
	<?php

   if($query_docs->have_posts()) { 
	?><div class="header__search-col">
	<div class="header__search-col_caption">
	<?php the_field( 'search_doc_caption', 'option' ); ?>

	</div>
	<?php
        while ($query_docs->have_posts()) { 
			$query_docs->the_post(); ?>
			<?php $file = get_field( 'file' ); ?>
							<a class="links-link documents" href="<?php echo esc_url( $file['url'] ); ?>" target="_blank">
					<span class="links-link__icon"></span>
					<div class="links-link__caption">
						<?php the_title();?>
					</div>
				</a>
	<?php }
	?>
	</div>
	<?php
    } 


?>
	
		
<?php

   if($query_links->have_posts()) { 
	?>	<div class="header__search-col">
				<div class="header__search-col_caption">
				<?php the_field( 'search_link_caption', 'option' ); ?>
				</div>
				<?php
        while ($query_links->have_posts()) { $query_links->the_post(); ?>
			<?php $link = get_field( 'link' ); ?>
					<a class="links-link links" href="<?php echo esc_url( $link['url'] ); ?>"		 target="_blank">
					<span class="links-link__icon"></span>
					<div class="links-link__caption">
						<?php the_title();?>
					</div>
				</a>
		<?php }
		?>
		</div>
		<?php
    } 
}
else {
	echo "<div class='no-result'><span> " .get_field( 'search_no_result', 'option' ). "</span><br>";
	
	echo "<span>" . $_POST['term']. "</span> </div>";
}
?>
	
		</div>
	</div>
</div>
<?php
 exit;
}

add_action('wp_ajax_nopriv_search_ajax_search', 'search_ajax_search');
add_action('wp_ajax_search_ajax_search', 'search_ajax_search');
