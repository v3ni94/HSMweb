<div class="wrap">
<header class="page-head"><h1><?= e($page['title']) ?></h1>
<?php if ($code === 404): ?><p class="lead">Die aufgerufene Adresse existiert nicht oder wurde verschoben.</p>
<?php elseif ($code === 410): ?><p class="lead">Dieser Inhalt wurde dauerhaft entfernt und hat keinen Nachfolger.</p>
<?php else: ?><p class="lead">Es ist ein technischer Fehler aufgetreten. Bitte versuchen Sie es später erneut oder rufen Sie uns an.</p><?php endif; ?>
</header>
<section class="section">
  <h2>Weiter geht es hier</h2>
  <ul class="checklist"><li><a href="/">Startseite</a></li><li><a href="/leistungen/">Leistungen</a></li><li><a href="/foerderung/">Förderung</a></li><li><a href="/kontakt/">Kontakt</a></li></ul>
  <p>Telefon: <a href="tel:<?= e($company['phoneE164']) ?>"><?= e($company['phoneDisplay']) ?></a></p>
</section>
</div>
