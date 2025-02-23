<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<title>Add a autocomplete widget to Goong JS</title>
<meta name="viewport" content="initial-scale=1,maximum-scale=1,user-scalable=no" />
<script src="https://cdn.jsdelivr.net/npm/@goongmaps/goong-js@1.0.9/dist/goong-js.js"></script>
<link href="https://cdn.jsdelivr.net/npm/@goongmaps/goong-js@1.0.9/dist/goong-js.css" rel="stylesheet" />
<style>
	body { margin: 0; padding: 0; }
	#map { position: absolute; top: 0; bottom: 0; width: 100%; }
</style>
</head>
<body>
<!-- Load the `goong-geocoder` plugin. -->
<script src="https://cdn.jsdelivr.net/npm/@goongmaps/goong-geocoder@1.1.1/dist/goong-geocoder.min.js"></script>
<link
    href="https://cdn.jsdelivr.net/npm/@goongmaps/goong-geocoder@1.1.1/dist/goong-geocoder.css"
    rel="stylesheet"
    type="text/css"
/>

<!-- Promise polyfill script is required -->
<!-- to use Goong Geocoder in IE 11. -->
<script src="https://cdn.jsdelivr.net/npm/es6-promise@4/dist/es6-promise.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/es6-promise@4/dist/es6-promise.auto.min.js"></script>

<div id="map"></div>

<script>
    goongjs.accessToken = 'nOBVsYUmcnYdo2WgXUAntf3FscSjtKk7Fa52D7oB';
    var map = new goongjs.Map({
        container: 'map',
        style: 'https://tiles.goong.io/assets/goong_map_web.json',
        center: [105.84478, 21.02897],
        zoom: 13
    });

    // Add the control to the map.
    map.addControl(
        new GoongGeocoder({
            accessToken: 'nOBVsYUmcnYdo2WgXUAntf3FscSjtKk7Fa52D7oB',
            goongjs: goongjs
        })
    );
</script>

</body>
</html>