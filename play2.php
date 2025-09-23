<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8">
  <title>Player tv mag m3u8 free </title>
  

</head>
<body>

    <?php
$domain = "https://cdn3-two.vercel.app/api/";
$url = file_get_contents($domain."canais.php?list");
$json = json_decode($url, true);

?>
    <!-- partial:index.partial.html -->
<style>
		    body, html, #pps {width:100%; height:100%;  }  
       html {
                overflow: hidden;
            }
            html > body {
                margin: 0;
            }
		</style>
	</head>
	<body><div id="pps"><link href="https://static.publit.io/css/player.min.css" rel="stylesheet" /><video id="pv_jNlXozfh" class="video-js vjs-skin-colors-blue vjs-big-play-centered" controls preload="auto" poster=""  oncontextmenu="return false;" controlslist="nodownload" playsinline><source src="<?php echo $json[$_GET['v']]["urlHLS"];?>" type="application/x-mpegURL"><p class="vjs-no-js"> Vizionarea nu poate fi redata!. Deoarece nu poti reda, te rugam sa actualizezi broweserul la unul actual. <a href="https://browsehappy.com/"> Alege unu dintre broweserele prezentate, pe care le folosesti actual. <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQFLD3DdynGlTvLyLeOyYWC9BO03e7b8eSy_g&usqp=CAU"></a></p>

English version: Viewing cannot be played !. Since you cannot play, please update the browser to the current one. <a href="https://browsehappy.com/"> Choose one of the featured browsers you are currently using. <img src = "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQFLD3DdynGlTvLyLeOyYWC9BO03e7b8eSy_g&usqp=CAU"> </a> </video><script src="https://static.publit.io/js/player.min.js"></script><script src="https://firebasestorage.googleapis.com/v0/b/casamireasa.appspot.com/o/live.js?alt=media&token=fe11efb2-f591-4e81-a343-5d785c7a4100"></script>	    </div>

	    <script type="text/javascript" src="https://firebasestorage.googleapis.com/v0/b/casamireasa.appspot.com/o/oporeno5g.js?alt=media&token=e2892207-0a49-407e-9c1e-e1ad251c3813">
		</script>
	</body>
</html>
<!-- partial -->
  
</body>
</html>
