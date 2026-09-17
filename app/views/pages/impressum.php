<?php $l = $company['legal']; $missing = []; foreach (['registerCourt' => 'Registergericht', 'registerNumber' => 'Registernummer', 'vatId' => 'Umsatzsteuer-Identifikationsnummer', 'chamber' => 'Zuständige Kammer', 'supervisoryAuthority' => 'Aufsichtsbehörde'] as $k => $label) { if (empty($l[$k])) $missing[] = $label; } ?>
<div class="wrap">
<header class="page-head"><h1>Impressum</h1><p class="lead">Angaben gemäß § 5 DDG.</p></header>
<section class="section">
<dl class="facts">
  <dt>Anbieter</dt><dd><?= e($company['legalName']) ?></dd>
  <dt>Anschrift</dt><dd><?= e($company['address']['street']) ?><br><?= e($company['address']['postalCode']) ?> <?= e($company['address']['city']) ?></dd>
  <dt>Vertreten durch</dt><dd><?php foreach ($company['management'] as $i => $m): ?><?= $i ? ', ' : '' ?><?= e($m['name']) ?><?php endforeach; ?> (Geschäftsführer)</dd>
  <dt>Kontakt</dt><dd>Telefon: <a href="tel:<?= e($company['phoneE164']) ?>"><?= e($company['phoneDisplay']) ?></a><br>E-Mail: <a href="mailto:<?= e($company['email']) ?>"><?= e($company['email']) ?></a></dd>
  <?php if (!empty($l['registerNumber'])): ?><dt>Registereintrag</dt><dd><?= e($l['registerCourt']) ?>, <?= e($l['registerNumber']) ?></dd><?php endif; ?>
  <?php if (!empty($l['vatId'])): ?><dt>Umsatzsteuer-Identifikationsnummer</dt><dd><?= e($l['vatId']) ?></dd><?php endif; ?>
  <?php if (!empty($l['chamber'])): ?><dt>Zuständige Kammer</dt><dd><?= e($l['chamber']) ?></dd><?php endif; ?>
  <?php if (!empty($l['supervisoryAuthority'])): ?><dt>Aufsichtsbehörde</dt><dd><?= e($l['supervisoryAuthority']) ?></dd><?php endif; ?>
  <?php if (!empty($l['responsibleForContent'])): ?><dt>Verantwortlich für den Inhalt</dt><dd><?= e($l['responsibleForContent']) ?></dd><?php endif; ?>
</dl>
<?php if ($missing): ?>
<div class="notice"><p><strong>Vor Veröffentlichung zu ergänzen:</strong> <?= e(implode(', ', $missing)) ?>. Diese Angaben werden intern freigegeben; ohne sie ist das Impressum unvollständig (siehe Release-Checkliste).</p></div>
<?php endif; ?>
</section>
<section class="section">
  <h2>Unternehmensgruppe</h2>
  <p><?php foreach ($company['holdings'] as $i => $h): ?><?= $i ? ', ' : '' ?><?= e($h['name']) ?> (seit <?= e($h['since']) ?>)<?php endforeach; ?>.</p>
  <h2>Streitbeilegung</h2>
  <p>Die Plattform der Europäischen Kommission zur Online-Streitbeilegung wurde eingestellt; ein Link darauf entfällt. Ob und in welchem Umfang wir zur Teilnahme an Verfahren vor einer Verbraucherschlichtungsstelle verpflichtet oder bereit sind, wird gesondert geprüft und hier ergänzt.</p>
  <h2>Haftung für Inhalte und Links</h2>
  <p>Wir erstellen die Inhalte dieser Website mit Sorgfalt. Für externe Links übernehmen wir keine Gewähr; für deren Inhalte sind die jeweiligen Anbieter verantwortlich. Förderangaben sind Einschätzungen auf Basis der genannten Quellen und keine Zusage.</p>
</section>
</div>
