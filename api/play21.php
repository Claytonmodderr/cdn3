<?php
$domain = "https://cdn3-tau.vercel.app/api/";
$url = file_get_contents($domain."canais.php?list");
$json = json_decode($url, true);

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script disable-devtool-auto src='https://cdn.jsdelivr.net/npm/disable-devtool@latest'></script>
    <meta name="referrer" content="no-referrer">
    <title>Player</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: black;
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

        .chromecast-button {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 100;
            background: none;
            border: none;
            cursor: pointer;
            color: white;
        }
    </style>
    <script src="https://www.gstatic.com/cv/js/sender/v1/cast_sender.js?loadCastFramework=1"></script>
    <script>
if (window.top == window.self) {
	window.location.href = "https://google.com";
}

document.addEventListener('contextmenu', event => event.preventDefault());
document.onkeydown = function(e) {
        if (e.ctrlKey && 
            (e.keyCode === 67 || 
             e.keyCode === 86 || 
			 e.keyCode === 83 || 
             e.keyCode === 85 || 
			 e.keyCode === 80 || 
			 e.keyCode === 123 || 
			 e.keyCode === 73 || 
             e.keyCode === 74 ||  
             e.keyCode === 117)) {
            alert('PROIBIDO');
            return false;
        } else {
            return true;
        }
};
	
$(document).bind('keydown', function(e) {
  if(e.ctrlKey && (e.which == 83)) {
    e.preventDefault();
    alert('PROIBIDO');
    return false;
  }
});
$(document).keydown(function (event) {
    if (event.keyCode == 123) { // Prevent F12
        return false;
    } else if (event.ctrlKey && event.shiftKey && event.keyCode == 73) { // Prevent Ctrl+Shift+I        
        return false;
    }
});
$(document).on("contextmenu", function (e) {        
    e.preventDefault();
});

function devtoolIsOpening() {
    console.clear();
    let before = new Date().getTime();
    debugger;
    let after = new Date().getTime();
    if (after - before > 200) {
        document.write(" Dont open Developer Tools. ");
        window.location.replace("https://www.google.com");
    }
    setTimeout(devtoolIsOpening, 100);
}
devtoolIsOpening();
</script>
</head>

<body>
    <video id="player" controls>
        <source src="https://evovideom.online/playplus/canais.php?list" type="application/x-mpegURL">
    </video>
    <button class="chromecast-button" id="castButton">
        <svg width="32" height="32" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
            <path d="M 6 6 C 4.897 6 4 6.897 4 8 L 4 12 L 6 12 L 6 8 L 26 8 L 26 24 L 18 24 L 18 26 L 26 26 C 27.103 26 28 25.103 28 24 L 28 8 C 28 6.897 27.103 6 26 6 L 6 6 z M 4 14 L 4 16 C 9.169375 16 13.436179 19.942273 13.949219 24.978516 C 13.983421 25.314265 14 25.655375 14 26 L 16 26 C 16 19.383 10.617 14 4 14 z M 4 18 L 4 20 C 7.309 20 10 22.691 10 26 L 12 26 C 12 21.589 8.411 18 4 18 z M 4 22 L 4 26 L 8 26 C 8 23.791 6.209 22 4 22 z" fill="currentColor" />
        </svg>
    </button>

    <script>
        const videoUrl = "<?php echo $json[$_GET['v']]["urlHLSBackup"];?>";
        const video = document.getElementById('player');
        video.src = videoUrl;
        video.autoplay = true;

        function initChromecast() {
            if (typeof chrome === undefined) {
                return;
            }
            const loadCastInterval = setInterval(function () {
                if (chrome.cast.isAvailable) {
                    clearInterval(loadCastInterval);
                    initCastApi();
                    buttonEvents();
                }
            }, 1000);
        }

        function initCastApi() {
            cast.framework.CastContext.getInstance().setOptions({
                receiverApplicationId: chrome.cast.media.DEFAULT_MEDIA_RECEIVER_APP_ID,
                autoJoinPolicy: chrome.cast.AutoJoinPolicy.ORIGIN_SCOPED
            });
        }

        function connectToSession() {
            return Promise.resolve()
                .then(() => {
                    const castSession = cast.framework.CastContext.getInstance().getCurrentSession();
                    if (!castSession) {
                        return cast.framework.CastContext.getInstance().requestSession()
                            .then(() => Promise.resolve(cast.framework.CastContext.getInstance().getCurrentSession()));
                    }
                    return Promise.resolve(castSession);
                });
        }

        function buttonEvents() {
            document.getElementById('castButton').addEventListener('click', function () {
                launchApp();
            });
        }

        function launchApp() {
            return connectToSession()
                .then((session) => {
                    const mediaInfo = new chrome.cast.media.MediaInfo(videoUrl, 'application/x-mpegURL');
                    const request = new chrome.cast.media.LoadRequest(mediaInfo);
                    request.autoplay = true;
                    return session.loadMedia(request);
                })
                .catch((error) => {
                    console.log(error);
                });
        }

        initChromecast();
    </script>
    
    


</html>




