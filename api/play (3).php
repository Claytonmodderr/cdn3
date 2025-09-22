<?php 
$canal = $_GET['canal'];
$canais = include('listacanais.php');
if (isset($canais[$canal]) == true){
?>
<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	  
    <!-- Bloquear indexação -->
    <meta name="robots" content="noindex, nofollow, noarchive">
    <meta name="googlebot" content="noindex, nofollow, noarchive">
    <meta name="bingbot" content="noindex, nofollow, noarchive">
    <meta name="referrer" content="no-referrer">

    <title>Player</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet">

    <style type="text/css">
      html, body, .main, iframe {
        margin: 0; padding: 0; width: 100%; height: 100%;
        border: none; overflow: hidden;
      }
      .meu-botao {
        font-size: 14px; font-weight: bold; padding: 5px 10px;
        background: #000; color: #fff; border: none; border-radius: 10px;
        position: absolute; top: 10px; left: 50%; transform: translateX(-50%);
        z-index: 10; cursor: pointer;
      }
    </style>

    <script disable-devtool-auto src="https://cdn.jsdelivr.net/npm/disable-devtool@latest"></script>
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

      function devtoolIsOpening() {
          console.clear();
          let before = new Date().getTime();
          debugger;
          let after = new Date().getTime();
          if (after - before > 200) {
              document.write("Dont open Developer Tools.");
              window.location.replace("https://www.google.com");
          }
          setTimeout(devtoolIsOpening, 100);
      }
      devtoolIsOpening();
    </script>
  </head>
  <body>
    <button id="btnTravou" class="meu-botao">Travou? Clique aqui</button>

    <div class="main">
      <iframe id="playerFrame" src="<?php echo $canais[$canal]; ?>" width="100%" height="100%" 
        allow="encrypted-media" scrolling="no" frameborder="0" allowfullscreen></iframe>
    </div>

    <script>
      // Agora o script vem depois do HTML, garantindo que o botão existe
      document.getElementById("btnTravou").addEventListener("click", function() {
        let iframe = document.getElementById("playerFrame");
        iframe.src = iframe.src; // Recarrega o iframe
      });
    </script>
  </body>
</html>

<?php
}else{
  echo "Este canal não existe";
}
?>
