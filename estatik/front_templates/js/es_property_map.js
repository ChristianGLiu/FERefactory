function initializes() {
    var zooms = parseInt(zoom),
        mapOptions = {
            zoom: zooms,
            center: new google.maps.LatLng(data[0].latitude, data[0].longitude)
        },
        map = new google.maps.Map(document.getElementById("es-map-canvas"), mapOptions),
        i;
    for(i = 0; i < data.length; i++){
        add_marker(map, data[i], data[i].img);
    }
}
window.onload = function(){
    if ( typeof zoom !== 'undefined' )  {
        initializes();        
    }
    if ( typeof height !== 'undefined' )  {
        jQuery('#es-map-canvas').css({height: height});
    }
};
function add_marker(map, data, icon) {
    var myLatlng = new google.maps.LatLng(data.latitude, data.longitude);
    var marker = new google.maps.Marker({
        map: map,
        position: myLatlng,
        title: data.address,
        icon: new google.maps.MarkerImage(icon)
    });
	var scripts = document.getElementsByTagName("script");
	for (var i=0;i<scripts.length;i++) {
		 if (scripts[i].src) {
			 if (scripts[i].src.indexOf("es_property_map.js") != -1) {js_path = scripts[i].src.substr(0, scripts[i].src.lastIndexOf('/')+1); break;}
		 }
	}
	var titleColor;
	var boxText = document.createElement("div");
	
	
    if(data.color == 'pink'){
        titleColor = 'ib_title-wrapper_pink';
    }
    else if (data.color == 'blue'){
        titleColor = 'ib_title-wrapper_blue';
    }
	
    else if (data.color == 'green'){
        titleColor = 'ib_title-wrapper_green';
    }		
	
	boxText.id = 'infobox';
	boxText.innerHTML = "<div id='" + titleColor + "' class='img-rounded'><div class='ib_title'>" 
    + data.title 
    + "</div></div><br><div id='infobox-container'><table class='table-infobox'><tr class='table-infobox'><td id='infobox-img' class='table-infobox'><img id='imagewrap' src='" 
    + data.image + "' class='img-rounded'></td><td class='table-infobox'><div class='ib_address'>" 
    + data.address + "</div><div class='ib_price'>" + data.currency + data.price 
    + "</div><div class='ib_desc'>" + "<span class='ib-area'>" + data.area 
    + "</span><span class='ib-bedrooms'> " + data.bedrooms + "</span><span class='ib-bathrooms'> " 
    + data.bathrooms + "</span>" + "</div><br><div class='ib_link'><a href='" + data.link 
    + "'>Details</a></div></td></tr></table>";
    var color;
    if(data.color == 'pink'){
        color = js_path + "../images/ib_close_pink.png";
    }
    else if (data.color == 'blue'){
        color = js_path + "../images/ib_close_blue.png";
    }
    else if (data.color == 'green'){
        color = js_path + "../images/ib_close_green.png";
    }	
    
	var myOptions = {
         content: document.getElementById("infobox"),
		 content: boxText,
         disableAutoPan: false,
         maxWidth: 420,
         maxHeight: 200,
         pixelOffset: new google.maps.Size(-140, 0),
         zIndex: null,
         boxStyle: {
            //background: "url('http://google-maps-utility-library-v3.googlecode.com/svn/trunk/infobox/examples/tipbox.gif') no-repeat",
			background: "url('" + js_path + "../images/tipbox.png') no-repeat",			
            opacity: 1
            //width: "450px",
            //height: '200px'
        },
        closeBoxMargin: "22px 7px 5px 5px",
        //closeBoxURL: "http://www.google.com/intl/en_us/mapfiles/close.gif",
        closeBoxURL: js_path + '../images/close.png',
		// closeBoxURL: color,
        infoBoxClearance: new google.maps.Size(1, 1)
		
	};
	
	var ib = new InfoBox(myOptions);	
		
   google.maps.event.addListener(marker, 'click', function(){
       ib.open(map, marker);
       map.setCenter(marker.getPosition());
   });		
		
}