// Regressionstests für den Fördercheck. Ausführen: node tests/funding-calc.test.js
// Erwartungswerte aus dem freigegebenen Regelwerk (Anhang A des Auftrags).
const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const F = require('../public/assets/js/funding-calc.js');
const rules = JSON.parse(fs.readFileSync(path.join(__dirname, '../public/assets/data/funding-rules.json'), 'utf8'));

const base = { measure: 'heizung-austausch', state: 'NRW', building: 'efh', units: 1, applicant: 'privat-selbstnutzer', existing: true, mixed: false, oldSystem: 'fossil-funktion', newSystem: 'waermepumpe', costs: 2800000, date: '2026-09-18', started: false, income: 'unknown', child: false, today: '2026-09-18' };
let n = 0;
function t(name, fn) { fn(); n++; console.log('ok  ' + name); }
const grant = (r, id) => r.grants.find(g => g.id === id);

t('Geldparser', () => {
  assert.equal(F.parseEuroToCents('28.000'), 2800000);
  assert.equal(F.parseEuroToCents('28000'), 2800000);
  assert.equal(F.parseEuroToCents('28.000,50'), 2800050);
  assert.equal(F.parseEuroToCents('28000.5'), 2800050);
  assert.equal(F.parseEuroToCents('-5'), null);
  assert.equal(F.parseEuroToCents('abc'), null);
});
t('Prozentrechnung ganzzahlig', () => { assert.equal(F.pct(2800000, 30), 840000); assert.equal(F.pct(1, 50), 1); assert.equal(F.pct(10001, 33), 3300); });

t('Grundförderung ohne Boni (Vermieter): 8.400 EUR', () => {
  const r = F.calculate(rules, { ...base, applicant: 'privat-vermieter' });
  assert.equal(grant(r, 'kfw-458').grantCents, 840000);
  assert.equal(grant(r, 'kfw-458').totalPoints, 30);
});
t('Klimabonus Selbstnutzer ohne Einkommensangabe: 12.880 EUR', () => {
  const r = F.calculate(rules, base);
  assert.equal(grant(r, 'kfw-458').grantCents, 1288000);
  assert.ok(r.warnings.some(w => /Einkommensangabe/.test(w)));
});
t('Niedrigste Einkommensstufe, gedeckelt 80 %: 22.400 EUR', () => {
  const g = grant(F.calculate(rules, { ...base, income: 'le30' }), 'kfw-458');
  assert.equal(g.grantCents, 2240000); assert.equal(g.capApplied, true); assert.equal(g.capPoints, 80);
});
t('Mittlere Stufe, gedeckelt 70 %: 19.600 EUR', () => {
  const g = grant(F.calculate(rules, { ...base, income: 'le40' }), 'kfw-458');
  assert.equal(g.grantCents, 1960000); assert.equal(g.capApplied, true);
});
t('Oberste Bonusstufe 56 %: 15.680 EUR', () => {
  const g = grant(F.calculate(rules, { ...base, income: 'le50' }), 'kfw-458');
  assert.equal(g.grantCents, 1568000); assert.equal(g.totalPoints, 56); assert.equal(g.capApplied, false);
});
t('Über 50.000 EUR: kein Einkommensbonus', () => { assert.equal(grant(F.calculate(rules, { ...base, income: 'gt50' }), 'kfw-458').totalPoints, 46); });
t('Qualifizierendes Kind hebt Schwelle: le40 + Kind -> Stufe 40 Punkte', () => {
  const g = grant(F.calculate(rules, { ...base, income: 'le40', child: true }), 'kfw-458');
  assert.equal(g.lines.find(l => l.label === 'Einkommensbonus').points, 40); assert.equal(g.grantCents, 2240000);
});
t('Kostenobergrenze: 40.000 EUR Kosten -> förderfähig 28.000', () => {
  const g = grant(F.calculate(rules, { ...base, costs: 4000000, applicant: 'privat-vermieter' }), 'kfw-458');
  assert.equal(g.eligibleCostsCents, 2800000); assert.equal(g.costsCapped, true); assert.equal(g.grantCents, 840000);
});
t('Kostenobergrenze Mehrfamilienhaus 8 WE', () => { assert.equal(F.kfw458Cap(rules.programs.find(p => p.id === 'kfw-458'), null, 8), 2800000 + 5 * 1500000 + 2 * 800000); });
t('Kosten unter Obergrenze: 10.000 EUR -> 3.000 EUR bei 30 %', () => { assert.equal(grant(F.calculate(rules, { ...base, applicant: 'privat-vermieter', costs: 1000000 }), 'kfw-458').grantCents, 300000); });
t('Unternehmen: kein 458, Hinweis auf 459 und manuelle Prüfung', () => {
  const r = F.calculate(rules, { ...base, applicant: 'unternehmen' });
  assert.equal(grant(r, 'kfw-458'), undefined);
  assert.ok(r.infos.some(p => p.id === 'kfw-459'));
  assert.ok(r.manualCheck.some(m => /459/.test(m)));
});
t('WEG -> manuelle Prüfung, keine Berechnung', () => {
  const r = F.calculate(rules, { ...base, applicant: 'weg', building: 'mfh', units: 6 });
  assert.equal(r.grants.length, 0); assert.ok(r.manualCheck.some(m => /Wohnungseigentümergemeinschaft/.test(m)));
});
t('Datumswechsel: Antrag ab 01.02.2027 -> angekündigter Regelstand, keine Berechnung', () => {
  const r = F.calculate(rules, { ...base, date: '2027-02-15', today: '2026-09-18' });
  assert.equal(grant(r, 'kfw-458'), undefined);
  assert.ok(r.infos.some(p => p.id === 'kfw-458' && /angekündigt/.test(p.reason)));
});
t('Datum vor Regelstand -> Prüfung statt stillschweigender Anwendung', () => {
  const r = F.calculate(rules, { ...base, date: '2026-01-10', today: '2026-01-10' });
  assert.equal(r.grants.length, 0); assert.ok(r.manualCheck.some(m => /Antragszeitpunkt/.test(m)));
});
t('Abgelaufene Quellenprüfung -> Berechnung deaktiviert', () => {
  const r = F.calculate(rules, { ...base, today: '2026-12-01', date: '2026-12-01' });
  assert.equal(grant(r, 'kfw-458'), undefined);
  assert.ok(r.infos.some(p => p.id === 'kfw-458' && /überfällig/.test(p.reason)));
});
t('Geschlossenes Programm 455-B nicht eingerechnet', () => {
  const r = F.calculate(rules, { ...base, measure: 'bad-barriere', costs: 1500000 });
  assert.equal(r.totalGrantCents, 0);
  assert.ok(r.infos.some(p => p.id === 'kfw-455-b' && /geschlossen/.test(p.reason)));
  assert.ok(r.loans.some(p => p.id === 'kfw-159'));
});
t('Pflegekasse: 6.000 EUR Kosten, 1 Person -> 4.180 EUR', () => {
  const r = F.calculate(rules, { ...base, measure: 'bad-barriere-pflege', careGrade: '1', carePersons: 1, costs: 600000 });
  assert.equal(grant(r, 'pflegekasse-wohnumfeld').grantCents, 418000);
});
t('Pflegekasse: 3.000 EUR Kosten -> tatsächliche Kosten 3.000 EUR', () => { assert.equal(grant(F.calculate(rules, { ...base, measure: 'bad-barriere-pflege', careGrade: '1', carePersons: 1, costs: 300000 }), 'pflegekasse-wohnumfeld').grantCents, 300000); });
t('Pflegekasse: 5 Personen begrenzt auf 16.720 EUR', () => {
  const r = F.calculate(rules, { ...base, measure: 'bad-barriere-pflege', careGrade: '1', carePersons: 5, costs: 5000000 });
  assert.equal(grant(r, 'pflegekasse-wohnumfeld').grantCents, 1672000);
});
t('Ohne Pflegegrad kein Pflegekassen-Zuschuss', () => { assert.equal(F.calculate(rules, { ...base, measure: 'bad-barriere-pflege', careGrade: '0', costs: 600000 }).totalGrantCents, 0); });
t('Kredit/Zuschuss getrennt: 358/359 in loans, nicht in grants', () => {
  const r = F.calculate(rules, base);
  assert.ok(r.loans.some(p => p.id === 'kfw-358-359')); assert.ok(!r.grants.some(g => g.id === 'kfw-358-359'));
  assert.ok(r.tax.some(p => p.id === 'steuer-35c')); assert.ok(r.warnings.some(w => /35c/.test(w)));
});
t('Heizungsoptimierung BAFA: 2.000 EUR -> 300 EUR (15 %)', () => {
  const r = F.calculate(rules, { ...base, measure: 'heizung-optimierung', costs: 200000 });
  const g = grant(r, 'bafa-heizungsoptimierung'); assert.equal(g.grantCents, 30000); assert.equal(g.totalPoints, 15);
  assert.ok(g.notes.some(n => /iSFP/.test(n)));
});
t('Heizungsoptimierung BAFA: Obergrenze 30.000 EUR bei 1 WE, 6 WE -> 105.000 EUR', () => {
  assert.equal(grant(F.calculate(rules, { ...base, measure: 'heizung-optimierung', costs: 4000000 }), 'bafa-heizungsoptimierung').eligibleCostsCents, 3000000);
  const r = F.calculate(rules, { ...base, measure: 'heizung-optimierung', building: 'mfh', units: 5, applicant: 'privat-vermieter', costs: 20000000 });
  assert.equal(grant(r, 'bafa-heizungsoptimierung').capCents, 3000000 + 4 * 1500000);
});
t('Heizungsoptimierung BAFA: mehr als 5 WE oder unter 300 EUR -> keine Berechnung', () => {
  assert.equal(F.calculate(rules, { ...base, measure: 'heizung-optimierung', building: 'mfh', units: 6, applicant: 'privat-vermieter', costs: 500000 }).grants.length, 0);
  assert.equal(F.calculate(rules, { ...base, measure: 'heizung-optimierung', costs: 20000 }).grants.length, 0);
});
t('Emissionsminderung Biomasse: nur Information mit 50 %', () => {
  const r = F.calculate(rules, { ...base, measure: 'heizung-optimierung', costs: 500000 });
  const p = r.infos.find(p => p.id === 'bafa-emissionsminderung'); assert.ok(p); assert.equal(p.rates.basePoints, 50);
});
t('§ 35a als steuerliche Alternative bei Bad ohne Pflegegrad gelistet', () => {
  const r = F.calculate(rules, { ...base, measure: 'bad-barriere', costs: 1500000 });
  assert.ok(r.tax.some(p => p.id === 'steuer-35a' && p.status === 'aktiv'));
});
t('progres.nrw nach 30.06.2027 weiterhin nur Information, Regelwerk validUntil gesetzt', () => {
  const p = rules.programs.find(p => p.id === 'progres-nrw-geothermie'); assert.equal(p.validUntil, '2027-06-30'); assert.equal(p.calculationApproved, false);
});
t('Negative/ungültige Kosten', () => {
  assert.equal(F.calculate(rules, { ...base, costs: -100 }).ok, false);
  assert.equal(F.calculate(rules, { ...base, costs: null }).ok, false);
  assert.equal(F.calculate(rules, { ...base, costs: 0 }).ok, false);
  assert.equal(F.calculate(rules, { ...base, date: 'gestern' }).ok, false);
  assert.equal(F.calculate(rules, { ...base, units: 0 }).ok, false);
});
t('Vorhabenbeginn erfolgt -> manuelle Prüfung, keine Berechnung', () => {
  const r = F.calculate(rules, { ...base, started: true }); assert.equal(r.grants.length, 0); assert.ok(r.manualCheck.length > 0);
});
t('Keine Altanlage -> kein 458', () => { assert.equal(F.calculate(rules, { ...base, oldSystem: 'keine' }).grants.length, 0); });
t('Andere Bundesländer: NRW-Programme ausgeblendet', () => {
  const r = F.calculate(rules, { ...base, state: 'other' }); assert.ok(!r.loans.some(p => p.id === 'nrw-bank-gebaeudesanierung'));
  const r2 = F.calculate(rules, base); assert.ok(r2.loans.some(p => p.id === 'nrw-bank-gebaeudesanierung'));
});
t('Kein Rundungsfehler: 12.345,67 EUR bei 46 %', () => { assert.equal(grant(F.calculate(rules, { ...base, costs: 1234567 }), 'kfw-458').grantCents, Math.floor((1234567 * 46 + 50) / 100)); });
t('Öffentliche Regeldatei enthält keine internen Notizen', () => { assert.equal(JSON.stringify(rules).includes('internalNotes'), false); });
console.log(`\n${n} Tests bestanden.`);
