<?php use App\Services\View; $icon = fn($n) => View::component('icon', ['name' => $n]); $parent = $content->page($page['parent'] ?? ''); ?>
<div class="wrap">
<header class="page-head has-aside">
  <div>
    <?php if ($parent): ?><p class="eyebrow"><?= e($parent['title']) ?></p><?php endif; ?>
    <h1><?= e($page['h1'] ?? $page['title']) ?></h1>
    <p class="lead"><?= e($page['lead']) ?></p>
    <p class="btn-row"><a class="btn btn-primary" href="#anfrage"><?= ($page['form'] ?? '') === 'damage' ? 'Schaden melden' : 'Anfrage stellen' ?> <?= $icon('arrow') ?></a> <a class="btn btn-ghost" href="tel:<?= e($company['phoneE164']) ?>"><?= $icon('phone') ?> <?= e($company['phoneDisplay']) ?></a></p>
  </div>
  <aside class="facts-card" data-reveal>
    <h2>Auf einen Blick</h2>
    <dl>
      <dt>Leistungsbereich</dt><dd><?= e($parent['title'] ?? 'Leistungen') ?></dd>
      <dt>Ansprechpartner</dt><dd>HSM Tec GmbH, Düren</dd>
      <?php if (!empty($page['steps'])): ?><dt>Ablauf</dt><dd><?= count($page['steps']) ?> Schritte, von der Anfrage bis zur Übergabe</dd><?php endif; ?>
      <?php if (!empty($page['funding'])): ?><dt>Förderung</dt><dd>Prüfung möglich</dd><?php endif; ?>
      <dt>Kontakt</dt><dd><a href="mailto:<?= e($company['email']) ?>"><?= e($company['email']) ?></a></dd>
    </dl>
    <a class="btn btn-primary" href="#anfrage">Jetzt anfragen <?= $icon('arrow') ?></a>
  </aside>
</header>
<?= View::component('sections', ['sections' => $page['sections'] ?? []]) ?>
<?= View::component('steps', ['steps' => $page['steps'] ?? []]) ?>
<?php if (!empty($page['limits'])): ?>
<section class="section limits" data-reveal>
  <h2>Leistungsgrenzen und Hinweise</h2>
  <ul><?php foreach ($page['limits'] as $l): ?><li><?= e($l) ?></li><?php endforeach; ?></ul>
</section>
<?php endif; ?>
<?= View::component('funding-teaser', ['funding' => $page['funding'] ?? null, 'content' => $content]) ?>
<?= View::component('faq', ['faq' => $page['faq'] ?? []]) ?>
<?= View::component('related', ['related' => $page['related'] ?? [], 'content' => $content]) ?>
<?php if (($page['form'] ?? 'none') !== 'none'): ?><?= View::component('form', ['type' => $page['form'], 'topic' => $page['formTopic'] ?? 'Allgemeine Anfrage', 'page' => $page, 'company' => $company, 'content' => $content]) ?><?php endif; ?>
</div>
