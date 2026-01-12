document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('bouton-dalto');
    const body = document.body;

    // Vérifie si le mode était déjà activé auparavant
    if (localStorage.getItem('mode-access') === 'active') {
        body.classList.add('dalto-mode');
        if (btn) btn.innerText = "Mode Classique";
    }

    // Gestion du clic sur le bouton
    if (btn) {
        btn.addEventListener('click', () => {
            body.classList.toggle('dalto-mode');
            
            if (body.classList.contains('dalto-mode')) {
                btn.innerText = "Mode Classique";
                localStorage.setItem('mode-access', 'active');
            } else {
                btn.innerText = "Mode Accessibilité";
                localStorage.setItem('mode-access', 'inactive');
            }
        });
    }
});