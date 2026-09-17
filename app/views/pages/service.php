<?php use App\Services\View; ?>
<div class="wrap">
<header class="page-head">
  <h1><?= e($page['h1'] ?? $page['title']) ?></h1>
  <p class="lead"><?= e($page['lead']) ?></p>
  <p class="btn-row"><a class="btn btn-primary" href="#anfrage"><?= ($page['form'] ?? '') === 'damage' ? 'Schaden melden' : 'Anfrage stellen' ?></a> <a class="btn btn-ghost" href="tel:<?= e($company['phoneE164']) ?>">Anrufen: <?= e($company['phoneDisplay']) ?></a></p>
</header>
<?= View::component('sections', ['sections' => $page['sections'] ?? []]) ?>
<?= View::component('steps', ['steps' => $page['steps'] ?? []]) ?>
<?php if (!empty($page['limits'])): ?>
<section class="section limits">
  <h2>Leistungsgrenzen und Hinweise</h2>
  <ul><?php foreach ($page['limits'] as $l): ?><li><?= e($l) ?></li><?php endforeach; ?></ul>
</section>
<?php endif; ?>
<?= View::component('funding-teaser', ['funding' => $page['funding'] ?? null, 'content' => $content]) ?>
<?= View::component('faq', ['faq' => $page['faq'] ?? []]) ?>
<?= View::component('related', ['related' => $page['related'] ?? [], 'content' => $content]) ?>
<?php if (($page['form'] ?? 'none') !== 'none'): ?><?= View::component('form', ['type' => $page['form'], 'topic' => $page['formTopic'] ?? 'Allgemeine Anfrage', 'page' => $page, 'company' => $company, 'content' => $content]) ?><?php endif; ?>
</div>
