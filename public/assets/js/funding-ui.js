/* Fördercheck: Oberfläche. Lädt die öffentliche Regeldatei, rechnet lokal mit HsmFunding, speichert nichts. */
(function () {
  'use strict';
  var root = document.querySelector('[data-funding-calc]');
  if (!root || !window.HsmFunding) return;
  var form = root.querySelector('[data-calc-form]');
  var resultBox = root.querySelector('[data-calc-result]');
  var body = root.querySelector('[data-result-body]');
  var rules = null;

  function eur(cents) { return (cents / 100).toLocaleString('de-DE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' EUR'; }
  function esc(s) { var d = document.createElement('div'); d.textContent = String(s); return d.innerHTML; }

  fetch(root.getAttribute('data-rules-url'), { cache: 'no-cache' })
    .then(function (r) { if (!r.ok) throw new Error('rules'); return r.json(); })
    .then(function (json) {
      if (!json || !Array.isArray(json.programs)) throw new Error('rules');
      rules = json; root.hidden = false; updateVisibility();
    })
    .catch(function () {
      root.hidden = false;
      form.hidden = true;
      resultBox.hidden = false;
      body.innerHTML = '<p class="notice">Das Regelwerk konnte nicht geladen werden. Bitte nutzen Sie die Förderübersicht oder das Formular unten.</p>';
    });

  function updateVisibility() {
    var measure = form.elements.measure.value;
    var applicant = form.elements.applicant.value;
    root.querySelectorAll('[data-only]').forEach(function (fs) {
      var show = fs.getAttribute('data-only') === measure;
      if (fs.hasAttribute('data-selfuse-only')) show = show && applicant === 'privat-selbstnutzer';
      fs.hidden = !show;
      fs.disabled = !show;
    });
  }
  form.addEventListener('change', updateVisibility);
  form.addEventListener('reset', function () { setTimeout(function () { updateVisibility(); resultBox.hidden = true; body.innerHTML = ''; }, 0); });

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    if (!rules) return;
    var el = form.elements;
    var input = {
      measure: el.measure.value, state: el.state.value, building: el.building.value, units: el.units.value, applicant: el.applicant.value,
      existing: el.existing.checked, mixed: el.mixed.checked,
      oldSystem: el.oldSystem ? el.oldSystem.value : null, oldAge: el.oldAge ? el.oldAge.value : null, newSystem: el.newSystem ? el.newSystem.value : null,
      careGrade: el.careGrade ? el.careGrade.value : '0', carePersons: el.carePersons ? el.carePersons.value : '1',
      costs: window.HsmFunding.parseEuroToCents(el.costs.value), date: el.date.value, started: el.started.checked,
      income: el.income ? el.income.value : 'unknown', child: el.child ? el.child.checked : false
    };
    var r = window.HsmFunding.calculate(rules, input);
    render(r, input);
  });

  function render(r, input) {
    root.querySelector('[data-rule-version]').textContent = r.ruleSetVersion || '';
    var h = '';
    if (!r.ok) {
      h += '<div class="notice" role="alert"><ul>' + r.errors.map(function (m) { return '<li>' + esc(m) + '</li>'; }).join('') + '</ul></div>';
      body.innerHTML = h; resultBox.hidden = false; resultBox.focus && resultBox.setAttribute('tabindex', '-1'); resultBox.focus(); return;
    }
    if (r.manualCheck.length) {
      h += '<div class="notice"><p><strong>Manuelle Prüfung empfohlen:</strong></p><ul>' + r.manualCheck.map(function (m) { return '<li>' + esc(m) + '</li>'; }).join('') + '</ul></div>';
    }
    h += '<table class="result-table"><tbody>';
    h += '<tr><th scope="row">Projektkosten (Ihre Angabe)</th><td>' + eur(r.projectCostsCents) + '</td></tr>';
    r.grants.forEach(function (g) {
      h += '<tr><th scope="row">Förderfähige Kosten ' + esc(g.name) + (g.costsCapped ? ' (gedeckelt auf ' + eur(g.capCents) + ')' : '') + '</th><td>' + eur(g.eligibleCostsCents) + '</td></tr>';
      g.lines.forEach(function (l) { h += '<tr><th scope="row">' + esc(l.label) + '</th><td>' + (l.points === 100 ? '' : l.points + ' %') + '</td></tr>'; });
      if (g.capApplied) h += '<tr><th scope="row">Fördersatz auf Höchstsatz begrenzt</th><td>' + g.capPoints + ' %</td></tr>';
      h += '<tr><th scope="row">Zuschuss ' + esc(g.name) + (g.totalPoints !== 100 ? ' (' + g.totalPoints + ' %)' : '') + '</th><td>' + eur(g.grantCents) + '</td></tr>';
    });
    h += '<tr class="result-total"><th scope="row">Mögliche Zuschüsse gesamt</th><td>' + eur(r.totalGrantCents) + '</td></tr>';
    h += '<tr><th scope="row">Verbleibende Investition</th><td>' + eur(r.remainingCents) + '</td></tr>';
    h += '</tbody></table>';
    if (!r.grants.length) h += '<p>Für die gewählte Kombination wird derzeit kein Zuschuss automatisch berechnet. Das bedeutet nicht, dass kein Anspruch bestehen kann. Die Einordnung der Programme finden Sie unten.</p>';
    if (r.warnings.length) h += '<div class="notice"><p><strong>Hinweise:</strong></p><ul>' + r.warnings.map(function (m) { return '<li>' + esc(m) + '</li>'; }).join('') + '</ul></div>';
    r.grants.forEach(function (g) {
      h += '<div class="result-card"><h3>' + esc(g.name) + '</h3><p class="meta">Regelstand ' + esc(g.ruleVersion) + (g.periodFrom ? ', gültig ab ' + esc(g.periodFrom) : '') + ', geprüft am ' + esc(g.checkedAt) + '</p><ul class="compact">' + g.notes.map(function (n) { return '<li>' + esc(n) + '</li>'; }).join('') + '</ul>' + sources(g.sourceUrls) + '</div>';
    });
    if (r.infos.length) { h += '<h3>Weitere Programme (Einordnung ohne Betrag)</h3>'; r.infos.forEach(function (p) { h += card(p, p.reason); }); }
    if (r.loans.length) { h += '<h3>Kredite und Darlehen (keine Zuschüsse)</h3>'; r.loans.forEach(function (p) { h += card(p, capText(p)); }); }
    if (r.tax.length) { h += '<h3>Steuerliche Alternativen (nicht mit Zuschuss für dieselbe Maßnahme kombinierbar)</h3>'; r.tax.forEach(function (p) { h += card(p, p.status !== 'aktiv' ? 'Regelstand nicht verifiziert.' : 'Individuelle Wirkung mit Steuerberatung klären.'); }); }
    body.innerHTML = h;
    resultBox.hidden = false;
    resultBox.setAttribute('tabindex', '-1'); resultBox.focus();
    root.querySelector('[data-transfer]').onclick = function () { transfer(input); };
    root.querySelector('[data-print]').onclick = function () { window.print(); };
  }

  function capText(p) {
    var c = p.caps || {};
    if (c.loanPerUnitCents) return 'Bis ' + eur(c.loanPerUnitCents) + ' je Wohneinheit.';
    if (c.loanCents) return 'Bis ' + eur(c.loanCents) + (c.maxRepaymentWaiverPoints ? ', Tilgungsnachlass bis ' + c.maxRepaymentWaiverPoints + ' %' : '') + '.';
    return '';
  }
  function sources(urls) { return (urls || []).length ? '<p class="meta">' + urls.map(function (u) { return '<a class="source" href="' + esc(u) + '" rel="noopener">Quelle</a>'; }).join(' ') + '</p>' : ''; }
  function card(p, reason) {
    return '<div class="result-card"><h3>' + esc(p.name) + '</h3><p class="meta">' + esc(p.fundingType) + ', Status: ' + esc(p.status) + ', geprüft am ' + esc(p.checkedAt) + '</p>' + (reason ? '<p>' + esc(reason) + '</p>' : '') + '<ul class="compact">' + (p.notes || []).map(function (n) { return '<li>' + esc(n) + '</li>'; }).join('') + '</ul>' + sources(p.sourceUrls) + '</div>';
  }

  /** Übernahme nicht sensibler Projektdaten in das Kontaktformular nach sichtbarer Auswahl. */
  function transfer(input) {
    var note = root.querySelector('[data-transfer-note]'); note.hidden = false;
    var f = document.querySelector('form[data-form="contact"]'); if (!f) return;
    var labels = { 'heizung-austausch': 'Heizungstausch', 'heizung-optimierung': 'Heizungsoptimierung', 'bad-barriere': 'Barrierearmes Bad', 'bad-barriere-pflege': 'Wohnraumanpassung bei Pflegebedarf', 'energetische-sanierung': 'Energetische Sanierung' };
    var bld = { efh: 'Einfamilienhaus', zfh: 'Zweifamilienhaus', mfh: 'Mehrfamilienhaus', nwg: 'Nichtwohngebäude' };
    var msg = 'Ergebnis aus dem Fördercheck: Vorhaben ' + (labels[input.measure] || input.measure) + ', ' + (bld[input.building] || '') + ' mit ' + input.units + ' Wohneinheit(en), Projektkosten ca. ' + eur(input.costs) + '. Bitte um Einordnung und Terminvorschlag.';
    var m = f.querySelector('[name="message"]'); if (m && !m.value) m.value = msg;
    var t = f.querySelector('[name="topic"]'); if (t) { for (var i = 0; i < t.options.length; i++) { if (t.options[i].value === 'Fördercheck') t.selectedIndex = i; } }
    f.scrollIntoView({ behavior: 'smooth', block: 'start' }); var n = f.querySelector('[name="name"]'); if (n) n.focus({ preventScroll: true });
  }
})();
