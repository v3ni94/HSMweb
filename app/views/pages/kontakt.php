<?php use App\Services\View; $topic = isset($_GET['thema']) && is_string($_GET['thema']) ? mb_substr($_GET['thema'], 0, 60) : $page['formTopic']; ?>
<div class="wrap">
<header class="page-head"><div><p class="eyebrow">Kontakt</p><h1><?= e($page['h1']) ?></h1><p class="lead"><?= e($page['lead']) ?></p></div></header>
<section class="section contact-grid">
  <div>
    <h2>HSM Tec GmbH</h2>
    <address>
      <?= e($company['address']['street']) ?><br><?= e($company['address']['postalCode']) ?> <?= e($company['address']['city']) ?><br>
      Telefon: <a href="tel:<?= e($company['phoneE164']) ?>"><?= e($company['phoneDisplay']) ?></a><br>
      E-Mail: <a href="mailto:<?= e($company['email']) ?>"><?= e($company['email']) ?></a>
    </address>
    <p><a href="https://www.openstreetmap.org/search?query=<?= rawurlencode($company['address']['street'] . ', ' . $company['address']['postalCode'] . ' ' . $company['address']['city']) ?>" rel="noopener">Anfahrt in OpenStreetMap öffnen</a> (externer Link, keine eingebettete Karte)</p>
    <?php if (!empty($company['legal']['openingHours'])): ?><p>Geschäftszeiten: <?= e($company['legal']['openingHours']) ?></p><?php else: ?><p class="hint">Geschäftszeiten werden nach Freigabe ergänzt.</p><?php endif; ?>
  </div>
  <div>
    <h2>Wasserschaden?</h2>
    <p>Nutzen Sie das Schadenformular mit Objektbezug und Schadenart oder rufen Sie an. Bei akutem Austritt zuerst den Hauptabsperrhahn schließen.</p>
    <p><a class="btn btn-secondary" href="/wasserschaden/#anfrage">Wasserschaden melden</a></p>
    <h2>Bewerbung?</h2>
    <p><a href="/karriere/">Zu den offenen Stellen</a></p>
  </div>
</section>
<?= View::component('form', ['type' => 'contact', 'topic' => $topic, 'page' => $page, 'company' => $company, 'content' => $content]) ?>
</div>
