<?php $class = ( etheme_get_option( 'header_overlap' ) && etheme_get_custom_field( 'current_header_overlap' ) != 'off' || etheme_get_custom_field( 'current_header_overlap' ) == 'on' ) ? ' header-overlap' : ''; ?>

<div class="header-wrapper header-type-custom<?php echo $class; ?>">

	<?php get_template_part( 'headers/parts/top-bar', 1 ); ?>

	<header class="header main-header">

		<div class="container">

			<?php echo etheme_get_custom_field( 'current_custom_header', et_get_page_id() ) ? et_get_block( etheme_get_custom_field( 'current_custom_header', et_get_page_id() ) ) : et_get_block( etheme_get_option( 'custom_header' ) ) ; 
			?>

			<div class="navbar" role="navigation">
				<div class="container-fluid">
					<div id="st-trigger-effects" class="column">
						<button data-effect="mobile-menu-block" class="menu-icon"></button>
					</div>
					<div class="header-logo">
						<?php etheme_logo(); ?>
					</div>
					
					<div class="clearfix visible-md visible-sm visible-xs"></div>
					<div class="tbs">
						<div class="collapse navbar-collapse">
							<?php et_get_main_menu(); ?>
						</div><!-- /.navbar-collapse -->
					</div>

					<div class="navbar-header navbar-right">
						<div class="navbar-right">
				            <?php if(class_exists('Woocommerce') && !etheme_get_option('just_catalog') && etheme_get_option('cart_widget')): ?>

								<?php echo do_shortcode( '[et_top_cart]' ); ?>

				            <?php endif ;?>
				            
				            <?php if(etheme_get_option('search_form')): ?>
								<?php etheme_search_form(); ?>
							<?php endif; ?>

						</div>
					</div>
				</div><!-- /.container-fluid -->
			</div>

		</div>

	</header>

</div><!-- header-wrapper header-type-custom -->