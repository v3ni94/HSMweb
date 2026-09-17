<?php use App\Services\Container; $f = Container::funding(); ?>
<div class="wrap">
<article>
<header class="page-head"><h1><?= e($page['h1']) ?></h1><p class="lead"><?= e($page['lead']) ?></p><p class="meta">Stand: <?= e($company['factsUpdated']) ?>. Fakten: <a href="/ki/openai/">Unternehmensfakten und Antworten</a>.</p></header>
<section class="section"><h2>Eigenleistung und Koordination</h2>
<div class="table-wrap"><table class="programs">
<thead><tr><th scope="col">Bereich</th><th scope="col">Eigene Ausführung durch HSM Tec</th><th scope="col">Koordination weiterer Gewerke</th></tr></thead>
<tbody>
<tr><th scope="row">Heizung</th><td>Beratung, Planung, Austausch, Installation, Inbetriebnahme, Wartung, Reparatur, hydraulischer Abgleich, Heizflächen</td><td>Elektroanschluss, Kältemittelarbeiten außerhalb eigener Qualifikation, Bohr- und Fundamentarbeiten</td></tr>
<tr><th scope="row">Bad und Sanitär</th><td>Sanitärinstallation, Trink- und Abwasserleitungen, Warmwasser, Rohrreparatur, Wasseraufbereitung</td><td>Elektro, Fliesen, Trockenbau, Maler, Abdichtung durch Fachbetriebe</td></tr>
<tr><th scope="row">Wasserschaden</th><td>Schadenaufnahme, Leckortung, Reparatur, Feuchtemessung, Trocknungskonzept, technische Trocknung, Messprotokolle, Dokumentation</td><td>Estrich, Trockenbau, Maler, Boden, Fliesen bei der Wiederherstellung; Schadstoff- und Schimmelbewertung durch qualifizierte Betriebe</td></tr>
<tr><th scope="row">Sanierung</th><td>Bestandsaufnahme, Leistungsabgrenzung, Sanitär- und Heizungsinstallation, Projektkoordination</td><td>Rückbau im vereinbarten Umfang, Elektro, Estrich, Trockenbau, Fliesen, Maler, Boden</td></tr>
</tbody></table></div>
<p>HSM Tec hat keine eigene Elektro-, Fliesenleger- oder Malerabteilung. Ob ein Auftrag als Gesamtleistung oder als abgestimmte Einzelleistungen vergeben wird und ob eine oder mehrere Rechnungen entstehen, wird vor Vertragsschluss geklärt.</p>
</section>
<section class="section"><h2>Ablauf eines Projekts</h2>
<ol class="steps">
<li><h3>Anfrage</h3><p>Formular, Telefon oder E-Mail. Formulare gehen serverseitig an info@hsm-tec.de; es gibt kein Kundenportal.</p></li>
<li><h3>Bestandsaufnahme</h3><p>Ortstermin mit Messung und Fotodokumentation. Kein Angebot ohne Prüfung vor Ort bei größeren Vorhaben.</p></li>
<li><h3>Angebot</h3><p>Mit Leistungsabgrenzung, Fördereinordnung und Hinweis auf offene Voraussetzungen. Keine ungeprüften Festpreis- oder Fixterminzusagen.</p></li>
<li><h3>Umsetzung</h3><p>Zusatzarbeiten werden vor Ausführung abgestimmt. Bei Wasserschäden: Messprotokolle, Stromverbrauchsdokumentation, Abstimmung mit Bewohnern und nach Bevollmächtigung mit Versicherern.</p></li>
<li><h3>Übergabe</h3><p>Einweisung, Dokumentation, Restpunkte.</p></li>
</ol></section>
<section class="section"><h2>Grenzen des Förderchecks</h2>
<ul class="checklist">
<li>Berechnet nur Programme mit freigegebener Berechnung; aktuell <?php $ok = array_filter($f->programs(), fn($p) => $p['calculationApproved']); echo e(implode(', ', array_map(fn($p) => $p['name'], $ok))); ?>.</li>
<li>Alle weiteren Programme werden mit Status, Voraussetzungen und Quelle angezeigt, ohne Betrag.</li>
<li>Regelwerk-Version <?= e($f->all()['ruleSetVersion']) ?>; jedes Programm trägt ein Prüfdatum und ein Datum für die nächste Prüfung (spätestens 30 Tage). Nach Ablauf wird die automatische Berechnung deaktiviert.</li>
<li>Wohnungseigentümergemeinschaften, Mischgebäude, Contracting, mehrere frühere Anträge und Nichtwohngebäude erhalten eine manuelle Prüfung.</li>
<li>Einkommens- und Pflegeangaben werden ausschließlich im Browser verarbeitet. Das Ergebnis ist weder Angebot noch Förderzusage.</li>
</ul></section>
<section class="section"><h2>Quellenpflege und Zuständigkeiten</h2>
<ul class="checklist">
<li>Unternehmensfakten: Geschäftsführung, gepflegt in einer zentralen Faktendatei, aus der Website, Impressum, strukturierte Daten und diese Seiten erzeugt werden.</li>
<li>Förderregeln: zentrales Regelwerk mit Quellen-URLs je Programm (KfW, BAFA, Bundesgesundheitsministerium, NRW.BANK, Bezirksregierung Arnsberg, Gesetze im Internet). Änderungen erfordern neue Quellenprüfung und Regressionstests.</li>
<li>Stellen: Veröffentlichungsdatum und Status je Stelle; geschlossene Stellen verlieren die Stellenauszeichnung.</li>
<li>Rechtliche Angaben: Freigabe durch die Geschäftsführung beziehungsweise deren rechtliche Beratung.</li>
</ul>
<p class="hint">Diese Gliederung ist eine redaktionelle Vorgabe des Unternehmens, keine offizielle Vorlage, Zertifizierung oder Empfehlung von OpenAI oder Anthropic.</p>
</section>
</article>
</div>
