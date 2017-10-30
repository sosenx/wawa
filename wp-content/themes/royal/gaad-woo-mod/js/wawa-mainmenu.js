
(function($, w){
	
	var wawa_menu_parser = function( args ){
		
		this.args = args;
		
		
		this.getVP = function(){			
			var e = window
			, a = 'inner';
			if ( !( 'innerWidth' in window ) )
			{
			a = 'client';
			e = document.documentElement || document.body;
			}
			return { width : e[ a+'Width' ] , height : e[ a+'Height' ] }
		}
		
		this.getColumnsDevider = function(){
			var devider = 1;
			var vp = this.getVP();
						
			if( vp.width >= 320 ) devider = 1;
			if( vp.width >= 768 ) devider = 2;
			if( vp.width >= 1280 ) devider = 3;
						
			return devider;
		}
		
		
		this.addon_produkty = function( obj ){
			var sub = jQuery(obj).children('.sub-menu-holder');
			var menu_panel_produkty = jQuery( '<div id="menu_panel_produkty_addon">' );
			
			jQuery( menu_panel_produkty ).appendTo( sub );
			
			menu_panel_produkty_app = new Vue({
				
				el : "#menu_panel_produkty_addon",
				template: "#menu_panel_produkty_addon_template",
				data : {
					
					msg : "Hello World!!!"
					
				}
				
			});
			
			
		}
		
		this.produkty = function( obj ){
			var sub = jQuery(obj).children('.sub-menu-holder');
			var devider = this.getColumnsDevider()
			var children = sub.children();
			var listLength = children.length;
			var sortA = [];
			var itemsByCollumn = parseInt(listLength / devider) + 1;
			var columns = [];
			
			for( var i=0; i < devider; i++ ){
				columns[i] = jQuery('<div>').addClass('sub-menu-col').appendTo(sub);
			}
		
			var col_index = 0;
			for( var i=0; i < listLength; i++ ){
				
				if( i> 0 && i % itemsByCollumn == 0 ){
					col_index += 1;
				}
				
				jQuery( columns[ col_index ] ).append( children[ i ] );
			}
			
						
			return this;
		}
		
		
		this.o_nas = function( obj ){
			var sub = jQuery(obj).children('.sub-menu-holder');
			var devider = 1;
			var children = sub.children();
			var listLength = children.length;
			var sortA = [];
			var itemsByCollumn = parseInt(listLength / devider) + 1;
			var columns = [];
			
			for( var i=0; i < devider; i++ ){
				columns[i] = jQuery('<div>').addClass('sub-menu-col').appendTo(sub);
			}
		
			var col_index = 0;
			for( var i=0; i < listLength; i++ ){
				
				if( i> 0 && i % itemsByCollumn == 0 ){
					col_index += 1;
				}
				
				jQuery( columns[ col_index ] ).append( children[ i ] );
			}
			
						
			return this;
		}		
		
		
		this.wspolpraca = function( obj ){
			var sub = jQuery(obj).children('.sub-menu-holder');
			var devider = 1;
			var children = sub.children();
			var listLength = children.length;
			var sortA = [];
			var itemsByCollumn = parseInt(listLength / devider) + 1;
			var columns = [];
			
			for( var i=0; i < devider; i++ ){
				columns[i] = jQuery('<div>').addClass('sub-menu-col').appendTo(sub);
			}
		
			var col_index = 0;
			for( var i=0; i < listLength; i++ ){
				
				if( i> 0 && i % itemsByCollumn == 0 ){
					col_index += 1;
				}
				
				jQuery( columns[ col_index ] ).append( children[ i ] );
			}
			
						
			return this;
		}
		
		
		
		
		
		return this;
	}
	
	w._menu_parser = new wawa_menu_parser();
	
})(jQuery, window);







var mitem = Vue.extend({
	template : '#wawa_mainmenui_template',		
	
	props : ['item'],
	
	data : function( ){
		return {	
			item_data : this.item,
			css : null
		};
	},
	

	methods: {
		
		
		
		sub_over : function( e ){
			e.stopPropagation();
			var target = jQuery( e.currentTarget );
			target.parents(".sub[level=0]").eq(0).addClass('showing-sub');				
			
			
			
		},
				
		mitem_over : function( e ){
			e.stopPropagation();
			var target = jQuery( e.currentTarget );
			var id = target.attr( "id" );

			this.$root.currentMenuItemn = id;

		},
		
		mitem_out : function( e ){
			e.stopPropagation();
			debugger
		},
		
		uniqID : function(  ){			
			var n=Math.floor(Math.random() * 11);
			var k = Math.floor(Math.random() * 1000000);
			//var m = String.fromCharCode(n)+k;
			return k;
		}
		
		
	},
	
	watch : {
		
		css : function( val ){			
			/*
			Nadawanie szerokośći menu
			*/
			var hasSub = jQuery(this.$el).is('.sub');
			if( hasSub ){
				var sub = jQuery(this.$el).children('.sub-menu-holder');
				jQuery( sub ).css( {
					width : val.dim.width
					} );
			}
						
			return val;
		}
		
	},
	
	mounted : function(){
		var id = 'mitem' + this.$root.uniqID();
		
		var slug = this.$root.convertToSlug( this.item_data.title )
		var parsingFnName = slug.replace(/-/g, '_');
		jQuery(this.$el).attr( 'id', id ).addClass( slug );	
		
		/*
		* Parsowanie panelu rozwijanego 
		*/
		
		if( typeof window._menu_parser[parsingFnName] == "function" ){						
			window._menu_parser[parsingFnName]( this.$el );
			
			if( typeof window._menu_parser[ 'addon_' + parsingFnName ] == "function" ){				
				window._menu_parser[ 'addon_' + parsingFnName ]( this.$el );				
			}
			
			
			
		} else {
			
			//console.log( 'brak funkcji do parsowania menu: ' + parsingFnName );
		}
			
		
		
		
	},
	
	
	
});

Vue.component( 'mitem', mitem );


var wawa_mainmenu = new Vue({
	 
	template : "#wawa_mainmenu_template",
	el : "#wawa_mainmenu",
	data: {
		currentMenuItemnParent: null,
		menu : wawa_mainmenu_data,
		currentMenuItemn : null,
		currentTree : null,
		
		css : {}
		
	},
	
	
	created : function(){
		
			
	},
	
	mounted : function(){
		var id = 'mitem' + this.uniqID();
		jQuery(this.$el).attr( 'id', id );		
		
		this.css.dim = this.setDim();
		
		
		var childrens = this.$children;
		for( var i = 0; i < childrens.length; i++ ){
			var children = childrens[ i ];
			children.css = this.css;
		}
		
		
		jQuery(this.$el).on( 'mouseleave', this.clearMenu );	
	},
	
	watch : {
		
		currentTree : function ( val ){
			
			if( val == null ){
				var target = jQuery(this.$el);
				var subs = target.find( '.sub-menu-holder' ).css('display', 'none');				
				jQuery('.showing-sub').removeClass('showing-sub');
				
				return val;
			}
			
			for(var i = 0; i<val.length; i++){
				
				var item = jQuery( '#' + val [ i ] );
				var subs = item.children( '.sub-menu-holder' );
				
				for( var i =0; i<val.length; i++){					
					jQuery( subs[i] ).css( 'display', 'flex');
				}				
			}			
			return val;
		},
		
		
		/*
		* Generowanie widoku sub menu
		*/
		currentMenuItemn : function( val ){			
			var target = jQuery( '#' + val);
			
			//function escape
			if( !jQuery( target ) .is('*') ){
				return;
			}
			
			var parents = target.parents( '.mitem[id*="mitem"]' );
			
			var treeBak = [ target[0].id ];
			if( parents.length > 0 ){
				for( var i=0; i<parents.length; i++ ){
					var parent = parents[ i ];
					treeBak.push(parent.id);
				}
			}
			var level = target.attr('level');
			jQuery('[level="'+level+'"]').not(target).has('.sub-menu-holder[style]').children('.sub-menu-holder').css('display', 'none');
			
			this.currentTree = treeBak;
			
			if( jQuery( target ).is('[level=0]') ){
				jQuery('.showing-sub').removeClass('showing-sub');
				jQuery( target ).addClass('showing-sub');				
			}
			
			return val;
		}
		
	},
	
	
	methods: {
		
		
		createQueryString : function( e ){
			
		},
		
		doSearch : function( e ){
			
		},		
		
		convertToSlug : function ( Text ){
			
			return Text
				.toLowerCase()
				.replace('ą','a')
				.replace('ę','e')
				.replace('ó','o')
				.replace('ś','s')
				.replace('ł','l')
				.replace('ż','z')
				.replace('ź','z')
				.replace('ć','c')
				.replace('ń','n')
				.replace(/ /g,'-')
				.replace(/[^\w-]+/g,'');
		},
		
		/*
		* Ustawia dane pomocnicze, wiekośćie elementów do wykorzystania w czasie generowania widoków menu		
		*/
		setDim : function(){
			
			return  {
				width : jQuery(this.$el).width(),
				height : jQuery(this.$el).height()
				
			}
		},
		
		clearMenu : function( target ){
			this.currentMenuItemn = null;
			this.currentTree = null;
		},
		
		
		uniqID : function(  ){			
			var n=Math.floor( Math.random() * 11 );
			var k = Math.floor( Math.random() * 1000000 );			
			return k;
		}
		
		
	},
});







