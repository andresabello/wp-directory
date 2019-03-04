<?php
	/*Get the general theme options*/
	$pi_options = get_option('pi_general_settings');
	$logo = ( isset($pi_options['pi_logo']) ? $pi_options['pi_logo'] : IMAGES . '/logo.png');
	$phone_number = ( isset($pi_options['pi_number']) ? $pi_options['pi_number'] : '(999)999-9999' ); 
	// Get the favicon.
	$favicon = IMAGES . '/icons/favicon.png';
	// Get the custom touch icon.
	$touch_icon = IMAGES . '/icons/apple-touch-icon-152x152-precomposed.png';
	// Get the custom touch icon.
?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" <?php language_attributes(); ?>> <!--<![endif]-->
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <!-- SEO Specific Metas -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" >
    <!-- Mobile Specific Metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title><?php wp_title( '|', true, 'right' ); ?><?php bloginfo( 'name' ); ?></title>
    <meta name="description" content="<?php bloginfo( 'description' ); ?>">
    <link href="<?php bloginfo('stylesheet_url'); ?>" rel="stylesheet">
	<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->

	<!-- Favicon and Apple Icons -->
	<link rel="shortcut icon" href="<?= $favicon; ?>">
	<link rel="apple-touch-icon-precomposed" sizes="152x152" href="<?= $touch_icon; ?>">

    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
	

	<div class="choose-wrap">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<h3>Choose One of the Following Questionnaires</h3>
					<button class="btn btn-primary" data-toggle="modal" data-target="#pi-questionnaire-alcohol" name="alcohol">Alcohol Questionnaire</button>
					<button class="btn btn-success" data-toggle="modal" data-target="#pi-questionnaire-drug" name="drug">Drug Questionnaire</button>
					<button type="button" class="pi-close"><span aria-hidden="true">&times;</span></button>
				</div>
			</div>
		</div>

	</div>	
	<!-- header -->
	<div class="section-fullwidth dark-grey-box upper-menu">
		<div class="container">
			<div class="row">
				<div class="col-md-6">
					<ul class="list-inline">
					    <li><a href="<?= home_url(); ?>/contact-us">Contact Us</a></li>
					    <li><a href="<?= home_url(); ?>/reviews">Review a Facility</a></li>
					    <li><a href="<?= home_url(); ?>/add-facility">Add a facility</a></li>
					    <li  class="pi-choose" >Am I Addicted?</li>
					</ul>
				</div>
				<div class="col-md-6">
					<h4 class="pull-right">Speak to a caring addiction specialist today! <a href="tel:<?= preg_replace("/[^0-9,.]/", "", $phone_number) ; ?> "> Call us  <?= $phone_number;  ?></a></h4>
				</div>
			</div>
		</div>
	</div>
    <header class="pi-header transparent-header" role="banner">
        <div class="container">
            <div class="row">
                <div class="col-xs-3">
                    <div class="logo">
                        <a href="<?= home_url();?>" rel="home">
                            <img src="<?= $logo; ?>" alt="<?php bloginfo('name'); ?>">
                        </a>
                    </div>
                </div>
                <div class="col-xs-9">
                	<nav class="navbar navbar-default" role="navigation">
			            <!-- Brand and toggle get grouped for better mobile display -->
			            <div class="navbar-header">
								<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#main_navigation">
								<span class="sr-only">Toggle navigation</span>
								<span class="ac-toggle">
									<span class="icon-bar"></span>
									<span class="icon-bar"></span>
									<span class="icon-bar"></span>
								</span>
								<span class="navmenu">Menu</span>
							</button>
			            </div>
			            <div class="collapse navbar-collapse" id="main_navigation">
			                <?php
			                wp_nav_menu( 
			                    array(
			                        'menu'              => 'primary',
			                        'theme_location'    => 'main-menu',
			                        'depth'             => 3,
			                        'container'         => 'div',
			                        'container_class'   => 'collapse navbar-collapse',
			                        'container_id'      => 'navbar',
			                        'menu_class'        => 'nav navbar-nav alignright',
			                        'fallback_cb'       => 'Pi_Bootstrap_Navwalker::fallback',
			                        'walker'            => new Pi_Bootstrap_Navwalker()
			                    )         
			                );
			                ?>
			            </div>
					</nav>
                </div>
            </div>
        </div>
    </header>
    <!-- header ends -->