let deferredPrompt;

window.addEventListener('beforeinstallprompt', (e) => {
  // Prevent the mini-infobar from appearing on mobile
  e.preventDefault();
  // Stash the event so it can be triggered later
  deferredPrompt = e;
  
  // Show the install button
  const installButton = document.getElementById('installButton');
  if (installButton) {
    installButton.style.display = 'block';
  }
});

document.getElementById('installButton').addEventListener('click', async () => {
  if (deferredPrompt) {
    // Show the install prompt
    deferredPrompt.prompt();
    
    // Wait for the user to respond to the prompt
    const { outcome } = await deferredPrompt.userChoice;
    
    if (outcome === 'accepted') {
      console.log('User accepted the install prompt');
    } else {
      console.log('User dismissed the install prompt');
    }
    
    // Reset the deferred prompt
    deferredPrompt = null;
    
    // Hide the install button
    const installButton = document.getElementById('installButton');
    if (installButton) {
      installButton.style.display = 'none';
    }
  }
});

// Handle installation for iOS devices
function isIOS() {
  return /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
}

function isStandalone() {
  return ('standalone' in navigator) && (navigator.standalone);
}

function showIOSInstallPrompt() {
  if (isIOS() && !isStandalone()) {
    const prompt = document.createElement('div');
    prompt.className = 'ios-install-prompt';
    prompt.innerHTML = `
      <div class="ios-prompt-content">
        <i class="fas fa-mobile-alt"></i>
        <h3>Adicione à Tela Inicial</h3>
        <p>Toque no botão de compartilhamento e selecione "Adicionar à Tela de Início" para uma experiência de app nativo.</p>
        <button class="close-ios-prompt">Entendi</button>
      </div>
    `;
    
    document.body.appendChild(prompt);
    
    prompt.querySelector('.close-ios-prompt').addEventListener('click', () => {
      prompt.remove();
    });
  }
}

// Modify the existing CSS to add styles for iOS install prompt
const iOSPromptStyles = `
.ios-install-prompt {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.8);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 20px;
  text-align: center;
}

.ios-prompt-content {
  background: #2a2a2a;
  border-radius: 15px;
  padding: 30px;
  max-width: 350px;
  color: #fff;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

.ios-prompt-content i {
  font-size: 50px;
  color: #375666;
  margin-bottom: 20px;
}

.ios-prompt-content h3 {
  margin-bottom: 15px;
  font-size: 20px;
}

.ios-prompt-content p {
  margin-bottom: 20px;
  color: #a0a3a7;
  line-height: 1.5;
}

.close-ios-prompt {
  background: #375666;
  color: #fff;
  border: none;
  padding: 12px 24px;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.close-ios-prompt:hover {
  background: #2a4251;
}
`;

const styleElement = document.createElement('style');
styleElement.textContent = iOSPromptStyles;
document.head.appendChild(styleElement);

// Add event listener to install button for iOS
document.getElementById('installButton').addEventListener('click', () => {
  if (isIOS()) {
    showIOSInstallPrompt();
  }
});