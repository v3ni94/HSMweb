<?php /** @var string|null $funding */ if (empty($funding)) { return; } $fp = $content->page($funding); ?>
<aside class="section callout" aria-labelledby="funding-teaser">
  <h2 id="funding-teaser">Förderung prüfen</h2>
  <p>Für dieses Vorhaben kann eine Förderung infrage kommen. Voraussetzungen und Höhe hängen von Gebäude, Nutzung und Antragszeitpunkt ab. Eine Förderzusage erteilt ausschließlich der Fördergeber.</p>
  <p class="btn-row"><a class="btn btn-primary" href="/foerderrechner/">Förderung prüfen</a><?php if ($fp): ?> <a class="btn btn-ghost" href="<?= e($funding) ?>"><?= e($fp['title']) ?></a><?php endif; ?></p>
</aside>
