<?php use App\Services\View; ?>
<div class="wrap">
<header class="page-head"><div><p class="eyebrow">Rechtliches</p><h1><?= e($page['h1']) ?></h1><?php if (!empty($page['lead'])): ?><p class="lead"><?= e($page['lead']) ?></p><?php endif; ?></div></header>
<div class="island island-read"><?= View::component('sections', ['sections' => $page['sections'] ?? []]) ?></div>
<p>Allgemeiner Kontakt: <a href="/kontakt/">Kontaktseite</a>.</p>
</div>
