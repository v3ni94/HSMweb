<?php use App\Services\View; $init = $job['type'] === 'initiative'; ?>
<div class="wrap">
<article>
<header class="page-head"><div><p class="eyebrow">Karriere</p>
  <h1><?= e($job['title']) ?><?= $init ? '' : ' (m/w/d)' ?></h1>
  <?php if (!$open): ?><p class="notice">Diese Stelle ist derzeit nicht ausgeschrieben. Sie können sich gerne <a href="/karriere/initiativbewerbung/">initiativ bewerben</a>.</p><?php endif; ?>
  <p class="lead"><?= e($job['summary']) ?></p>
  <dl class="facts">
    <dt>Arbeitsort</dt><dd><?= e($job['location']) ?></dd>
    <dt>Beschäftigungsmodell</dt><dd><?= e($job['employmentType']) ?></dd>
    <dt>Veröffentlicht</dt><dd><?= e(fmt_date($job['datePosted'])) ?></dd>
    <?php if (!empty($job['aliasTitles'])): ?><dt>Auch bekannt als</dt><dd><?= e(implode(', ', $job['aliasTitles'])) ?></dd><?php endif; ?>
  </dl>
</div></header>
<?php if (!empty($job['tasks'])): ?><section class="section"><h2>Aufgaben</h2><ul class="checklist"><?php foreach ($job['tasks'] as $t): ?><li><?= e($t) ?></li><?php endforeach; ?></ul></section><?php endif; ?>
<?php if (!empty($job['required'])): ?><section class="section"><h2>Das bringen Sie mit</h2><ul class="checklist"><?php foreach ($job['required'] as $t): ?><li><?= e($t) ?></li><?php endforeach; ?></ul></section><?php endif; ?>
<?php if (!empty($job['desired'])): ?><section class="section"><h2>Wünschenswert</h2><ul class="checklist"><?php foreach ($job['desired'] as $t): ?><li><?= e($t) ?></li><?php endforeach; ?></ul></section><?php endif; ?>
<section class="section"><h2>Bewerbungsablauf</h2><p><?= e($job['process']) ?></p><p class="hint">Wir sprechen alle Geschlechter an. Entscheidend sind die tatsächlichen beruflichen Anforderungen.</p></section>
</article>
<?php if ($open): ?><?= View::component('form', ['type' => 'application', 'job' => $job, 'page' => $page, 'company' => $company, 'content' => $content, 'heading' => 'Kurzbewerbung: ' . $job['title']]) ?><?php endif; ?>
<p><a href="/karriere/">Alle Stellen</a></p>
</div>
