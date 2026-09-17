<?php use App\Services\View; $jobs = $content->jobs(true); $groups = ['Handwerk und Technik' => ['anlagenmechaniker-shk', 'kundendienstmonteur', 'shk-meister'], 'Wasserschaden' => ['leckorter', 'trocknungstechniker'], 'Büro und Projektleitung' => ['buero-organisation', 'projektleitung-sanierung']]; ?>
<div class="wrap">
<header class="page-head"><h1><?= e($page['h1']) ?></h1><p class="lead"><?= e($page['lead']) ?></p></header>
<section class="section" aria-labelledby="jobs-h" data-jobfilter>
  <h2 id="jobs-h">Offene Stellen</h2>
  <div class="finder-options" role="group" aria-label="Stellen filtern">
    <button type="button" class="chip" data-filter="alle" aria-pressed="true">Alle</button>
    <?php foreach (array_keys($groups) as $g): ?><button type="button" class="chip" data-filter="<?= e($g) ?>" aria-pressed="false"><?= e($g) ?></button><?php endforeach; ?>
  </div>
  <ul class="card-grid">
    <?php foreach ($jobs as $j): if ($j['type'] === 'initiative') continue; $grp = 'Weitere'; foreach ($groups as $gn => $slugs) { if (in_array($j['slug'], $slugs, true)) { $grp = $gn; } } ?>
      <li class="card" data-group="<?= e($grp) ?>"><a href="/karriere/<?= e($j['slug']) ?>/"><h3><?= e($j['title']) ?> (m/w/d)</h3><p><?= e($j['summary']) ?></p><p class="meta"><?= e($j['employmentType']) ?>, <?= e($j['location']) ?></p></a></li>
    <?php endforeach; ?>
  </ul>
  <p class="hint">Heizungsbauer und Anlagenmechaniker SHK: Die Anforderungen sind bei uns identisch, deshalb gibt es eine gemeinsame Anzeige.</p>
  <p><a class="btn btn-secondary" href="/karriere/initiativbewerbung/">Initiativbewerbung</a></p>
</section>
<?= View::component('sections', ['sections' => $page['sections'] ?? []]) ?>
<section class="section">
  <h2>Was Sie von uns erwarten können</h2>
  <p>Klare Aufgaben, direkte Ansprechpartner in der Geschäftsführung und Projekte, in denen Sie Verantwortung übernehmen. Konkrete Angaben zu Vergütung, Arbeitszeit und weiteren Konditionen besprechen wir im Gespräch; wir veröffentlichen keine Versprechen, die wir nicht belegen können.</p>
</section>
<?= View::component('form', ['type' => 'application', 'page' => $page, 'company' => $company, 'content' => $content]) ?>
</div>
