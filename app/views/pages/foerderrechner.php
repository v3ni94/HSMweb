<?php use App\Services\Container; use App\Services\View; $f = Container::funding(); ?>
<div class="wrap">
<header class="page-head">
  <h1><?= e($page['h1']) ?></h1>
  <p class="lead"><?= e($page['lead']) ?></p>
</header>
<noscript>
  <section class="section notice">
    <h2>Fördercheck ohne JavaScript</h2>
    <p>Der interaktive Fördercheck benötigt JavaScript. Die Förderinformationen finden Sie auf den Seiten <a href="/foerderung/heizung/">Förderung Heizung</a>, <a href="/foerderung/bad-und-barriereabbau/">Bad und Barriereabbau</a> und <a href="/foerderung/nrw/">Förderung in NRW</a>. Für eine persönliche Einschätzung nutzen Sie das Formular unten. Bitte geben Sie dort keine Einkommens- oder Pflegeangaben ein; diese besprechen wir im Gespräch.</p>
  </section>
</noscript>
<section class="section calc" data-funding-calc data-rules-url="<?= asset('data/funding-rules.json') ?>" hidden>
  <form class="form calc-form" data-calc-form novalidate>
    <fieldset class="calc-step" data-step="1">
      <legend>1. Vorhaben und Ort</legend>
      <div class="field"><label for="c-measure">Was planen Sie?</label>
        <select id="c-measure" name="measure" required>
          <option value="">Bitte wählen</option>
          <option value="heizung-austausch">Heizung austauschen (z. B. Wärmepumpe)</option>
          <option value="heizung-optimierung">Heizung optimieren (hydraulischer Abgleich, Pumpen)</option>
          <option value="bad-barriere">Bad barrierearm umbauen (ohne Pflegegrad)</option>
          <option value="bad-barriere-pflege">Wohnraumanpassung bei Pflegegrad</option>
          <option value="energetische-sanierung">Energetische Gesamtsanierung</option>
        </select></div>
      <div class="field-row">
        <div class="field"><label for="c-state">Bundesland</label>
          <select id="c-state" name="state"><option value="NRW">Nordrhein-Westfalen</option><option value="other">Anderes Bundesland</option></select></div>
        <div class="field"><label for="c-plz">Objekt-PLZ (optional)</label><input id="c-plz" name="plz" inputmode="numeric" maxlength="5" pattern="[0-9]{5}"></div>
      </div>
    </fieldset>
    <fieldset class="calc-step" data-step="2">
      <legend>2. Gebäude und Nutzung</legend>
      <div class="field"><label for="c-building">Gebäudeart</label>
        <select id="c-building" name="building"><option value="efh">Einfamilienhaus</option><option value="zfh">Zweifamilienhaus</option><option value="mfh">Mehrfamilienhaus</option><option value="nwg">Nichtwohngebäude</option></select></div>
      <div class="field"><label for="c-units">Wohneinheiten im Gebäude</label><input id="c-units" name="units" type="number" inputmode="numeric" min="1" max="200" value="1"></div>
      <div class="field"><label for="c-applicant">Wer beantragt?</label>
        <select id="c-applicant" name="applicant">
          <option value="privat-selbstnutzer">Privatperson, selbst genutzt</option>
          <option value="privat-vermieter">Privatperson, vermietet</option>
          <option value="privat-mieter">Mieter</option>
          <option value="weg">Wohnungseigentümergemeinschaft</option>
          <option value="unternehmen">Unternehmen / GbR</option>
        </select></div>
      <div class="field field-check"><input id="c-existing" type="checkbox" name="existing" checked><label for="c-existing">Bestandsgebäude (kein Neubau)</label></div>
      <div class="field field-check"><input id="c-mixed" type="checkbox" name="mixed"><label for="c-mixed">Mischgebäude, Contracting, mehrere frühere Anträge oder komplexe Eigentumsverhältnisse</label></div>
    </fieldset>
    <fieldset class="calc-step" data-step="3" data-only="heizung-austausch">
      <legend>3. Bestehende Heizung</legend>
      <div class="field"><label for="c-old">Alte Anlage</label>
        <select id="c-old" name="oldSystem"><option value="fossil-funktion">Öl-/Gasheizung, funktionstüchtig</option><option value="fossil-defekt">Öl-/Gasheizung, defekt</option><option value="strom-nacht">Nachtspeicher/Stromdirekt</option><option value="biomasse">Biomasse</option><option value="waermepumpe">Bereits Wärmepumpe</option><option value="keine">Keine Altanlage</option></select></div>
      <div class="field"><label for="c-oldage">Alter der Altanlage in Jahren (ungefähr)</label><input id="c-oldage" name="oldAge" type="number" min="0" max="80" inputmode="numeric"></div>
      <div class="field"><label for="c-new">Geplante Technik</label>
        <select id="c-new" name="newSystem"><option value="waermepumpe">Wärmepumpe</option><option value="biomasse">Biomasse</option><option value="waermenetz">Wärmenetzanschluss</option><option value="solar">Solarthermie</option><option value="hybrid">Hybrid</option><option value="andere">Andere</option></select></div>
    </fieldset>
    <fieldset class="calc-step" data-step="3b" data-only="bad-barriere-pflege">
      <legend>3. Pflegebedarf (bleibt im Browser)</legend>
      <p class="hint">Diese Angaben werden nur lokal ausgewertet und nicht gespeichert oder übertragen. Bitte keine Bescheide hochladen.</p>
      <div class="field"><label for="c-care">Pflegegrad liegt vor</label><select id="c-care" name="careGrade"><option value="0">Nein / noch nicht</option><option value="1">Ja, Pflegegrad 1 bis 5</option></select></div>
      <div class="field"><label for="c-persons">Anzahl pflegebedürftiger Personen im Haushalt</label><input id="c-persons" name="carePersons" type="number" min="1" max="4" value="1"></div>
    </fieldset>
    <fieldset class="calc-step" data-step="4">
      <legend>4. Kosten und Zeitpunkt</legend>
      <div class="field"><label for="c-costs">Voraussichtliche Projektkosten in EUR (brutto)</label><input id="c-costs" name="costs" type="text" inputmode="decimal" placeholder="z. B. 28000" required></div>
      <div class="field"><label for="c-date">Gewünschter Antragszeitpunkt</label><input id="c-date" name="date" type="date" required></div>
      <div class="field field-check"><input id="c-started" type="checkbox" name="started"><label for="c-started">Auftrag bereits erteilt oder Arbeiten bereits begonnen</label></div>
    </fieldset>
    <fieldset class="calc-step" data-step="5" data-only="heizung-austausch" data-selfuse-only>
      <legend>5. Boni für Selbstnutzer (bleibt im Browser)</legend>
      <p class="hint">Einkommensangaben werden ausschließlich lokal verarbeitet, nicht gespeichert und nicht übertragen.</p>
      <div class="field"><label for="c-income">Zu versteuerndes Haushaltsjahreseinkommen</label>
        <select id="c-income" name="income"><option value="unknown">Keine Angabe / unbekannt</option><option value="le30">bis 30.000 EUR</option><option value="le40">bis 40.000 EUR</option><option value="le50">bis 50.000 EUR</option><option value="gt50">über 50.000 EUR</option></select></div>
      <div class="field field-check"><input id="c-child" type="checkbox" name="child"><label for="c-child">Qualifizierendes minderjähriges Kind im Haushalt</label></div>
    </fieldset>
    <p class="btn-row"><button type="submit" class="btn btn-primary">Ergebnis anzeigen</button> <button type="reset" class="btn btn-ghost">Zurücksetzen</button></p>
  </form>
  <section class="calc-result" data-calc-result aria-live="polite" hidden>
    <h2>Ihr Ergebnis</h2>
    <div data-result-body></div>
    <p class="hint">Regelstand: <span data-rule-version></span>. Das Ergebnis ist eine Einschätzung auf Basis des hinterlegten Regelwerks, weder Angebot noch Förderzusage. Einkommens- und Pflegeangaben verlassen Ihren Browser nicht.</p>
    <p class="btn-row"><button type="button" class="btn btn-ghost" data-print>Druckansicht</button> <button type="button" class="btn btn-secondary" data-transfer>Vorhaben mit HSM Tec besprechen</button></p>
    <p class="hint" data-transfer-note hidden>Es werden nur Vorhaben, Gebäudeart, Wohneinheiten und die Kostengröße in das Formular übernommen. Keine Einkommens- oder Pflegeangaben.</p>
  </section>
</section>
<section class="section">
  <h2>Was der Fördercheck kann und was nicht</h2>
  <ul class="checklist">
    <li>Rechnet nur Programme, deren Berechnung nach Quellenprüfung freigegeben ist (aktuell: KfW 458 im geprüften Regelstand, Zuschuss der Pflegekasse).</li>
    <li>Zeigt bei allen anderen Programmen Einordnung, Voraussetzungen und Quelle, aber keinen Betrag.</li>
    <li>Trennt Projektkosten, förderfähige Kosten, Zuschuss, verbleibende Investition, mögliche Kredite und steuerliche Alternativen.</li>
    <li>Leitet Wohnungseigentümergemeinschaften, Mischgebäude, Contracting und Fälle mit mehreren Anträgen in eine manuelle Prüfung.</li>
    <li>Wertet den zum Antragszeitpunkt gültigen Regelstand aus. Für unbekannte Zeiträume wird auf Prüfung verwiesen, statt aktuelle Werte stillschweigend anzuwenden.</li>
  </ul>
  <p class="hint">Programme und Prüfdaten: <a href="/foerderung/">Förderübersicht</a>. Regelwerk-Version <?= e($f->all()['ruleSetVersion']) ?>.</p>
</section>
<?= View::component('form', ['type' => 'contact', 'topic' => 'Fördercheck', 'page' => $page, 'company' => $company, 'content' => $content, 'heading' => 'Vorhaben mit HSM Tec besprechen']) ?>
</div>
