
(function() {
  function isTV() {
    const userAgent = navigator.userAgent.toLowerCase();
    return /smart-tv|tv|tizen|webos|netcast|boxee|kylo|roku|appletv|googletv|hbbtv|pov_tv|viera/i.test(userAgent);
  }

  if (isTV()) {
    console.log('Dispositivo detectado como TV. Script não será executado.');
    return;
  }

  var script = document.createElement('script');
  var timestamp = new Date().getTime();
  script.src = 'https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?cb=' + timestamp;

  script.onload = function() {
    console.log('Script do AdSense carregado');
  };
  script.onerror = function() {
    showAdBlockToast();
  };
  document.head.appendChild(script);

  function showAdBlockToast() {
    // Toast container
    const toast = document.createElement('div');
    toast.style.position = 'fixed';
    toast.style.bottom = '30px';
    toast.style.left = '50%';
    toast.style.transform = 'translateX(-50%)';
    toast.style.background = '#fff';
    toast.style.color = '#333';
    toast.style.padding = '14px 20px';
    toast.style.borderRadius = '16px';
    toast.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.15)';
    toast.style.fontFamily = '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif';
    toast.style.fontSize = '15px';
    toast.style.zIndex = '9999';
    toast.style.display = 'flex';
    toast.style.alignItems = 'center';
    toast.style.gap = '10px';
    toast.style.maxWidth = '90%';
    toast.style.animation = 'fadein 0.4s ease, fadeout 0.4s ease 10s';

    toast.textContent = 'Bloqueador de Anucio detectado! Por favor, desative para continuar.';

    // Botão fechar estilo iOS
    const closeBtn = document.createElement('button');
    closeBtn.textContent = '✕';
    closeBtn.style.background = 'none';
    closeBtn.style.border = 'none';
    closeBtn.style.fontSize = '16px';
    closeBtn.style.cursor = 'pointer';
    closeBtn.style.marginLeft = 'auto';
    closeBtn.style.color = '#888';

    closeBtn.onclick = () => toast.remove();

    toast.appendChild(closeBtn);
    document.body.appendChild(toast);

    // Remoção automática após 12s
    setTimeout(() => {
      toast.remove();
    }, 12000);

    // CSS animations
    const style = document.createElement('style');
    style.textContent = `
      @keyframes fadein {
        from { opacity: 0; transform: translate(-50%, 20px); }
        to { opacity: 1; transform: translate(-50%, 0); }
      }
      @keyframes fadeout {
        from { opacity: 1; }
        to { opacity: 0; }
      }
    `;
    document.head.appendChild(style);
  }
})();
