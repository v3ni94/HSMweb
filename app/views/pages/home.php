<?php use App\Services\View; ?>
<section class="hero">
  <div class="wrap hero-inner">
    <p class="eyebrow">HSM Tec GmbH, Düren</p>
    <h1><?= e($page['h1']) ?></h1>
    <p class="lead"><?= e($page['lead']) ?></p>
    <p class="btn-row">
      <a class="btn btn-primary" href="/kontakt/#anfrage">Projekt anfragen</a>
      <a class="btn btn-secondary" href="/foerderrechner/">Förderung prüfen</a>
      <a class="btn btn-ghost" href="/wasserschaden/#anfrage">Wasserschaden melden</a>
    </p>
  </div>
</section>
<div class="wrap">
<section class="section" aria-labelledby="areas-h">
  <h2 id="areas-h">Vier Leistungsbereiche, ein Ansprechpartner</h2>
  <ul class="card-grid areas">
    <?php foreach ($page['areas'] as $a): ?>
      <li class="card"><a href="<?= e($a['path']) ?>"><h3><?= e($a['title']) ?></h3><p><?= e($a['text']) ?></p></a></li>
    <?php endforeach; ?>
  </ul>
</section>

<section class="section finder" aria-labelledby="finder-h" data-projectfinder>
  <h2 id="finder-h">Projektfinder: Was haben Sie vor?</h2>
  <p>Wählen Sie Ihr Thema. Sie erhalten die passenden nächsten Schritte und ein vorbereitetes Formular.</p>
  <div class="finder-options" role="group" aria-label="Thema wählen">
    <button type="button" class="chip" data-finder="heizung" aria-pressed="false">Heizung</button>
    <button type="button" class="chip" data-finder="bad" aria-pressed="false">Bad</button>
    <button type="button" class="chip" data-finder="wasserschaden" aria-pressed="false">Wasserschaden</button>
    <button type="button" class="chip" data-finder="sanierung" aria-pressed="false">Sanierung</button>
  </div>
  <div class="finder-result" data-finder-panel="heizung" hidden>
    <h3>Heizung: nächste Schritte</h3>
    <ol><li>Typenschild und Heizraum fotografieren, Verbrauch der letzten Jahre bereitlegen.</li><li>Ziel klären: Reparatur, Wartung, Modernisierung oder Wärmepumpe.</li><li>Anfrage senden, wir vereinbaren einen Ortstermin zur Bestandsaufnahme.</li></ol>
    <p class="btn-row"><a class="btn btn-primary" href="/kontakt/?thema=Heizung#anfrage">Heizungsanfrage stellen</a> <a class="btn btn-ghost" href="/heizung/">Zum Bereich Heizung</a> <a class="btn btn-ghost" href="/foerderrechner/">Förderung prüfen</a></p>
  </div>
  <div class="finder-result" data-finder-panel="bad" hidden>
    <h3>Bad: nächste Schritte</h3>
    <ol><li>Wünsche sammeln: Dusche oder Wanne, barrierearm, Ausstattung.</li><li>Grundriss oder Maße und Fotos des Bads bereitlegen.</li><li>Anfrage senden, wir planen mit Ihnen Ablauf und Leistungsabgrenzung.</li></ol>
    <p class="btn-row"><a class="btn btn-primary" href="/kontakt/?thema=Badsanierung#anfrage">Badanfrage stellen</a> <a class="btn btn-ghost" href="/sanitaer/badsanierung/">Zur Badsanierung</a></p>
  </div>
  <div class="finder-result" data-finder-panel="wasserschaden" hidden>
    <h3>Wasserschaden: nächste Schritte</h3>
    <ol><li>Bei laufendem Austritt: Hauptabsperrhahn schließen, Strom im Bereich abschalten lassen.</li><li>Schaden fotografieren, Zeitpunkt notieren, Versicherung informieren.</li><li>Schaden melden, wir vereinbaren die Schadenaufnahme.</li></ol>
    <p class="btn-row"><a class="btn btn-primary" href="/wasserschaden/#anfrage">Wasserschaden melden</a> <a class="btn btn-ghost" href="/ratgeber/wie-laeuft-eine-leckortung-ab/">Ablauf einer Leckortung</a></p>
  </div>
  <div class="finder-result" data-finder-panel="sanierung" hidden>
    <h3>Sanierung: nächste Schritte</h3>
    <ol><li>Umfang grob beschreiben: Wohnung, Haus, einzelne Räume.</li><li>Bekannte Probleme notieren: Leitungen, Heizung, Feuchte.</li><li>Anfrage senden, wir vereinbaren eine Bestandsaufnahme mit Leistungsabgrenzung.</li></ol>
    <p class="btn-row"><a class="btn btn-primary" href="/kontakt/?thema=Sanierung#anfrage">Sanierungsanfrage stellen</a> <a class="btn btn-ghost" href="/sanierung/">Zum Bereich Sanierung</a></p>
  </div>
  <noscript><p>Ohne JavaScript: Wählen Sie den passenden Bereich oben oder nutzen Sie direkt das <a href="#anfrage">Anfrageformular</a>.</p></noscript>
</section>

<?= View::component('steps', ['steps' => $page['process'], 'heading' => 'Der Projektablauf']) ?>

<section class="section callout">
  <h2>Was „aus einer Hand“ bei uns bedeutet</h2>
  <p><?= e($page['oneHandExplained']) ?></p>
</section>

<section class="section" aria-labelledby="gf-h">
  <h2 id="gf-h">Geschäftsführung</h2>
  <ul class="people">
    <?php foreach ($company['management'] as $m): ?>
      <li class="person"><h3><?= e($m['name']) ?></h3><p class="role"><?= e($m['role']) ?></p><p><?= e($m['description']) ?></p></li>
    <?php endforeach; ?>
  </ul>
  <p><a href="/unternehmen/">Mehr über das Unternehmen</a></p>
</section>

<section class="section callout" aria-labelledby="fc-h">
  <h2 id="fc-h">Fördercheck</h2>
  <p>Heizungstausch, Bad und Barriereabbau, energetische Sanierung: Der regelbasierte Fördercheck zeigt in wenigen Schritten, welche Programme infrage kommen und welche Voraussetzungen zu prüfen sind. Ohne Registrierung, alle Angaben bleiben im Browser.</p>
  <p class="btn-row"><a class="btn btn-primary" href="/foerderrechner/">Förderung prüfen</a> <a class="btn btn-ghost" href="/foerderung/">Förderübersicht</a></p>
</section>

<?php $projects = $content->projects(); if ($projects): ?>
<section class="section" aria-labelledby="pr-h">
  <h2 id="pr-h">Projekte</h2>
  <ul class="card-grid">
    <?php foreach (array_slice($projects, 0, 3) as $p): ?>
      <li class="card"><h3><?= e($p['title']) ?></h3><p><?= e($p['summary']) ?></p></li>
    <?php endforeach; ?>
  </ul>
  <p><a href="/projekte/">Alle Projekte</a></p>
</section>
<?php endif; ?>

<section class="section" aria-labelledby="kar-h">
  <h2 id="kar-h">Karriere</h2>
  <p>Wir suchen Anlagenmechaniker SHK, Kundendienstmonteure, Leckorter, Trocknungstechniker und Verstärkung für Büro und Projektleitung. Kurzbewerbung ohne Anschreiben.</p>
  <p class="btn-row"><a class="btn btn-secondary" href="/karriere/">Offene Stellen</a></p>
</section>

<?= View::component('faq', ['faq' => $page['faq']]) ?>
<?= View::component('form', ['type' => 'contact', 'topic' => $page['formTopic'], 'page' => $page, 'company' => $company, 'content' => $content, 'heading' => 'Kurz anfragen']) ?>
</div>
