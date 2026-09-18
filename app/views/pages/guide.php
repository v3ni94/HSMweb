<?php use App\Services\View; ?>
<div class="wrap">
<article>
<header class="page-head"><div><p class="eyebrow">Ratgeber</p><h1><?= e($guide['title']) ?></h1><p class="meta">Veröffentlicht: <?= e(fmt_date($guide['published'])) ?>, aktualisiert: <?= e(fmt_date($guide['updated'])) ?></p><p class="lead"><?= e($guide['intro']) ?></p></div></header>
<div class="island island-read"><?= View::component('sections', ['sections' => $guide['sections']]) ?></div>
<?= View::component('faq', ['faq' => $guide['faq'] ?? []]) ?>
<?= View::component('related', ['related' => $guide['related'] ?? [], 'content' => $content]) ?>
</article>
<?= View::component('form', ['type' => 'contact', 'topic' => $page['formTopic'], 'page' => $page, 'company' => $company, 'content' => $content]) ?>
</div>
