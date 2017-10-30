(function($, w){
	
	var gformat = function( args ){
		
		this.args = args || {};
		
		this.selectFormat = function(){
			var counter = 0;
			for( var i in this.less_waste_format ){				
				var _format = this.args.prod_format[ this.less_waste_format[i].slug ];
				_format.$format_rect.addClass( 'selected-format-' + counter );
				counter++;
			}
			
		}
		
		
		this.set = function(){
			
			if( typeof this.args.model == 'undefined' ){
				this.args.model = {
					'instance_id' : 'gaad-format-app',	
					'unit' : 'px'
				}
			}
			
			
			/*
			* Ustawianie formatów arkuszy produkcyjnych 
			*/
			if( typeof this.args.prod_format == 'undefined' ){
				this.args.prod_format = {
					a3p : {
						w: 440,
						h: 315,
						margin : 0
					},
					b4 : {
						w: 350,
						h: 250,
						margin : 0
					},
					ra3 : {
						w: 430,
						h: 305,
						margin : 0
					},
					sra3p : {
						w: 487,
						h: 330,
						margin : 0
					},
					sra3 : {
						w: 450,
						h: 320,
						margin : 0
					},
					ra3p : {
						w: 440,
						h: 315,
						margin : 0
					}
				}
			}
			
			
			jQuery( '#' + this.args.model.instance_id ).remove();
			if( !jQuery( '#' + this.args.model.instance_id ).is( '*' ) ){
				var el = jQuery('<div>').attr({ 'id' : this.args.model.instance_id });
				el.appendTo('body');				
			} 
			
			
			return this;
		}	
		
		this.returnJSON = function(){
			var _json = {};
			
			for( var i in this.less_waste_format ){
				var _format = this.args.prod_format[ this.less_waste_format [i].slug ];
				var __format = jQuery.extend({}, _format );
				delete __format.$format_rect;
				_json[ i ] = __format;				
			}
			
			return _json;
		}
		
		this.drawURect = function( ){
			
			var urect = this.args.uformat;
			var urect_area =  urect.w *  urect.h;
			var $urect = jQuery( '<div>' )
				.addClass('uformat')
				.css({
					'width' : urect.w + this.args.model.unit,
					'height' : urect.h + this.args.model.unit,
					'margin' : urect.bleed + this.args.model.unit,
				});
			
			for( var i in this.args.prod_format ){
				var $rect = this.args.prod_format[ i ].$format_rect;
				var _format = this.args.prod_format[ i ];
				var resided = false;
				
				this.args.prod_format[ i ].area = _format.w * _format.h;
				this.args.prod_format[ i ].uarea = 0;
				
				while( !resided ){
					
					if( typeof this.args.prod_format[ i ].counter != 'undefined' ){
						$urect.css({
							'height' : urect.w + this.args.model.unit,
							'width' : urect.h + this.args.model.unit							
						});
					}
					
					var $current_urect = $urect.clone();
					$rect.append( $current_urect );
					this.args.prod_format[ i ].uarea += urect_area;
					$rect_w = $rect.width() + _format.margin * 2;
					$rect_h = $rect.height() + _format.margin * 2;
					
					
					if( _format.w != $rect_w || _format.h != $rect_h  ){
						$current_urect.remove();
						counter --;
						this.args.prod_format[ i ].uarea -= urect_area;
						
						//* pierwsze podejscie, poziomo
						if( typeof this.args.prod_format[ i ].counter == 'undefined' ){
							this.args.prod_format[ i ].counter = $rect.find('.uformat').length;
							
							/*							
							* czyszczenie formatu produkcyjnego
							*/
							$rect.empty();
							this.args.prod_format[ i ].uarea = 0;
							
						} else {
							this.args.prod_format[ i ].counter = 
								this.args.prod_format[ i ].counter < $rect.find('.uformat').length ?
								$rect.find('.uformat').length
								: this.args.prod_format[ i ].counter;
							
							//resetowanie
							$urect.css({
								'width' : urect.w + this.args.model.unit,
								'height' : urect.h + this.args.model.unit							
							});
							
							resided = true;	
							this.args.prod_format[ i ].waste = ( _format.area - _format.uarea ) / _format.counter;
							
						}						
					}
				}				
			}
			
			
			/*
			* Sprawdzanie przy którym formacie jest najmniejsza strata materialu
			*/
			var less_waste_format = []; 
			var current_waste;
			var counter = 0;
			for( var i in this.args.prod_format ){
				var _format = this.args.prod_format[ i ];		
				
				if( typeof current_waste == 'undefined' ){
					current_waste = _format.waste / _format.counter;
				}
				
				if( _format.waste / _format.counter <= current_waste ){
					less_waste_format[ counter ] = _format;
					less_waste_format[ counter ].slug = i;
					current_waste = _format.waste / _format.counter;
					counter ++;
				}
			}
			
			this.less_waste_format = less_waste_format.reverse();
			return this;
		}
		
		this.drawRect = function(){
			
			for( var i in this.args.prod_format ){
				var _format = this.args.prod_format[ i ];
				var $format_rect = jQuery('<div>')
					.addClass( 'format format-' + i )
					.attr({						
					})
					.css({
						'width' : _format.w + this.args.model.unit,
						'min-height' : _format.h + this.args.model.unit,
						'padding' : _format.margin + this.args.model.unit,
					});				
				
				
				jQuery('<style>').attr({ 'id': i, 'type' : 'text/css' }).html(
					'.format-' + i + ':after { content: "'+ i +' ('+ _format.w +'x'+ _format.h +')"; position:absolute; bottom:-2rem; right:0;}'
				).appendTo('head');
				
				$format_rect.appendTo( this.$el );
				this.args.prod_format[ i ].$format_rect = $format_rect;
			}
			
			return this;
		}	
				
		this.init = function( ){
			this.set();			
			this.$el = $( '#' + this.args.model.instance_id );			
			/*
			//ukrywanie ui
			this.$el.css({
				'opacity' : 0,
				'overflow' : 'hidden',
				'width' : 0,
				'height' : 0,
				'positon' : 'absolute'				
				
			});
			*/
			this.drawRect();
			this.drawURect();
			this.selectFormat();
			
			return this.returnJSON();
		}
		
		return this;
	}

	
	
	w.gformat = gformat;
	
})(jQuery, window);