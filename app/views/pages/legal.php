<?php use App\Services\View; ?>
<div class="wrap">
<header class="page-head"><h1><?= e($page['h1']) ?></h1><?php if (!empty($page['lead'])): ?><p class="lead"><?= e($page['lead']) ?></p><?php endif; ?></header>
<?= View::component('sections', ['sections' => $page['sections'] ?? []]) ?>
<p>Allgemeiner Kontakt: <a href="/kontakt/">Kontaktseite</a>.</p>
</div>
