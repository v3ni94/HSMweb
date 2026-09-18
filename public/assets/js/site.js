/* HSM Tec: Bedienungsverbesserungen. Inhalte funktionieren ohne dieses Skript. */
(function () {
  'use strict';

  // Navigation
  var toggle = document.querySelector('[data-nav-toggle]');
  var nav = document.querySelector('[data-nav]');
  if (toggle && nav) {
    toggle.addEventListener('click', function () {
      var open = nav.classList.toggle('is-open');
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && nav.classList.contains('is-open')) {
        nav.classList.remove('is-open');
        toggle.setAttribute('aria-expanded', 'false');
        toggle.focus();
      }
    });
  }

  // Mobile-Leiste ausblenden, wenn ein Formularfeld fokussiert ist (verdeckt sonst Eingaben)
  var bar = document.querySelector('[data-mobile-bar]');
  if (bar) {
    document.addEventListener('focusin', function (e) {
      if (e.target.closest && e.target.closest('form')) bar.classList.add('is-hidden');
    });
    document.addEventListener('focusout', function (e) {
      if (!e.relatedTarget || !e.relatedTarget.closest || !e.relatedTarget.closest('form')) bar.classList.remove('is-hidden');
    });
  }

  var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  // Header: Schatten nach Scroll
  var header = document.querySelector('[data-header]');
  if (header) {
    var onScroll = function () { header.classList.toggle('is-scrolled', window.scrollY > 8); };
    onScroll(); window.addEventListener('scroll', onScroll, { passive: true });
  }

  // Sanftes Einblenden beim Scrollen (ohne IntersectionObserver oder bei reduzierter Bewegung sofort sichtbar)
  var reveals = document.querySelectorAll('[data-reveal]');
  if (reveals.length) {
    if (!('IntersectionObserver' in window) || reduceMotion) {
      reveals.forEach(function (el) { el.classList.add('is-visible'); });
    } else {
      // Elemente im sichtbaren Bereich sofort markieren, dann erst die js-Klasse setzen: kein Flackern über der Falz
      var vh = window.innerHeight || document.documentElement.clientHeight;
      reveals.forEach(function (el) { var r = el.getBoundingClientRect(); if (r.top < vh && r.bottom > 0) el.classList.add('is-visible'); });
      document.documentElement.classList.add('js');
      var io = new IntersectionObserver(function (entries) {
        entries.forEach(function (en) { if (en.isIntersecting) { en.target.classList.add('is-visible'); io.unobserve(en.target); } });
      }, { rootMargin: '0px 0px -8% 0px', threshold: 0.05 });
      reveals.forEach(function (el) { io.observe(el); });
      // Sicherheitsnetz: nach 2 s alles sichtbar
      setTimeout(function () { reveals.forEach(function (el) { el.classList.add('is-visible'); }); }, 2000);
    }
  }

  // Akkordeon: animiertes Öffnen/Schließen
  document.querySelectorAll('[data-accordion] details').forEach(function (d) {
    var summary = d.querySelector('summary'); var panel = d.querySelector('.accordion-panel');
    if (!summary || !panel || reduceMotion || !panel.animate) return;
    summary.addEventListener('click', function (e) {
      e.preventDefault();
      if (d.classList.contains('is-animating')) return;
      d.classList.add('is-animating');
      if (d.open) {
        var h = panel.offsetHeight;
        var a = panel.animate([{ height: h + 'px', opacity: 1 }, { height: '0px', opacity: 0 }], { duration: 220, easing: 'ease-out' });
        a.onfinish = function () { d.open = false; d.classList.remove('is-animating'); panel.style.height = ''; };
      } else {
        d.open = true;
        var h2 = panel.offsetHeight;
        var a2 = panel.animate([{ height: '0px', opacity: 0 }, { height: h2 + 'px', opacity: 1 }], { duration: 260, easing: 'ease-out' });
        a2.onfinish = function () { d.classList.remove('is-animating'); };
      }
    });
  });

  // Projektfinder
  var finder = document.querySelector('[data-projectfinder]');
  if (finder) {
    var chips = finder.querySelectorAll('[data-finder]');
    var panels = finder.querySelectorAll('[data-finder-panel]');
    chips.forEach(function (chip) {
      chip.addEventListener('click', function () {
        var key = chip.getAttribute('data-finder');
        chips.forEach(function (c) { c.setAttribute('aria-pressed', c === chip ? 'true' : 'false'); });
        chips.forEach(function (c) { c.setAttribute('aria-selected', c === chip ? 'true' : 'false'); });
        panels.forEach(function (p) { p.hidden = p.getAttribute('data-finder-panel') !== key; });
        var ph = finder.querySelector('[data-finder-placeholder]'); if (ph) ph.hidden = true;
        var active = finder.querySelector('[data-finder-panel="' + key + '"]');
        if (active) { active.setAttribute('tabindex', '-1'); active.focus({ preventScroll: true }); }
      });
    });
  }

  // Stellenfilter
  var jf = document.querySelector('[data-jobfilter]');
  if (jf) {
    var fchips = jf.querySelectorAll('[data-filter]');
    var cards = jf.querySelectorAll('[data-group]');
    fchips.forEach(function (chip) {
      chip.addEventListener('click', function () {
        var key = chip.getAttribute('data-filter');
        fchips.forEach(function (c) { c.setAttribute('aria-pressed', c === chip ? 'true' : 'false'); });
        cards.forEach(function (c) { c.hidden = key !== 'alle' && c.getAttribute('data-group') !== key; });
      });
    });
  }

  // Thema aus Query (?thema=) in Kontaktformular übernehmen, falls Server es nicht getan hat
  try {
    var params = new URLSearchParams(location.search);
    var thema = params.get('thema');
    if (thema) {
      document.querySelectorAll('form[data-form="contact"] select[name="topic"]').forEach(function (sel) {
        for (var i = 0; i < sel.options.length; i++) { if (sel.options[i].value === thema) { sel.selectedIndex = i; break; } }
      });
    }
  } catch (e) { /* ignorieren */ }

  // Fehlerbox fokussieren
  var errBox = document.querySelector('[data-form-errors]');
  if (errBox) { errBox.focus(); }

  // Formulare: CSRF-Token frisch holen (gegen gecachte Seiten), Doppelklick verhindern, Fetch-Versand mit Fallback
  document.querySelectorAll('form[data-form]').forEach(function (form) {
    var csrf = form.querySelector('[data-csrf]');
    if (csrf && window.fetch) {
      fetch('/csrf-token', { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' })
        .then(function (r) { return r.ok ? r.json() : null; })
        .then(function (d) { if (d && d.token) csrf.value = d.token; })
        .catch(function () { /* Server-Token bleibt */ });
    }
    var submitting = false;
    form.addEventListener('submit', function (e) {
      if (submitting) { e.preventDefault(); return; }
      // Native Validierung anzeigen, aber Server bleibt maßgeblich
      if (form.checkValidity && !form.checkValidity()) {
        e.preventDefault();
        var firstInvalid = form.querySelector(':invalid');
        if (firstInvalid) { firstInvalid.focus(); if (firstInvalid.reportValidity) firstInvalid.reportValidity(); }
        return;
      }
      submitting = true;
      var btn = form.querySelector('[data-submit]');
      if (btn) { btn.setAttribute('aria-busy', 'true'); btn.textContent = 'Wird gesendet …'; }
      // Normaler POST (Antwortseite mit Ergebnis/Fehlern, ohne JS-Abhängigkeit)
    });
  });
})();
