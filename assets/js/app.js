// assets/js/app.js
// Toggle de tema, toasts y validación simple.

(function() {
  // Tema
  const themeBtn = document.querySelector('[data-theme-toggle]');
  const html = document.documentElement;
  const saved = localStorage.getItem('theme');
  if (saved) html.classList.toggle('light', saved === 'light');

  function setTheme(next) {
    html.classList.toggle('light', next === 'light');
    localStorage.setItem('theme', next);
  }

  if (themeBtn) {
    themeBtn.addEventListener('click', () => {
      const next = html.classList.contains('light') ? 'dark' : 'light';
      setTheme(next);
      showToast(`Modo ${next === 'light' ? 'claro' : 'oscuro'} activado`, 'info');
    });
  }

  // Toast
  window.showToast = function(message, type='info') {
    const el = document.getElementById('toast');
    if (!el) return;
    el.innerHTML = `<strong>${type.toUpperCase()}</strong> · ${message}`;
    el.className = `toast show`;
    setTimeout(() => { el.className = 'toast'; }, 3000);
  };

  // Mostrar toasts por query params ?msg=...&type=...
  const params = new URLSearchParams(location.search);
  if (params.get('msg')) {
    const t = params.get('type') || 'info';
    const m = params.get('msg');
    showToast(decodeURIComponent(m), t);
    // limpiar params
    const url = new URL(location.href);
    url.searchParams.delete('msg'); url.searchParams.delete('type');
    history.replaceState({}, '', url);
  }

  // Validación simple para formularios con data-validate
  document.querySelectorAll('form[data-validate]').forEach(form => {
    form.addEventListener('submit', (e) => {
      let ok = true;
      form.querySelectorAll('[required]').forEach(input => {
        if (!input.value.trim()) { ok = false; input.classList.add('invalid'); }
        else { input.classList.remove('invalid'); }
      });
      if (!ok) {
        e.preventDefault();
        showToast('Por favor, completa los campos obligatorios.', 'warning');
      }
    });
  });

})();
