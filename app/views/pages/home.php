<?php use App\Services\View; $icon = fn(string $n, string $c = '') => View::component('icon', ['name' => $n, 'class' => $c]); ?>
<section class="hero-dark">
  <?= View::component('stripes', ['class' => 'stripes']) ?>
  <div class="wrap">
    <div class="hero-inner">
      <p class="eyebrow">HSM Tec GmbH, Düren</p>
      <h1><?= e($page['h1']) ?></h1>
      <p class="lead"><?= e($page['lead']) ?></p>
      <p class="btn-row">
        <a class="btn btn-primary" href="/kontakt/#anfrage">Projekt anfragen <?= $icon('arrow') ?></a>
        <a class="btn btn-secondary" href="/foerderrechner/">Förderung prüfen</a>
        <a class="btn btn-ghost" href="/wasserschaden/#anfrage">Wasserschaden melden</a>
      </p>
    </div>
    <div class="hero-cards" data-reveal>
      <?php foreach ($page['areas'] as $i => $a): ?>
        <a class="hero-card<?= $i === 2 ? ' is-primary' : '' ?>" href="<?= e($a['path']) ?>"><strong><?= e($a['title']) ?></strong><span><?= e($a['text']) ?></span></a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<div class="wrap">
<section class="section" aria-labelledby="areas-h">
  <div class="section-head" data-reveal>
    <p class="eyebrow">Leistungen</p>
    <h2 id="areas-h">Vier Leistungsbereiche, ein Ansprechpartner</h2>
    <p>Wir führen Heizung und Sanitär selbst aus und koordinieren die weiteren Gewerke. So bleibt Ihr Projekt in einer Hand, mit klarer Abgrenzung im Angebot.</p>
  </div>
  <ul class="card-grid areas">
    <?php $icons = ['heizung', 'bad', 'wasser', 'sanierung']; foreach ($page['areas'] as $i => $a): ?>
      <li class="card" data-reveal><a href="<?= e($a['path']) ?>"><span class="card-icon"><?= $icon($icons[$i]) ?></span><span class="num">0<?= $i + 1 ?></span><h3><?= e($a['title']) ?></h3><p><?= e($a['text']) ?></p><span class="card-arrow">Mehr erfahren <?= $icon('arrow') ?></span></a></li>
    <?php endforeach; ?>
  </ul>
</section>

</div>
<section class="band finder" aria-labelledby="finder-h" data-projectfinder data-reveal>
  <div class="wrap finder-inner">
  <div>
    <p class="eyebrow">Projektfinder</p>
    <h2 id="finder-h">Was haben Sie vor?</h2>
    <p>Wählen Sie Ihr Thema. Sie erhalten die passenden nächsten Schritte und ein vorbereitetes Formular.</p>
    <div class="finder-options" role="tablist" aria-label="Thema wählen">
      <button type="button" class="chip" role="tab" id="finder-tab-heizung" aria-controls="finder-panel-heizung" data-finder="heizung" aria-selected="false"><?= $icon('heizung') ?> Heizung</button>
      <button type="button" class="chip" role="tab" id="finder-tab-bad" aria-controls="finder-panel-bad" data-finder="bad" aria-selected="false"><?= $icon('bad') ?> Bad</button>
      <button type="button" class="chip" role="tab" id="finder-tab-wasserschaden" aria-controls="finder-panel-wasserschaden" data-finder="wasserschaden" aria-selected="false"><?= $icon('wasser') ?> Wasserschaden</button>
      <button type="button" class="chip" role="tab" id="finder-tab-sanierung" aria-controls="finder-panel-sanierung" data-finder="sanierung" aria-selected="false"><?= $icon('sanierung') ?> Sanierung</button>
    </div>
  </div>
  <div class="finder-panels">
    <div class="finder-placeholder" data-finder-placeholder><p>Thema wählen, dann erscheinen hier die nächsten Schritte.</p></div>
    <div class="finder-result island" role="tabpanel" id="finder-panel-heizung" aria-labelledby="finder-tab-heizung" data-finder-panel="heizung" hidden>
      <h3>Heizung: nächste Schritte</h3>
      <ol><li>Typenschild und Heizraum fotografieren, Verbrauch der letzten Jahre bereitlegen.</li><li>Ziel klären: Reparatur, Wartung, Modernisierung oder Wärmepumpe.</li><li>Anfrage senden, wir vereinbaren einen Ortstermin zur Bestandsaufnahme.</li></ol>
      <p class="btn-row"><a class="btn btn-primary" href="/kontakt/?thema=Heizung#anfrage">Heizungsanfrage stellen <?= $icon('arrow') ?></a> <a class="btn btn-ghost" href="/heizung/">Zum Bereich Heizung</a> <a class="btn btn-ghost" href="/foerderrechner/">Förderung prüfen</a></p>
    </div>
    <div class="finder-result island" role="tabpanel" id="finder-panel-bad" aria-labelledby="finder-tab-bad" data-finder-panel="bad" hidden>
      <h3>Bad: nächste Schritte</h3>
      <ol><li>Wünsche sammeln: Dusche oder Wanne, barrierearm, Ausstattung.</li><li>Grundriss oder Maße und Fotos des Bads bereitlegen.</li><li>Anfrage senden, wir planen mit Ihnen Ablauf und Leistungsabgrenzung.</li></ol>
      <p class="btn-row"><a class="btn btn-primary" href="/kontakt/?thema=Badsanierung#anfrage">Badanfrage stellen <?= $icon('arrow') ?></a> <a class="btn btn-ghost" href="/sanitaer/badsanierung/">Zur Badsanierung</a></p>
    </div>
    <div class="finder-result island" role="tabpanel" id="finder-panel-wasserschaden" aria-labelledby="finder-tab-wasserschaden" data-finder-panel="wasserschaden" hidden>
      <h3>Wasserschaden: nächste Schritte</h3>
      <ol><li>Bei laufendem Austritt: Hauptabsperrhahn schließen, Strom im Bereich abschalten lassen.</li><li>Schaden fotografieren, Zeitpunkt notieren, Versicherung informieren.</li><li>Schaden melden, wir vereinbaren die Schadenaufnahme.</li></ol>
      <p class="btn-row"><a class="btn btn-primary" href="/wasserschaden/#anfrage">Wasserschaden melden <?= $icon('arrow') ?></a> <a class="btn btn-ghost" href="/ratgeber/wie-laeuft-eine-leckortung-ab/">Ablauf einer Leckortung</a></p>
    </div>
    <div class="finder-result island" role="tabpanel" id="finder-panel-sanierung" aria-labelledby="finder-tab-sanierung" data-finder-panel="sanierung" hidden>
      <h3>Sanierung: nächste Schritte</h3>
      <ol><li>Umfang grob beschreiben: Wohnung, Haus, einzelne Räume.</li><li>Bekannte Probleme notieren: Leitungen, Heizung, Feuchte.</li><li>Anfrage senden, wir vereinbaren eine Bestandsaufnahme mit Leistungsabgrenzung.</li></ol>
      <p class="btn-row"><a class="btn btn-primary" href="/kontakt/?thema=Sanierung#anfrage">Sanierungsanfrage stellen <?= $icon('arrow') ?></a> <a class="btn btn-ghost" href="/sanierung/">Zum Bereich Sanierung</a></p>
    </div>
    <noscript><p>Ohne JavaScript: Wählen Sie oben einen Leistungsbereich oder nutzen Sie direkt das <a href="#anfrage">Anfrageformular</a>.</p></noscript>
  </div>
  </div>
</section>
<div class="wrap">

<section class="section" aria-labelledby="ablauf-heading">
  <div class="section-head" data-reveal><p class="eyebrow">Ablauf</p><h2 id="ablauf-heading">Der Projektablauf</h2><p>Von der ersten Anfrage bis zur Übergabe in fünf nachvollziehbaren Schritten.</p></div>
  <ol class="steps is-horizontal" data-reveal>
    <?php foreach ($page['process'] as $s): ?><li><h3><?= e($s['title']) ?></h3><p><?= e($s['text']) ?></p></li><?php endforeach; ?>
  </ol>
</section>

</div>
<section class="band band-callout" data-reveal>
  <div class="wrap">
    <?= View::component('stripes', ['class' => 'stripes-sm']) ?>
    <p class="eyebrow">Klar geregelt</p>
    <h2>Was „aus einer Hand“ bei uns bedeutet</h2>
    <p><?= e($page['oneHandExplained']) ?></p>
  </div>
</section>
<div class="wrap">

<section class="section" aria-labelledby="gf-h">
  <div class="section-head" data-reveal><p class="eyebrow">Unternehmen</p><h2 id="gf-h">Geschäftsführung</h2></div>
  <ul class="people" data-reveal>
    <?php foreach ($company['management'] as $m): $ini = implode('', array_map(fn($w) => mb_substr($w, 0, 1), explode(' ', $m['name']))); ?>
      <li class="person"><span class="avatar" aria-hidden="true"><?= e($ini) ?></span><div><h3><?= e($m['name']) ?></h3><p class="role"><?= e($m['role']) ?></p><p><?= e($m['description']) ?></p></div></li>
    <?php endforeach; ?>
  </ul>
  <p class="btn-row"><a class="btn btn-ghost" href="/unternehmen/">Mehr über das Unternehmen <?= $icon('arrow') ?></a></p>
</section>

<section class="section" aria-labelledby="fc-h">
  <ul class="card-grid" data-reveal>
    <li class="card island"><div class="card-body"><span class="card-icon"><?= $icon('foerderung') ?></span><h3 id="fc-h">Fördercheck</h3><p>Heizungstausch, Bad und Barriereabbau, energetische Sanierung: In wenigen Schritten sehen Sie, welche Programme infrage kommen und was noch zu prüfen ist. Ohne Registrierung, alle Angaben bleiben im Browser.</p><a class="btn btn-primary" href="/foerderrechner/">Förderung prüfen <?= $icon('arrow') ?></a></div></li>
    <li class="card"><div class="card-body"><span class="card-icon"><?= $icon('verwaltung') ?></span><h3>Für Hausverwaltungen und Vermieter</h3><p>Objektzuordnung, Zugangstermine, Bewohnerabstimmung, Fotodokumentation und klare Auftragsabgrenzung.</p><a class="card-arrow" href="/fuer-hausverwaltungen-und-vermieter/">Zusammenarbeit ansehen <?= $icon('arrow') ?></a></div></li>
    <li class="card"><div class="card-body"><span class="card-icon"><?= $icon('karriere') ?></span><h3>Karriere</h3><p>Anlagenmechaniker SHK, Kundendienstmonteure, Leckorter, Trocknungstechniker, Büro und Projektleitung. Kurzbewerbung ohne Anschreiben.</p><a class="card-arrow" href="/karriere/">Offene Stellen <?= $icon('arrow') ?></a></div></li>
  </ul>
</section>

<?php $projects = $content->projects(); if ($projects): ?>
<section class="section" aria-labelledby="pr-h">
  <div class="section-head"><p class="eyebrow">Projekte</p><h2 id="pr-h">Freigegebene Projekte</h2></div>
  <ul class="card-grid"><?php foreach (array_slice($projects, 0, 3) as $p): ?><li class="card"><div class="card-body"><h3><?= e($p['title']) ?></h3><p><?= e($p['summary']) ?></p></div></li><?php endforeach; ?></ul>
  <p><a href="/projekte/">Alle Projekte</a></p>
</section>
<?php endif; ?>

<?= View::component('faq', ['faq' => $page['faq']]) ?>
<?= View::component('form', ['type' => 'contact', 'topic' => $page['formTopic'], 'page' => $page, 'company' => $company, 'content' => $content, 'heading' => 'Kurz anfragen']) ?>
</div>
