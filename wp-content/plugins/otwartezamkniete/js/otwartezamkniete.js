// JavaScript Document


var otwarteZamkniete_app = new Vue({
	
	el: "#otwarte-zamkniete",
	
	template: '<div id="otwarte-zamkniete">{{ workingHours.open ? \'otwarte\' : \'zamknięte\' }}</div>',
	
	data : {	
    
		workingHours : otwartezamkniete_data
		
	}
	
});