<?php 

class gaad_shortcodes{
	
	
	
	
	
	/*
	* pheader
	*/
	static function wawa_pheader_assets(  ){		
		?><script id="wawa_pheader_template" type="text/template">
			<div id="wawa_pheader" class="container">
				<div class="container-fluid">
					
					<div class="row">
						
						<div class="col-sm-12">
	
							
							
						</div>
						
					</div>
					
				</div>	
			</div>
			
		</script>
		<script type="text/javascript" defer src="<?php echo get_template_directory_uri() . '/gaad-woo-mod/js/wawa-pheader.js' ?>"></script>
		<?php				
	}	
	
	static function wawa_pheader( $atts = NULL ){		
		$r = '<div id="wawa_pheader"></div>';
		
		return $r;
	}
	
	static function wawa_pheader_echo( $atts = NULL ){
		
		if( !is_front_page() ){
			echo gaad_shortcodes::wawa_pheader( $atts );	
		}
		
		
	}
	
	
	
	
	
	static function product_actions(){
		$r = array(
			
			array(
				'icon' => '<i class="fa fa-envelope-o" aria-hidden="true"></i>', 
				'title' => 'Wyślij tą ofertę do znajomego',
				'url' => '#', 
				'class' => 'send_email', 
				
				'click' => 'show_advertise_product', 
			),
			
			array(
				'icon' => '<i class="fa fa-facebook" aria-hidden="true"></i>', 
				'title' => 'Udostępnij na Facebooku',
				'url' => '#', 
				'class' => 'fb_share',
			), 
			
			array(
				'label' => 'Produkty powiązane', 
				'icon' => '<i class="fa fa-caret-square-o-down" aria-hidden="true"></i>', 
				'title' => 'Pokaż podobne produkty',
				'url' => '#', 
				'class' => 'show_crosssell',
				
				'click' => 'show_crosssell_event', 
				
			)
		
		);
			
		return json_encode( $r );
	}
	
	/*
	* BREADCRUMBS
	*/
	static function wawa_breadcrumbs_assets(  ){
		
	 	if( !function_exists( 'add_tag_url' ) ){
			function add_tag_url( $item ){				
				$tag_id = $item->term_id;
				$item->permalink =  get_tag_link($tag_id); 
				return $item;
			}
		}
			global $post;
			$terms = get_the_terms( $post->ID, 'product_tag' );
			@array_filter( $terms, 'add_tag_url' );
			$tags = json_encode( $terms );
			$breadcrumbs = json_encode( get_crumbs( array(
    				'home' => '<i class="fa fa-home" aria-hidden="true"></i>'
					)  
				)
			);
	
		
		?><script id="wawa_breadcrumbs_model" type="text/javascript">
			
			
			
			var breadcrumbs_model = {
				'tags' : <?php echo $tags; ?>,
				'thumbnail_url' : '<?php the_post_thumbnail_url( 'small' ); ?>', 
				'id' : <?php echo $post->ID ?>, 
				'title' : '<?php echo get_the_title( ); ?>', 
				'breadcrumbs' : <?php echo $breadcrumbs; ?>, 				
				'excerpt' : '<?php echo get_the_excerpt(); ?>',
				'actions' : <?php echo gaad_shortcodes::product_actions(); ?>, 
				
			};

		</script>
		
		
		<script id="wawa_breadcrumbs_template" type="text/template">
			<div id="wawa_breadcrumbs" class="wawa_breadcrumbs">
			
				
				<div class="container-fluid crumbs-wf">
				
					<div class="container">
				
						<div class="row">
						
							<div class="col-sm-12">						
								<ul class="post-crumbs">					
									<li v-for="(crumb, key, index) in breadcrumbs" class="crumb">						

										<a v-bind:href="crumb.url" v-html="crumb.title"></a>
										<span v-if="breadcrumbs.length != key + 1" class="separator"><i class="fa fa-angle-double-right" aria-hidden="true"></i></span>

									</li>
								</ul>							
							</div>
						
						
						</div>
					</div>
				</div>
				
				
			
				<div class="container">
					
					<div class="row crumbs-main-row">
						
						
						

						
						<div class="col-md-3">
							<img v-bind:src="thumbnail_url" class="breadcrumb-thumbnail"/>	
							
							
							<div class="price-wrap">
							
								<i class="fa fa-certificate" aria-hidden="true"></i>
								
								
								<span class="price_prefix">Tylko</span>
								<span class="price_price">{{ price._price }} zł</span>
								<span class="price_sufix">
									<span class="separator">za&nbsp;</span>{{ price._quantity }}<span class="separator">&nbsp;szt.</span>
								</span>
							</div>
							
						</div>
 

						<div class="col-md-5">
						
							<h1>{{ title }}</h1>
							
							<ul class="post-tags">
								<li class="icon" ><i class="fa fa-tags" aria-hidden="true"></i></li>				
								<li v-for="(tag, key) in tags" class="tag">						
									<a v-bind:href="tag.permalink">{{ tag.name }}</a><span v-if="breadcrumbs.length > key - 1" class="separator">,&nbsp;</span>
								</li>
							</ul>
							
							<ul class="product-actions">
								<li v-for="action in actions" class="action-btn">
									
									
									<a 	class="action"
										@click="typeof action.click !== 'undefined' ? event( action.click ) : function(){}"
										v-if="typeof action.url !== 'undefined'" v-bind:action="action.url"
										v-bind:title="typeof action.title !== 'undefined' ? action.title : '' " 
										v-bind:href="typeof action.url !== 'undefined' ? action.url : ''"
									>
										<span class="icon" v-if="typeof action.icon !== 'undefined'" v-html="action.icon"></span>
										
										<span class="label" v-if="typeof action.label !== 'undefined'" >{{ action.label }}</span>
									</a>
									
									
									
								</li>					
							</ul>
							
						</div>


						<div class="col-md-4 post-excerpt">
							<h3 class="excerpt-title">Opis produktu:</h3>
							<p v-if="excerpt.length > 0" class="excerpt" v-html="excerpt"></p>
							<p v-if="excerpt.length == 0" class="excerpt">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ut, saepe ab rem animi cum laudantium. Earum nemo voluptate numquam corporis velit animi quidem ut alias dolores provident aliquid non quis, quae incidunt esse rem cupiditate culpa, eius eveniet omni.</p>
                
              
						</div>
			
			
					</div>

				
					


				</div>	
				
			
				
					

			</div>

		</script>
		
		
		<link rel="stylesheet" href="<?php echo get_template_directory_uri() . '/gaad-woo-mod/css/wawa_breadcrumbs.css' ?>">
		<script type="text/javascript" defer src="<?php echo get_template_directory_uri() . '/gaad-woo-mod/js/wawa-breadcrumbs.js' ?>"></script>
		
		<?php		
		
		
	}	
	
	static function wawa_breadcrumbs( $atts = NULL ){		
		$r = '<div id="wawa_breadcrumbs"></div>';
		
		return $r;
	}
	
	static function wawa_breadcrumbs_echo( $atts = NULL ){
		echo gaad_shortcodes::wawa_breadcrumbs( $atts );
	}
	
	
	
	/*
	* BREADCRUMBS
	*/
	static function wawa_side_addon_assets(  ){
		
		
		?><script id="wawa_side_addon_template" type="text/template">
			<div id="wawa_side_addon">wawa side_addon</div>

		</script>
		<script type="text/javascript" defer src="<?php echo get_template_directory_uri() . '/gaad-woo-mod/js/wawa-side_addon.js' ?>"></script>
		
		<?php		
		
		
	}	
	
	static function wawa_side_addon( $atts = NULL ){		
		$r = '<div id="wawa_side_addon"></div>';
		
		return $r;
	}
	
	static function wawa_side_addon_echo( $atts = NULL ){
		echo gaad_shortcodes::wawa_side_addon( $atts );
	}
	
	
	/*
	* HEADER
	*/
	static function wawa_header_assets(  ){
		
		
		?>		
		<script id="wawa_header_template" type="text/template">
			<div id="wawa_header" class="row wawa-header">
				<div class="col-sm-12 col-md-6 logo">

					<div class="header-logo">
						<?php etheme_logo(); ?>
					</div>

				</div>		
				
				<div class="col-sm-12 col-md-6 tmenu">
					<ul>
						<li>
							<a href="\u\koszyk">
								<i class="fa fa-shopping-cart" aria-hidden="true"></i>
								<span>Twój koszyk</span>
							</a>
						</li>

						<li v-if="user < 0">
							<a href="\u\rejestracja">
								<i class="fa fa-user" aria-hidden="true"></i>
								<span>Zarejetruj się</span>
							</a>
						</li>	

						<li v-if="user > 0">
							<a href="\u">
								<i class="fa fa-user" aria-hidden="true"></i>
								<span>Zalogowany: </span>
								<span class="user-login">{{ user_data.data.user_login }}</span>
								
							</a>
							
							<a href="\u\out" class="log-out">
								<i class="fa fa-sign-out" aria-hidden="true" title="wyloguj"></i>							
							</a>
							
							
							
						</li>

						<li v-if="user < 0">
							<a href="\u\login">
								<i class="fa fa-lock" aria-hidden="true"></i>
								<span>Zaloguj się</span>
							</a>
						</li>

					</ul>
				</div>		
			
			
			
			
			</div>
			
		</script>
		
		<script type="text/javascript">
			var wawa_header_data = <?php echo json_encode( gaad_shortcodes::wawa_header_data() );  ?>
		
		</script>
		<script type="text/javascript" defer src="<?php echo get_template_directory_uri() . '/gaad-woo-mod/js/wawa-header.js' ?>"></script>
		<link rel="stylesheet" href="<?php echo get_template_directory_uri() . '/gaad-woo-mod/css/wawa-header.css' ?>">
		<?php		
		
		
	}	
	
	static function wawa_header( $atts = NULL ){
		$r = '<div id="wawa_header"></div>';
		return $r;
	}
	
	/*
	* generuje obiekt data dla aplikacji
	*/
	static function wawa_header_data( $atts = NULL ){
	
		$user_data = wp_get_current_user();
		$user = is_user_logged_in() ? $user_data->ID : -1;
		$r = array(
			'user' => $user,
			'user_data' => $user_data, 
		);
		
		return $r;
	}
	
	
	/*	
	* FOOTER
	*/
	static function wawa_footer_assets(  ){		
		?><script id="wawa_footer_template" type="text/template">
			<?php include(get_template_directory() . '/gaad-woo-mod/templates/wawa_footer_template.php'); ?>
		</script>
		<script type="text/javascript" defer src="<?php echo get_template_directory_uri() . '/gaad-woo-mod/js/wawa-footer.js' ?>"></script>		
		<?php				
	}	
	
	static function wawa_footer( $atts = NULL ){
		$r = '<div id="wawa_footer"></div>';
		
		return $r;
	}
	
	static function buildTree( array &$elements, $parentId = 0 ){
		$branch = array();
		foreach ( $elements as &$element ){
			if ( $element->menu_item_parent == $parentId ){
				$children = gaad_shortcodes::buildTree( $elements, $element->ID );
				if ( $children )
					$element->submenu = $children;

				$branch[$element->ID] = $element;
				unset( $element );
			}
		}
		return $branch;
	}
	
	/*	
	* Main menu
	*/
	
	
	/*
	* generuje obiekt data dla aplikacji
	*/
	static function wawa_mainmenu_data( $atts = NULL ){	
		$menu_items = wp_get_nav_menu_items( 'Main Menu' );
        $menu_tree = gaad_shortcodes::buildTree( $menu_items, 0 );
		return $menu_tree;
	}
	
	static function wawa_mainmenu_assets(  ){
		
		
		?><script id="menu_panel_produkty_addon_template" type="text/template">
			<div id="menu_panel_produkty_addon" class="wawa_menu_addon" >
				
				<p>
					<strong>Aktualna promocja</strong><br>
					<ul>
						<li>Ulotki A4 składane (Z, C)</li>
						<li>kreda 170g, zadruk 4+4</li>
						<li>falcowanie GRATIS!</li>
					</ul>

				
				</p>
				
				<img src="http://wawaprint.pl/wp-content/uploads/2017/06/wawa_ulotki_promocja.png">
			
			</div>

		</script>
		
		<script id="wawa_mainmenu_template" type="text/template">
			<div id="wawa_mainmenu" class="wawa_mainmenu row" >
			
				
				<!-- czesc z menu-->
				<div class="col-md-8 menu-holder">
			
					<div class="wawa_menu-icon">
						<i class="fa fa-home" aria-hidden="true"></i>
					</div>


					<div v-for="item in menu" class="mitem top-level" v-bind:id="'mitem' + uniqID()">

						<mitem v-bind:item="item" v-bind:level="item.menu_item_parent" ></mitem>

					</div>
			
				</div>
				
				<!-- szuakanie-->
				
				<div class="col-md-4 menu-search">
					<span class="search-prefix">szukaj:</span>
					<input type="text" class="short" @blur="createQueryString">
					<i class="fa fa-search" aria-hidden="true" @click="doSearch"></i>
				</div>
				
			
			</div>	

		</script>
		
		
		<script id="wawa_mainmenui_template" type="text/template">
			<div 	class="mitem"
			
					v-bind:class="item_data.submenu ? 'sub' : ''" 
					v-bind:level="item_data.menu_item_parent" 
					
					@mouseover="mitem_over( $event )"
					>
					
				<a v-bind:href="item_data.url">
					<i v-if="item_data.menu_item_parent != 0" class="fa fa-angle-double-right" aria-hidden="true"></i>
					<span>{{ item_data.title }}</span>
					
					
				</a>
				
				<div 
					class="sub-menu-holder" v-if="item_data.submenu" @mouseover="sub_over( $event )" >
					
					
					
					<mitem v-bind:item="item" v-for="item in item_data.submenu" :key="item.ID"></mitem>
				</div>	

			</div>

		</script>
		<script type="text/javascript" defer src="<?php echo get_template_directory_uri() . '/gaad-woo-mod/js/wawa-mainmenu.js' ?>"></script>
		
		<script type="text/javascript">
			var wawa_mainmenu_data = <?php echo json_encode( gaad_shortcodes::wawa_mainmenu_data() );  ?>
		
		</script>
		<link rel="stylesheet" href="<?php echo get_template_directory_uri() . '/gaad-woo-mod/css/wawa-mainmenu.css' ?>">
		<?php		
		
		
	}	
	
	static function wawa_mainmenu( $atts = NULL ){
		
		$r = '<div id="wawa_mainmenu"></div>';
		
		return $r;
	}
	
	
	static function wawa_mainmenu_echo( $atts = NULL ){
		echo gaad_shortcodes::wawa_mainmenu( $atts );
	}
	
	
	

}



add_shortcode( 'wawa_header', 'gaad_shortcodes::wawa_header' );
add_action( 'wp_enqueue_scripts', 'gaad_shortcodes::wawa_header_assets' );

add_shortcode( 'wawa_breadcrumbs', 'gaad_shortcodes::wawa_breadcrumbs' );
add_action( 'wp_enqueue_scripts', 'gaad_shortcodes::wawa_breadcrumbs_assets' );

add_shortcode( 'wawa_side_addon', 'gaad_shortcodes::wawa_side_addon' );
add_action( 'wp_enqueue_scripts', 'gaad_shortcodes::wawa_side_addon_assets' );

add_shortcode( 'wawa_footer', 'gaad_shortcodes::wawa_footer' );
add_action( 'wp_enqueue_scripts', 'gaad_shortcodes::wawa_footer_assets' );

add_shortcode( 'wawa_mainmenu', 'gaad_shortcodes::wawa_mainmenu' );
add_action( 'wp_enqueue_scripts', 'gaad_shortcodes::wawa_mainmenu_assets' );

remove_all_actions('woocommerce_before_main_content');
remove_all_actions('et_page_heading');
//add_action( 'woocommerce_before_main_content', 'gaad_shortcodes::wawa_mainmenu_echo' );
//add_action( 'woocommerce_before_main_content', 'gaad_shortcodes::wawa_mainmenu_assets' );

add_action( 'woocommerce_before_main_content', 'gaad_shortcodes::wawa_breadcrumbs_echo' );
add_action( 'woocommerce_before_main_content', 'gaad_shortcodes::wawa_breadcrumbs_assets' );

add_action( 'woocommerce_before_main_content', 'gaad_shortcodes::wawa_side_addon_echo' );
add_action( 'woocommerce_before_main_content', 'gaad_shortcodes::wawa_side_addon_assets' );

add_action( 'woocommerce_before_main_content', 'gaad_shortcodes::wawa_pheader_echo' );
add_action( 'woocommerce_before_main_content', 'gaad_shortcodes::wawa_pheader_assets' );




add_action( 'et_page_heading', 'gaad_shortcodes::wawa_pheader_echo' );
add_action( 'et_page_heading', 'gaad_shortcodes::wawa_pheader_assets' );


?>