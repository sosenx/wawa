// JavaScript Document
/*

* eKPERYMERNT, POMOC W LICZENIU MARZY W ZAKRESIE NAKLADÓW DLA DANEGO PRODUKTU.
*/


priceChartApp = new Vue({
	el : "#product-price-chart",
	template : "#price-charts-app",
	data : {
		calc_data : calc_data,		
		d : [], //cena jednostkowa / naklad
		e : [], //
		bw_markup : [],
		color_markup : [],
		cover_markup : [],
		color_sheets : [],
		bw_sheets : [],
		color_cost : [],
		bw_cost : [],
		price : []
	},
	created : function(){		
		
		//this.d = this.d.reverse();
	},
	
	watch : {
		calc_data : function( val ){
			jQuery( this.$el ).children().empty();
			this.d = [];
			this.e = [];
			this.bw_markup = [];
			this.color_markup = [];
			this.cover_markup = [];
			this.color_sheets = [];
			this.bw_sheets = [];
			this.color_cost = [];
			this.bw_cost = [];
			this.price = [];
			
			this.setChart();
			return val;
		}
	}, 
	
	mounted: function(){
	
		this.setChart();
		
		
		
	},
	
	methods : {
	
		setChart : function(){
			var q = this.calc_data.q;
			
		for( var i=q.length-1; i>=0; i-- ){
		
			this.price.push( [ 
				q[i]._quantity, 
				q[ i ]._regular_price
				] );
			this.d.push( [ 
				q[i]._quantity, 
				Math.round(q[ i ]._regular_price / q[ i ]._quantity * 100)/100
				] );
				
			this.e.push( [ 
				q[i]._quantity, 
				q[i].variant_production.totals.average_markup
				] );					
			
			if( typeof q[i].variant_production.bw == 'object' ){
				this.bw_markup.push( [ 
					q[i]._quantity, 
					q[i].variant_production.bw.markup
					] );					
				this.bw_sheets.push( [ 
					q[i]._quantity, 
					q[i].variant_production.totals.bw_sheets
					] );					

				this.bw_cost.push( [ 
					q[i]._quantity, 
					q[i].variant_production.totals.bw_cost
					] );
			
			
			}
				
			this.color_markup.push( [ 
				q[i]._quantity, 
				q[i].variant_production.color.markup
				] );				
			
			if( typeof q[i].variant_production.cover == 'object' ){
				this.cover_markup.push( [ 
					q[i]._quantity, 
					q[i].variant_production.cover.markup
					] );
			}
				
			this.color_sheets.push( [ 
				q[i]._quantity, 
				q[i].variant_production.totals.color_sheets
				] );	
				
									
				
			this.color_cost.push( [ 
				q[i]._quantity, 
				q[i].variant_production.totals.color_cost
				] );					
			
			}

			jQuery.plot( jQuery(this.$el).find("#placeholder1"), 
				[
					//{ label: "cena szt.",  data: this.d, color:'red'},
					{ label: "śr. marża",  data: this.e, color:'green'},
					{ label: "1+1 marża",  data: this.bw_markup, color:'black'},
					{ label: "4+4 marża",  data: this.color_markup, color:'orange'},
					{ label: "okładka marża",  data: this.cover_markup, color:'purple'}
				], {
					series: {
						lines: { show: true , fill: true, fillColor: "rgba(255, 255, 255, 0.2)"},
						points: { show: true, radius: 1}
					},
					xaxis: {
						ticks: [20,40,60,80,100,120,140,160,180,200,250,300,400,500,600,700,800, 900, 1000, 2000, 3000, 5000],

					},
					yaxis: {
						ticks: 30,

					},
					grid: {
						backgroundColor: { colors: ["#555", "#222"] },
						color: '#bbb'
					},
					legend: {
						
						color: '#222',
						noColumns: 6,
						container: '#chart-legend', 						
						position: "ne",
						labelBoxBorderColor: '#ddd'
					}
			});
		
			jQuery.plot( jQuery(this.$el).find("#placeholder2"), 
				[
					{ label: "cena całość",  data: this.price, color:'green'},
					//{ label: "A3 kolor",  data: this.color_sheets, color:'red'},
				//	{ label: "A3 cz-b",  data: this.bw_sheets, color:'black'},
					
				], {
					series: {
						lines: { show: true , fill: true, fillColor: "rgba(255, 255, 255, 0.2)"},
						points: { show: true, radius: 1}
					},
					xaxis: {
						ticks: this.d.length,

					},
					yaxis: {
						ticks: 10,

					},
					grid: {
						backgroundColor: { colors: ["#555", "#222"] },
						color: '#bbb'
					},
					legend: {
						
						color: '#222',
						noColumns: 6,
						container: '#chart-legend2', 						
						position: "ne",
						labelBoxBorderColor: '#ddd'
					}
			});
		
		}
	
	}
	
});