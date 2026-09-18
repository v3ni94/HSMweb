<?php /** @var array $faq */ if (empty($faq)) { return; } ?>
<section class="section faq" aria-labelledby="faq-heading">
  <div class="section-head" data-reveal><p class="eyebrow">Fragen</p><h2 id="faq-heading"><?= e($heading ?? 'Häufige Fragen') ?></h2></div>
  <div class="accordion" data-accordion data-reveal>
    <?php foreach ($faq as $i => $f): ?>
      <details class="accordion-item">
        <summary><?= e($f['q']) ?></summary>
        <div class="accordion-panel"><p><?= e($f['a']) ?></p></div>
      </details>
    <?php endforeach; ?>
  </div>
</section>
