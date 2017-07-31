var map;
var marker;
var geocoder;
var centerChangedLast;
var reverseGeocodedLast;
var currentReverseGeocodeResponse;
var find_pendent = 0;
var find_executing = 0;
var focusin = false;
var first_location_lat;
var first_location_lng;
function initiatemp(){
	if(typeof initateMapInt != "undefined")
		clearInterval(initateMapInt);
	if(!document.getElementById("address").value || first_location_lat === false)
		var latlng = new google.maps.LatLng(41.3947688, 2.0787283);
	else
		var latlng = new google.maps.LatLng(first_location_lat, first_location_lng);
	var myOptions = {
		zoom: 14,
		maxZoom: 14,
		center: latlng,
		mapTypeId: google.maps.MapTypeId.ROADMAP,
		componentRestrictions: {
			   country: 'es',
			   types: ['(cities)'],
		}
	};
	map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

	SearchBox = document.getElementById('address');
	autocomplete = new google.maps.places.Autocomplete(SearchBox, {
		componentRestrictions: {
			   country: 'es'
		}
	});

	setupEvents();
	centerChanged();
	$(".container .preload").hide();
}
function getReverseLatLon(results, status){
	if(status == "OK" && results){
		result = results[0];
		first_location_lat = result.geometry.location.lat();
		first_location_lng = result.geometry.location.lng();
	}else{
		first_location_lat = false;
	}
}
function initialize() {
	address = document.getElementById("address").value;
	geocoder = new google.maps.Geocoder();
	if(!address){
		initiatemp();
	}else{

		maps_lat = geocoder.geocode({
		   'address': address,
		   'partialmatch': true
	   	}, getReverseLatLon);
	   	initateMapInt = setInterval(function(){
			if(first_location_lat || first_location_lat === false){
				initiatemp();
			}
		}, 1);
	}
}

function setupEvents() {
  reverseGeocodedLast = new Date();
  centerChangedLast = new Date();

  setInterval(function() {
	if((new Date()).getSeconds() - centerChangedLast.getSeconds() > 1) {
	  if(reverseGeocodedLast.getTime() < centerChangedLast.getTime()){
			reverseGeocode();
		}
	}
  }, 1000);
	google.maps.event.trigger(map, 'resize');
  google.maps.event.addListener(map, 'zoom_changed', function() {
	document.getElementById("zoom_level").innerHTML = map.getZoom();
  });
  google.maps.event.addListener(map, 'dragstart', function() {
	  $("#crosshair").addClass("drag");
	  $("#crosshair").removeClass("loading fa-spin");
  });
  google.maps.event.addListener(map, 'zoom_changed', function() {
	  //$("#crosshair").removeClass("loading fa-spin");
  });
  google.maps.event.addListener(map, 'dragend', function() {
  	$("#crosshair").removeClass("drag");
  	dragTiming = setTimeout(function(){
		if(!$("#crosshair.drag").length){
  			$("#crosshair:not(drag)").addClass("loading fa-spin");
			$("#saveLocation").prop("disabled", true);
		}
	}, 1000);
	/*google.maps.event.addListener(map, 'place_changed', function() {
		$("#crosshair:not(drag)").addClass("loading fa-spin");
		$("#saveLocation").prop("disabled", true);
	});*/
  });
  google.maps.event.addListener(map, 'center_changed', centerChanged);

  google.maps.event.addDomListener(document.getElementById('crosshair'),'dblclick', function() {
	 map.setZoom(map.getZoom() + 1);
  });

}

function getCenterLatLngText() {
  return '(' + map.getCenter().lat() +', '+ map.getCenter().lng() +')';
}

function centerChanged() {
	centerChangedLast = new Date();
	var latlng = getCenterLatLngText();
	document.getElementById('latlng').innerHTML = latlng;
	document.getElementById('formatedAddress').innerHTML = '';
	currentReverseGeocodeResponse = null;
}

function reverseGeocode() {
  reverseGeocodedLast = new Date();
  geocoder.geocode({latLng:map.getCenter()},reverseGeocodeResult);
}

function reverseGeocodeResult(results, status) {
  currentReverseGeocodeResponse = results;
  if(status == 'OK') {
	if(results.length == 0) {
	  document.getElementById('formatedAddress').innerHTML = 'None';
	} else {
	  document.getElementById('formatedAddress').innerHTML = results[0].formatted_address;
	  localidad = results[0].address_components[2].long_name;
	  provincia = results[0].address_components[3].long_name;
	  region = results[0].address_components[4].long_name;
	  pais = results[0].address_components[5].long_name;
	 // console.log(localidad, provincia, region, pais);
	 if(!focusin){

 		$(".finder #address").val(localidad+", "+ pais);
		dragged = false;
	}
	  $.ajax({
		  url: "/ajax/geolocate/updateLocation",
		  data: {
			  	localidad: localidad,
			   	provincia: provincia,
			    region: region,
				complete: localidad+", "+pais
		  },
		  type: "POST",
		  dataType: "json",
		  success: function(data){
			  $("#crosshair").removeClass("loading fa-spin");
			  $("#saveLocation").prop("disabled", false);
			  $(".container .preload").hide();
			  //$("#saveLocation").prop("disabled", false);
			 // console.log(data);
			 // if(!cleared)
			  //	$(".finder #address").val(results[0].formatted_address);
		  }, complete: function(){
			  $("#crosshair").removeClass("loading fa-spin");
			  $(".container .preload").hide();
			  $("#saveLocation").prop("disabled", false);
		  }, error: function(){
			  $("#crosshair").removeClass("loading fa-spin");
			  $(".container .preload").hide();
			  $("#saveLocation").prop("disabled", false);
		  }
	  })
	  //console.log(localidad, provincia, region, pais);
	  find_executing = 0;
	}
  } else {
	document.getElementById('formatedAddress').innerHTML = 'Error';
  }
}
function geocode() {
  	var address = document.getElementById("address").value;
  	geocoder.geocode({
		'address': address,
		'partialmatch': true
	}, geocodeResult);
}

function geocodeResult(results, status) {
  if (status == 'OK' && results.length > 0) {
	map.fitBounds(results[0].geometry.viewport);
  } else {
	//alert("Geocode was not successful for the following reason: " + status);
  }
}

function addMarkerAtCenter() {
  var marker = new google.maps.Marker({
	  position: map.getCenter(),
	  map: map
  });

  var text = 'Lat/Lng: ' + getCenterLatLngText();
  if(currentReverseGeocodeResponse) {
	var addr = '';
	if(currentReverseGeocodeResponse.size == 0) {
	  addr = 'None';
	} else {
	  addr = currentReverseGeocodeResponse[0].formatted_address;
	}
	text = text + '<br>' + 'address: <br>' + addr;
  }

  var infowindow = new google.maps.InfoWindow({ content: text });

  google.maps.event.addListener(marker, 'click', function() {
	infowindow.open(map,marker);
  });
}
var serachInterval;
var cleared = false;
$(document).ready(function(){
	$(this).on("keyup change", ".finder #address", function(){
		if($(this).val() == ''){
			cleared = true;
		}
		find_pendent = 1;
		$("#crosshair").addClass("loading fa-spin");
		$("#saveLocation").prop("disabled", true);
		resetIntervalSearch();
	}).on("focus", ".finder #address", function(){
		//$(this).select();
		focusin = true;
	}).on("blur", ".finder #address", function(){
		focusin = false;
	});
	/*$(window).load(function(){
		value = $(".finder #address").val();
		if(value){
			$(".finder #address").val("Loading...");
			setTimeout(function(){
				$(".finder #address").val(value).trigger("change");
			}, 500);
		}
	});*/
	$("button#saveLocation").on("click", function(){
		parent.jQuery.fancybox.close();
	});
});
function resetIntervalSearch(){
	clearInterval(serachInterval);
	serachInterval = setInterval(function(){
		if(!$("#crosshair.drag").length){
			if(find_pendent){
				find_executing = 1;

				//console.log("Executing now 1");
				geocode();
				find_pendent = 0;
			}
			find_pendent = 0;
		}
	}, 1000);
}
