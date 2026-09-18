<?php use App\Services\View; if (empty($funding)) { return; } $fp = $content->page($funding); ?>
<aside class="callout callout-dark" aria-labelledby="funding-teaser" data-reveal>
  <?= View::component('stripes', ['class' => 'stripes-sm']) ?>
  <p class="eyebrow">Förderung</p>
  <h2 id="funding-teaser">Förderung prüfen</h2>
  <p>Für dieses Vorhaben kann eine Förderung infrage kommen. Voraussetzungen und Höhe hängen von Gebäude, Nutzung und Antragszeitpunkt ab. Eine Förderzusage erteilt ausschließlich der Fördergeber.</p>
  <p class="btn-row"><a class="btn btn-primary" href="/foerderrechner/">Förderung prüfen <?= View::component('icon', ['name' => 'arrow']) ?></a><?php if ($fp): ?> <a class="btn btn-ghost" href="<?= e($funding) ?>"><?= e($fp['title']) ?></a><?php endif; ?></p>
</aside>
