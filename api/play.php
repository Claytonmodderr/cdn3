
<?php

$domain = "https://cdnapimega.lat/record/";
$url = file_get_contents($domain."canais.php?list");
$json = json_decode($url, true);
?>

<!--PLAYER-->
<!doctype html>
<html lang="pt_BR">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<style>
		body {
			margin: 0;
			padding: 0;
		}

		#player {
			position: absolute;
			top: 0;
			left: 0;
			bottom: 0;
			height: 100%;
			width: 100%;
			border: 0;
			overflow: hidden;
		}
	</style>
	<meta name="referrer" content="no-referrer">
	<script src="https://content.jwplatform.com/libraries/KB5zFt7A.js"></script>
	<script>
		jwplayer.key = 'XSuP4qMl+9tK17QNb+4+th2Pm9AWgMO/cYH8CI0HGGr7bdjo';
	</script>
</head>

<body>
	<div id="player"></div>
	<script>
		var playerInstance = jwplayer("player");
		playerInstance.setup({
			playlist: [{
				"sources": [{
					"default": false,
					"type": "hls",
					"file": "<?php echo $json[$_GET['v']]["urlHLS"];?>",
					"label": "0"
				}]
			}],
			width: "100%",
			height: "100%",
			aspectratio: "16:9",
			autostart: false,
			cast: {},
			sharing: false
		});
	</script>

</body>

</html>
<!--PLAYER-->
