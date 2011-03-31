
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <title>Twitter Search and Map</title>
    <script type="text/javascript" src="http://www.google.com/jsapi?key=ABQIAAAAz63czigrXvVeQM0gi_2IpxRMKMK__c5Odlrfjjvp8Pe3QZTX3hSTUycYgZ63EUy_rU9WDULK8ogblg"></script>
       <script type="text/javascript" src="downloadxml.js"></script>
       <meta name="description" content="Project Haiti documents the Twitter tweets that were written after the massive earthquake of January 12, 2010. The earliest collected data regarding the earthquake is at 03:21:27 UTC, about 5 hours after the first earthquake." />
        <script type="text/javascript" src="nns.min.js"></script>
    <script type="text/javascript" src="jshashtable.js"></script>
    <script type="text/javascript" src="jquery.min.js"></script>
    <script type="text/javascript" src="json2.js"></script>   
	<script type="text/javascript" src="date-en-US.js"></script>
    <script type="text/javascript" src="jquery-ui.min.js"></script>
    <script type="text/javascript" src="selectToUISlider.jQuery.js"></script> 
    <link type="text/css" href="dot-luv/jquery-ui-1.8.6.custom.css" rel="stylesheet" />
    <script type="text/javascript" src="selectToUISlider.jQuery.js"></script> 
    <link rel="Stylesheet" href="ui.slider.extras.css" type="text/css" />

    <!--[if IE]><script type="text/javascript" src="excanvas.js"></script><![endif]-->
    <script type="text/javascript">
var masterData;
var styles = [{
    featureType: 'all',
    stylers: [{
        invert_lightness: 'true'
    },
    {
        hue: "#033d6e"
    }]
},
{
    featureType: "administrative",
    elementType: "all",
    stylers: [{
        visibility: "off"
    }]
},
{
    featureType: "water",
    elementType: "labels",
    stylers: [{
        visibility: "off"
    }]
}];


var styledMapOptions = {
    name: "Night"
}
var canvas;
var ctx;
var PK = function (version) {

    function CanvasTileLayerOverlay(map, id) {
        var TILEFACTOR = 8;
        var TILESIDE = 1 << TILEFACTOR;
        var RADIUS = 2;

        var container = document.createElement("div");
        container.id = "id-tilelayer-" + id;

        this._container = container;
        this._map = map;
        this._zoom = -1;
        this._X0 =
        this._Y0 =
        this._X1 =
        this._Y1 = -1;

        this._zooms = {}; // zooms already processed for this set of points
        this._apoints = []; // absolute x/y coordinates of points to display
        this._zpoints = {}; // absolute x/y coordinates at a particular zoom
        var LngToX = function (lng) {
            return (1 + lng / 180);
        }
        var LatToY = function (lat) {
            var sinofphi = Math.sin(lat * Math.PI / 180);
            return (1 - 0.5 / Math.PI * Math.log((1 + sinofphi) / (1 - sinofphi)));
        }
        var XToLng = function (x) { // return (x-1)*180;
            return (x / 268435456 - 1) * 180;
        }
        var YToLat = function (y) {
            return (Math.PI / 2 - 2 * Math.atan(Math.exp((Math.round(y) - 268435456) / (268435456 / Math.PI)))) * 180 / Math.PI;
        }

        this.getTileKey = function (tilex, tiley, zoom) {
            return 'id-' + tilex + '-' + tiley + '-' + zoom;
        }

        this.addPointXY = function (tilex, tiley, zoom, intilex, intiley, masterIndex) {
            var tkey = this.getTileKey(tilex, tiley, zoom);
            if (!this._zpoints[tkey]) this._zpoints[tkey] = [];
            this._zpoints[tkey].push({
                x: intilex,
                y: intiley,
                masterIndex: masterIndex
            });
        }

        this.mapToZoom = function (c, zoom) {
            return~~ (0.5 + c * (2 << (zoom + 6)));
        }

        this.mapPointToTile = function (x, y, zoom,masterIndex) {
            // calculate what tile the point belongs too
            x = this.mapToZoom(x, zoom);
            y = this.mapToZoom(y, zoom);
            var tileX = ~~ (x / TILESIDE); // x coord of the tile
            var tileY = ~~ (y / TILESIDE); // y coord of the tile
            var inTileX = x % TILESIDE; // x coord inside the tile
            var inTileY = y % TILESIDE; // y coord inside the tile
            // add the point to the array of points to display
            // draw the points that get on the tile
            this.addPointXY(tileX, tileY, zoom, inTileX, inTileY,masterIndex);

            // draw the points that miss the tile by less than RADIUS;
            // every point that has x or y less than RADIUS and/or more than TILESIDE-RADIUS 
            // needs to also go to the tile adjacent to that side (up to 3 tiles)
            var radius = RADIUS;
            var missX = inTileX < radius ? -1 : inTileX > TILESIDE - radius ? +1 : 0;
            var missY = inTileY < radius ? -1 : inTileY > TILESIDE - radius ? +1 : 0;
            if (missX) this.addPointXY(tileX + missX, tileY, zoom, inTileX - missX * TILESIDE, inTileY);
            if (missY) this.addPointXY(tileX, tileY + missY, zoom, inTileX, inTileY - missY * TILESIDE);
            if (missX && missY) this.addPointXY(tileX + missX, tileY + missY, zoom, inTileX - missX * TILESIDE, inTileY - missY * TILESIDE);
        }

        this.addPointLatLng = function (lat, lng, masterIndex) {
            var x = LngToX(lng);
            var y = LatToY(lat);
            this._apoints.push({
                x: x,
                y: y,
                masterIndex: masterIndex
            });
            //alert(this._apoints.length);
            this.mapPointToTile(x, y, this._zoom || this._map.getZoom(), masterIndex);
        }

        this.removeData = function () {
            this._apoints = [];
            this._zpoints = [];
            //alert(this._apoints.length);
        }
        this.getOffset = function (center, zoom) {
            var map = this._map;
            var div = (version == 2 ? map : this.getProjection()).fromLatLngToDivPixel(center);
            var abs = {
                x: this.mapToZoom(LngToX(center.lng()), zoom),
                y: this.mapToZoom(LatToY(center.lat()), zoom)
            };

            var x = div.x - abs.x;
            var y = div.y - abs.y;

            var c = (version == 2 ? map.getContainer() : map.getDiv());
            var w = c.clientWidth;
            var h = c.clientHeight;

            var x0 = abs.x - (w + 1) / 2;
            var y0 = abs.y - (h + 1) / 2;
            var x1 = abs.x + (w + 1) / 2;
            var y1 = abs.y + (h + 1) / 2;

            var X0 = x0 >> TILEFACTOR;
            var Y0 = y0 >> TILEFACTOR;
            var X1 = ((x1 - 1) >> TILEFACTOR) + 1;
            var Y1 = ((y1 - 1) >> TILEFACTOR) + 1;

            var offx = (X0 << TILEFACTOR) + x;
            var offy = (Y0 << TILEFACTOR) + y;

            return this._offset = {
                X0: X0,
                Y0: Y0,
                X1: X1,
                Y1: Y1,
                offx: offx,
                offy: offy,
                tileside: TILESIDE
            };
        };

        this.addCanvas = function (id, offset) {
            canvas = document.createElement("canvas");

            if (typeof G_vmlCanvasManager != 'undefined') G_vmlCanvasManager.initElement(canvas);

            canvas.id = id;
            canvas.width = canvas.height = TILESIDE;
            canvas.style.width = canvas.width + 'px';
            canvas.style.height = canvas.height + 'px';
            canvas.style.left = offset.x + 'px';
            canvas.style.top = offset.y + 'px';
            canvas.style.position = "absolute";

            return canvas;
        }


	           
        this.drawTile = function (canvas, tile, zoom) {
            var radius = RADIUS;
            function doThis(lax, lay, i, x) { 
            	console.log("new point");
	                ctx.fillStyle = 'rgba(255,255,255,'+x+')';
	                ctx.beginPath();
	                ctx.arc(lax, lay, RADIUS, 0, Math.PI * 2, true);
	                ctx.fill();
	           };
            var points = this._zpoints[this.getTileKey(tile.x, tile.y, zoom)] || [];
            ctx = canvas.getContext("2d");
            for (var i = 0; i < points.length; i++) {
            	//for (var j = 1; j < 2; j++) {
            		var x = 0.5;
            		var lax = points[i].x;
            		var lay = points[i].y;
            		doThis(lax, lay, i, x);
            		//(version == 2 ? canvasLayer.draw(true) : google.maps.event.trigger(canvasLayer, 'draw', true));
            	//}
            }
            return canvas;
        }

function rgb() {
    for (var i = 0, c = 'rgb('; i < 3; i++) {
        c += (Math.random() * 240 >> 0) + ',';
    }
    return c.replace(/\,$/, ')');
}
        var cursor = container.style.cursor;
        var that = this;
        var m;
        var c = (version == 2 ? map.getContainer() : map.getDiv());
        var _tileX, _tileY; // to clear the tile pointer just left
        var pos = document.getElementById("position");
        (version == 2 ? google.maps.Event : google.maps.event).addDomListener(c, "mousemove", function (e) {
            if (!that._offset) return; // to protect against someone moving before the first redraw
            //alert(that._apoints.length)
            // find the element that carries the offset when the map is moved
            if (!m) m = container.parentNode.parentNode;

            // this is a "normal" way to get offset information
            var moffx = m.offsetLeft;
            var moffy = m.offsetTop;

            // this covers those cases where transform is used
            if (!moffx && !moffy && window.getComputedStyle) {
                var matrix = document.defaultView.getComputedStyle(m, null).MozTransform || document.defaultView.getComputedStyle(m, null).WebkitTransform;
                if (matrix) {
                    var parms = matrix.split(",");
                    moffx += parseInt(parms[4]) || 0;
                    moffy += parseInt(parms[5]) || 0;
                }
            }

            // calculate offset based on what's returned by getOffset
            var offset = that._offset;
            var x = e.clientX - offset.offx - moffx;
            var y = e.clientY - offset.offy - moffy;

            // calculate tile we're on and position inside that tile
            var tileX = ~~ (x / TILESIDE) + offset.X0; // x coord of the tile
            var tileY = ~~ (y / TILESIDE) + offset.Y0; // y coord of the tile
            x = x % TILESIDE; // x coord inside the tile
            y = y % TILESIDE; // y coord inside the tile
            // calculate Lng/Lat based of X/Y coordinates
            var lng = XToLng((tileX * TILESIDE + x) << (21 - that._zoom));
            var lat = YToLat((tileY * TILESIDE + y) << (21 - that._zoom));

            // get all the points the tile under the pointer has
            var id = that.getTileKey(tileX, tileY, that._zoom);
            var apoints = that._apoints || [];
            var points = that._zpoints[id] || [];
            if (!points.length) {
                container.style.cursor = cursor;
                return;
            }
            
            //$("#position").html("");
            //var htmls = "<br />lat: " + lat.toFixed(6) + "<br />lng: " + lng.toFixed(6) + "<br />tileX: " + tileX + "<br />tileY: " + tileY + "<br />inTileX: " + x + "<br />inTileY: " + y + "<br />" + JSON.stringify(points) + "<br />" + JSON.stringify(apoints);
			//$("#position").append(htmls);
            // find the closest point
            var min = TILESIDE * 2;
            var near = -1;
            for (var i = points.length - 1; i >= 0; i--) {
                var d = Math.sqrt((points[i].x - x) * (points[i].x - x) + (points[i].y - y) * (points[i].y - y));
                if (d < min) {
                    min = d;
                    near = i;
                }
            }
            
             
            //$("#position").append("<br />Nearest id: " + points[near].masterIndex + "<br />");
            //if (min < 10) {
	           		//var ctx = canvas.getContext("2d");  
	           		//alert(ctx);          
	                //ctx.fillStyle = rgb();
	                //ctx.beginPath();
	                //var pointz = points[this.getTileKey(tile.x, tile.y, zoom)]
	                //ctx.arc(points[i].x, points[i].y, radius, 0, Math.PI * 2, true);
	                //ctx.arc(points[near].x,points[near].y, RADIUS, 0, Math.PI * 2, true);
	                //console.log("writing");
	                //ctx.fill();
	            if (min < 3) {
					$.ajax({
					  		type: "GET",
					        url: 'getTweet.php',
					        data: "id=" + points[near].masterIndex + "&query=" + query,
					        success: function (data) {
					        	//$("#position").append(data);
					        	$("#position").html(data);
					        }
					 });
	            }
           // }
               // change the cursor
          if (min <=  RADIUS) {
          	console.log("Changing cursor");
          	container.style.cursor = 'pointer';
          } else {
          	
          	container.style.cursor = cursor;
          }
        });
    }

    CanvasTileLayerOverlay.prototype = (version == 2 ? new google.maps.Overlay() : new google.maps.OverlayView());

    CanvasTileLayerOverlay.prototype.constructor = CanvasTileLayerOverlay;

    CanvasTileLayerOverlay.prototype.onAdd = // version == 3
    CanvasTileLayerOverlay.prototype.initialize = function () {
        (version == 2 ? this._map.getPane(google.maps.MAP_MAP_PANE) : this.getPanes().mapPane).appendChild(this._container);
    };

    CanvasTileLayerOverlay.prototype.onRemove = // version == 3
    CanvasTileLayerOverlay.prototype.remove = function () {
        this._container.parentNode.removeChild(this._container);
    };

    CanvasTileLayerOverlay.prototype.draw = // version == 3
    CanvasTileLayerOverlay.prototype.redraw = function (force) {
        var map = this._map;
        var zoom = map.getZoom();
        var o = this.getOffset(map.getCenter(), zoom);
        var X0 = o.X0,
            Y0 = o.Y0,
            X1 = o.X1,
            Y1 = o.Y1;
        var offx = o.offx,
            offy = o.offy;
        var TILESIDE = o.tileside;

        // check force, zoom, and corner tiles; if something changed, need to redraw
        if (!force && this._zoom == zoom && this._X0 == X0 && this._Y0 == Y0 && this._X1 == X1 && this._Y1 == Y1) return;

        // re-map the points if the zoom has changed
        if (this._zoom != zoom && !this._zooms[zoom]) {
            for (var i = 0; i < this._apoints.length; i++)
            this.mapPointToTile(this._apoints[i].x, this._apoints[i].y, zoom,this._apoints[i].masterIndex);
        }

        this._zoom = zoom;
        this._zooms[zoom] = this._apoints.length; // don't set if no points
        this._X0 = X0;
        this._Y0 = Y0;
        this._X1 = X1;
        this._Y1 = Y1;

        var container = this._container;
		console.log("redrawing");
        google.maps.Log.write("redrawing");

        // identify tiles that need to be shown
        var tilesToShow = {};
        for (var tilex = X0; tilex <= X1; tilex++)
        for (var tiley = Y0; tiley <= Y1; tiley++)
        tilesToShow['id-' + tilex + '-' + tiley + '-' + zoom] = 1;

        for (var l = container.childNodes.length - 1; l >= 0; l--) {
            if (tilesToShow[container.childNodes[l].id])
            // delete from the list of tiles to draw as we already have it
            delete tilesToShow[container.childNodes[l].id];
            else
            // delete the element as it's not on the list of tiles to show
            container.removeChild(container.childNodes[l]);
        }

        google.maps.Log.write("removed");

        // go through all the tiles that need to be shown
        for (var tilex = X0; tilex <= X1; tilex++) {
            for (var tiley = Y0; tiley <= Y1; tiley++) {
                var id = 'id-' + tilex + '-' + tiley + '-' + zoom;
                if (!force && !tilesToShow[id]) continue;

                var points = this._zpoints[id] || [];
                if (!points.length) continue;

                // generate and add those that are missing
                var left = offx + (tilex - X0) * TILESIDE;
                var top = offy + (tiley - Y0) * TILESIDE;
                var canvas = this.addCanvas(id, {
                    x: left,
                    y: top
                });
                this.drawTile(canvas, {
                    x: tilex,
                    y: tiley
                }, zoom)
                container.appendChild(canvas);
            }
        }
        google.maps.Log.write("finished");
    };

    return {
        CanvasTileLayerOverlay: CanvasTileLayerOverlay
    }
}
PK.param = function (name) {
    var results = (new RegExp("[\\?&;]" + name + "=([^&#;]*)")).exec(window.location.search);
    //alert(results);
    return results == null ? null : results[1];
}
// Read a page's GET URL variables and return them as an associative array.
function getUrlVars()
{
    var vars = [], hash;
    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');

    for(var i = 0; i < hashes.length; i++)
    {
        hash = hashes[i].split('=');
        vars.push(hash[0]);
        vars[hash[0]] = hash[1];
    }

    return vars;
}
var svars = getUrlVars();
var query = svars["query"];
var lat = +PK.param("lat") || 20.523;
//var lat = +PK.param("lat") || 20.52392;
var lng = +PK.param("lng") || -25.90884;
var zoom = +PK.param("z") || 2;
var speed = +PK.param("speed") || 100;
var timeinterval = +PK.param("interval") || 600;
//var query = "stanford";
//alert(lat);
var version = +PK.param("v") || 3;
var canvasLayer;
google.load("maps", "3.x", {
    other_params: "sensor=false"
});
google.setOnLoadCallback(function () {
	

	    $.ajax({
  		type: "GET",
        url: 'getData3.php',
        data: "query=" + query,
        success: function (data) {
        	masterData = eval(data);
        	
    // set the logger to console.log or noop for v3
    if (!google.maps.Log) google.maps.Log = {
        write: function () {}
    };
    if (typeof console != "undefined") google.maps.Log.write = console.log;
    google.maps.Log.write = function () {}; // disable logging for demo
    var pk = PK(version);
    var el = document.getElementById("map");
    var map;
    if (version == 2) {
        map = new google.maps.Map2(el);
        map.addControl(new google.maps.SmallZoomControl3D());
        map.setCenter(new google.maps.LatLng(lat, lng), zoom);
    } else {
        map = new google.maps.Map(el, {
            zoom: zoom,
            center: new google.maps.LatLng(lat, lng),
            mapTypeControlOptions: {
                mapTypeId: [google.maps.MapTypeId.ROADMAP, 'hiphop'],
            },
            mapTypeId: 'hiphop',
            mapTypeControl: false,
            scaleControl: false,
            navigationControlOptions: {
                style: google.maps.NavigationControlStyle.DEFAULT
            },
            streetViewControl: false
        });
        // Custom controls
        var jayzMapType = new google.maps.StyledMapType(
        styles, styledMapOptions);

        map.mapTypes.set('hiphop', jayzMapType);
        map.setMapTypeId('hiphop');
    }
    google.maps.Log.write("initialized");

    canvasLayer = new pk.CanvasTileLayerOverlay(map, "mylayer");
    (version == 2 ? map.addOverlay(canvasLayer) : canvasLayer.setMap(map));

    if (version == 3) // make the map to redraw more frequently by listening to center_changed
    google.maps.event.addListener(map, 'center_changed', function () {
        canvasLayer.redraw.apply(canvasLayer, arguments);
    });

    //var tover = (version == 2 ? 3 : 2); // set version to switch to
    //var link = document.getElementById("link");
    //link.innerHTML = link.innerHTML + tover;
    //link.onclick = function () {
    //    this.href = ((new RegExp("([^?#]+)")).exec(window.location.href))[1] + "?v=" + tover + ";lat=" + map.getCenter().lat() + ";lng=" + map.getCenter().lng() + ";z=" + map.getZoom();
    //    return true;
    //};
$("#speed").val(speed);
$("#interval").val(timeinterval);
	for (var i = 0; i < 1500;i++){ 
		canvasLayer.addPointLatLng(masterData[i].t, masterData[i].g,i);
		
        }
        	(version == 2 ? canvasLayer.draw(true) : google.maps.event.trigger(canvasLayer, 'draw', true));

        }
	    });  	     
});
//]]>


function expandRange(start, end) // start and end are your two Date inputs
{
    var range;
    if (start.isBefore(end)) {
        start = start.clone();
        range = [];

        while (!start.same().day(end)) {
            range.push(start.clone());
            start.addDays(1);
        }
        range.push(end.clone());

        return range;
    } else {
        // arguments were passed in wrong order
        return expandRange(end, start);
    }
}

<?php
include("config.php");

$db = mysql_connect("$dbHost", "$dbUser", "$dbPass") or die ("Error connecting to database.");
$db_found = mysql_select_db("$dbDatabase", $db) or die ("Couldn't select the database.");


	$row =  mysql_fetch_array(mysql_query("SELECT date FROM tweets where query = '".$_GET["query"]."' order by date desc limit 0,1"));
	$newest = $row["date"];
// newest
$row =  mysql_fetch_array(mysql_query("SELECT date FROM tweets where query = '".$_GET["query"]."' order by date asc limit 0,1"));
	$oldest = $row["date"];
$step = (strtotime($newest) - strtotime($oldest))/ 72;
//oldest 


?>
var timeinterval = <?php echo $step; ?>;
var animator = (function () {
    var data;
    var idx = 0;
    var point = null;
    var interval = null;
    var firstMonth, firstYear, lastMonth, lastYear;
    var datePairs = [];
    
    var startDate = Date.parse("<?php echo $oldest; ?>");
    var origDate = Date.parse("<?php echo $newest; ?>");
    var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

    var start = function () {
        var vals = $('.ui-slider').slider("option", "values");
  
       

        //if (vals[1] + 1 >= datePairs.length) $('.ui-slider').slider("options", "values", [vals[0], vals[0] + 1]);
        //if (datePairs.length < 15) {
            interval = setInterval("animator.showNext()", speed);
        //} else {
        //    interval = setInterval("animator.showNext()", Math.max(1000, TOTAL_ANIM_TIME_MS / datePairs.length));
        //}

    }
    var showNext = function () {
        var vals = $('.ui-slider').slider( "option", "values");
        //alert(vals);
        //alert(vals[1]);
         if (vals[1] + 1 > 72) {
        	stop();
         } else {
         	//alert();
 $("#time").html(startDate.add({ seconds: timeinterval }).toString("yyyy-MM-dd HH:mm:ss"));
 //alert(vals + 1);
        $('.ui-slider').slider("option", "values", [0, vals[1] + 1]);
        		canvasLayer.removeData();
        	 	canvasLayer.redraw(true)
        	     google.maps.event.trigger(canvasLayer, 'draw', true);
        	
        	  	for (var i = 0; i< masterData.length;i++) {
        		if (masterData[i].d <= timeinterval*vals[1] && masterData[i].d >= timeinterval*(vals[1]-1)) {
        			canvasLayer.addPointLatLng(masterData[i].t, masterData[i].g,i);		
        		}
        	}
        	canvasLayer.redraw(true)
        	google.maps.event.trigger(canvasLayer, 'draw', true);
        	 
        }

    }
    var setData = function (theData) {
        data = theData;
    };

	var update = function () {
var vals = $('.ui-slider').slider( "option", "values");
        //alert(vals);
        alert(vals);
         if (vals + 1 >= 72) {
        	stop();
         } else {
         	//alert();
         	startDate = origDate.clone();
 $("#time").html(startDate.add({ seconds: vals[1] }).toString("yyyy-MM-dd HH:mm:ss"));
 //alert(vals + 1);
       // $('.ui-slider').slider("option", "values", [0, vals[1] + 1]);
        		canvasLayer.removeData();
        	 	canvasLayer.redraw(true)
        	     google.maps.event.trigger(canvasLayer, 'draw', true);
        	
        	  	for (var i = 0; i< masterData.length;i++) {
        		if (masterData[i].d <= timeinterval*vals[1] && masterData[i].d >= timeinterval*(vals[1]-1)) {
        			canvasLayer.addPointLatLng(masterData[i].t, masterData[i].g,i);		
        		}
        	}
        	canvasLayer.redraw(true)
        	google.maps.event.trigger(canvasLayer, 'draw', true);
        	 }
        //alert(vals);
	};
    var stop = function () {
  
    	        clearInterval(interval);
        interval = null;
        $('#play span').removeClass('ui-icon-pause');
        $('#play span').addClass('ui-icon-play');
    };

    return {
        showNext: showNext,
        setData: setData,
        update: update,
        stop: stop,
        start: start,
        isActive: function () {
            return interval != null;
        }
    };

})();

var dateslist = expandRange(new Date('2010-01-13'), new Date('2010-01-14'));

$(document).ready(function () {
    var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

    var options = '';
    
    $('#taa').html(options);
    //var temp = $('select').selectToUISlider({
    //    labels: 10
    //});
    $("#slider").slider({stop: function(e, i) { animator.update(); },range: true, min: 0,max: 70});
    var x = 5;

    //$('#play').show();
$('#about').find("a").click(function() {
	$('#position').html("hi");
});
    $('#play').click(function () {
        if (animator.isActive()) {
            animator.stop();
        } else {
$('.ui-slider').slider("option", "values", [0, 0]);
            $(this).find('span').removeClass('ui-icon-play');
            $(this).find('span').addClass('ui-icon-pause');
            animator.start();
        }
            });
            $(".ui-slider-handle:first").hide();


});

    </script>

    <style type="text/css">  
      html, body, #map { width: 100%; height: 100%; }  
      html { overflow: hidden; } 
      body { 
        margin: 0px; 
        font-family: Verdana,Geneva,Georgia,Chicago,Arial,Sans-serif,"MS Sans Serif";
        font-size: 0.8em;
      }  
      #info { 
        position: fixed; padding: 10px; right: 10px; top: 10px; 
        width: 400px; z-index: 10; 
        background-color: #FFFFFF; border: 1px solid #002D80; 
        color: #002D80;
        opacity: 0.8;
        -moz-border-radius: 5px;-webkit-border-radius: 5px;
      }
      #time { 
        position: fixed; padding: 10px; left: 10px; bottom: 100px; 
        width: 400px; z-index: 10; 
        font-size:40px;
        font-family:Georgia;
        color: #fff;
        opacity: 0.8;
      }
      #speed { 
        position:fixed;
        padding: 4px; left: 30px; bottom: 40px; 
        width: 30px; z-index: 10; 
        font-size:10px;
      
        font-family:Georgia;
        color: #333;
        opacity: 0.8;
      }
       #interval { 
        position:fixed;
        padding: 4px; left: 90px; bottom: 40px; 
        width: 30px; z-index: 10; 
        font-size:10px;
      
        font-family:Georgia;
        color: #333;
        opacity: 0.8;
      }
  
      #position {
        font-family: monospace;
        font-size: 13px;
      }
      #slider {
          position: absolute;
          left: 100;
          right: 90;
          margin-top: 3;
        }
       #play {
          
		  float:left;
          height: 17px;
          width: 17px;
          padding: 5px;
          cursor: pointer;
        }
        
        #slider {
        	  float:left;
            font-size: 1.3em;
            margin-left:50px;
            margin-top:7px;
            width: 300px;
        }
                #controls {
            position: absolute;
            left: 30px;
            bottom: 0;
            width: 100%;
            height: 150px;
        }
        #about {
        	position:absolute;
        	color:#fff;
            left: 30px;
            bottom: 50px;
            z-index:10;
            display:none;
        }
        #about a {
        	color:#fff;
        }
        #container {
        		
       
        	margin-top:50px;
        	width:400px;
          height: 10px;
        }
        .hidden {
        	z-index:10;
        	position:fixed;
        	left:200px;
       	}
    </style>
         </head>

  <body>
    <div id="map"></div>
    <div id="info">
      <a href="#" id="link"></a>
      <div id="position">Hover over any tweet to read its contents!</div>
    </div>
     <div id="about"></div>
     <div id="time"></div>
 
     <input type="text" style="display:none;" id="speed" name="speed">
     <input type="text" style="display:none;" id="interval" name="interval"> seconds
    <input type="submit" class="hidden">  </form>
     <div id="controls">

               <div id="container"> 
                <span id="play" class="ui-state-default ui-corner-all">
                        <span class="ui-icon ui-icon-play"></span>
                    </span>
                <div id="slider">
                   
                </div>
                <!--<img id="play" src="play.png" alt="Play">-->
            </div>
           
        </div>
        <!-- Start of StatCounter Code -->
<script type="text/javascript">
var sc_project=6547851; 
var sc_invisible=1; 
var sc_security="a7647b38"; 
</script>

<script type="text/javascript"
src="http://www.statcounter.com/counter/counter.js"></script><noscript><div
class="statcounter"><a title="tumblr page counter"
href="http://statcounter.com/tumblr/" target="_blank"><img
class="statcounter"
src="http://c.statcounter.com/6547851/0/a7647b38/1/"
alt="tumblr page counter" ></a></div></noscript>
<!-- End of StatCounter Code -->
  </body>
</html>

