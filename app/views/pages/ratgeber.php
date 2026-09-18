<?php use App\Services\View; ?>
<div class="wrap">
<header class="page-head"><div><p class="eyebrow">Ratgeber</p><h1><?= e($page['h1']) ?></h1><p class="lead"><?= e($page['lead']) ?></p></div></header>
<section class="section"><ul class="card-grid">
  <?php foreach ($content->guides() as $g): ?>
    <li class="card"><a href="/ratgeber/<?= e($g['slug']) ?>/"><h2 class="h3"><?= e($g['title']) ?></h2><p><?= e($g['metaDescription']) ?></p><p class="meta">Aktualisiert: <?= e(fmt_date($g['updated'])) ?></p></a></li>
  <?php endforeach; ?>
</ul></section>
<?= View::component('form', ['type' => 'contact', 'topic' => 'Allgemeine Anfrage', 'page' => $page, 'company' => $company, 'content' => $content]) ?>
</div>
