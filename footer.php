
<?php 
	$pi_options = get_option('pi_general_settings');
	$phone_number = ( isset($pi_options['pi_number']) ? $pi_options['pi_number'] : '(999)999-9999' );
	$footer_options = get_option('pi_footer_settings');
	$footer_columns = $footer_options['footer_columns']; 
?>
<?php if(!is_front_page()):?>
<!-- Pages Bottom CTA -->
<div class="section black-row">
	<div class="container">
		<div class="row">
			<div class="col-md-12 text-center">
				<h2>Get Free addiction advise.</h2>
				<p class="lead">We can help you make the right choice for your future. Get the TREATMENT you need today.</p>
				<p><a class="btn btn-lg btn-default btn-outline" href="tel:<?= $phone_number ; ?>"> Call us now <?= $phone_number; ?></a></p>
			</div>
		</div>
	</div>
</div>
<?php endif;?>
<?php 
    echo get_pi_form_drug_assesment();
    echo get_pi_form_alcohol_assesment();
    
?>
<!-- footer -->
<footer class="site-footer">
	<div class="container">
        <div class="row">
            <?php if($footer_columns > 3 ): ?>
                <div class="col-md-3">
                    <?php if ( !function_exists('dynamic_sidebar')
                    || !dynamic_sidebar("Footer Left") ) : ?>  
                    <?php endif; ?>                   
                </div>
                <div class="col-md-3">
                    <?php if ( !function_exists('dynamic_sidebar')
                    || !dynamic_sidebar("Footer Left Center") ) : ?>  
                    <?php endif; ?>                   
                </div>
                <div class="col-md-3">
                    <?php if ( !function_exists('dynamic_sidebar')
                    || !dynamic_sidebar("Footer Right Center") ) : ?>  
                    <?php endif; ?>                   
                </div>
                <div class="col-md-3">
                    <?php if ( !function_exists('dynamic_sidebar')
                    || !dynamic_sidebar("Footer Right") ) : ?>  
                    <?php endif; ?>                 
                </div>
            <?php else: ?>
                <div class="col-md-4">
                    <?php if ( !function_exists('dynamic_sidebar')
                    || !dynamic_sidebar("Footer Left") ) : ?>  
                    <?php endif; ?>                   
                </div>
                <div class="col-md-4">
                    <?php if ( !function_exists('dynamic_sidebar')
                    || !dynamic_sidebar("Footer Left Center") ) : ?>  
                    <?php endif; ?>                   
                </div>
                <div class="col-md-4">
                    <?php if ( !function_exists('dynamic_sidebar')
                    || !dynamic_sidebar("Footer Right Center") ) : ?>  
                    <?php endif; ?>                   
                </div>
            <?php endif; ?>
        </div>
		<div class="row">
			<div class="col-md-12">
				<div class="copyright">
					<p class="text-center">
						&copy; <?php echo date( 'Y' ); ?>
						<a href="<?php echo home_url(); ?>"><?php bloginfo( 'name' ); ?></a>
						<?php _e( 'All rights reserved.', 'alpha' ); ?>
					</p>
				</div> <!-- end copyright -->
			</div>
		</div>
	</div> <!-- end container -->
</footer> 
<!-- end footer -->
<?php wp_footer(); ?>
</body>
</html>