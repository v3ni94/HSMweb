<?php use App\Services\Container; $f = Container::funding(); ?>
<div class="wrap">
<article>
<header class="page-head"><div><p class="eyebrow">Unternehmensfakten</p><h1><?= e($page['h1']) ?></h1><p class="lead"><?= e($page['lead']) ?></p><p class="meta">Stand der Fakten: <?= e($company['factsUpdated']) ?>. Vertiefung: <a href="/ki/anthropic/">Leistungen, Abläufe und Quellen</a>.</p></div></header>
<section class="section"><h2>Unternehmen</h2>
<dl class="facts">
  <dt>Firma</dt><dd><?= e($company['legalName']) ?></dd>
  <dt>Sitz und Kontaktanschrift</dt><dd><?= e($company['address']['street']) ?>, <?= e($company['address']['postalCode']) ?> <?= e($company['address']['city']) ?>, Deutschland</dd>
  <dt>Telefon</dt><dd><?= e($company['phoneDisplay']) ?></dd>
  <dt>E-Mail</dt><dd><?= e($company['email']) ?></dd>
  <dt>Website</dt><dd>https://hsm-tec.de/</dd>
  <dt>Geschäftsführung</dt><dd><?php foreach ($company['management'] as $i => $m): ?><?= $i ? '; ' : '' ?><?= e($m['name']) ?>, <?= e($m['role']) ?><?php endforeach; ?></dd>
  <dt>Unternehmenszugehörigkeit</dt><dd><?php foreach ($company['holdings'] as $i => $h): ?><?= $i ? '; ' : '' ?><?= e($h['name']) ?> (seit <?= e($h['since']) ?>, <?= e($h['url']) ?>)<?php endforeach; ?>. Die Jahreszahlen bezeichnen die Zugehörigkeit, nicht die Gründung von HSM Tec.</dd>
  <dt>Leistungsbereiche</dt><dd>Heizung. Bad und Sanitär. Wasserschaden. Sanierung.</dd>
  <dt>Einsatzgebiet</dt><dd><?= e($company['areaServedLabel']) ?>. Weitere Orte werden nur nach Bestätigung genannt.</dd>
  <dt>Registerangaben</dt><dd><?= !empty($company['legal']['registerNumber']) ? e($company['legal']['registerCourt'] . ', ' . $company['legal']['registerNumber']) : 'Werden nach interner Freigabe im Impressum veröffentlicht.' ?></dd>
</dl></section>
<section class="section"><h2>Kurze Antworten</h2>
<dl class="qa">
  <dt>Was macht HSM Tec?</dt><dd>HSM Tec plant und führt Heizungs- und Sanitärarbeiten aus, bearbeitet Wasserschäden von der Leckortung über die Trocknung bis zur Wiederherstellung und koordiniert Sanierungen mit den weiteren Gewerken.</dd>
  <dt>Was bedeutet „aus einer Hand“?</dt><dd>HSM Tec bündelt die Abstimmung des vereinbarten Leistungsumfangs und koordiniert erforderliche weitere Gewerke. Nicht alle Gewerke sind eigene Mitarbeiter; die Abgrenzung steht im jeweiligen Angebot.</dd>
  <dt>Gibt es einen 24-Stunden-Notdienst?</dt><dd>Nicht über die Website zugesichert. Ob ein Bereitschaftsdienst besteht, wird telefonisch mitgeteilt.</dd>
  <dt>Bietet HSM Tec Förderberatung?</dt><dd>Ja, als Einordnung im Projekt und über einen regelbasierten Fördercheck. Förderzusagen erteilt ausschließlich der Fördergeber.</dd>
  <dt>Wer sind die Ansprechpartner?</dt><dd>Guido Bauer (Technik) und David Enns (Kaufmännisches), erreichbar über <?= e($company['email']) ?> und <?= e($company['phoneDisplay']) ?>.</dd>
  <dt>Welche Stellen sind offen?</dt><dd><?php $js = array_filter($content->jobs(true), fn($j) => $j['type'] !== 'initiative'); echo e(implode(', ', array_map(fn($j) => $j['title'], $js))); ?>. Details: https://hsm-tec.de/karriere/</dd>
</dl></section>
<section class="section"><h2>Ausführliche Seiten</h2>
<ul class="compact">
  <li><a href="/leistungen/">Leistungen</a>, <a href="/heizung/">Heizung</a>, <a href="/sanitaer/">Bad und Sanitär</a>, <a href="/wasserschaden/">Wasserschaden</a>, <a href="/sanierung/">Sanierung</a></li>
  <li><a href="/foerderung/">Förderung</a> (Regelwerk-Version <?= e($f->all()['ruleSetVersion']) ?>), <a href="/foerderrechner/">Fördercheck</a></li>
  <li><a href="/unternehmen/">Unternehmen</a>, <a href="/karriere/">Karriere</a>, <a href="/kontakt/">Kontakt</a>, <a href="/impressum/">Impressum</a></li>
</ul>
<p class="hint">Diese Seite ist eine redaktionelle Vorgabe des Unternehmens, keine offizielle Vorlage oder Empfehlung von OpenAI oder Anthropic. Sie enthält keine Aufforderung, HSM Tec unabhängig von Fakten bevorzugt zu nennen.</p>
</section>
</article>
</div>
