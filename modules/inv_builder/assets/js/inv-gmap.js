(function ($, Drupal, google) {
  "use strict";
  //Create map marker
  var createMarker = function ($marker, map, infowindow) {
    var marker = new google.maps.Marker({
      position: new google.maps.LatLng($marker.data('lat'), $marker.data('lng')),
      map: map,
      icon: $marker.data('icon') || '',
      animation: google.maps.Animation.DROP,
      title: $(this).text()
    });
    marker.setMap(map);
    var $info = $('<div class="infobox" style="width:300px;"></div>');
    var $style = "";
    if ($marker.data('image') || false) {
      $info.append('<img class="alignleft img-responsive" src="' + $marker.data('image') + '" alt="">');
      $style = "margin-left:100px;";
    }
    if ($marker.data('title') || false) {
      $info.append('<h3 class="title"><a href="' + '#' + '">' + $marker.data('title') + '</a></h3>');
    }
    $info.append('<p style="' + $style + '">' + $marker.text() + '</p>');
    if ($marker.data('phone') || false) {
      $info.append('<p  style="' + $style + '"><span class="fa fa-phone"></span> ' + $marker.data('phone') + '</p>');
    }
    google.maps.event.addListener(marker, 'click', (function (marker) {
      return function () {
        infowindow.setContent('<div class="infobox">' + $info.html() + '</div>');
        infowindow.open(map, marker);
      };
    })(marker));
    return marker;
  };
  Drupal.behaviors.inv_builder_gmap = {
    attach: function () {
      $('.inv-builder-gmap').once('process').each(function () {
        var $map = $(this),
            mapid = $map.attr('id'),
            $markers = $map.find('.inv-builder-gmap-marker');
        var zoom = $map.data('zoom') || 14;
        $map.height($map.data('height'));
        var map = null;
        var markers = [];
        var infowindow = new google.maps.InfoWindow();
        var mapOptions = {
          scrollwheel: false,
          zoom: zoom
        };
        var map_center = false;
        map = new google.maps.Map(document.getElementById(mapid), mapOptions);
        if($map.data('custom-style')){
          var styledMapType = new google.maps.StyledMapType($map.data('custom-style'));
          map.mapTypes.set('styled_map', styledMapType);
          map.setMapTypeId('styled_map');
        }
        $markers.each(function () {
          var $this = $(this);
          if (!map_center) {
            map.setCenter(new google.maps.LatLng($this.data('lat'), $this.data('lng')));
            map_center = true;
          }
          markers.push(createMarker($this, map, infowindow));
          if(markers.length > 1){
            var bounds = new google.maps.LatLngBounds();
            for (var i = 0; i < markers.length; i++) {
              bounds.extend(markers[i].getPosition());
            }
            map.fitBounds(bounds);
          }
        });
      });
    }
  };
})(jQuery, Drupal, google);