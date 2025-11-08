<?php
$domain = "https://cdn3-xi.vercel.app/api/";
$url = file_get_contents($domain."canais.php?list");
$json = json_decode($url, true);

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Player</title>
    <link rel="stylesheet" href="https://cdn.plyr.io/3.7.8/plyr.css" />
    <style>
        :root {
            --plyr-color-main: #ff0000; /* Vermelho */
        }
        body, html { margin: 0; padding: 0; width: 100%; height: 100%; overflow: hidden; background-color: #000; }
        .plyr { width: 100%; height: 100%; }
        .resume-overlay {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.7);
            display: none; justify-content: center; align-items: center; z-index: 100;
        }
        .resume-box {
            background-color: #1e1e1e; color: white; padding: 25px; border-radius: 8px; text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.5);
        }
        .resume-box h3 { margin: 0 0 10px; font-size: 1.4em; }
        .resume-box p { margin: 0 0 20px; font-size: 0.9em; color: #ccc; }
        .resume-btn {
            background-color: #e50914; color: white; border: none; padding: 10px 20px;
            border-radius: 5px; cursor: pointer; font-size: 1em; margin: 0 10px; transition: background-color 0.2s;
        }
        .resume-btn:hover { background-color: #f40612; }
        .resume-btn.secondary { background-color: #555; }
        .resume-btn.secondary:hover { background-color: #666; }

        /* Estilos para o botão de download e responsividade */
        .plyr__control--download svg {
            width: 20px;
            height: 20px;
            fill: white;
        }

        @media (max-width: 768px) {
            .plyr--video .plyr__controls .plyr__control[data-plyr='volume'] {
                min-width: auto; /* Remove a largura mínima para caber o ícone */
            }
            .plyr__volume input[type=range] {
                display: none; /* Oculta a barra de volume */
            }
        }
    </style>
</head>
<body>
    <div id="resume-overlay" class="resume-overlay">
        <div class="resume-box">
            <h3>Deseja continuar assistindo?</h3>
            <p>Você parou em: <span id="resume-time"></span></p>
            <button id="resume-continue-btn" class="resume-btn">Continuar</button>
            <button id="resume-restart-btn" class="resume-btn secondary">Recomeçar</button>
        </div>
    </div>
    <video id="player" playsinline controls preload="auto" data-poster="https://i0.wp.com/imgs.hipertextual.com/wp-content/uploads/2022/08/disney-plus-2-scaled.jpg?fit=2560%2C1440&amp;quality=70&amp;strip=all&amp;ssl=1" autoplay ></video>

    <script src="https://cdn.plyr.io/3.7.8/plyr.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const source = "<?php echo $json[$_GET['v']]["urlHLSBackup"];?>";
            const video = document.getElementById('player');
            const defaultOptions = {
                autoplay: true,
                muted: false,
                controls: ['play-large', 'rewind', 'play', 'fast-forward', 'progress', 'current-time', 'mute', 'volume', 'captions', 'settings', 'pip', 'airplay', 'fullscreen'],
                settings: ['captions', 'quality', 'speed', 'loop'],
                seekTime: 10,
            };

            const storageKey = 'plyr_progress_' + btoa(source);

            // Função para adicionar o botão de download ao player
            function addDownloadButton(player, sourceUrl) {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'plyr__control plyr__control--download';
                button.setAttribute('aria-label', 'Download');
                button.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="#FFFFFF"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/></svg>';

                button.onclick = function() {
                    // Constrói a URL de download que passa pelo nosso proxy PHP
                    const downloadUrl = window.location.pathname + '?action=download&url=' + encodeURIComponent(sourceUrl);
                    
                    // Cria um link temporário para iniciar o download sem sair da página
                    const link = document.createElement('a');
                    link.href = downloadUrl;
                    
                    // O nome do arquivo é definido pelo servidor, mas podemos sugerir um aqui
                    link.download = sourceUrl.split('/').pop() || 'video.mp4';

                    // Adiciona, clica e remove o link do DOM
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                };

                const volumeControl = player.elements.volume;
                if (volumeControl) {
                    volumeControl.parentNode.insertBefore(button, volumeControl.nextSibling);
                } else {
                    // Fallback to inserting before settings or at the end
                    const settingsMenu = player.elements.settings;
                    if (settingsMenu) {
                        settingsMenu.parentNode.insertBefore(button, settingsMenu);
                    } else {
                        player.elements.controls.appendChild(button);
                    }
                }
            }

            function formatTime(seconds) {
                const date = new Date(0);
                date.setSeconds(seconds);
                return date.toISOString().substr(11, 8);
            }

            function setupPlayerWithResume(player, sourceUrl) {
                const storageKey = 'plyr_progress_' + btoa(sourceUrl);

                player.on('timeupdate', event => {
                    if (player.currentTime > 5 && player.playing) {
                        if (!player.lastSavedTime || (player.currentTime - player.lastSavedTime) >= 15) {
                            localStorage.setItem(storageKey, player.currentTime.toString());
                            player.lastSavedTime = player.currentTime;
                        }
                    }
                });

                player.on('pause', event => {
                    if (player.currentTime > 5) {
                        localStorage.setItem(storageKey, player.currentTime.toString());
                    }
                });

                player.on('ready', event => {
                    const savedTime = parseFloat(localStorage.getItem(storageKey));
                    if (savedTime && savedTime > 5 && player.duration && savedTime < player.duration - 10) {
                        document.getElementById('resume-time').innerText = formatTime(savedTime);
                        document.getElementById('resume-overlay').style.display = 'flex';
                        player.pause();

                        document.getElementById('resume-continue-btn').onclick = () => {
                            player.currentTime = savedTime;
                            player.play();
                            document.getElementById('resume-overlay').style.display = 'none';
                        };

                        document.getElementById('resume-restart-btn').onclick = () => {
                            localStorage.removeItem(storageKey);
                            player.currentTime = 0;
                            player.play();
                            document.getElementById('resume-overlay').style.display = 'none';
                        };
                    }
                });
            }

            function setupFullscreenRotation(player) {
                player.on('enterfullscreen', () => {
                    if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
                        if (screen.orientation && typeof screen.orientation.lock === 'function') {
                            screen.orientation.lock('landscape').catch(err => {
                                console.log("A orientação da paisagem não é suportada, mas está tudo bem.");
                            });
                        }
                    }
                });

                player.on('exitfullscreen', () => {
                    if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
                        if (screen.orientation && typeof screen.orientation.unlock === 'function') {
                            screen.orientation.unlock();
                        }
                    }
                });
            }

            if (Hls.isSupported() && (source.endsWith('.m3u8') || source.endsWith('.txt'))) {
                const hlsConfig = {
                    maxBufferLength: 300,
                    maxMaxBufferLength: 600,
                    maxBufferSize: 300 * 1000 * 1000,
                    maxBufferHole: 1,
                    startFragPrefetch: true
                };
                const hls = new Hls(hlsConfig);
                hls.loadSource(source);
                hls.attachMedia(video);

                hls.on(Hls.Events.MANIFEST_PARSED, function (event, data) {
                    const availableQualities = hls.levels.map((l) => l.height);
                    const qualityOptions = [-1, ...availableQualities];

                    defaultOptions.quality = {
                        default: -1, // Default to Auto
                        options: qualityOptions,
                        forced: true,
                        onChange: (quality) => {
                            if (quality === -1) {
                                hls.currentLevel = -1; // Enable auto level switching
                            } else {
                                hls.currentLevel = hls.levels.findIndex(l => l.height === quality);
                            }
                        },
                    };

                    defaultOptions.i18n = {
                        qualityLabel: { '-1': 'Auto' }
                    };

                    var portugueseTrackFound = false;
                    var audioOptions = [];
                    for (var i = 0; i < hls.audioTracks.length; i++) {
                        var lang = hls.audioTracks[i].lang.toLowerCase();
                        var name = hls.audioTracks[i].name.toLowerCase();
                        audioOptions.push({index: i, name: hls.audioTracks[i].name});
                        if (lang.startsWith('pt') || lang.includes('portuguese') || name.includes('português') || name.includes('portuguese') || name.includes('por') || name.includes('brazilian')) {
                            hls.audioTrack = i;
                            portugueseTrackFound = true;
                        }
                    }

                    // Adiciona controle manual de troca de áudio na engrenagem
                    defaultOptions.controls = ['play-large', 'rewind', 'play', 'fast-forward', 'progress', 'current-time', 'mute', 'volume', 'captions', 'settings', 'pip', 'airplay', 'fullscreen'];
                    defaultOptions.settings = ['captions', 'quality', 'speed', 'loop', 'audio'];

                    defaultOptions.i18n = {
                        qualityLabel: { '-1': 'Auto' },
                        audioLabel: 'Áudio'
                    };

                    const player = new Plyr(video, defaultOptions);
                    addDownloadButton(player, source);
                    setupPlayerWithResume(player, source);
                    setupFullscreenRotation(player);

                    // Cria menu de seleção de áudio
                    player.on('ready', () => {
                        const settingsMenu = player.elements.settings;
                        if (settingsMenu) {
                            const audioSetting = document.createElement('div');
                            audioSetting.className = 'plyr__menu__item';
                            audioSetting.innerHTML = '<div class="plyr__menu__title">Áudio</div>';
                            const audioList = document.createElement('div');
                            audioList.className = 'plyr__menu__list';

                            audioOptions.forEach(option => {
                                const button = document.createElement('button');
                                button.className = 'plyr__control';
                                button.textContent = option.name;
                                button.onclick = () => {
                                    hls.audioTrack = option.index;
                                    player.toggleMenu(false);
                                };
                                audioList.appendChild(button);
                            });

                            audioSetting.appendChild(audioList);
                            settingsMenu.appendChild(audioSetting);
                        }
                    });

                    if (!portugueseTrackFound && hls.audioTracks.length > 0) {
                        hls.audioTrack = 0;
                    }
                });
                window.hls = hls;
            } else {
                // Define o tipo MIME para vídeos .mkv para garantir compatibilidade
                if (source.endsWith('.mkv')) {
                    video.type = 'video/x-matroska';
                }

                video.src = source;

                // Como não há suporte a múltiplas faixas de áudio para vídeos simples, apenas inicializa o player normalmente
                const player = new Plyr(video, defaultOptions);
                addDownloadButton(player, source);
                setupPlayerWithResume(player, source);
                setupFullscreenRotation(player);
            }


        });
    </script>
</body>
</html>
