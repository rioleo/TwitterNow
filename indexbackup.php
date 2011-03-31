<html>
  <head>
    <title>Project Haiti</title>
    <script type="text/javascript" src="http://polymaps.org/protodata.min.js?3.2"></script>
    <script type="text/javascript" src="http://polymaps.org/polymaps.min.js?2.2.0"></script>
    <script type="text/javascript" src="nns.min.js"></script>
    <script type="text/javascript" src="jshashtable.js"></script>
    <script type="text/javascript" src="jquery.min.js"></script>
    <script type="text/javascript" src="jquery-ui.min.js"></script>
    <script type="text/javascript" src="jquery.numberformatter.min.js"></script>
    <script type="text/javascript" src="jquery.ui.autocomplete.selectFirst.js"></script>
    <link type="text/css" href="dot-luv/jquery-ui-1.8.6.custom.css" rel="stylesheet" />
    <script type="text/javascript" src="selectToUISlider.jQuery.js"></script> 
    <link rel="Stylesheet" href="ui.slider.extras.css" type="text/css" />
    <style type="text/css">

        #map { position: relative; }

        p { clear:both; }
        form { margin: 0 30px;}
        fieldset { border:0; margin-top: 1em;}    
        label {font-weight: normal; float: left; margin-right: .5em; font-size: 1.1em;}
        select {margin-right: 1em; float: left;}
        .ui-slider {clear: both; top: 2em;}

        #controls {
            position: absolute;
            left: 0;
            bottom: 0;
            width: 100%;
            height: 150px;
        }
        
        #container {
          height: 10px;
        }

        #slider {
          position: absolute;
          left: 100;
          right: 90;
          margin-top: 3;
        }

        #play {
          position: absolute;
          left: -50px;
          top: 30px;
          height: 17px;
          width: 17px;
          padding: 10px;
          cursor: pointer;
        }
        
        #slider {
            font-size: 1em;
            margin: 0 auto;
            width: 700px;
        }
        .layer path {
            fill: lightsteelblue;
            fill-opacity: .5;
            /*stroke: steelblue;*/
        }

        .layer circle {
            fill: lightcoral;
            fill-opacity: .5;
            stroke: brown;
        }

        body {
            margin: 0;
            background: #E5E0D9;
        }

        svg {
            display: block;
            overflow: hidden;
            width: 100%;
            height: 100%;
        }

        #copy {
            position: absolute;
            left: 0;
            bottom: 4px;
            padding-left: 5px;
            font: 9px sans-serif;
            color: #fff;
            cursor: default;
        }

        #copy a {
            color: #fff;
        }

        .compass .back {
            fill: #eee;
            fill-opacity: .8;
        }

        .compass .fore {
            stroke: #999;
            stroke-width: 1.5px;
        }

        .compass rect.back.fore {
            fill: #999;
            fill-opacity: .3;
            stroke: #eee;
            stroke-width: 1px;
            shape-rendering: crispEdges;
        }

        .compass .direction {
            fill: none;
        }

        .compass .chevron {
            fill: none;
            stroke: #999;
            stroke-width: 5px;
        }

        .compass .zoom .chevron {
            stroke-width: 4px;
        }

        .compass .active .chevron, .compass .chevron.active {
            stroke: #fff;
        }

        .compass.active .active .direction {
            fill: #999;
        }

        #title {
            position: absolute;
            top: 0;
            left: 0;
            background: rgba(0, 0, 0, 0.5);
            padding: 0.25em;
        }
        #title h1 {
            color: white;
            font-weight: bold;
            font-size: 4.5em;
            font-family: Georgia, "Times New Roman", Times, serif;
            margin: 0;
            padding: 0;
        }

        #stats {
            width: 325px;
            position: absolute;
            top: 0;
            right: 0;
            background: rgba(0, 0, 0, 0.5);
            padding: 0.25em 1em;
        }
        #stats td {
            color: white;
            font-size: 2.25em;
            font-weight: bold;
            text-align: right;
            font-family: Georgia, "Times New Roman", Times, serif;
        }
        #stats td.label {
            font-weight: normal;
            color: #ddd;
            text-align: left;
        }
        #searchbox {
            background: inherit;
            border: 0;
            font-family: Georgia, "Times New Roman", Times, serif;
            color: white;
            font-weight: bold;
            font-size: 4.5em;
            height: inherit;
            width: 500px;
            padding: 0 0.25em;
            display:none;
        }
        
        /*
         .ui-autocomplete-loading {
             background: url('ui-lightness/images/ui-anim_baasic_16x16.gif') right center no-repeat;
         }
         */
    </style>
    <script type="text/javascript">

        var ZOOM = 2.8;
        var LON_START = 0;
        var LAT_START = 30;
        var TOTAL_ANIM_TIME_MS = 20000;
        var urlParams = {};
        (function () {
            var e,
                a = /\+/g,  // Regex for replacing addition symbol with a space
                r = /([^&;=]+)=?([^&;]*)/g,
                d = function (s) { return decodeURIComponent(s.replace(a, " ")); },
                q = window.location.search.substring(1);

            while (e = r.exec(q))
            urlParams[d(e[1])] = d(e[2]);
        })();

        var style = urlParams['style'];
        if (style == null)
            style = "midnight";
        var presentationMode = true;
        //if (urlParams['presentationMode'] && urlParams['presentationMode'] == "1")
        //    presentationMode = true;

        if (!presentationMode) {
            $('html').css('width', 1024);
            $('html').css('height', 550);
        }

        var project;
        if ("project" in urlParams) project = urlParams["project"];
        else project = "";
        var lines;
        if ('lines' in urlParams) lines = eval(urlParams['lines']);
        else lines = true;
        var circles;
        if ('circles' in urlParams) circles = eval(urlParams['circles']);
        else circles = true;
        var names;
        if ('names' in urlParams) names = eval(urlParams['names']);
        else names = false;
        var scale;
        if ('scale' in urlParams) scale = eval(urlParams['scale']);
        else scale = 1;
        if ('linkAggregation' in urlParams) linkAggregation = urlParams['linkAggregation'] == 'True';
        else linkAggregation = false;
        if ('linkMaxScale' in urlParams) linkMaxScale = parseInt(urlParams['linkMaxScale']);
        else linkMaxScale = 0;
        

        var formatUrlParams = function(overrides) {
            overrides = overrides || {}
            first = true;
            formatted = '?';
            for (var key in urlParams) {
                formatted += (first ? '' : '&') + key + '=' + (overrides[key] || urlParams[key]);
                overrides[key] = undefined;
                first = false;
            }
            for (var key in overrides) {
                if (overrides[key]) {
                    formatted += (first ? '' : '&') + key + '=' + overrides[key]
                    first = false;
                }
            }
            return formatted;
        }

        var get_link_opacity = function(d, min_opacity) {
            temp = LINK_OPACITY_SCALE / d;
            if (temp < min_opacity){
                return min_opacity;
            }
            else {
                return temp;
            }
        }
        DOT_STROKE_OPACITY = 0.5;
        
        var HOST = 'http://localhost:8080';

        if (style == "original") {
            CLOUDMADE_MAP_ID = 998;
            DOT_FILL_COLOR = "green";
            DOT_STROKE_COLOR = "black";
            DOT_SCALE = 50;
            NAME_FILL_COLOR = "black";
            NAME_STROKE_COLOR = "green";
            NAME_OPACITY = 0.5;
            NAME_FONT_SIZE = 12;
            LINK_COLOR = "black";
            LINK_WIDTH = 3;
            LINK_OPACITY_SCALE = 10;
            LINK_MIN_OPACITY = 0.0;
        }
        else if (style == "midnight") {
            // original land color was #08314b
            // too dark: #062436
            // better: #072D43
            // still too dark: #06293D
            //CLOUDMADE_MAP_ID = 999;  // original midnight commander
            CLOUDMADE_MAP_ID = 28046; // midnight commander, minus ocean labels
            DOT_FILL_COLOR = "white";
            DOT_STROKE_COLOR = "white";
            DOT_SCALE = 25; // use 1 (!) for one month w/all projects
            NAME_FILL_COLOR = "black";
            NAME_STROKE_COLOR = "green";
            NAME_OPACITY = 0.5;
            NAME_FONT_SIZE = 12;
            LINK_COLOR = "white";
            LINK_WIDTH = 3;
            LINK_OPACITY_SCALE = 25;
            // try not to let this get too light:
            LINK_MIN_OPACITY = 0.02
        }
        else if (style == "blue") {
            // BH: would probably look quite nice if borders could be removed!!!
            // nice, bright blue from colorbrewer
            brightblue = "2C7FB8";
            CLOUDMADE_MAP_ID = 28028;
            DOT_FILL_COLOR = brightblue;
            DOT_STROKE_COLOR = "white";
            DOT_SCALE = 25;
            NAME_FILL_COLOR = "black";
            NAME_STROKE_COLOR = "green";
            NAME_OPACITY = 0.5;
            NAME_FONT_SIZE = 12;
            LINK_COLOR = brightblue;
            LINK_WIDTH = 3;
            LINK_OPACITY_SCALE = 25;
            LINK_MIN_OPACITY = 0.00;
        }

        // Sorts circles to by size descending
        var sortCirclesSize = function(a, b)
        {
            return b.loc_count - a.loc_count;
        };

        // Sorts links by opacity descending
        var sortLinksOpacity = function(a, b)
        {
            return b[1] - a[1];
        };
        
        var sortLinksDate = function(a, b)
        {
            return eval(a[2]).getTime() - eval(b[2]).getTime();
        }

        var linearScale = function(maxIndex, minValue, maxValue)
        {
            return function(index)
            {
                return minValue + (maxValue - minValue) * (index/maxIndex)
            }
        };

        var animator = (function()
        {
            var data;
            var idx = 0;
            var point = null;
            var interval = null;
            var firstMonth, firstYear, lastMonth, lastYear;
            var datePairs = [];
            var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            
            
            var draw = function() {
                var vals = $('.ui-slider').slider('values');
                var date_start = datePairs[vals[0]];
                date_start = new Date(date_start[1], date_start[0]-1);
                var date_end = datePairs[vals[1]];
                date_end = new Date(date_end[1], date_end[0]-1);
                var data = {links: [], locations: {}, stats: {}};
                var elDate, link, hash;
                for (var i = 0; i < masterData.links.length; i++)
                {
                    link = masterData.links[i];
                    elDate = eval(link[2]);
                    if ((date_start.getTime() < elDate.getTime()) && (elDate.getTime() < date_end.getTime()))
                    {
                        data.links.push(link);
                        hash = link[0][0]+','+link[0][1];
                        if (hash in data.locations)
                        {
                            data.locations[hash] += 1;
                        }
                        else
                        {
                            data.locations[hash] = 1;
                        }
                    }
                }
                var circles = [];
				
                for (var idx in data.locations)
                {
                    var strip = idx.split(',');
                    circles.push({long: parseFloat(strip[0]), lat: parseFloat(strip[1]), loc_count: data.locations[idx]});
                }
                data.locations = circles;
                data.stats.link_count = data.links.length;
                data.stats.location_count = data.locations.length;
                annotateMap();
            };
            
            var showNext = function()
            {   
                var vals = $('.ui-slider').slider('values');
                if (vals[1]+1 >= datePairs.length)
                {
                    stop();
                }
                else
                {
                    $.each(svgEls, function(idx, el){
                       $.each(el, function(i, e){
                          map.remove(e); 
                       });
                    });

                    svgEls = {
                        circles: [],
                        lines: []
                    };
                    $('.ui-slider').slider('values', [vals[0], vals[1]+1]);
                    draw();
                }

                
            }
            
            var init = function()
            {
                // sort by date ascending
                var link_data = [[[37.615101, 55.75687], [-79.385324, 43.64856], "new Date('Fri Jan 14 03:08:16 2010')"], [[-70.251734, 43.63761], [-73.990364, 40.692455], "new Date('Wed Jun 23 13:44:00 2010')"]];
                link_data.sort(sortLinksDate);
                
                // get m/y endpoints
                var dt = eval(link_data[0][2]);
                firstMonth = dt.getMonth()+1;
                firstYear = dt.getFullYear();
                dt = eval(link_data[link_data.length-1][2]);
                lastMonth = dt.getMonth()+1;
                lastYear = dt.getFullYear();
                
                // generate array of m/y pairs
                var month = firstMonth;
                var year = firstYear;
                var options = '';
                //options = '<optgroup label="'+firstYear+'">\n';
                while (true)
                {
                    var end = false;
                    if (month == lastMonth && year == lastYear)
                    {
                        end = true;
                    }
                    datePairs.push([month, year]);
                    options += '<option value="'+month+'/'+year+'">'
                        +months[month-1]+' '+((year%100) < 10 ? "0"+(year%100) : (year%100))+'</option>\n';
                    month++;
                    if (end)
                    {
                        options += '</optgroup>\n';
                        break;
                    }
                        
                    if (month > 12)
                    {
                        year++;
                        //options += '</optgroup>\n';
                        //options += '<optgroup label="'+year+'">';
                        month = 1;
                    }
                }
                
                var newEl = '<form action="#"><select style="display:none;" name="valueA" id="valueA">'+options+'</select>'
                            +'<select style="display:none;" name="valueB" id="valueB">'+options+'</select></form>';
                
                $('#slider').append($(newEl));
                $('#valueB option')[$('#valueB option').length-1].selected = true;
                var temp = $('select').selectToUISlider({
                                labels: 7
                            });
                var x = 5;
                            
                $('#play').show();
            }
            
            var start = function() {
                var vals = $('.ui-slider').slider('values');
                if (vals[1]+1 >= datePairs.length)
                $('.ui-slider').slider('values', [vals[0], vals[0]+1]);
                if (datePairs.length < 15)
                {
                    interval =  setInterval("animator.showNext()", 800);
                }
                else { 
                    interval =  setInterval("animator.showNext()", Math.max(100, TOTAL_ANIM_TIME_MS/datePairs.length));
                }
                
            }

            var setData = function(theData) {
                data = theData;
            };

            var stop = function() {
                clearInterval(interval);
                interval = null;
                //$('#play').attr("src", 'play.png');
                $('#play span').removeClass('ui-icon-pause');
                $('#play span').addClass('ui-icon-play');
            };

            return {
                init: init,
                setData : setData,
                showNext : showNext,
                stop : stop,
                start : start,
                isActive : function() { return interval != null;}
            };
        })();
        
        var svgEls = {
            circles: [],
            lines: []
        };
        
        var masterData;
        
        var annotateMap = function()
        {
            var link_data, location_data, stats_data, source;
            if (arguments.length)
            {
                source = arguments[0];
            }
            else
            {
                source = masterData;
            }
            console.log(source);
            //link_data = [[[37.615101, 55.75687], [-79.385324, 43.64856], "new Date('Fri Sep 29 03:08:16 2006')"], [[-70.251734, 43.63761], [-73.990364, 40.692455], "new Date('Wed Jun 17 13:44:00 2009')"]];
            //console.log(link_data);
            //masterData.links = link_data;
            //locations = [{"authors": ["andy"], "lat": 55.75687, "loc_count": 1, "locations": ["Moscow"], "long": 37.615101},{"authors": ["ryan"], "lat":40.7143528, "loc_count": 1, "locations": [], "long": -74.0059731}];
			 //locations = [{"date": "Wed Jan 13 03:21:27 +0000 2010", "lat": 40.7143528, "long": -74.0059731}];
var locationsz = <?php include("map.txt") ?>;
            //location_data = locations;

            stats_data = null;
             var arr = []
             //location_data.sort(sortCirclesSize);
             if (circles) {
                 for (var i = 0; i < locationsz.length; i++) {
                     var l = locationsz[i]
                     var el = po.geoJson().features([
                         {
                             "geometry": {
                                 "coordinates": [ parseFloat(l.lng), parseFloat(l.lat) ],
                                 "type": "Point"
                             },
                             "size": 1
                         }
                     ]).on(
                         'load',
                         function(e) {
                             e.features.forEach(function(f) {
                                 var p = n$(f.element);
                                 /*
                                 p.on('mouseover', function() {
                                     point = n$(this);
                                     point.style('fill', 'black');
                                     point.style('opacity', 1);
                                 });
                                 p.on('mouseout', function() {
                                     point = n$(this);
                                     point.style('opacity', 'green');
                                     point.style('opacity', f.data.size / 2);
                                 });
                                 */
                                 p.style('fill', DOT_FILL_COLOR);
                                 p.style('stroke', DOT_STROKE_COLOR);
                                 p.style('stroke-opacity', DOT_STROKE_OPACITY);
                                 p.style('opacity', f.data.size / 2);
                                 if (!presentationMode)
                                    p.attr('r', Math.sqrt(f.data.size * DOT_SCALE * scale));
                                else
                                    p.attr('r', Math.pow(f.data.size * DOT_SCALE * scale, 0.35));
                             });
                         }
                     );
                     svgEls.circles.push(el);
                     map.add(el);

                     if (names) {
                         map.add(po.geoJson().features([
                             {
                                 "geometry": {
                                     "coordinates": [ l.long, l.lat ],
                                     "type": "Point"
                                 },
                                 "location": l.locations[0],
                                 "size": l.loc_count
                             }
                         ]).on(
                             'load',
                             function(e) {
                                 e.features.forEach(function(f) {
                                     var p = n$(f.element);
                                     p.attr('opacity', 0);
                                     if (f.data.location) {
/*
                                         var g = p.parent().add('svg:g', p);
                                         g.attr('transform', p.attr('transform'));
                                         var r = Math.sqrt(f.data.size * 50);
                                         g.add('svg:text')
                                             .attr('transform', 'translate(' + (r + 3) + ',' + (-r) + ')')
                                             .style('fill', NAME_FILL_COLOR)
                                             .style('stroke', NAME_STROKE_COLOR)
                                             .style('opacity', NAME_OPACITY)
                                             .style('font-size', NAME_FONT_SIZE)
                                             .text(f.data.location);
                                         
                                         g.add('svg:rect')
                                             .attr('x', -3)
                                             .attr('y', -11)
                                             .attr('width', text.element.offsetWidth)
                                             .attr('height', 15)
                                             .style('opacity', '0.5')
                                             .style('fill', 'black');
                                         */
                                     }
                                 });
                             }
                         ));
                     }
                 }
             }
			 lines = 0;
             if (lines) {
                 //link_data.sort(sortLinksOpacity);
                 var scaleFunc;
                 if (linkMaxScale)
                     scaleFunc = linearScale(linkMaxScale, 0.02, 0.3);
                 for (var i = 0; i < link_data.length; i++)
                 {
                     var l = link_data[i];
                     var strength = l[1];
                     var c = l[0];
                     var coords;
                     if (linkAggregation)
                         coords = [ [c[0][0], c[0][1]], [c[1][0], c[1][1]] ];
                     else
                         coords = [ [l[0][0], l[0][1]], [l[1][0], l[1][1]] ];
                     //arr.push([ [l[0].long, l[0].lat], [l[1].long, l[1].lat] ]);
                     var el = po.geoJson().features([
                         {
                             "geometry": {
                                 "coordinates": coords,
                                 "type": "LineString"
                             }
                         }
                     ]).on(
                         'load', (function(){
                         var oVal = 0.05//get_link_opacity(link_data.length, LINK_MIN_OPACITY);
                         if (linkAggregation && scaleFunc != null)
                             oVal = scaleFunc(strength);
                         return po.stylist().attr("stroke", LINK_COLOR)
                                     .attr("stroke-width", LINK_WIDTH)
                                     .attr("stroke-opacity", oVal);
                         })()
                     );
                     svgEls.lines.push(el);
                     map.add(el);
                 }
             }

        }

        $(document).ready(function() {
            map = null;
            po = org.polymaps;
            map = po.map()
                .container(document.getElementById("map").appendChild(po.svg("svg")))
                .center({lat: LAT_START, lon: LON_START})
                .zoom(ZOOM)
                .add(po.drag())
                .add(po.dblclick())
                .add(po.wheel());

            map.add(po.image()
                .url(po.url("http://{S}tile.cloudmade.com"
                + "/1a1b06b230af4efdbb989ea99e9841af" // http://cloudmade.com/register
                + "/"
                + CLOUDMADE_MAP_ID
                + "/256/{Z}/{X}/{Y}.png")
                .hosts(["a.", "b.", "c.", ""]))
            );
annotateMap();
animator.init();
            $.ajax({
                url: HOST+'/query' + formatUrlParams(),
                dataType: "jsonp",
                jsonpCallback: "jsonpcallback",
                success: function(data, status)
                {
                    masterData = data;
                    $('#project_name').text(project);
                    $('#searchbox').attr('value', project);
                    annotateMap();
                    if (presentationMode)
                    {
                        animator.init();
                    }

                }
            });

            $.fn.defaultText = function(value){
                var element = this.eq(0);
                element.data('defaultText',value);

                element.focus(function(){
                    if(element.val() == value){
                        element.val('').removeClass('defaultText');
                    }
                }).blur(function(){
                    if(element.val() == '' || element.val() == value){
                        element.addClass('defaultText').val(value);
                    }
                });

                return element.blur();
            }
            
            $('#searchbox').defaultText(project || 'Search...');
            $('#searchbox').autocomplete({
                source: '/projects',
                selectFirst: true,
                select: function(event, ui) {
                    window.location = '/static/map.html' + formatUrlParams({'project': ui.item.value});
                }
            });
            
            $('#play').click(function(){
                if (animator.isActive())
                {
                    animator.stop();
                }
                else
                {
                    //$(this).attr("src", 'stop.png');
                    $(this).find('span').removeClass('ui-icon-play');
                    $(this).find('span').addClass('ui-icon-pause');
                    animator.start(); 
                }
                
               
            });
        });
    </script>
    <!--<script type="text/javascript" src="map.js"></script>-->
  </head>
  <body>
    <div id="map">
        <div style="display:none;" id="stats">
            <table width="100%">
                <tr>
                    <td class="label"># people:</td>
                    <td id="people_count">0</td>
                </tr>
                <tr>
                    <td class="label"># commits:</td>
                    <td id="commit_count">0</td>
                </tr>
                <tr>
                    <td class="label"># links:</td>
                    <td id="link_count">0</td>
                </tr>
                <tr style="display:none;">
                    <td class="label"># locations:</td>
                    <td id="location_count">0</td>
                </tr>
            </table>
        </div>
        <div id="title">
            <input id="searchbox"/>
            <!--<h1 id="project_name"></h1>-->
        </div>
        <div id="controls">
            <div id="container"> 
                <div id="slider">
                    <span style="display:none;" id="play" class="ui-state-default ui-corner-all">
                        <span class="ui-icon ui-icon-play"></span>
                    </span>
                </div>
                <!--<img id="play" src="play.png" alt="Play">-->
            </div>
        </div>
    </div>
  </body>
</html>

<!--
vim: ts=4 sw=4 sts=4 expandtab:
-->
