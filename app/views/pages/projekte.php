<?php use App\Services\View; $projects = $content->projects(); ?>
<div class="wrap">
<header class="page-head"><div><p class="eyebrow">Projekte</p><h1><?= e($page['h1']) ?></h1><p class="lead"><?= e($page['lead']) ?></p></div></header>
<?php if ($projects): ?>
<section class="section"><ul class="card-grid">
  <?php foreach ($projects as $p): ?><li class="card"><h2><?= e($p['title']) ?></h2><p><?= e($p['summary']) ?></p></li><?php endforeach; ?>
</ul></section>
<?php else: ?>
<section class="section notice"><p>Derzeit sind keine Projekte zur Veröffentlichung freigegeben.</p></section>
<?php endif; ?>
<?= View::component('form', ['type' => 'contact', 'topic' => 'Allgemeine Anfrage', 'page' => $page, 'company' => $company, 'content' => $content]) ?>
</div>
