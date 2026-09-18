<?php use App\Services\View; $icon = fn($n) => View::component('icon', ['name' => $n]); $iconMap = ['/heizung/' => 'heizung', '/sanitaer/' => 'bad', '/wasserschaden/' => 'wasser', '/sanierung/' => 'sanierung', '/fuer-hausverwaltungen-und-vermieter/' => 'verwaltung', '/fuer-gewerbekunden/' => 'gewerbe', '/foerderung/' => 'foerderung']; ?>
<section class="hero-dark hero-compact on-dark">
  <?= View::component('stripes', ['class' => 'stripes']) ?>
  <div class="wrap"><div class="hero-inner">
    <p class="eyebrow"><?= e($page['parent'] ? ($content->page($page['parent'])['title'] ?? 'Leistungen') : 'HSM Tec GmbH') ?></p>
    <h1><?= e($page['h1'] ?? $page['title']) ?></h1>
    <?php if (!empty($page['lead'])): ?><p class="lead"><?= e($page['lead']) ?></p><?php endif; ?>
    <p class="btn-row"><a class="btn btn-primary" href="#anfrage"><?= ($page['form'] ?? '') === 'damage' ? 'Schaden melden' : 'Anfrage stellen' ?> <?= $icon('arrow') ?></a> <a class="btn btn-ghost" href="tel:<?= e($company['phoneE164']) ?>"><?= $icon('phone') ?> <?= e($company['phoneDisplay']) ?></a></p>
  </div></div>
</section>
<div class="wrap">
<?php if (!empty($page['emergencyHint'])): ?><p class="notice notice-top"><?= e($page['emergencyHint']) ?></p><?php endif; ?>
<?php if (!empty($page['hubChildren'])): ?>
<section class="section" aria-labelledby="hub-h">
  <div class="section-head" data-reveal><p class="eyebrow">Themen</p><h2 id="hub-h">Unsere Leistungen im Detail</h2></div>
  <ul class="card-grid" data-reveal>
    <?php foreach ($page['hubChildren'] as $i => $cp): $c = $content->page($cp); if (!$c) continue; ?>
      <li class="card"><a href="<?= e($cp) ?>"><?php if (isset($iconMap[$cp])): ?><span class="card-icon"><?= $icon($iconMap[$cp]) ?></span><?php else: ?><span class="num"><?= sprintf('%02d', $i + 1) ?></span><?php endif; ?><h3><?= e($c['title']) ?></h3><p><?= e(mb_strimwidth($c['metaDescription'] ?? '', 0, 150, '…')) ?></p><span class="card-arrow">Mehr erfahren <?= $icon('arrow') ?></span></a></li>
    <?php endforeach; ?>
    <?php foreach ($page['extraLinks'] ?? [] as $x): ?>
      <li class="card card-muted"><a href="<?= e($x['path']) ?>"><?php if (isset($iconMap[$x['path']])): ?><span class="card-icon"><?= $icon($iconMap[$x['path']]) ?></span><?php endif; ?><h3><?= e($x['title']) ?></h3><span class="card-arrow">Zur Seite <?= $icon('arrow') ?></span></a></li>
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
