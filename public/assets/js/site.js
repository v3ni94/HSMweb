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
      // Sicherheitsnetz: nach 800 ms alles sichtbar
      setTimeout(function () { reveals.forEach(function (el) { el.classList.add('is-visible'); }); }, 800);
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

  // Projektfinder als Tabs: Klick, Pfeiltasten, Pos1 und Ende wählen ein Thema, roving tabindex erst ab der ersten Auswahl
  var finder = document.querySelector('[data-projectfinder]');
  if (finder) {
    var chips = Array.prototype.slice.call(finder.querySelectorAll('[data-finder]'));
    var panels = finder.querySelectorAll('[data-finder-panel]');
    var selectChip = function (chip, focusChip) {
      var key = chip.getAttribute('data-finder');
      chips.forEach(function (c) {
        var on = c === chip;
        c.setAttribute('aria-selected', on ? 'true' : 'false');
        c.setAttribute('tabindex', on ? '0' : '-1');
      });
      panels.forEach(function (p) { p.hidden = p.getAttribute('data-finder-panel') !== key; });
      var ph = finder.querySelector('[data-finder-placeholder]'); if (ph) ph.hidden = true;
      if (focusChip) { chip.focus(); return; }
      var active = finder.querySelector('[data-finder-panel="' + key + '"]');
      if (active) { active.setAttribute('tabindex', '-1'); active.focus({ preventScroll: true }); }
    };
    chips.forEach(function (chip, i) {
      chip.addEventListener('click', function () { selectChip(chip, false); });
      chip.addEventListener('keydown', function (e) {
        var n = chips.length; var target = null;
        if (e.key === 'ArrowRight' || e.key === 'ArrowDown') target = chips[(i + 1) % n];
        else if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') target = chips[(i - 1 + n) % n];
        else if (e.key === 'Home') target = chips[0];
        else if (e.key === 'End') target = chips[n - 1];
        if (target) { e.preventDefault(); selectChip(target, true); }
      });
    });
  }

  // Stellenfilter: Karten ausblenden, Anzahl sichtbarer Stellen aus dem DOM schreiben
  var jf = document.querySelector('[data-jobfilter]');
  if (jf) {
    var fchips = jf.querySelectorAll('[data-filter]');
    var cards = jf.querySelectorAll('[data-group]');
    var counter = jf.querySelector('[data-jobcount]');
    var animate = document.documentElement.classList.contains('js') && !reduceMotion;
    var updateCount = function () {
      if (!counter) return;
      var n = Array.prototype.filter.call(cards, function (c) { return !c.hidden; }).length;
      counter.textContent = n === 1 ? '1 Stelle' : n + ' Stellen';
      counter.hidden = false;
    };
    var hideCard = function (c) {
      if (c.hidden) return;
      if (animate) { c.classList.add('is-filtered-out'); setTimeout(function () { c.hidden = true; }, 200); }
      else { c.hidden = true; }
    };
    var showCard = function (c) {
      c.hidden = false;
      if (animate) { requestAnimationFrame(function () { c.classList.remove('is-filtered-out'); }); }
      else { c.classList.remove('is-filtered-out'); }
    };
    fchips.forEach(function (chip) {
      chip.addEventListener('click', function () {
        var key = chip.getAttribute('data-filter');
        fchips.forEach(function (c) { c.setAttribute('aria-pressed', c === chip ? 'true' : 'false'); });
        cards.forEach(function (c) { if (key !== 'alle' && c.getAttribute('data-group') !== key) hideCard(c); else showCard(c); });
        // Zähler aus dem Zielzustand, nicht aus dem laufenden Übergang
        var n = Array.prototype.filter.call(cards, function (c) { return key === 'alle' || c.getAttribute('data-group') === key; }).length;
        if (counter) { counter.textContent = n === 1 ? '1 Stelle' : n + ' Stellen'; counter.hidden = false; }
      });
    });
    updateCount();
  }

  // Lesefortschritt auf Ratgeber- und Rechtstexten: Balken skaliert mit der Scrollposition (CSSOM, kein Inline-Stilattribut)
  var progressBar = document.querySelector('[data-read-progress]');
  if (progressBar) {
    progressBar.hidden = false;
    var ticking = false;
    var paint = function () {
      var max = document.documentElement.scrollHeight - window.innerHeight;
      var ratio = max > 0 ? Math.min(1, Math.max(0, window.scrollY / max)) : 0;
      progressBar.style.transform = 'scaleX(' + ratio + ')';
      ticking = false;
    };
    window.addEventListener('scroll', function () { if (!ticking) { ticking = true; requestAnimationFrame(paint); } }, { passive: true });
    paint();
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
