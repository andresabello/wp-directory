<?php 
/**
 * template-contact.php
 *
 * Template Name: Contact Page
 */
$pi_options = get_option('pi_general_settings');
$phone_number = ( isset($pi_options['pi_number']) ? $pi_options['pi_number'] : '(999)999-9999' ); 
?>

<?php get_header(); ?>
    <!-- header ends -->
    <!-- content -->
    <div class="container">
		<div class="row">
			<div class="col-md-4">
				<?php if(have_posts()) : while(have_posts()) : the_post(); ?>
					<h1><?php the_title(); ?></h1>
					<p>We thank you for your interest in <a href="<?= home_url(); ?>"><?= bloginfo('name'); ?>.</a></p>
					<p>If you are in need of addiction treatment, and need to speak to someone, <a href="tel:<?= $phone_number; ?>">please call <?= $phone_number; ?></a> or use our “Live Chat” feature to speak to a qualified sponsor.</p>
					<?php the_content(); ?>
					<p>If you have any questions about how <a href="<?= home_url(); ?>"><?= bloginfo('name'); ?></a> works, visit our <a href="<?= home_url(); ?>/faq">FAQ page first.</a></p>
				<?php endwhile; endif; ?>
				<?php 
					if ( is_user_logged_in() ) {
						echo '<p>';
							edit_post_link( __( 'Edit', 'pidirectory' ), '<span class="meta-edit">', '</span>' );
						echo '</p>';
					}

				?>
			</div>
			<div class="main-content col-md-8" role="main">
				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<!-- Article content -->
					<div class="entry-content">
						<div class="pi-form home-form" style="margin-top: 20px;">
							<form action="<?= $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
							    <div class='row'>           
								    <!-- Name -->
								    <div class='col-md-6'>
									    <div class="form-group">
									    	<label for="pi_name">Name: <span class="red">*</span></label>
									    	<input type="text" name="pi_name" class="required col-md-12">
									    </div>
								    </div>
								    <!-- Phone -->
								    <div class='col-md-6'>
									    <div class="form-group">
									    	<label for="pi_phone">Phone: <span class="red">*</span></label>
									    	<input type="text" name="pi_phone" class="required col-md-12">
									    </div>
								    </div>
							    </div>

							    <div class="row">
							    	<div class="col-md-6">
									    <div class="form-group">
										    <!-- Email -->
										    <label for="pi_email">Email: <span class="red">*</span></label>
										    <input type="email" name="pi_email" class="required col-md-12">
									    </div>
								    </div>
								    <div class="col-md-6">
									    <div class="form-group">
										    <!-- Treatment for -->
										    <label for="pi_select">Who are you Seeking Treatment for?</label>
										    <select name="pi_select" class=" col-md-12">
										        <option value="none">Select Option</option>
										        <option value="Addicted person’s spouse / significant other">Addicted person’s spouse / significant other</option>
										        <option value="Addicted person’s mother">Addicted person’s mother</option>
										        <option value="Addicted person’s father">Addicted person’s father</option>
										        <option value="Addicted person’s grandparent">Addicted person’s grandparent</option>
										        <option value="Addicted person’s brother/sister">Addicted person’s brother/sister</option>
										        <option value="Addicted person’s family">Addicted person’s family</option>
										        <option value="Addicted person’s friend">Addicted person’s friend</option>
										        <option value="Self">Self</option>
										        <option value="Other">Other</option>
										    </select>
									    </div>
								    </div>
							    </div>

							    <div class="row">
								    <div class="col-md-6">
									    <div class="form-group">
									    	<label for="pi_choice">Drug of choice</label>
									    	<input type="text" name="pi_choice" class="col-md-12">
									    </div>
								    </div>
									<div class="col-md-6">
									    <div class="form-group">
									    	<label for="pi_time">Time using Drug of choice:</label>
									    	<input type="text" name="pi_time" class="col-md-12">
									    </div>
								    </div>
							    </div>
							    <!-- Message -->
							    <div class="form-group">
								    <label for="pi_message">Questions or comments:</label>
								    <textarea name="pi_message" class="comment" rows="10"></textarea>
							    </div>
							    <!-- Captcha -->
							    <div class="form-group">
									<label for="total">Are you human?</label>				
									<span class="rand1"></span> + <span class="rand2"></span> = <input type="text" id="total" name="total" class="required">
							    </div>
								<!-- Submit Button -->
							    <button id="pi-submit" class="pi-submit btn btn-primary" type="submit"><strong>Take the First Step</strong> Contact an Addiction Specialist Right Now</button>
							    <p class="privacy">We respect your <a href="<?= home_url();?>/privacy-policy/">privacy</a>. <u>All information</u> provided is <strong>100% Confidential</strong>.</p>
							</form>     
						</div>
					</div> <!-- end entry-content -->
				</article>
			</div> <!-- end main-content -->
		</div> <!-- end row -->
	</div> <!-- end container -->
	<!-- end content -->


<?php get_footer(); ?>