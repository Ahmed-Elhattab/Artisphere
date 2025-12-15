// Minimal client-side validation (keeps UX friendly; server-side handling can be added later)
document.addEventListener('DOMContentLoaded', () => {
  const form = document.querySelector('.contact-form');
  if (!form) return;

  form.addEventListener('submit', (e) => {
    const required = form.querySelectorAll('[required]');
    let ok = true;

    required.forEach((field) => {
      if (!field.value || !field.value.trim()) {
        ok = false;
        field.setAttribute('aria-invalid', 'true');
      } else {
        field.removeAttribute('aria-invalid');
      }
    });

    if (!ok) {
      e.preventDefault();
      const first = form.querySelector('[aria-invalid="true"]');
      if (first) first.focus();
    }
  });
});
