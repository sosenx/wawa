var gaad_nav_app = new Vue({
	template : "#product-nav",
	el: "#product-nav-app",
	data : {
		
		active : 0, 
		/*
		* Listy włączonyi i wyłączonych aplikacji, sposob jest do dupy ale dziala :) 
		*/
		
		parameters :{
			visible : [ 'variations_form', 'naklady_app', 'productSummaryApp' ],
			hidden : ['productDescription', 'productTemplates', 'productCrosssell', 'productSend' ],
			active : 0
		},
		
		description : {
			visible : [ 'productDescription' ],
			hidden : [ 'variations_form', 'naklady_app', 'productSummaryApp', 'productTemplates', 'productCrosssell', 'productSend' ],
			active : 1
		},
		
		templates : {
			visible : [ 'productTemplates' ],
			hidden : [ 'variations_form', 'naklady_app', 'productSummaryApp', 'productDescription', 'productCrosssell', 'productSend' ],
			active : 2
		},
		
		crosssell : {
			visible : [ 'productCrosssell' ],
			hidden : [ 'variations_form', 'naklady_app', 'productSummaryApp', 'productDescription', 'productTemplates', 'productSend' ],
			active : 3
		},
		
		send2friend : {
			visible : [ 'productSend' ],
			hidden : [ 'productCrosssell', 'variations_form', 'naklady_app', 'productSummaryApp', 'productDescription', 'productTemplates' ],
			active : 4
		}
		
	},
	
	methods : {
		
		changeView : function( val ){
			var actions = this[ val ];
			
			for( var i in actions.hidden ){
				var app = window[ actions.hidden[ i ] ];
				if( typeof app !== 'undefined' ){
					app.visible = false;	
				}				
			}
			
			for( var i in actions.visible ){
				var app = window[ actions.visible[ i ] ];
				if( typeof app !== 'undefined' ){
					app.visible = true;	
				}		
			}
			
			this.active = actions.active;
			
		}
		
	}
	
});