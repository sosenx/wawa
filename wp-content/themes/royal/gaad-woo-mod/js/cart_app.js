// Speed up calls to hasOwnProperty
var hasOwnProperty = Object.prototype.hasOwnProperty;

function isEmpty(obj) {

    // null and undefined are "empty"
    if (obj == null) return true;

    // Assume if it has a length property with a non-zero value
    // that that property is correct.
    if (obj.length > 0)    return false;
    if (obj.length === 0)  return true;

    // If it isn't an object at this point
    // it is empty, but it can't be anything *but* empty
    // Is it empty?  Depends on your application.
    if (typeof obj !== "object") return true;

    // Otherwise, does it have any properties of its own?
    // Note that this doesn't handle
    // toString and valueOf enumeration bugs in IE < 9
    for (var key in obj) {
        if (hasOwnProperty.call(obj, key)) return false;
    }

    return true;
}



var payment = Vue.extend({
	template : '#order-payment',
	
	data : function(){
		return {			
		}
	},
	
	watch : {
		order_payment : function(){
		debugger
			this.checkPayment();
			return;
		}	
	},
	
	methods : {
		afterCheckPayment : function( data ){
			if( data !== '' ){
				
				if( typeof data == 'object' && data.error == 0 ){					
					this.$root.payment.status = parseInt( data.error );
					this.$root.refresh('payment');
					
					var tmp = this.$parent.order_payment;  this.$parent.order_payment = { id: false };
					this.$parent.order_payment = tmp;
					//debugger;
				}
				
				//ponowienie sprawdzania
				if( typeof data == 'object' && data.error == -1 ){				
					var order_id = parseInt(window.location.search.substr(1).split('=')[1]);
					if( !isNaN( order_id ) ){
						this.$root.payment.id = order_id;
					}
					
					this.checkPayment();	
					console.log('-1 ponawiam sprawdzanei');
				}
				
				if( data == '0'){
					debugger
				}
				
			} else {
				
				
			
			}
		},
		
		checkPayment : function(){
			if( !this.payment_ok() ){
				setTimeout(this.checkPayment2, 1000 );		
			}
		},
		
		checkPayment2 : function(){
			
			var data = {
				action: 'gaad_verify_payment',
				_id: order_payment.id
			}

			jQuery.post({
				url: 'http://' + window.location.host + '/wp-admin/admin-ajax.php',
				data: data,	
				success: this.afterCheckPayment,
				error: function(errorThrown){
					console.log(errorThrown);
				}

			});
			
		},
		
		processing_payment : function(){
			return !( typeof this.$root.payment.status !== 'undefined' && this.$root.payment.status == 0 );
		},
		payment_ok : function(){
			return !this.processing_payment();
		}
		
	},
	
	props : {		
		order_payment : {
			validator: function ( val ) {				
				if( typeof val.id !== 'undefiend'){					
					return true;					
				}				
			return false;				
		  	}
		}
	}
});

var cartTotal = Vue.extend({
	template : '#cart-total',
	
	props : [ 'cart_sett', 'items', 'order_total', 'otype', 'order_payment' ],
	
	watch : {		
	},
	
	methods : {
		checkFiles : function(){
			
			var raport = {
				min_status : 3000,
				total_status : false
			};
			
			for( var i in this.$parent.items ){
				var item = this.$parent.items[i];
				if( typeof item === 'object' && typeof item.att_list === 'object'){
					var files = item.att_list;
					
					if( files.length == 0 ){
						raport.min_status = -1;
						continue;
					}
					
					for( var j=0; j<files.length; j++ ){
						
						raport[ files[j].file_id ] = {
							status : files[j].attachment_status
						}
					
						if( raport.min_status > files[j].attachment_status ){
							raport.min_status = files[j].attachment_status;
						}
					}
				}
			}
			
			
			if( raport.min_status >= this.$parent.order_totals.min_file_status ){
				raport.total_status = true;
			}
			if( raport.min_status == 3000 ){
				raport.min_status = -1;
				raport.total_status = false;
			}
			
			this.files_total = raport;
			return raport;
			
		},
		
		afterPlaceOrder : function( data ){
			window.location = '/u/zamowienie/platnosci/'
		},
		
		payOrder2 : function( data ){
			debugger
		},
		
		payOrder1 : function( data ){
			//var data = JSON.parse(data);
			if( typeof data.token != 'undefined' ){
				window.location = 'https://sandbox.przelewy24.pl/trnRequest/' + data.token;
			}
		},
		
		payOrder: function(){
			
			var data = {
				action 	: 'gaad_process_payment',
				_id 	: this.cart_sett.order_id

			}
			jQuery.post({
					url: 'http://' + window.location.host + '/wp-admin/admin-ajax.php',
					data: data,
					success:this.payOrder1,
					error: function(errorThrown){
						console.log(errorThrown);
					}

				});		
		
		},
		
		pushOrder : function( ){
			this[ this.otype + 'Order']();
		},		
		
		placeOrder : function(){
			if( this.checkFiles().total_status ){
				
				
				var data = {
					action: 'gaad_set_order_status',
					_id : this.cart_sett.order_id,
					_st : 'pending'
				}

				jQuery.post({
					url: 'http://' + window.location.host + '/wp-admin/admin-ajax.php',
					data: data,
					success:this.afterPlaceOrder,
					error: function(errorThrown){
						console.log(errorThrown);
					}

				});		
				
			} else {
				this.$parent.checkFilesError();
			}
		},
		
		
	},
	
	data : function(){
		this.checkFiles();
		return {			
			//order_total : order_totals,
			files_total : this.files_total						
		}
	}
});

var delFile = Vue.extend({
	template : '#delete-item-file',	
});
var submitStatusIcon = Vue.extend({
	template : '#submit-status-icon',	
	props : ['status']
});
var statusIcon = Vue.extend({
	template : '#status-icon',	
	props : ['status']
});
var itemOverlay = Vue.extend({
	template : '#item-overlay',	
	props : ['status']
});

var itemDropZone = Vue.extend({
	template : '<div class="cart-drop-zone" v-if="dropzoneEnabled"><div class="event-layer" v-on:dragenter="fileDroppedPrevent($event)" v-on:dragover="fileDroppedPrevent($event)" v-on:drop="fileDropped($event)"><h1>Upuść pliki na to pole</h1></div></div>',
	
	props : [ 'dropzoneEnabled', 'dropZoneItemId' ],
	
	methods : {
	
		dropzoneStatus : function( e ){
			return 
			(this.dropzoneEnabled === "true");
		},
		
		fileDroppedPrevent : function( e ){
			e.preventDefault();
			e.stopPropagation();
		},
		
		fileDropped : function( e ){ 
			e.preventDefault();
			e.stopPropagation();
			var f = e.dataTransfer.files;

			var in_progress = typeof this.aa != 'undefined' ?
				( typeof this.$parent.aa[ this.dropZoneItemId ] != 'undefined' ? this.$parent.aa.data.files.length : 0  )
				: 0;
			var current_fiels = this.$parent.items[ this.dropZoneItemId ].att_list.length;
			var total_files = in_progress + current_fiels + f.length;
			var max_files = parseInt( typeof this.$parent.items[ this.dropZoneItemId ].pa_max_files != 'undefined' ? this.$parent.items[ this.dropZoneItemId ].pa_max_files : 1);			
			var max_files_1 = max_files - current_fiels;
			
			if( total_files > max_files ){				
				this.$parent.tooManyFilesError();
				this.$parent.items[ this.dropZoneItemId ].dropzone = false;
				this.$parent.refresh('items');
				return;
			}
			
			this.$parent.items[ this.dropZoneItemId ].overlay = true;
			
			for( var i=0; i<f.length; i++ ){
				var file = f[ i ];
				
				this.$parent.createAttachmentAplet( this.dropZoneItemId );
								
					var data = new FormData();		
					var files = [ file ];					
					jQuery.each(files, function(key, value){ data.append(key, value); });

					jQuery.ajax({
					
						 xhr: function() {
							var xhr = new window.XMLHttpRequest();
							xhr.upload.addEventListener("progress", function(evt) {
								if (evt.lengthComputable) {
									var percentComplete = evt.loaded / evt.total;
									console.log('upload: ' + percentComplete );
									
								}
						   }, false);

						   xhr.addEventListener("progress", function(evt) {
							   if (evt.lengthComputable) {
								   var percentComplete = evt.loaded / evt.total;
								   //Do something with download progress
							   }
						   }, false);

						   return xhr;
						},
						
						onprogress: function(a, b){
							debugger
						},
						
						url: 'http://' + window.location.host + '/wp-content/themes/royal/gaad-woo-mod/upload-file.php?mf='+max_files_1+'&id=' +  this.dropZoneItemId,
						type: 'POST',
						data: data,
						cache: false,
						dataType: 'json',
						processData: false, // Don't process the files
						contentType: false, // Set content type to false as jQuery will tell the server its a query string request
						success:this.$parent.fileUploaded,
						});


				
				
			}
		/*
		* Wyłączenie drop zona
		*/
		this.$parent.items[ this.dropZoneItemId ].dropzone = false;
			
		},
	}
	
});

Vue.component( 'payment', payment );
Vue.component( 'overlay', itemOverlay );
Vue.component( 'del-file', delFile );
Vue.component( 'drop-zone', itemDropZone );
Vue.component( 'cart-total', cartTotal );
Vue.component( 'status-icon', statusIcon );
Vue.component( 'submit-status-icon', submitStatusIcon );

var gaad_cart_data = {
		order_totals : {},
		cart_sett : {},
		payment : {},
		items : {},
		
		/*tablica buforowa dla items*/
		aa : {},
		
		ship: {
			days : 0,
			date : null
		}
	};

var gaad_cart_watch = {
		
		/*
		* Przeglada tablice swiezo uploadowanych plikow i ustawia akcje zalezne od statusow ich elementow
		*/
		aa : function( val ){
			for( var i in this.aa){
				var a = this.aa[ i ];
				
				if( a.attachmentStatus == 1 ){
					
					/*
					* Do produktu zamowienia moze byc dopietych kilka plikow
					* kazdy z nich ma niezalezne statusy
					* zamowienie ma status taki, jak jego elementy  ( najslabsze ogniwo );
					*/
					var attachments = a.data;
					for( var att in attachments ){
						var attachment = attachments[att];
						/*
						* Plik jest przeznaczony do sprawdzenia, status jest nadawany zaraz po  uplodowaniu pliku
						*/
						if( attachment.attachmentStatus == 1 ){
							
							
							for( var f in attachment.files){
								/*
								* Zmiana statusu na: W trakcie wstępnego sprawdzania 
								*/
								var file_name = attachment.files[ f ];

								if( !file_name ){
									debugger	
								}

								this.aa[ i ].attachmentStatus = 11;									
								this.aa[ i ].data[att].attachmentStatus = 11;
								/*
								* Aktualizacja bazy danch							
								*/
								this.updateItemStatus( i, file_name, 11 );

								var data = {
									action: 'gaad_check_file',
									item_id: i,
									file_name : file_name,
									a : a
								}

								jQuery.post({
									url: 'http://' + window.location.host + '/wp-admin/admin-ajax.php',
									data: data,
									success:this.afterCheckFile,
									error: function(errorThrown){
										console.log(errorThrown);
									}

								});								
							}
						}							
					}					
				}

				
				/*
				* Poziom główny Attachment, nie mylic aatablica files w któej zawarte są pliki atachmentu
				*
				* Przenoszenie itema do tablicy att_list
				*/
				if( a.attachmentStatus >= 200 ) {
					this.moveToAttList( a );
				}
				
				
				/*
				* Poziom główny Attachment, obsługa wieloplikowych archiwum
				*				
				*/
				if( a.attachmentStatus == 150 ) {
					//debugger
				}
				
			}
		},
	
		/*
		* Dodadkowe obliczenia wewnatrz elementu zamówienia
		*/
		items : function( val ){
			for( i in val){
				var item = val[i];
				item.after_tax = Math.round( ( parseFloat(item.line_total) + parseFloat(item.line_tax) ) * 100) / 100;
				
				/*
				* Odswiewanie atrybutów opisujących cechy produktu do oddzielnej tablicy 
				*/
				
					item.pa_list = this.getPaList( item );
				
				 
				/*
				* Odswiewanie zalacznikow do oddzielnej tablicy 
				*/
				if( !item.paListUpdated || typeof item.paListUpdated === 'undefined' ){
					item.att_list = this.getAttList( item );
				}
				
				
				if( typeof item.dropzone === 'undefined' ){
					item.dropzone = false;
				}
				
				if( typeof item.overlay === 'undefined' ){
					item.overlay = false;
				}
				
				/*
				* Obliczanie terminu dostawy całego zamówienia poprzez sprawdzenie terminu dostawy najwolniej realizownego elementu
				*/
				var ship_days = parseInt( item.ship_days );
				
				if( ship_days > this.ship.days ){
					this.ship.days = ship_days;
					this.ship.date = item.ship_date;
				}
				
				
			}
			
			if( typeof this.$refs['cart-total'] !== 'undefined' ){
				this.$refs['cart-total'].checkFiles();
			} else {
				console.log( 'ref cart-total undefined' );
			}
			
			return val;
		}
		
	};

var gaad_cart_methods = {
		isEmpty : function( obj ){
			return isEmpty( obj );
		},
		
		historyBack : function( ){
			history.back();
		},

		afterRefreshTotals: function(data){
			this.order_totals = data;
		},
		refreshTotals : function (){
			var data = {
				action: 'gaad_get_totals',
				order_id : cart_sett.order_id
			}

			jQuery.post({
				url: 'http://' + window.location.host + '/wp-admin/admin-ajax.php',
				data: data,	
				success : this.afterRefreshTotals,
				error: function(errorThrown){
					console.log(errorThrown);
				}

			}); 
		
		},
		
		deleteFileFromOrderItem : function( item_id, file_id ){
			if( confirm("Czy na pewno chcesz skasowac ten plik?") ){
				for( var i in this.items [ item_id ].att_list ){

					if( this.items [ item_id ].att_list[ i ].file_id === file_id ){

						var data = {
							action: 'gaad_delete_attachment_file',
							item_id: item_id,
							file_name : this.items [ item_id ].att_list[ i ].path
						}

						jQuery.post({
							url: 'http://' + window.location.host + '/wp-admin/admin-ajax.php',
							data: data,	
							
							error: function(errorThrown){
								console.log(errorThrown);
							}

						}); 


						this.items [ item_id ].att_list.splice( i , 1 );
						delete this.items [ item_id ][ 'attachment-' + file_id ];
						delete this.items [ item_id ][ 'ststus-' + file_id ];	
						delete this.items [ item_id ].item_meta[ '_attachment-' + file_id ];
						delete this.items [ item_id ].item_meta[ '_status-' + file_id ];	

						gaad_cart.refresh('items');
						return true;
					}
				}			
			}
		},
	
		deleteFileFromArray: function(item_id, file_id){
			for(var i in this.aa[ item_id ].data ){
				var item = this.aa[ item_id].data[ i ];
				var files = item.files;
				
				for( var j in files){
					var file = files[ j ];
					if( file_id == this.getFileId( file ) ){
						
						if( this.aa[ item_id ].data[i].files.length == 1 ){
							this.aa[ item_id ].data.splice( i, 1 );
							
							if( this.aa[ item_id ].data.length == 1 ){
								delete this.aa[ item_id ];
							}
							
						} else {
							debugger
						}
					}					
				}
			}
		}, 
		
		
		/*
		* Umieszcza item bezposrednio na liscie uploadowanych plików
		*/
		moveToAttList : function( item ){
			
			"use strict";

			for(var i in item.data){
				var file = item.data[ i ].files[0];
				if( typeof file === 'undefined' ){
					continue;
				}
				var r = {
					file_id : this.getFileId( file ),
					path : file,
					th_path : this.thumbnailPath( file ),
					attachment_status : parseInt(item.attachmentStatus)
				};
				
				var file_id = this.getFileId( file );
				
				this.items[ item.item_id ].att_list.unshift( r );
				this.items[ item.item_id ].paListUpdated = true;
				
				this.items [ item.item_id ][ 'attachment-' + file_id ] = file;
				this.items [ item.item_id ][ 'status-' + file_id ] = item.data[ i ].attachmentStatus;	
				this.items [ item.item_id ].item_meta[ '_attachment-' + file_id ] = [];
				this.items [ item.item_id ].item_meta[ '_attachment-' + file_id ][0] = file;
				this.items [ item.item_id ].item_meta[ '_status-' + file_id ] = [];
				this.items [ item.item_id ].item_meta[ '_status-' + file_id ][0] = item.data[ i ].attachmentStatus;
				
				this.deleteFileFromArray(item.item_id, r.file_id );				
				
			}		
			
			this.refresh('items');
			
		},
		
		arrayRemove : function(array, from, to) {
		  var rest = array.slice((to || from) + 1 || array.length);
		  array.length = from < 0 ? array.length + from : from;
		  return array.push.apply(array, rest);
		},
		
		updateItemStatusMeta : function( data ){
			if( data.status > 11 ){
			if( typeof this.aa[ data.item_id ] !== 'undefined' ){
				for( var item in this.aa[ data.item_id ].data ){
					var itemO = this.aa[ data.item_id ].data[ item ];
					
					for( var i = 0; i < itemO.files.length; i++ ){
						if( itemO.files[i] === data.file_name ){
						
							var f = {
								attachment_status: data.status,
								file_id: this.getFileId( data.file_name ),
								path: data.file_name,
								th_path: this.thumbnailPath( data.file_name )
							};
							
							/*
							* Uzupełnianie pol z których funkcja setPaList odtwarza listę uploadowanych plików
							*/
							
							this.items [ data.item_id ][ 'attachment-' + f.file_id ] = f.path;
							this.items [ data.item_id ][ 'status-' + f.file_id ] = f.attachment_status;	
							this.items [ data.item_id ].item_meta[ '_attachment-' + f.file_id ] = [];
							this.items [ data.item_id ].item_meta[ '_attachment-' + f.file_id ][0] = f.path;
							this.items [ data.item_id ].item_meta[ '_status-' + f.file_id ] = [];
							this.items [ data.item_id ].item_meta[ '_status-' + f.file_id ][0] = f.attachment_status;
							
							this.items[ data.item_id ].att_list.unshift( f );
							this.arrayRemove( itemO.files, i );							
						}
					}
					
					if( itemO.files.length == 0 ){
						this.arrayRemove( this.aa[ data.item_id ].data, item );	
						continue;
					}
					
				}
				}
				if( typeof this.aa[ data.item_id ] !== 'undefined' ){
					if( this.aa[ data.item_id ].data.length == 0 ){
						delete this.aa[ data.item_id ]
					}				
				}
				
				this.refresh( 'aa' );
				this.$refs['cart-total'].checkFiles();
			}			
		},
		
		refresh : function( dataObjName ){
			var tmp = this[ dataObjName ];
			this[ dataObjName ] = null;
			this[ dataObjName ] = tmp;			
		},
		
		
		/*
		* BACKEND
		* Aktualizowanie statusu pliku wchodzącego w sklad attachmentu
		*/
		updateItemStatus : function( item_id, filename, status ){
			
			var data = {
				action: 'gaad_update_item_status',
				item_id: item_id,
				file_name : filename,
				status : status
			}
				
			jQuery.post({
				url: 'http://' + window.location.host + '/wp-admin/admin-ajax.php',
				data: data,		
				success:this.updateItemStatusMeta,
				error: function(errorThrown){
					console.log(errorThrown);
				}				
			});  			
		},		
		
		
		basename: function( src ){
			if( typeof src === 'undefined' || src === null ){
				return null;
			}
			return src.split(/(\\|\/)/g).pop();
		},
		
		thumbnailPath : function( src ){
			var th_path = src.split(/(\\|\/)/g);
			th_path[ th_path.length - 1 ] = 'th_' + th_path[ th_path.length - 1 ];
			return th_path.join('');
		},
		
		getAttList : function( val ){
			var patt = /^_attachment-(.*)/;
			var r = [];
			for( var i in val.item_meta){
				var match = patt.exec( i );
				if( typeof val.item_meta[ i ] !== "function" && match ){					
					
					r.push( {
						file_id : match[1],
						path : val.item_meta[ i ][0],
						th_path : this.thumbnailPath( val.item_meta[ i ][0] ),
						attachment_status : parseInt( typeof val.item_meta[ '_status-' + match[1] ] === 'string' ? val.item_meta[ '_status-' + match[1] ] : val.item_meta[ '_status-' + match[1] ][0] )
					});	
					
				}
			}
			return r;
		},
		
		/*
		* Szukanie najnizszego statusu wsrod dodanych plikow do elementow zamowienia
		*/
		updataAAStatus : function( attId ){			
			var tmp_aa = this.aa;
			var lowestStatus = 10000;
			for(var i in tmp_aa){
				var files = tmp_aa[ i ].data;
				
				for(var j in files){
					var file = files[ j ];
					if( file.attachmentStatus < lowestStatus ){
						lowestStatus = file.attachmentStatus; 
					}
				}				
				
				tmp_aa[ i ].attachmentStatus = lowestStatus;	
			}
			
			this.aa = false;
			this.aa = tmp_aa;	
		},
		
		getFileId : function( path ){
			var patt = /[0-9]+-([a-zA-Z0-9]+)-.*/;
			var filename = this.basename( path );
			var r = patt.exec( filename );
			
			if( typeof r[1] !== 'undefined' ){
				return r[1];
			}
			return 'error-id';
		},
		
		afterCheckFile : function( data ){			
			var tmp_aa = this.aa;
			var files = tmp_aa[ data.item_id ].data;
			
			for( var i in files ){
				
				var tmp = files[i].files;
				for( var j in tmp ){
					if( tmp[j] == data.file_name ){
						
						if( tmp.length > 1 ){
							files[i].attachmentStatus = 150;
						} else {
							files[i].attachmentStatus = data.attachmentStatus;	
						}
						
						
						this.updateItemStatus( data.item_id, data.file_name, data.attachmentStatus );
					}					
				}
									
			}
			
			this.aa = false;
			this.aa = tmp_aa;			
			this.updataAAStatus( data.item_id );
		},
		
		updateAttachmentMeta : function( item_id, data ){
			var data = {
					action: 'gaad_update_attachment_meta',
					item_id: item_id,
					attachment_data : data
				}
				
				jQuery.post({
					url: 'http://' + window.location.host + '/wp-admin/admin-ajax.php',
					data: data,
					/*success:this.orderItemTrashed,*/
					error: function(errorThrown){
						console.log(errorThrown);
					}
				
				});  
			
			if( !this.items[item_id].paListUpdated || typeof this.items[item_id].paListUpdated === 'undefined' ){			
				this.items[item_id].att_list = this.getAttList( this.items[item_id], data );	
			}
			
		},
		
		/*
		* Wyświetla komunikat o błędzie poprawnosci dołączonych plików do zamóeinai
		*/
		checkFilesError : function(){
			var msg = typeof msg === 'undefined' ? 'Pliki dołączone do zamówienia nie są prawidłowe, sprawdź pliki i spróbuj ponownie.' : msg;
			alert( msg );
			return false;
		},
		
		/**
		* 
		*
		* @return void
		*/
		 tooManyFilesError : function( msg ) {
		 	var msg = typeof msg === 'undefined' ? 'Przekroczono maksymalną ilość plików dla tego elementu. Usuń pliki i spróbuj jeszcze raz.' : msg;
			alert( msg );
			return false;
		},
		
		/*
		* statusy: 0 upload- 1 -sprawdzanie
		*/
		fileUploaded : function( data ){	
		
			var status_151 = typeof data.attachment_status != 'undefined' ? data.attachment_status : false;
		
			if( status_151 == 151 ){
				this.tooManyFilesError("W archiwum znajduje się zbyt duża liczba plików.");
				return;
			}
		
			var aplet = this.aa[data.id];
			var tmp_aa = this.aa;
			
			tmp_aa[data.id].data = tmp_aa[data.id].data || [];
			data.attachmentStatus = 1;
			tmp_aa[data.id].data.push( data );
			tmp_aa[data.id].attachmentStatus = 1;

			this.updateAttachmentMeta( data.id, data );

			this.items[data.id].overlay = false;
			
			this.aa = false;
			this.aa = tmp_aa;
		},
		
		uploadAttachment: function(val, e){
			var data = new FormData();
			var files = e.target.files
			 
			this.items[val].overlay = true;
			this.refresh('items');
			
			jQuery.each(files, function(key, value){ data.append(key, value); });

			jQuery.ajax({
				url: 'http://' + window.location.host + '/wp-content/themes/royal/gaad-woo-mod/upload-file.php?id=' + val,
				type: 'POST',
				data: data,
				cache: false,
				dataType: 'json',
				processData: false, // Don't process the files
				contentType: false, // Set content type to false as jQuery will tell the server its a query string request
				success:this.fileUploaded,
				});
			
		},
		
		destroyAttachmentAplet : function( item_id ){
		
		},
		
		createAttachmentAplet : function( item_id ){
			var attAplet = {
				item_id : item_id,
				just_show : true,
				attachmentStatus : 0,
			}
			
			if( typeof this.aa[ item_id ] === 'undefined' ){
				var temp_attachmentAplets = this.aa;
				temp_attachmentAplets[ item_id ] = attAplet;				
				this.aa = false;				
				this.aa = temp_attachmentAplets;				
			}
		},
		
		isAdvancedUpload : function(){
			var isAdvancedUpload = function() {
			  var div = document.createElement('div');
			  return (('draggable' in div) || ('ondragstart' in div && 'ondrop' in div)) && 'FormData' in window && 'FileReader' in window;
			}();
			
			if( !( this.cart_sett.advanced_upload )){
				return false;
			}
			
			
			return isAdvancedUpload;
		},
		
		openAttachmetsAplet  : function( val ){
		
			if( this.isAdvancedUpload() ){
				this.items[ val ].dropzone = true;
				this.refresh( 'items' );
			} else {
				this.createAttachmentAplet( val );
			}
			
		},
		
		orderItemTrashed : function( data ){
			var newItems = {};
			for( var i in this.items ){
				if( i !== data.id ){
					newItems[ i ] = this.items[i];
				}
			}
			this.items = newItems;
			this.refreshTotals();
		},
		
		trashOrderItem : function( val ){
			
			if( confirm('delete') ){
				
				var data = {
					action: 'gaad_remove_item_from_order',
					item_id: val
				}
				
				jQuery.post({
					url: 'http://' + window.location.host + '/wp-admin/admin-ajax.php',
					data: data,
					success:this.orderItemTrashed,
					error: function(errorThrown){
						console.log(errorThrown);
					}
				
				
			
				});  
			
			}
			
		},
		
		getPaList : function( item ){		
			pa_list = [];
			for( var i in item.item_meta ){
				var patt = /^pa_.?/;
				if( patt.exec( i ) !== null ){
					
					//wyjątki, pomijanie parametrów dodadkowych
					if( 
						i == 'pa_item_status' ||
						i == 'pa_termin-wykonania' ||
						i == 'pa_naklad' 
					){
						continue
					}
					
					pa_list.push({
						slug : i,
						value : item.item_meta[ i ][0],
						label : item.labels.attributes[i].terms[ item.item_meta[ i ][0] ]
					});
				}
			}
			
			return pa_list;
		},
		
	};


Vue.filter( 'parseShipDate', function( value, input ){
	var months = [ '', 'styczeń', 'luty', 'marzec', 'kwiecień', 'maj', 'lipiec', 'sierpień', 'wrzesień', 'październik', 'listopad', 'grudzień' ];
	var date_parts = value.split('.');
	date_parts[1] = months[ parseInt( date_parts[ 1 ] ) ];
	return date_parts.join( ' ' );
} );



/**
* Komponuje obiekt startowy dla aplikacji vue Koszyk
*
* @return void
*/
function gaad_cart_starter ( el ) {
	var cart_starter = {
		template : '#cart-body',	
		data : gaad_cart_data,
		watch: gaad_cart_watch,	
		methods : gaad_cart_methods	
	};
	cart_starter.el = el;
	return cart_starter;
}

/**
* Tworzy obiekt Vue Koszyka w oparciu o dostarczone tablice danych
*
* @return void
*/
function new_gaad_cart( el, var_name, _items, _totals, _sett ){
	window[ 'order_totals' ] = {};
	var starter = gaad_cart_starter( el );
	starter.data.order_totals = _totals;
	
	window[ var_name ] = new Vue( starter );
	Vue.set( window[ var_name ], 'cart_sett', _sett);
	Vue.set( window[ var_name ], 'payment', order_payment);
	Vue.set( window[ var_name ], 'items', _items);
	
	return window[ var_name ];
}


/*
* Generowanie treści koszyka
* Sprawdzanie czy istnieje tablica dancyh dla wielu koszyków
*/

if( typeof window['multiple_carts'] !== 'undefined' || window['multiple_carts'] == true ){
	var carts = window['multiple_carts'];
	for( var i =0; i<carts.length; i++ ){
		var cart = carts[ i ];
		
		//new_gaad_cart( '#' + cart.var_name, cart.var_name, cart.order_items, cart.order_totals, cart.cart_sett )
		
		starter = {
			template : '#cart-body',	
			data : Object.assign({}, gaad_cart_data ),
			watch: gaad_cart_watch,	
			methods : Object.assign({}, gaad_cart_methods )
		};
		starter.data.cart_sett = cart.cart_sett;
		starter.data.order_totals = cart.order_totals;
		starter.data.order_id = cart.order_id;
		starter.data.items = cart.order_items;
		starter.el = '#' + cart.var_name;		
		starter.aa = {};		
		starter.ship = {
			days : 0,
			date : null
		};
		
		
		
		window[ cart.var_name ] = new Vue( starter );
		window[ cart.var_name ].refresh('items');
		
		
		console.log('created: '+cart.var_name);
		
		
	}
	
} else {
	var gaad_cart = new_gaad_cart( '#wawa_cart', 'gaad_cart', order_items, order_totals, cart_sett );
}





