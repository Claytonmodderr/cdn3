<?php
$domain = "https://cdn3-two.vercel.app/api/";
$url = file_get_contents($domain."canais.php?list");
$json = json_decode($url, true);

?>
<!--PLAYER-->
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <title>Player</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, shrink-to-fit=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="mobile-web-app-capable" content="yes">
    <meta http-equiv="Content-Language" content="pt-br" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="format-detection" content="telephone=no">
    <meta name="referrer" content="no-referrer">

    <link rel="stylesheet" type="text/css"
        href="//cdn.jsdelivr.net/gh/reidoscanais/rdc@main/assets/css/player-v3.1.min.css">
    <link rel="stylesheet" href="//cdn.jsdelivr.net/gh/reidoscanais/rdc@main/assets/css/hotstar.css">
    <script src="//cdn.jsdelivr.net/gh/reidoscanais/rdc@main/assets/jwplayer/jwplayer.latest.js"></script>

    <style>
        * {margin:0;padding:0;box-sizing:border-box;}
        html,body {
            width:100%;height:100%;overflow:hidden;
            background:#000;
            font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Arial,sans-serif;
            user-select:none;
        }
        #player-container {position:relative;width:100vw;height:100vh;background:#000;}
        #player,#mobile-player {
            width:100%!important;height:100%!important;
            position:absolute;top:0;left:0;border:0;overflow:hidden;
        }
        #mobile-player {object-fit:contain;}
        #loading {
            position:fixed;top:0;left:0;width:100%;height:100%;
            background:rgba(0,0,0,0.9);
            display:flex;justify-content:center;align-items:center;
            z-index:9999;color:#fff;
        }
        .loading-content{text-align:center;padding:20px;}
        .loading-text{font-size:16px;margin-bottom:20px;font-weight:500;}
        .spinner {
            width:40px;height:40px;
            border:3px solid rgba(255,255,255,0.3);
            border-top:3px solid #fff;border-radius:50%;
            animation:spin 1s linear infinite;margin:0 auto;
        }
        @keyframes spin {0%{transform:rotate(0deg);}100%{transform:rotate(360deg);}}
        @media screen and (max-width:768px){.loading-text{font-size:14px;}.spinner{width:35px;height:35px;}}
        @media screen and (max-width:480px){.loading-text{font-size:12px;margin-bottom:15px;}.spinner{width:30px;height:30px;border-width:2px;}}
    </style>
</head>

<body>

    <script src="https://cdn.jsdelivr.net/npm/console-ban@4.1.0/dist/console-ban.min.js"></script>
    <script src="//ssl.p.jwpcdn.com/player/v/8.21.0/jwplayer.js"></script>
    <script>
        jwplayer.key = 'ITWMv7t88JGzI0xPwW8I0+LveiXX9SWbfdmt0ArUSyc='; 
    </script>

    <div id="player-container">
        <div id="player" style="display: none;"></div>
        <video id="mobile-player" controls style="display: none;"></video>
    </div>

    <div id="loading">
        <div class="loading-content">
            <div class="loading-text">Carregando transmissão...</div>
            <div class="spinner"></div>
        </div>
    </div>

    <script>
    try {
        // Detecta dispositivo
        const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

        // Pega parâmetros da URL
        const urlParams = new URLSearchParams(window.location.search);
        const channelId = urlParams.get('id');
        const isAdmin = urlParams.get('admin') === 'true';

        // Proteção contra F12
        if (!isAdmin && !isMobile) {
            ConsoleBan.init({ redirect: 'https://www.google.com' });
        }

        // Endpoints
        const API_URL = "https://cdn3-two.vercel.app/api/canais.php?list";
        const PROXY_URL = "https://cors-anywhere.herokuapp.com/"; // coloque aqui se quiser usar proxy no desktop

        function hideLoading() {
            document.getElementById('loading').style.display = 'none';
        }
        function showPlayer() {
            if (isMobile) document.getElementById('mobile-player').style.display = 'block';
            else document.getElementById('player').style.display = 'block';
        }

        // Busca URL do canal
        async function getStreamUrl() {
            if (!channelId) {
                hideLoading();
                alert("Nenhum ID de canal informado.");
                return null;
            }
            try {
                const response = await fetch(API_URL);
                if (!response.ok) throw new Error("Erro ao carregar lista de canais");

                const canais = await response.json();
                const canal = canais[channelId];

                if (!canal) throw new Error("Canal não encontrado.");

                // usa backup como prioridade
                let streamUrl = canal.urlHLSBackup || canal.urlHLS;
                if (!streamUrl) throw new Error("Nenhum link válido encontrado.");

                if (isMobile) {
                    return streamUrl;
                } else {
                    return PROXY_URL ? PROXY_URL + encodeURIComponent(streamUrl) : streamUrl;
                }
            } catch (err) {
                hideLoading();
                alert("Erro: " + err.message);
                return null;
            }
        }

        // Player mobile
        async function setupMobilePlayer() {
            const streamUrl = await getStreamUrl();
            if (!streamUrl) return;

            hideLoading(); showPlayer();

            const video = document.getElementById('mobile-player');
            video.src = streamUrl;
            video.autoplay = true;
            video.controls = true;
            video.playsinline = true;

            window.mobilePlayer = video;
        }

        // Player desktop (JWPlayer)
        async function setupDesktopPlayer() {
            const streamUrl = await getStreamUrl();
            if (!streamUrl) return;

            hideLoading(); showPlayer();

            const playerInstance = jwplayer("player");
            playerInstance.setup({
                playlist: [{
                    sources: [{
                        file: streamUrl,
                        type: "hls",
                        label: "HD",
                        default: true
                    }]
                }],
                width: "100%", height: "100%", autostart: true, controls: true,
                skin: { name: "hotstar" }
            });

            window.desktopPlayer = playerInstance;
        }

        // Inicialização
        function initPlayer() {
            if (isMobile) setupMobilePlayer();
            else setupDesktopPlayer();
        }

        if (document.readyState === 'loading')
            document.addEventListener('DOMContentLoaded', initPlayer);
        else
            initPlayer();

    } catch (e) {
        alert("Erro crítico: " + e.message);
        window.location.href = "https://google.com";
    }
    </script>
</body>
</html>


