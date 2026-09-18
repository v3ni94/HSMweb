<?php use App\Services\View; $topic = isset($_GET['thema']) && is_string($_GET['thema']) ? mb_substr($_GET['thema'], 0, 60) : $page['formTopic']; ?>
<div class="wrap">
<header class="page-head"><div><p class="eyebrow">Kontakt</p><h1><?= e($page['h1']) ?></h1><p class="lead"><?= e($page['lead']) ?></p></div></header>
<section class="section contact-grid">
  <div>
    <h2>HSM Tec GmbH</h2>
    <address class="contact-tiles">
      <a class="contact-tile" href="tel:<?= e($company['phoneE164']) ?>"><?= View::component('icon', ['name' => 'phone', 'class' => 'icon']) ?><span><span class="contact-tile-label">Telefon</span><span class="contact-tile-value"><?= e($company['phoneDisplay']) ?></span></span></a>
      <a class="contact-tile" href="mailto:<?= e($company['email']) ?>"><?= View::component('icon', ['name' => 'mail', 'class' => 'icon']) ?><span><span class="contact-tile-label">E-Mail</span><span class="contact-tile-value"><?= e($company['email']) ?></span></span></a>
      <a class="contact-tile" href="https://www.openstreetmap.org/search?query=<?= rawurlencode($company['address']['street'] . ', ' . $company['address']['postalCode'] . ' ' . $company['address']['city']) ?>" rel="noopener"><?= View::component('icon', ['name' => 'verwaltung', 'class' => 'icon']) ?><span><span class="contact-tile-label">Anschrift</span><span class="contact-tile-value"><?= e($company['address']['street']) ?>, <?= e($company['address']['postalCode']) ?> <?= e($company['address']['city']) ?></span><span class="contact-tile-note">Anfahrt in OpenStreetMap öffnen (externer Link, keine eingebettete Karte)</span></span></a>
    </address>
    <?php if (!empty($company['legal']['openingHours'])): ?><p>Geschäftszeiten: <?= e($company['legal']['openingHours']) ?></p><?php else: ?><p class="hint">Geschäftszeiten werden nach Freigabe ergänzt.</p><?php endif; ?>
  </div>
  <div>
    <div class="contact-block">
      <h2>Wasserschaden?</h2>
      <p>Nutzen Sie das Schadenformular mit Objektbezug und Schadenart oder rufen Sie an. Bei akutem Austritt zuerst den Hauptabsperrhahn schließen.</p>
      <p><a class="btn btn-secondary" href="/wasserschaden/#anfrage">Wasserschaden melden</a></p>
    </div>
    <div class="contact-block">
      <h2>Bewerbung?</h2>
      <p><a class="btn btn-ghost" href="/karriere/">Zu den offenen Stellen</a></p>
    </div>
  </div>
</section>
<?= View::component('form', ['type' => 'contact', 'topic' => $topic, 'page' => $page, 'company' => $company, 'content' => $content]) ?>
</div>
