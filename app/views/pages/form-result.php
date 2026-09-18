<?php use App\Services\View; $label = match ($type) { 'damage' => 'Schadenmeldung', 'application' => 'Bewerbung', default => 'Anfrage' }; ?>
<div class="wrap">
<header class="page-head"><h1><?= e($page['title']) ?></h1></header>
<?php if ($ok): ?>
<section class="section notice-ok island" role="status">
  <p>Ihre <?= e($label) ?> wurde an unser Postfach übergeben. Wenn Sie eine E-Mail-Adresse angegeben haben, erhalten Sie eine kurze Eingangsbestätigung. Wir melden uns zu unseren Geschäftszeiten.</p>
  <p>Die Übergabe an den Mailserver ist kein Nachweis, dass die Nachricht bereits gelesen wurde. Bei dringenden Anliegen: <a href="tel:<?= e($company['phoneE164']) ?>"><?= e($company['phoneDisplay']) ?></a>.</p>
  <p class="btn-row"><a class="btn btn-primary" href="<?= e($page['parent'] ?? '/') ?>">Zurück</a> <a class="btn btn-ghost" href="/">Startseite</a></p>
</section>
<?php else: ?>
<section class="section">
  <?php if ($reason === 'send' || $reason === 'csrf' || $reason === 'ratelimit'): ?>
  <div class="notice island" role="alert"><p><?= e(reset($errors)) ?></p><p>Telefon: <a href="tel:<?= e($company['phoneE164']) ?>"><?= e($company['phoneDisplay']) ?></a>, E-Mail: <a href="mailto:<?= e($company['email']) ?>"><?= e($company['email']) ?></a></p></div>
  <?php $errors = []; endif; ?>
  <?= View::component('form', ['type' => $type, 'topic' => $data['topic'] ?? 'Allgemeine Anfrage', 'data' => $data, 'errors' => $errors, 'page' => $page, 'company' => $company, 'content' => $content, 'job' => isset($data['jobSlug']) ? $content->job($data['jobSlug']) : null]) ?>
</section>
<?php endif; ?>
</div>
