// Étoiles interactives
(function(){
  const stars = Array.from(document.querySelectorAll('.star'));
  const ratingInput = document.getElementById('rating');

  // valeur initiale
  let current = parseInt(ratingInput.value || '0', 10);
  paint(current);

  // souris : clic pour fixer la note, survol pour prévisualiser
  stars.forEach(star => {
    const value = parseInt(star.dataset.value, 10);

    star.addEventListener('mouseenter', () => paint(value));
    star.addEventListener('mouseleave', () => paint(current));
    star.addEventListener('click', () => {
      current = value;
      ratingInput.value = String(current);
      paint(current);
      updateAria(current);
    });

    // clavier (accessibilité) : gauche/droite et espace/entrée
    star.addEventListener('keydown', (e) => {
      if(['ArrowLeft','ArrowDown'].includes(e.key)){
        e.preventDefault();
        current = Math.max(1, current-1);
      } else if(['ArrowRight','ArrowUp'].includes(e.key)){
        e.preventDefault();
        current = Math.min(5, current+1);
      } else if([' ','Enter'].includes(e.key)){
        e.preventDefault();
      } else {
        return; // ignorer
      }
      ratingInput.value = String(current);
      paint(current);
      updateAria(current);
      stars[current-1].focus();
    });

    // rendre focusable
    star.setAttribute('tabindex','0');
    star.setAttribute('role','radio');
  });

  function paint(val){
    stars.forEach((s, i) => {
      if(i < val){ s.classList.add('filled'); } else { s.classList.remove('filled'); }
    });
  }
  function updateAria(val){
    stars.forEach((s, i) => s.setAttribute('aria-checked', i < val ? 'true' : 'false'));
  }

  // exemple d'envoi (ici, simple console.log)
  const sendBtn = document.getElementById('sendBtn');
  if(sendBtn){
    sendBtn.addEventListener('click', () => {
      console.log('Note envoyée :', ratingInput.value, 'Avis :', document.getElementById('avis').value);
      alert('Merci ! Note : ' + ratingInput.value);
    });
  }
})();
