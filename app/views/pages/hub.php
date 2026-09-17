<?php use App\Services\View; ?>
<div class="wrap">
<header class="page-head">
  <h1><?= e($page['h1'] ?? $page['title']) ?></h1>
  <?php if (!empty($page['lead'])): ?><p class="lead"><?= e($page['lead']) ?></p><?php endif; ?>
  <?php if (!empty($page['emergencyHint'])): ?><p class="notice"><?= e($page['emergencyHint']) ?></p><?php endif; ?>
</header>
<?php if (!empty($page['hubChildren'])): ?>
<section class="section" aria-labelledby="hub-h">
  <h2 id="hub-h" class="visually-hidden">Teilbereiche</h2>
  <ul class="card-grid">
    <?php foreach ($page['hubChildren'] as $cp): $c = $content->page($cp); if (!$c) continue; ?>
      <li class="card"><a href="<?= e($cp) ?>"><h3><?= e($c['title']) ?></h3><p><?= e(mb_strimwidth($c['metaDescription'] ?? '', 0, 150, '…')) ?></p></a></li>
    <?php endforeach; ?>
    <?php foreach ($page['extraLinks'] ?? [] as $x): ?>
      <li class="card card-muted"><a href="<?= e($x['path']) ?>"><h3><?= e($x['title']) ?></h3></a></li>
    <?php endforeach; ?>
  </ul>
</section>
<?php endif; ?>
<?= View::component('sections', ['sections' => $page['sections'] ?? []]) ?>
<?= View::component('funding-teaser', ['funding' => $page['funding'] ?? null, 'content' => $content]) ?>
<?= View::component('related', ['related' => $page['related'] ?? [], 'content' => $content]) ?>
<?= View::component('faq', ['faq' => $page['faq'] ?? []]) ?>
<?php if (($page['form'] ?? 'none') !== 'none'): ?><?= View::component('form', ['type' => $page['form'], 'topic' => $page['formTopic'] ?? 'Allgemeine Anfrage', 'page' => $page, 'company' => $company, 'content' => $content]) ?><?php endif; ?>
</div>
