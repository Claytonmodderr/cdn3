<?php
// ======================
// CONFIGURAÇÃO DO PLAYER
// ======================
$domain = "https://cdn3-two.vercel.app/api/"; 
$apiUrl = $domain . "/canais.php?list";

// tenta carregar o JSON
$response = @file_get_contents($apiUrl);
if($response === false){
    die("Erro ao carregar lista de canais.");
}

$json = json_decode($response, true);
if(!is_array($json)){
    die("Erro: resposta inválida da API.");
}

// pega o parâmetro 'v'
$channelId = isset($_GET['v']) ? intval($_GET['v']) : 0;
if(!isset($json[$channelId])){
    die("Canal não encontrado.");
}

// URL do stream
$streamUrl = $json[$channelId]["urlHLSChromecast"] ?? null;
if(!$streamUrl){
    die("Stream indisponível para este canal.");
}

// URL via proxy
$proxyUrl = "https://canaisnatv.me/proxy.php?url=" . urlencode($streamUrl);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Live Player — Custom HLS</title>

<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

<style>
  :root{
    --bg:#06080b;
    --panel: rgba(0,0,0,0.6);
    --accent:#06b6d4;
    --danger:#ff5252;
    --muted:#9aa7b2;
  }
  *{box-sizing:border-box}
 
  .player {
    width:100%;
    max-width:980px;
    background:#000;
    border-radius:12px;
    overflow:hidden;
    position:relative;
  }
  video{ width:100%; height:415px; display:block; background:black; }

  .meta-top { position:absolute; top:10px; left:12px; display:flex; gap:10px; align-items:center; z-index:30; }
  .live-badge{ background:var(--danger); color:white; font-weight:700; padding:6px 8px; border-radius:6px; font-size:13px; }
  .latency { background: rgba(255,255,255,0.04); color:var(--muted); padding:6px 8px; border-radius:6px; font-size:13px; }

  .center-play{ position:absolute; inset:0; display:grid; place-items:center; z-index:25; pointer-events:none; }
  .center-play button{ pointer-events:auto; border:0; background:rgba(255,255,255,0.08); padding:16px; border-radius:50%; font-size:40px; color:#fff; cursor:pointer; }
  .controls{ position:absolute; left:0; right:0; bottom:0; padding:12px; background: linear-gradient(0deg, rgba(0,0,0,0.72), transparent 40%); display:flex; flex-direction:column; gap:10px; z-index:30; }

  .progress-wrap{display:flex;align-items:center;gap:10px}
  .time { font-size:13px; color:var(--muted); min-width:56px; text-align:center; }
  .progress{ flex:1; height:8px; background:rgba(255,255,255,0.06); border-radius:999px; position:relative; overflow:hidden; }
  .progress .played{ position:absolute; left:0; top:0; height:100%; background:linear-gradient(90deg,var(--accent),#8b5cf6); width:0 }

  .row{ display:flex; align-items:center; gap:10px }
  .btn{ background:none; border:0; color:white; cursor:pointer; padding:8px; border-radius:8px; font-size:20px; display:inline-flex; align-items:center; justify-content:center; }
  .group-right{ margin-left:auto; display:flex; gap:6px; align-items:center }
  .volume{ display:flex; align-items:center; gap:8px; }
  .vol-slider{ width:120px; height:6px; background:rgba(255,255,255,0.06); border-radius:6px; position:relative; cursor:pointer; }
  .vol-slider .level{ position:absolute; left:0; top:0; bottom:0; width:50%; background:var(--accent); border-radius:6px; }

  .message{ position:absolute; left:50%; top:50%; transform:translate(-50%,-50%); background:rgba(0,0,0,0.6); padding:10px 14px; border-radius:8px; color:var(--muted); z-index:40; font-size:14px; display:none; }
</style>
</head>
<body>

<div class="player" id="player">
  <video id="video" playsinline webkit-playsinline muted></video>

  <div class="meta-top">
    <div class="live-badge">LIVE</div>
    <div class="latency" id="latency">-- ms</div>
  </div>

  <div class="center-play"><button id="centerPlayBtn"><span class="material-icons" id="centerIcon">play_arrow</span></button></div>

  <div class="controls">
    <div class="progress-wrap">
      <div class="time">--:--</div>
      <div class="progress"><div class="played" id="played"></div></div>
      <div class="time">LIVE</div>
    </div>
    <div class="row">
      <button class="btn" id="playBtn"><span class="material-icons" id="playIcon">play_arrow</span></button>
      <div class="volume">
        <button class="btn" id="muteBtn"><span class="material-icons" id="volIcon">volume_up</span></button>
        <div class="vol-slider" id="volSlider"><div class="level" id="volLevel"></div></div>
      </div>
      <div class="group-right">
        <button class="btn" id="fullscreenBtn"><span class="material-icons" id="fsIcon">fullscreen</span></button>
      </div>
    </div>
  </div>

  <div class="message" id="message"></div>
</div>

<script src="https://cdn.jsdelivr.net/npm/hls.js@1.5.2/dist/hls.min.js"></script>
<script>
(function(){
  const STREAM_URL = "<?php echo $proxyUrl; ?>";
  const video = document.getElementById('video');
  const playBtn = document.getElementById('playBtn');
  const playIcon = document.getElementById('playIcon');
  const centerPlayBtn = document.getElementById('centerPlayBtn');
  const centerIcon = document.getElementById('centerIcon');
  const muteBtn = document.getElementById('muteBtn');
  const volIcon = document.getElementById('volIcon');
  const volSlider = document.getElementById('volSlider');
  const volLevel = document.getElementById('volLevel');
  const fsBtn = document.getElementById('fullscreenBtn');
  const fsIcon = document.getElementById('fsIcon');
  const message = document.getElementById('message');

  if(Hls.isSupported()){
    const hls = new Hls();
    hls.loadSource(STREAM_URL);
    hls.attachMedia(video);
  } else if(video.canPlayType('application/vnd.apple.mpegurl')){
    video.src = STREAM_URL;
  } else {
    message.style.display = 'block';
    message.textContent = 'Seu navegador não suporta HLS.';
  }

  function togglePlay(){
    if(video.paused){ video.play(); } else { video.pause(); }
  }
  video.addEventListener('play', ()=>{ playIcon.textContent="pause"; centerIcon.textContent="pause"; });
  video.addEventListener('pause', ()=>{ playIcon.textContent="play_arrow"; centerIcon.textContent="play_arrow"; });

  playBtn.addEventListener('click', togglePlay);
  centerPlayBtn.addEventListener('click', togglePlay);

  muteBtn.addEventListener('click', ()=>{
    video.muted = !video.muted;
    volIcon.textContent = video.muted ? "volume_off" : "volume_up";
    volLevel.style.width = video.muted ? "0%" : (video.volume*100)+"%";
  });

  volSlider.addEventListener('click', (e)=>{
    const rect = volSlider.getBoundingClientRect();
    const pct = (e.clientX-rect.left)/rect.width;
    video.volume = Math.max(0, Math.min(1, pct));
    video.muted = false;
    volLevel.style.width = (video.volume*100)+"%";
    volIcon.textContent = video.volume===0 ? "volume_off" : "volume_up";
  });

  fsBtn.addEventListener('click', ()=>{
    if(!document.fullscreenElement){ video.parentElement.requestFullscreen(); fsIcon.textContent="fullscreen_exit"; }
    else{ document.exitFullscreen(); fsIcon.textContent="fullscreen"; }
  });
})();
</script>
</body>
</html>
