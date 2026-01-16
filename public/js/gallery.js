(function(){
  const main = document.getElementById('mainMedia');
  if(!main) return;

  const thumbs = document.querySelectorAll('.thumb[data-src]');
  if(!thumbs.length) return;

  function setActive(btn){
    thumbs.forEach(b => b.classList.remove('is-active'));
    btn.classList.add('is-active');
  }

  thumbs.forEach(btn => {
    btn.addEventListener('click', () => {
      main.src = btn.dataset.src;
      setActive(btn);
    });
  });

  // active par défaut = première
  setActive(thumbs[0]);
})();