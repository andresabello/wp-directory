<?php
/**
 * All extra functions to support theme functionality
 *
 * @package    Pi_Directory
 * @subpackage Pi_Directory_Extras/includes
 * @author     Andres Abello <abellowins@gmail.com>
 */ 
class Pi_Directory_Extras {
	public function pi_pagination(){
		global $wp_query;
		$bignum = 999999999;
		if ( $wp_query->max_num_pages <= 1 )
			return;
		
		echo '<div class="row">';
			echo '<div class="col-md-12">';
				echo '<nav class="pagination">';
					echo paginate_links( array(
						'base'         => str_replace( $bignum, '%#%', esc_url( get_pagenum_link($bignum) ) ),
						'format'       => '',
						'current'      => max( 1, get_query_var('paged') ),
						'total'        => $wp_query->max_num_pages,
						'prev_text'    => '<icon class="glyphicon glyphicon-arrow-left"></icon> Previous',
						'next_text'    => 'Next <icon class="glyphicon glyphicon-arrow-right"></icon>',
						'type'         => 'list',
						'end_size'     => 3,
						'mid_size'     => 3
					) );
				echo '</nav>';
			echo '</div>';
		echo '</div>';		
	}
	public function post_meta(){
		echo '<ul class="list-inline entry-meta">';

		if ( get_post_type() === 'post' || get_post_type() === 'page' ) {
			// If the post is sticky, mark it.
			if ( is_sticky() ) {
				echo '<li class="meta-featured-post"><i class="fa fa-thumb-tack"></i> ' . __( 'Sticky', 'pidirectory' ) . ' </li>';
			}

			// Get the post author.
			printf(
				'<li class="meta-author"><a href="%1$s" rel="author">%2$s</a></li>',
				esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
				get_the_author()
			);

			// Get the date.
			echo '<li class="meta-date"> ' . get_the_date() . ' </li>';

			// The categories.
			$category_list = get_the_category_list( ', ' );
			if ( $category_list ) {
				echo '<li class="meta-categories"> ' . $category_list . ' </li>';
			}

			// The tags.
			$tag_list = get_the_tag_list( '', ', ' );
			if ( $tag_list ) {
				echo '<li class="meta-tags"> ' . $tag_list . ' </li>';
			}

			// Comments link.
			if ( comments_open() ) :
				echo '<li>';
				echo '<span class="meta-reply">';
				comments_popup_link( __( 'Leave a comment', 'pidirectory' ), __( '1 comment so far', 'pidirectory' ), __( 'View all % comments', 'pidirectory' ) );
				echo '</span>';
				echo '</li>';
			endif;

			// Edit link.
			if ( is_user_logged_in() ) {
				echo '<li>';
				edit_post_link( __( 'Edit', 'pidirectory' ), '<button class=" meta-edit">', '</button>' );
				echo '</li>';
			}
		}elseif( get_post_type() === 'pi_listing' ){
			// Edit link.
			if ( is_user_logged_in() ) {
				echo '<li>';
				edit_post_link( __( 'Edit', 'pidirectory' ), '<button class="btn btn-success meta-edit">', '</button>' );
				echo '</li>';
			}			
		}
	}
	public function hex_to_rgba( $hex ) {
		$hex = str_replace("#", "", $hex);

		if(strlen($hex) == 3) {
			$r = hexdec(substr($hex,0,1).substr($hex,0,1));
			$g = hexdec(substr($hex,1,1).substr($hex,1,1));
			$b = hexdec(substr($hex,2,1).substr($hex,2,1));
		} else {
			$r = hexdec(substr($hex,0,2));
			$g = hexdec(substr($hex,2,2));
			$b = hexdec(substr($hex,4,2));
		}
		
		$rgb = array($r, $g, $b);
		$rgba = implode(",", $rgb);
		$rgba = 'rgba(' . $rgba . ', .8)';
		return $rgba;		
	}
	public function listing_reviews( $comment, $args, $depth ){
	    $GLOBALS['comment'] = $comment;
	    $rating = get_comment_meta($comment->comment_ID, 'pi_rating', true );
	    switch( $comment->comment_type ) :
	        case 'pingback' :
	        case 'trackback' : ?>
	            <li <?php comment_class(); ?> id="review<?php comment_ID(); ?>">
	            <div class="back-link">< ?php comment_author_link(); ?></div>
	        <?php break;
	        default : ?>
	            <li <?php comment_class(); ?> id="review-<?php comment_ID(); ?>">
	            <div class="review">
		            <div class="author vcard">
			            <?= ( $depth <= 1 ? get_avatar( $comment, 80 ) : get_avatar( $comment, 40 )); ?>
		            </div>
		            <div class="review-body">
		            	<div class="comment-header">
		            		<strong><?php comment_author(); ?></strong> on
							<time <?php comment_time( 'c' ); ?> class="review-time">
								<span class="date">
								<?php comment_date(); ?>
								</span>
								<span class="time">
								<?php comment_time(); ?>
								</span>
							</time>
							<?php if( $depth <= 1 ): ?>
								<div class="pi-stars clearfix">
									<div class="star-wrapper">
										<?php 
										for( $i = 1; $i <= 5; $i++ ){
											echo '<div class="star-'. $i .' pi-star">';
											if( $i <= round($rating) ){
												echo '<span class="glyphicon glyphicon-star gold" aria-hidden="true"></span>';
											}else{
												echo '<span class="glyphicon glyphicon-star grey" aria-hidden="true"></span>';
											}
											echo '</div>'; 
										}
										?>
								    </div>
								</div>
							<?php endif; ?>
						</div>
		            	<p><?= get_comment_text(); ?></p>
		            </div>
	 
	            </div><!-- #comment-<?php comment_ID(); ?> -->
	        <?php // End the default styling of comment
	        break;
	    endswitch;
	}
	//TODO Can create a claim class if you want for the listings
	public function claim_status( $post_id, $user_id ){
		$claimant = get_post_meta( $post_id, 'pi_claimant_user_id', true);
		$claim_status = get_post_meta( $post_id, 'pi_listing_claim_status', true);
		$user_info = get_userdata($claimant);
		$username = $user_info->user_login;
		$first_name = $user_info->first_name;
		$last_name = $user_info->last_name;

		if(  $claim_status === 'claimed'){
			return '<p class="register">Claimed by '. ucwords($username) . '</p>' ;
		}else{
			if ( is_user_logged_in() ) {
				$content = '<button id="pi-claim" class="btn btn-danger" data-id="' . $post_id . '" data-user="' . $user_id . '">Claim Listing</button>';
			} else {
				$link = wp_register('<span>', '</span>', false);
				$content = '<p class="register">' . $link . ' to Claim this Listing </p>';
			}
			return $content;
		}
	}
	public function edit_listing_button( $post_id, $link ){
		$user_id = get_current_user_id();
		$claimant = get_post_meta( $post_id, 'pi_claimant_user_id', true);
		$claim_status = get_post_meta( $post_id, 'pi_listing_claim_status', true);

		if( is_user_logged_in() && $user_id == $claimant || current_user_can( 'administrator' ) ){
			return '<a href="' . $link . '" class="btn btn-primary"> Edit Listing </a>';
		}else{
			return false;
		}		
	}
	public function register_advanced_search_widget() {
	    register_widget( 'Pi_Directory_Advanced_Search' );
	}
	public function listing_search_form(){
		ob_start();
		?>
		<form method="post" id="main-search" action="<?= esc_url( home_url( '/' ) ); ?>">
		    <h4 class="main-color"><?php _e( 'Look for Treatment in Illinois', 'pidirectory' ); ?></h4>
		    <div class="form-body">
		    	<div class="form-group">
		    		<input type="text" class="form-control" placeholder="<?php _e( 'City, County or Zip', 'pidirectory' ); ?>" name="ls" id="ls" >
		    		<?php wp_nonce_field( 'pi_listing_nonce_field', 'pi_listing_search_data' ); ?>
		    	</div>
		    	<button type="submit" class="btn btn-success btn-block">Search Now</button>
		    </div>
		</form>

		<?php
		$content = ob_get_contents();
		ob_end_clean();
		return $content; 		
	}
	public function add_excerpt_to_page() {
	    add_post_type_support( 'page', 'excerpt' );
	}
	// Breadcrumbs for website
	public function pi_breadcrumbs() {
		echo '<a href="';
		echo get_option('home');
		echo '">';
		bloginfo('name');
		echo "</a>";
			if (is_category() || is_single()) {
				echo "&nbsp;&nbsp;/&nbsp;&nbsp;";
				the_category('&nbsp;&nbsp; &bull; &nbsp;&nbsp;');
					if (is_single()) {
						echo " &nbsp;&nbsp;/&nbsp;&nbsp; ";
						the_title();
					}
	        } elseif (is_page()) {
	            echo "&nbsp;&nbsp;/&nbsp;&nbsp;";
	            echo the_title();
			} elseif (is_search()) {
	            echo "&nbsp;&nbsp;&#187;&nbsp;&nbsp;Search Results for... ";
				echo '"<em>';
				echo the_search_query();
				echo '</em>"';
	        }
	}	
}