<html>
  <head>
    <title>Project Haiti</title>
    <script type="text/javascript" src="http://polymaps.org/protodata.min.js?3.2"></script>
    <script type="text/javascript" src="polymaps.min.js"></script>
    <script type="text/javascript" src="nns.min.js"></script>
    <script type="text/javascript" src="jshashtable.js"></script>
    <script type="text/javascript" src="jquery.min.js"></script>
   
	<script type="text/javascript" src="date-en-US.js"></script>
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

var ZOOM = 2.8;
var LON_START = 0;
var LAT_START = 30;
var TOTAL_ANIM_TIME_MS = 20000;
var urlParams = {};
(function () {
    var e, a = /\+/g,
        // Regex for replacing addition symbol with a space
        r = /([^&;=]+)=?([^&;]*)/g,
        d = function (s) {
            return decodeURIComponent(s.replace(a, " "));
        },
        q = window.location.search.substring(1);

    while (e = r.exec(q))
    urlParams[d(e[1])] = d(e[2]);
})();

var style = urlParams['style'];
if (style == null) style = "midnight";
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


var formatUrlParams = function (overrides) {
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

var get_link_opacity = function (d, min_opacity) {
    temp = LINK_OPACITY_SCALE / d;
    if (temp < min_opacity) {
        return min_opacity;
    } else {
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
} else if (style == "midnight") {
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
} else if (style == "blue") {
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
var sortCirclesSize = function (a, b) {
    return b.loc_count - a.loc_count;
};

// Sorts links by opacity descending
var sortLinksOpacity = function (a, b) {
    return b[1] - a[1];
};

var sortLinksDate = function (a, b) {
    return eval(a[2]).getTime() - eval(b[2]).getTime();
}

var linearScale = function (maxIndex, minValue, maxValue) {
    return function (index) {
        return minValue + (maxValue - minValue) * (index / maxIndex)
    }
};

var animator = (function () {
    var data;
    var idx = 0;
    var point = null;
    var interval = null;
    var firstMonth, firstYear, lastMonth, lastYear;
    var datePairs = [];
    var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];


    var draw = function () {
        var vals = $('.ui-slider').slider('values');

        var date_start = datePairs[vals[0]];
        date_start = new Date(date_start[2], date_start[0] - 1, date_start[1]);

        var date_end = datePairs[vals[1]];
        var hour1 = date_end[3];
        //alert(hour1);
        date_end = new Date(date_end[2], date_end[0] - 1, date_end[1]);

        var date1 = date_end.getDate();
        var month1 = date_end.getMonth() + 1;
        if (month1 < 10) {
            month1 = "0" + month1;
        }
        var fullyear1 = date_end.getFullYear();

		var circles = [];
        $.ajax({
            type: "GET",
            url: 'getData.php',
            data: "date=" + fullyear1 + "-" + month1 + "-" + date1 + "&hour=" + hour1,
            success: function (data) {
                locationsz = eval(data);
                //console.log(data);
                annotateMap();
                //animator.init();
            }
        });


    };

    var showNext = function () {
        var vals = $('.ui-slider').slider('values');
        if (vals[1] + 1 >= datePairs.length) {
            stop();
        } else {
            $.each(svgEls, function (idx, el) {
                $.each(el, function (i, e) {
                    map.remove(e);
                });
            });

            svgEls = {
                circles: [],
                lines: []
            };
            $('.ui-slider').slider('values', [vals[0], vals[1] + 1]);
            draw();
        }


    }

    var init = function () {
        // sort by date ascending
        var link_data = [
            [
                [37.615101, 55.75687],
                [-79.385324, 43.64856], "new Date('Fri Jan 14 03:08:16 2010')"],
            [
                [-70.251734, 43.63761],
                [-73.990364, 40.692455], "new Date('Wed Jun 23 13:44:00 2010')"]
        ];
        link_data.sort(sortLinksDate);

        // get m/y endpoints
        var dt = eval(link_data[0][2]);
        firstDay = dt.getDate();
        firstMonth = dt.getMonth() + 1;
        firstYear = dt.getFullYear();
        dt = eval(link_data[link_data.length - 1][2]);
        lastDay = dt.getDate();
        lastMonth = dt.getMonth() + 1;
        lastYear = dt.getFullYear();
        var dateslist = expandRange(new Date('2010-01-13'), new Date('2010-02-23'));


        // generate array of m/y pairs
        var day = firstDay;
        var month = firstMonth;
        var year = firstYear;
        var options = '';
        //options = '<optgroup label="'+firstYear+'">\n';
        var arLen = dateslist.length;
        for (var i = 0, len = arLen; i < len; ++i) {

            var date = dateslist[i].getDate();
            var month = dateslist[i].getMonth() + 1;
            var year = dateslist[i].getFullYear();
            //var hour = dateslist[i].getHours();
            for (var j = 1; j <= 24; j++) {
                datePairs.push([month, date, year, j]);
                options += '<option value="' + month + '/' + date + '/' + j + '">' + months[month - 1] + ' ' + date + '</option>\n';
            }

        }

        var newEl = '<form action="#"><select style="display:none;" name="valueA" id="valueA">' + options + '</select>' + '<select style="display:none;" name="valueB" id="valueB">' + options + '</select></form>';

        $('#slider').append($(newEl));
        $('#valueB option')[$('#valueB option').length - 1].selected = true;
        var temp = $('select').selectToUISlider({
            labels: 10
        });
        var x = 5;

        $('#play').show();
    }

    var start = function () {
        var vals = $('.ui-slider').slider('values');
        if (vals[1] + 1 >= datePairs.length) $('.ui-slider').slider('values', [vals[0], vals[0] + 1]);
        if (datePairs.length < 15) {
            interval = setInterval("animator.showNext()", 1000);
        } else {
            interval = setInterval("animator.showNext()", Math.max(1000, TOTAL_ANIM_TIME_MS / datePairs.length));
        }

    }

    var setData = function (theData) {
        data = theData;
    };

    var stop = function () {
        clearInterval(interval);
        interval = null;

        //$('#play').attr("src", 'play.png');
        $('#play span').removeClass('ui-icon-pause');
        $('#play span').addClass('ui-icon-play');
    };

    return {
        init: init,
        setData: setData,
        showNext: showNext,
        stop: stop,
        start: start,
        isActive: function () {
            return interval != null;
        }
    };
})();

var svgEls = {
    circles: [],
    lines: []
};

var locationsz;
var masterData;

var annotateMap = function () {
    var link_data, location_data, stats_data, source;

    console.log(locationsz.length);
    stats_data = null;
    var arr = []

    for (var i = 0; i < locationsz.length; i++) {
        var l = locationsz[i]

        var el = po.geoJson().features([{
            "geometry": {
                "coordinates": [parseFloat(l.lng), parseFloat(l.lat)],
                "type": "Point"
            },
            "size": 1
        }]).on('load', function (e) {
            e.features.forEach(function (f) {
                var p = n$(f.element);
                p.style('fill', DOT_FILL_COLOR);
                p.style('stroke', DOT_STROKE_COLOR);
                p.style('stroke-opacity', DOT_STROKE_OPACITY);
                p.style('opacity', f.data.size / 2);
                if (!presentationMode) p.attr('r', Math.sqrt(f.data.size * DOT_SCALE * scale));
                else p.attr('r', Math.pow(f.data.size * DOT_SCALE * scale, 0.35));

            });
        });
        svgEls.circles.push(el);
        map.add(el);
    }
}

$(document).ready(function () {


    map = null;
    po = org.polymaps;
    map = po.map().container(document.getElementById("map").appendChild(po.svg("svg"))).center({
        lat: LAT_START,
        lon: LON_START
    }).zoom(ZOOM).add(po.drag()).add(po.dblclick()).add(po.wheel());

    map.add(po.image().url(po.url("http://{S}tile.cloudmade.com" + "/52b63b9f872c4e7888c4ef0a1c5a2b97" // http://cloudmade.com/register
    + "/" + CLOUDMADE_MAP_ID + "/256/{Z}/{X}/{Y}.png").hosts(["a.", "b.", "c.", ""])));

    $.ajax({
        type: "GET",
        url: 'getData.php',
        data: "date=2010-01-13&hour=3",
        success: function (data) {
            locationsz = eval(data);
            annotateMap();
            animator.init();

        }
    });


    $.fn.defaultText = function (value) {
        var element = this.eq(0);
        element.data('defaultText', value);

        element.focus(function () {
            if (element.val() == value) {
                element.val('').removeClass('defaultText');
            }
        }).blur(function () {
            if (element.val() == '' || element.val() == value) {
                element.addClass('defaultText').val(value);
            }
        });

        return element.blur();
    }

    $('#searchbox').defaultText(project || 'Search...');
    $('#searchbox').autocomplete({
        source: '/projects',
        selectFirst: true,
        select: function (event, ui) {
            window.location = '/static/map.html' + formatUrlParams({
                'project': ui.item.value
            });
        }
    });

    $('#play').click(function () {
        if (animator.isActive()) {
            animator.stop();
        } else {
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