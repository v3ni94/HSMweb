<?php use App\Services\View; ?>
<div class="wrap">
<header class="page-head"><h1><?= e($page['h1']) ?></h1><p class="lead"><?= e($page['lead']) ?></p></header>
<section class="section" aria-labelledby="gf-h">
  <h2 id="gf-h">Geschäftsführung</h2>
  <ul class="people">
    <?php foreach ($company['management'] as $m): ?>
      <li class="person"><h3><?= e($m['name']) ?></h3><p class="role"><?= e($m['role']) ?></p><p><?= e($m['area']) ?>. <?= e($m['description']) ?></p></li>
    <?php endforeach; ?>
  </ul>
  <p class="hint">Porträts werden nach Freigabe ergänzt. Bis dahin verwenden wir textbasierte Profile.</p>
</section>
<?= View::component('sections', ['sections' => $page['sections'] ?? []]) ?>
<section class="section" aria-labelledby="hold-h">
  <h2 id="hold-h">Unternehmensgruppe</h2>
  <ul class="checklist">
    <?php foreach ($company['holdings'] as $h): ?><li><a href="<?= e($h['url']) ?>" rel="noopener"><?= e($h['name']) ?></a>, Zugehörigkeit seit <?= e($h['since']) ?></li><?php endforeach; ?>
  </ul>
</section>
<section class="section" aria-labelledby="fakt-h">
  <h2 id="fakt-h">Auf einen Blick</h2>
  <dl class="facts">
    <dt>Unternehmen</dt><dd><?= e($company['legalName']) ?></dd>
    <dt>Sitz</dt><dd><?= e($company['address']['street']) ?>, <?= e($company['address']['postalCode']) ?> <?= e($company['address']['city']) ?></dd>
    <dt>Telefon</dt><dd><a href="tel:<?= e($company['phoneE164']) ?>"><?= e($company['phoneDisplay']) ?></a></dd>
    <dt>E-Mail</dt><dd><a href="mailto:<?= e($company['email']) ?>"><?= e($company['email']) ?></a></dd>
    <dt>Leistungsbereiche</dt><dd>Heizung. Bad und Sanitär. Wasserschaden. Sanierung.</dd>
    <dt>Einsatzgebiet</dt><dd><?= e($company['areaServedLabel']) ?></dd>
  </dl>
</section>
<?= View::component('related', ['related' => ['/karriere/', '/kontakt/', '/leistungen/'], 'content' => $content]) ?>
<?= View::component('form', ['type' => 'contact', 'topic' => 'Allgemeine Anfrage', 'page' => $page, 'company' => $company, 'content' => $content]) ?>
</div>
