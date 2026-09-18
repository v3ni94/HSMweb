/*
 * HSM Tec Fördercheck: reine Rechenfunktion ohne DOM.
 * Eingabe: rules (öffentliche Regeldatei), input (Formularwerte). Ausgabe: strukturiertes Ergebnis.
 * Alle Geldwerte in Cent (ganzzahlig), Prozentsätze in ganzen Prozentpunkten.
 * Nutzbar im Browser (window.HsmFunding) und in Node (module.exports) für Tests.
 */
(function (root, factory) {
  if (typeof module === 'object' && module.exports) { module.exports = factory(); }
  else { root.HsmFunding = factory(); }
}(typeof self !== 'undefined' ? self : this, function () {
  'use strict';

  /** Prozent von Cent, kaufmännisch gerundet, nur Ganzzahlarithmetik. */
  function pct(cents, points) {
    if (!Number.isInteger(cents) || !Number.isInteger(points) || cents < 0 || points < 0) throw new Error('pct: ungültige Eingabe');
    var num = cents * points;
    return Math.floor((num + 50) / 100);
  }

  /** "28.000,50" | "28000" | "28000.5" -> Cent oder null */
  function parseEuroToCents(str) {
    if (typeof str === 'number') str = String(str);
    if (typeof str !== 'string') return null;
    var s = str.trim().replace(/\s|€|EUR/gi, '');
    if (s === '') return null;
    if (/^-/.test(s)) return null;
    // Deutsches Format: Punkt als Tausender, Komma als Dezimal
    if (/,\d{1,2}$/.test(s)) { s = s.replace(/\./g, '').replace(',', '.'); }
    else if (/^\d{1,3}(\.\d{3})+$/.test(s)) { s = s.replace(/\./g, ''); }
    else if (/,/.test(s)) { s = s.replace(/,/g, ''); }
    if (!/^\d+(\.\d{1,2})?$/.test(s)) return null;
    var parts = s.split('.');
    var cents = parseInt(parts[0], 10) * 100 + (parts[1] ? parseInt((parts[1] + '0').slice(0, 2), 10) : 0);
    return Number.isSafeInteger(cents) ? cents : null;
  }

  function isoDateValid(d) { return typeof d === 'string' && /^\d{4}-\d{2}-\d{2}$/.test(d) && !isNaN(Date.parse(d)); }

  function findProgram(rules, id) {
    return (rules.programs || []).find(function (p) { return p.id === id; }) || null;
  }

  function findPeriod(program, date) {
    if (!Array.isArray(program.periods)) return null;
    return program.periods.find(function (p) {
      return p.validFrom <= date && (p.validUntil === null || p.validUntil === undefined || date <= p.validUntil);
    }) || null;
  }

  function programReviewOverdue(program, today) {
    return !!program.reviewDueAt && today > program.reviewDueAt;
  }

  /** KfW-458-Kostenobergrenze für n Wohneinheiten */
  function kfw458Cap(program, period, units) {
    var caps = program.costRules.unitCapsCents;
    var first = period && Number.isInteger(period.firstUnitCapCents) ? period.firstUnitCapCents : caps.first;
    if (units <= 1) return first;
    var total = first;
    var n = Math.min(units, 6) - 1;
    total += n * caps.secondToSixth;
    if (units > 6) total += (units - 6) * caps.further;
    return total;
  }

  function incomeTier(program, incomeKey, child) {
    var tiers = program.rates.incomeBonus.tiers;
    var map = { le30: 3000000, le40: 4000000, le50: 5000000 };
    if (!(incomeKey in map)) return null;
    var income = map[incomeKey];
    var add = child ? program.rates.incomeBonus.childThresholdIncreaseCents : 0;
    for (var i = 0; i < tiers.length; i++) {
      if (income <= tiers[i].maxIncomeCents + add) return tiers[i];
    }
    return null;
  }

  /**
   * Hauptfunktion.
   * input: { measure, state, building, units, applicant, existing, mixed, oldSystem, oldAge, newSystem,
   *          careGrade, carePersons, costs (Cent), date (JJJJ-MM-TT), started, income, child, today }
   */
  function calculate(rules, input) {
    var result = {
      ok: true,
      ruleSetVersion: rules.ruleSetVersion,
      projectCostsCents: null,
      grants: [],        // berechnete Zuschüsse
      infos: [],         // Programme nur mit Einordnung
      loans: [],         // Kredite / Darlehen
      tax: [],           // steuerliche Alternativen
      manualCheck: [],   // Gründe für manuelle Prüfung
      warnings: [],
      errors: [],
      totalGrantCents: 0,
      remainingCents: null
    };
    if (!rules || !Array.isArray(rules.programs)) { result.ok = false; result.errors.push('Regelwerk nicht verfügbar.'); return result; }

    var costs = input.costs;
    if (!Number.isInteger(costs) || costs <= 0) { result.ok = false; result.errors.push('Bitte geben Sie gültige Projektkosten größer als null an.'); }
    if (costs > 5000000000) { result.ok = false; result.errors.push('Die Kostenangabe ist unplausibel hoch.'); }
    if (!isoDateValid(input.date)) { result.ok = false; result.errors.push('Bitte geben Sie einen gültigen Antragszeitpunkt an.'); }
    var units = parseInt(input.units, 10);
    if (!Number.isInteger(units) || units < 1) { result.ok = false; result.errors.push('Bitte geben Sie die Anzahl der Wohneinheiten an (mindestens 1).'); }
    if (!input.measure) { result.ok = false; result.errors.push('Bitte wählen Sie ein Vorhaben.'); }
    if (!result.ok) return result;

    var today = isoDateValid(input.today) ? input.today : new Date().toISOString().slice(0, 10);
    result.projectCostsCents = costs;

    if (input.started) result.manualCheck.push('Auftrag bereits erteilt oder Arbeiten begonnen: Bei den meisten Programmen muss der Antrag vor Vorhabenbeginn gestellt werden. Bitte individuell prüfen, ob noch ein Antrag möglich ist.');
    if (input.mixed) result.manualCheck.push('Mischgebäude, Contracting, mehrere frühere Anträge oder komplexe Eigentumsverhältnisse erfordern eine manuelle Prüfung.');
    if (input.applicant === 'weg') result.manualCheck.push('Wohnungseigentümergemeinschaft: Zuordnung von Boni und Kostenobergrenzen je Einheit erfordert eine manuelle Prüfung.');
    if (input.building === 'nwg') result.manualCheck.push('Nichtwohngebäude werden in anderen Programmen gefördert und separat eingeordnet.');
    if (!input.existing) result.manualCheck.push('Neubau: Die hier abgebildeten Programme richten sich an Bestandsgebäude.');

    var canAutoHousing = input.existing && input.building !== 'nwg' && !input.mixed && !input.started && input.applicant !== 'weg';

    rules.programs.forEach(function (p) {
      var relevant = p.measures.indexOf(input.measure) !== -1 || (input.measure === 'bad-barriere-pflege' && p.measures.indexOf('bad-barriere') !== -1);
      if (!relevant) return;
      if (p.region !== 'DE' && p.region !== input.state) return;
      if (Array.isArray(p.applicantTypes) && p.applicantTypes.length && p.applicantTypes.indexOf(input.applicant) === -1) {
        if (!(input.applicant === 'privat-mieter' && p.id === 'pflegekasse-wohnumfeld')) return;
      }
      var entry = { id: p.id, name: p.name, provider: p.provider, fundingType: p.fundingType, status: p.status, checkedAt: p.checkedAt, sourceUrls: p.sourceUrls, notes: (p.eligibility && p.eligibility.notes) || [], caps: p.caps || {}, rates: p.rates || {} };
      var overdue = programReviewOverdue(p, today);
      if (overdue) entry.notes = entry.notes.concat(['Die Quellenprüfung dieses Programms ist überfällig (' + p.reviewDueAt + '). Eine automatische Berechnung erfolgt deshalb nicht.']);

      if (/kredit|darlehen/i.test(p.fundingType)) { result.loans.push(entry); return; }
      if (/steuer/i.test(p.fundingType)) { result.tax.push(entry); return; }

      if (p.status !== 'aktiv') { entry.reason = p.status === 'derzeit geschlossen' ? 'Programm derzeit geschlossen, nicht in die Summe eingerechnet.' : (p.status === 'Prüfung erforderlich' ? 'Regelstand nicht verifiziert, keine Berechnung.' : 'Programm ' + p.status + '.'); result.infos.push(entry); return; }
      if (!p.calculationApproved || overdue) { entry.reason = overdue ? 'Quellenprüfung überfällig.' : 'Automatische Berechnung nicht freigegeben; Einordnung nur als Information.'; result.infos.push(entry); return; }

      if (p.id === 'kfw-458') {
        if (!canAutoHousing) { entry.reason = 'Voraussetzungen für eine automatische Berechnung nicht erfüllt (siehe Prüfhinweise).'; result.infos.push(entry); return; }
        var period = findPeriod(p, input.date);
        if (!period) { entry.reason = 'Für den gewählten Antragszeitpunkt liegt kein geprüfter Regelstand vor. Bitte prüfen lassen.'; result.infos.push(entry); result.manualCheck.push('Antragszeitpunkt außerhalb des bekannten Regelstands von ' + p.name + '.'); return; }
        if (!period.calculationApproved) { entry.reason = 'Der Regelstand ab ' + period.validFrom + ' ist angekündigt, aber noch nicht zur Berechnung freigegeben. ' + (period.note || ''); result.infos.push(entry); return; }
        if (input.oldSystem === 'keine' || input.oldSystem === 'waermepumpe') { entry.reason = 'Ohne förderfähige Altanlage kein Heizungstausch im Sinne des Programms.'; result.infos.push(entry); return; }
        var cap = kfw458Cap(p, period, units);
        var eligible = Math.min(costs, cap);
        var points = p.rates.basePoints;
        var lines = [{ label: 'Grundförderung', points: p.rates.basePoints }];
        var selfUse = input.applicant === 'privat-selbstnutzer';
        var capPoints = p.caps.defaultCapPoints;
        if (selfUse && units > 1) result.warnings.push('Selbstnutzerboni gelten nur für die selbst genutzte Wohneinheit. Bei mehreren Einheiten werden sie hier nur auf die erste Einheit bezogen; Details bitte prüfen lassen.');
        if (selfUse && input.oldSystem === 'fossil-funktion' || selfUse && input.oldSystem === 'fossil-defekt' || selfUse && input.oldSystem === 'strom-nacht' || selfUse && input.oldSystem === 'biomasse') {
          var climate = period.climateSpeedBonusPoints;
          if (climate > 0) { lines.push({ label: 'Klimageschwindigkeitsbonus', points: climate }); points += climate; }
          if (input.oldSystem === 'fossil-defekt') result.warnings.push('Klimageschwindigkeitsbonus setzt eine funktionstüchtige Altanlage voraus; bei defekter Anlage bitte prüfen lassen.');
          if (input.oldSystem === 'biomasse') result.warnings.push('Bei Biomasse-Altanlagen gelten besondere Bedingungen für den Klimageschwindigkeitsbonus; bitte prüfen lassen.');
        }
        var tier = null;
        if (selfUse) {
          if (input.income === 'unknown') result.warnings.push('Ohne Einkommensangabe wurde kein Einkommensbonus berücksichtigt. Bei einem Haushaltseinkommen bis 50.000 EUR kann ein Bonus hinzukommen.');
          else tier = incomeTier(p, input.income, !!input.child);
          if (tier) { lines.push({ label: 'Einkommensbonus', points: tier.points }); points += tier.points; capPoints = tier.capPoints; }
        }
        var capped = false;
        if (points > capPoints) { points = capPoints; capped = true; }
        var grant = pct(eligible, points);
        var selfUseBonusInfo = selfUse ? [] : ['Keine Selbstnutzerboni bei Vermietung.'];
        var sub = units > 1 && selfUse ? [] : [];
        result.grants.push({
          id: p.id, name: p.name, provider: p.provider, fundingType: p.fundingType, status: p.status, checkedAt: p.checkedAt, ruleVersion: p.ruleVersion, periodFrom: period.validFrom, periodUntil: period.validUntil, sourceUrls: p.sourceUrls,
          eligibleCostsCents: eligible, capCents: cap, costsCapped: costs > cap, lines: lines, totalPoints: points, capPoints: capPoints, capApplied: capped, grantCents: grant,
          notes: entry.notes.concat(selfUseBonusInfo).concat(sub)
        });
        result.totalGrantCents += grant;
        return;
      }

      if (p.id === 'pflegekasse-wohnumfeld') {
        if (input.measure !== 'bad-barriere-pflege') { entry.reason = 'Nur bei Pflegegrad und pflegebedingter Anpassung.'; result.infos.push(entry); return; }
        if (String(input.careGrade) !== '1') { entry.reason = 'Ohne Pflegegrad kein Anspruch auf diesen Zuschuss.'; result.infos.push(entry); return; }
        var persons = parseInt(input.carePersons, 10);
        if (!Number.isInteger(persons) || persons < 1) persons = 1;
        if (persons > p.caps.maxPersons) { persons = p.caps.maxPersons; result.warnings.push('Höchstens ' + p.caps.maxPersons + ' berechtigte Personen werden berücksichtigt.'); }
        var perPerson = p.caps.perPersonCents;
        var totalCap = Math.min(persons * perPerson, p.caps.totalCapCents);
        var g = Math.min(costs, totalCap);
        result.grants.push({ id: p.id, name: p.name, provider: p.provider, fundingType: p.fundingType, status: p.status, checkedAt: p.checkedAt, ruleVersion: p.ruleVersion, sourceUrls: p.sourceUrls,
          eligibleCostsCents: g, capCents: totalCap, costsCapped: costs > totalCap, lines: [{ label: 'Zuschuss bis Höchstbetrag (' + persons + ' Person' + (persons > 1 ? 'en' : '') + ')', points: 100 }], totalPoints: 100, capPoints: 100, capApplied: false, grantCents: g,
          notes: entry.notes.concat(['Bewilligung durch die Pflegekasse vor Beginn erforderlich. Tatsächliche Kosten maßgeblich.']) });
        result.totalGrantCents += g;
        return;
      }

      if (p.id === 'bafa-heizungsoptimierung') {
        if (!input.existing || input.building === 'nwg' || input.mixed || input.started) { entry.reason = 'Voraussetzungen für eine automatische Berechnung nicht erfüllt (siehe Prüfhinweise).'; result.infos.push(entry); return; }
        if (units > p.eligibility.maxUnits) { entry.reason = 'Heizungsoptimierung wird nur für Bestandsgebäude mit höchstens ' + p.eligibility.maxUnits + ' Wohneinheiten gefördert.'; result.infos.push(entry); return; }
        if (costs < p.costRules.minInvestCents) { entry.reason = 'Das förderfähige Mindestinvestitionsvolumen von ' + (p.costRules.minInvestCents / 100) + ' EUR ist nicht erreicht.'; result.infos.push(entry); return; }
        var bcaps = p.costRules.unitCapsCents;
        var bcap = bcaps.first + (Math.min(units, 6) - 1) * bcaps.secondToSixth + (units > 6 ? (units - 6) * bcaps.further : 0);
        var beligible = Math.min(costs, bcap);
        var bgrant = pct(beligible, p.rates.basePoints);
        result.grants.push({ id: p.id, name: p.name, provider: p.provider, fundingType: p.fundingType, status: p.status, checkedAt: p.checkedAt, ruleVersion: p.ruleVersion, sourceUrls: p.sourceUrls,
          eligibleCostsCents: beligible, capCents: bcap, costsCapped: costs > bcap, lines: [{ label: 'Grundförderung Heizungsoptimierung', points: p.rates.basePoints }], totalPoints: p.rates.basePoints, capPoints: p.caps.defaultCapPoints, capApplied: false, grantCents: bgrant,
          notes: entry.notes.concat(['Ein möglicher iSFP-Bonus von ' + p.rates.isfpBonusPoints + ' Prozentpunkten ist nicht enthalten; er gilt nur oberhalb des Mindestinvestitionsvolumens von 30.000 EUR.']) });
        result.totalGrantCents += bgrant;
        return;
      }

      // Aktive, freigegebene, aber hier nicht implementierte Berechnung: nur Information
      entry.reason = 'Berechnung für dieses Programm ist nicht implementiert; Einordnung als Information.';
      result.infos.push(entry);
    });

    // Unzulässige Mehrfachförderung / Trennung
    if (result.grants.length > 1) result.warnings.push('Mehrere Zuschüsse für dieselbe Maßnahme sind in der Regel nicht kombinierbar. Die Summe ist nur ein Orientierungswert.');
    if (result.grants.some(function (g) { return g.id === 'kfw-458'; }) && result.tax.length) result.warnings.push('Die Steuerermäßigung nach § 35c EStG ist nicht für Maßnahmen möglich, für die ein Zuschuss in Anspruch genommen wird.');
    if (input.applicant === 'unternehmen' && input.measure === 'heizung-austausch') result.manualCheck.push('Unternehmen: KfW 459 sieht 30 % Grundförderung ohne private Boni vor. Kostenobergrenzen werden vor einer Betragsberechnung aus dem aktuellen Merkblatt geprüft.');

    result.remainingCents = Math.max(0, costs - result.totalGrantCents);
    return result;
  }

  return { calculate: calculate, pct: pct, parseEuroToCents: parseEuroToCents, kfw458Cap: kfw458Cap, findPeriod: findPeriod, incomeTier: incomeTier };
}));
