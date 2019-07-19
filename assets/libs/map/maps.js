  function initialize() { 
	var myLatlng = new google.maps.LatLng(20.97457, 105.75895);
    var myOptions = {
      zoom: 16,
      center: myLatlng,
      scrollwheel: false,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    }
	
    var map = new google.maps.Map(document.getElementById("mapCanvas"), myOptions);

    var contentString = 'BT L15-17 Khu đô thị mới Dương Nội - KM4 - Đường Tố Hữu - Quận Hà Đông - Hà Nội<br>'
						+'Điện thoại: 0913.922.986';

    var infowindow = new google.maps.InfoWindow({
        content: contentString
    });
    var marker = new google.maps.Marker({
        position: myLatlng,
        map: map,
        title: 'hn-store.net'
    });
    google.maps.event.addListener(marker, 'click', function() {
      infowindow.open(map,marker);
    });
  }

google.maps.event.addDomListener(window, 'load', initialize);