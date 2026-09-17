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

  // Projektfinder
  var finder = document.querySelector('[data-projectfinder]');
  if (finder) {
    var chips = finder.querySelectorAll('[data-finder]');
    var panels = finder.querySelectorAll('[data-finder-panel]');
    chips.forEach(function (chip) {
      chip.addEventListener('click', function () {
        var key = chip.getAttribute('data-finder');
        chips.forEach(function (c) { c.setAttribute('aria-pressed', c === chip ? 'true' : 'false'); });
        panels.forEach(function (p) { p.hidden = p.getAttribute('data-finder-panel') !== key; });
        var active = finder.querySelector('[data-finder-panel="' + key + '"]');
        if (active) { active.setAttribute('tabindex', '-1'); active.focus({ preventScroll: false }); }
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
