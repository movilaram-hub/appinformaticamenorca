(() => {
  const root = document.documentElement;
  const base = (() => {
    const css = document.querySelector('link[href*="assets/css/app.css"]');
    if (!css) return '';
    const href = css.getAttribute('href').split('/assets/')[0];
    return href === '' ? '' : href;
  })();

  // Estado de la tienda usando siempre la zona horaria de Menorca/Madrid.
  const status = document.getElementById('store-status');
  if (status) {
    const parts = new Intl.DateTimeFormat('en-GB', {timeZone:'Europe/Madrid',weekday:'short',hour:'2-digit',minute:'2-digit',hourCycle:'h23'}).formatToParts(new Date());
    const map = Object.fromEntries(parts.map(p => [p.type,p.value]));
    const dayMap = {Mon:1,Tue:2,Wed:3,Thu:4,Fri:5,Sat:6,Sun:0};
    const day = dayMap[map.weekday];
    const mins = Number(map.hour)*60 + Number(map.minute);
    const morningOpen=9*60, morningClose=14*60, afternoonOpen=16*60, afternoonClose=19*60;
    let text = status.dataset.closed;
    if (day >= 1 && day <= 5) {
      if (mins >= morningOpen && mins < morningClose) text = `${status.dataset.open} · ${status.dataset.until} 14:00`;
      else if (mins >= morningClose && mins < afternoonOpen) text = `${status.dataset.closed} · ${status.dataset.openat} 16:00`;
      else if (mins >= afternoonOpen && mins < afternoonClose) text = `${status.dataset.open} · ${status.dataset.until} 19:00`;
      else if (mins < morningOpen) text = `${status.dataset.closed} · ${status.dataset.openat} 09:00`;
      else if (day < 5) text = `${status.dataset.closed} · ${status.dataset.tomorrow} 09:00`;
      else text = `${status.dataset.closed} · ${status.dataset.monday} 09:00`;
    } else if (day === 0) text = `${status.dataset.closed} · ${status.dataset.monday} 09:00`;
    else text = status.dataset.closed;
    status.textContent = text;
  }

  // Selector de idioma: conserva la página actual.
  const locale = document.body.dataset.locale || 'es';
  const page = document.body.dataset.page || 'home';
  const route = page === 'home' ? '' : page + '/';
  document.querySelectorAll('[data-lang-link]').forEach(a => {
    const lang = a.dataset.langLink;
    a.href = `${base}/${lang}/${route}`.replace(/\/+/g,'/');
  });

  // Pequeña ayuda del selector solo una vez por sesión.
  const hint = document.getElementById('language-hint');
  if (hint && !sessionStorage.getItem('app-informatica-language-hint-seen')) {
    setTimeout(() => { hint.hidden = false; }, 700);
    const hide = () => { hint.hidden = true; sessionStorage.setItem('app-informatica-language-hint-seen','1'); };
    hint.querySelector('button')?.addEventListener('click', hide);
    setTimeout(hide, 6500);
  }

  // Microbarra para navegación interna.
  const progress = document.getElementById('nav-progress');
  document.addEventListener('click', e => {
    const a = e.target.closest('a');
    if (!a || !progress) return;
    if (a.target === '_blank' || e.ctrlKey || e.metaKey || e.shiftKey || e.altKey) return;
    const href = a.getAttribute('href') || '';
    if (!href || href.startsWith('#') || href.startsWith('mailto:') || href.startsWith('tel:')) return;
    try {
      const u = new URL(a.href, location.href);
      if (u.origin !== location.origin) return;
      progress.classList.add('active');
    } catch(_) {}
  });
})();
