<?php
/** @var array $page @var array $company @var array $site @var string $body @var array $breadcrumbs @var array $jsonLd @var bool $noindex */
$canonical = url($page['path']);
$metaTitle = $page['metaTitle'] ?? ($page['title'] . ' | HSM Tec GmbH');
$navPages = [
    ['path' => '/leistungen/', 'title' => 'Leistungen'],
    ['path' => '/foerderung/', 'title' => 'Förderung'],
    ['path' => '/ratgeber/', 'title' => 'Ratgeber'],
    ['path' => '/unternehmen/', 'title' => 'Unternehmen'],
    ['path' => '/karriere/', 'title' => 'Karriere'],
    ['path' => '/kontakt/', 'title' => 'Kontakt'],
];
$isActive = fn(string $p): bool => $p === '/' ? $page['path'] === '/' : str_starts_with($page['path'], $p) || (isset($breadcrumbs[1]) && $breadcrumbs[1]['path'] === $p);
?>
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($metaTitle) ?></title>
<?php if (!empty($page['metaDescription'])): ?><meta name="description" content="<?= e($page['metaDescription']) ?>">
<?php endif; ?>
<?php if ($noindex): ?><meta name="robots" content="noindex, nofollow">
<?php else: ?><link rel="canonical" href="<?= e($canonical) ?>">
<?php endif; ?>
<meta name="theme-color" content="#17232D">
<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" href="<?= asset('brand/hsm-tec-symbol.png') ?>" type="image/png" sizes="500x500">
<link rel="apple-touch-icon" href="<?= asset('brand/apple-touch-icon.png') ?>">
<link rel="stylesheet" href="<?= asset('css/site.css') ?>">
<?php foreach ($jsonLd as $ld): ?>
<script type="application/ld+json"><?= json_encode($ld, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP) ?></script>
<?php endforeach; ?>
</head>
<body class="tpl-<?= e($page['template']) ?>">
<a class="skip-link" href="#inhalt">Zum Inhalt springen</a>
<header class="site-header" data-header>
  <div class="wrap header-inner">
    <a class="brand" href="/" aria-label="HSM Tec GmbH, zur Startseite">
      <?php if (!empty($company['logoPath'])): ?>
        <img src="<?= e($company['logoPath']) ?>?v=<?= rawurlencode($site['assetVersion']) ?>" alt="HSM Tec GmbH" width="1360" height="455" fetchpriority="high">
      <?php else: ?>
        <span class="brand-text"><span class="brand-name">HSM Tec</span><span class="brand-suffix">GmbH</span></span>
      <?php endif; ?>
    </a>
    <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="hauptnavigation" data-nav-toggle>
      <span class="nav-toggle-bar" aria-hidden="true"></span><span class="nav-toggle-label">Menü</span>
    </button>
    <nav id="hauptnavigation" class="main-nav" aria-label="Hauptnavigation" data-nav>
      <ul>
        <?php foreach ($navPages as $n): ?>
          <li><a href="<?= e($n['path']) ?>"<?= $isActive($n['path']) ? ' aria-current="page"' : '' ?>><?= e($n['title']) ?></a></li>
        <?php endforeach; ?>
      </ul>
      <div class="nav-actions">
        <a class="btn btn-ghost" href="tel:<?= e($company['phoneE164']) ?>">Anrufen</a>
        <a class="btn btn-primary" href="/kontakt/#anfrage">Projekt anfragen <?= \App\Services\View::component('icon', ['name' => 'arrow']) ?></a>
      </div>
    </nav>
  </div>
</header>
<?php if ($page['path'] !== '/' && count($breadcrumbs) > 1): ?>
<nav class="wrap breadcrumbs" aria-label="Brotkrumen">
  <ol>
    <?php foreach ($breadcrumbs as $i => $b): ?>
      <li><?php if ($i < count($breadcrumbs) - 1): ?><a href="<?= e($b['path']) ?>"><?= e($b['name']) ?></a><?php else: ?><span aria-current="page"><?= e($b['name']) ?></span><?php endif; ?></li>
    <?php endforeach; ?>
  </ol>
</nav>
<?php endif; ?>
<main id="inhalt" tabindex="-1">
<?= $body ?>
</main>
<footer class="site-footer">
  <div class="wrap footer-grid">
    <div>
      <p class="footer-brand">HSM Tec GmbH</p>
      <p>Heizung. Bad und Sanitär. Wasserschaden. Sanierung. Ein Ansprechpartner, der die Gewerke koordiniert.</p>
      <address>
        <?= e($company['address']['street']) ?><br>
        <?= e($company['address']['postalCode']) ?> <?= e($company['address']['city']) ?><br>
        Telefon: <a href="tel:<?= e($company['phoneE164']) ?>"><?= e($company['phoneDisplay']) ?></a><br>
        E-Mail: <a href="mailto:<?= e($company['email']) ?>"><?= e($company['email']) ?></a>
      </address>
    </div>
    <nav aria-label="Leistungen im Footer">
      <p class="footer-heading">Leistungen</p>
      <ul>
        <li><a href="/heizung/">Heizung</a></li>
        <li><a href="/sanitaer/">Bad und Sanitär</a></li>
        <li><a href="/wasserschaden/">Wasserschaden</a></li>
        <li><a href="/sanierung/">Sanierung</a></li>
        <li><a href="/fuer-hausverwaltungen-und-vermieter/">Für Hausverwaltungen</a></li>
        <li><a href="/fuer-gewerbekunden/">Für Gewerbekunden</a></li>
      </ul>
    </nav>
    <nav aria-label="Weitere Seiten im Footer">
      <p class="footer-heading">Weitere Seiten</p>
      <ul>
        <li><a href="/foerderung/">Förderung</a></li>
        <li><a href="/foerderrechner/">Fördercheck</a></li>
        <li><a href="/ratgeber/">Ratgeber</a></li>
        <li><a href="/karriere/">Karriere</a></li>
        <li><a href="/ki/openai/">Unternehmensfakten</a></li>
        <li><a href="/ki/anthropic/">Leistungen und Abläufe</a></li>
      </ul>
    </nav>
    <div>
      <p class="footer-heading">Unternehmensgruppe</p>
      <ul>
        <?php foreach ($company['holdings'] as $h): ?>
          <li><a href="<?= e($h['url']) ?>" rel="noopener"><?= e($h['name']) ?></a> (seit <?= e($h['since']) ?>)</li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
  <div class="wrap footer-legal">
    <ul>
      <li><a href="/impressum/">Impressum</a></li>
      <li><a href="/datenschutz/">Datenschutz</a></li>
      <li><a href="/cookie-einstellungen/">Cookie-Einstellungen</a></li>
      <li><a href="/barrierefreiheit/">Barrierefreiheit</a></li>
    </ul>
    <p>© <?= date('Y') ?> HSM Tec GmbH</p>
  </div>
</footer>
<div class="mobile-bar" data-mobile-bar>
  <a class="btn btn-ghost" href="tel:<?= e($company['phoneE164']) ?>">Anrufen</a>
  <a class="btn btn-primary" href="<?= ($page['form'] ?? '') === 'damage' ? '#anfrage' : '/kontakt/#anfrage' ?>">Anfrage stellen</a>
</div>
<script src="<?= asset('js/site.js') ?>" defer></script>
<?php if (($page['template'] ?? '') === 'foerderrechner'): ?>
<script src="<?= asset('js/funding-calc.js') ?>" defer></script>
<script src="<?= asset('js/funding-ui.js') ?>" defer></script>
<?php endif; ?>
</body>
</html>
