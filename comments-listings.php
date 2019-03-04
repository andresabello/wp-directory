<?php 
/**
 * comments-listings.php
 *
 * The template for displaying comments for listings.
 */
?>

<?php 
	// If this file is called directly, abort.
	if ( ! defined( 'WPINC' ) ) {
		die( __( 'You cannot access this page directly.', 'pidirectory' ) );
	}
?>

<?php 
	// If the post is password protected, display info text and return.
	if ( post_password_required() ) : ?>
		<p>
			<?php 
				_e( 'This post is password protected. Enter the password to view the comments.', 'pidirectory' );

				return;
			?>
		</p>
	<?php endif; ?>

<!-- Comments Area -->
<div class="reviews-area">
	<?php if ( have_comments() ) : ?>
		<h2 class="reviews-title">
			<?php 
				printf( _nx( '1 review', '%1$s reviews', get_comments_number(), 'Title', 'pidirectory' ), number_format_i18n( get_comments_number() ) );
			?>
		</h2>

			<?php wp_list_comments('type=comment&callback=pi_listing_reviews'); ?>

		<?php 
			// If the comments are paginated, display the controls.
			if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) :
		?>
		<nav class="comment-nav" role="navigation">
			<p class="comment-nav-prev">
				<?php previous_comments_link( __( '&larr; Older Reviews', 'pidirectory' ) ); ?>
			</p>

			<p class="comment-nav-next">
				<?php next_comments_link( __( 'Newer Reviews &rarr;', 'pidirectory' ) ); ?>
			</p>
		</nav> <!-- end comment-nav -->
		<?php endif; ?>

		<?php 
			// If the comments are closed, display an info text.
			if ( ! comments_open() && get_comments_number() ) :
		?>
			<p class="no-comments">
				<?php _e( 'Reviews are closed.', 'pidirectory' ); ?>
			</p>
		<?php endif; ?>
	<?php endif; ?>

	<?php
	$comments_args = array(
	    'label_submit'  => 'Submit Review',
		'id_submit' 	=> 'submit-comment', 
		'class_submit'  => 'btn btn-primary'
	);
	comment_form($comments_args); 

	?>
</div> <!-- end comments-area -->