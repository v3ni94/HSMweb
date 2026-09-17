<?php /** @var array $faq */ if (empty($faq)) { return; } ?>
<section class="section faq" aria-labelledby="faq-heading">
  <h2 id="faq-heading"><?= e($heading ?? 'Häufige Fragen') ?></h2>
  <div class="accordion" data-accordion>
    <?php foreach ($faq as $i => $f): $id = 'faq-' . $i . '-' . substr(md5($f['q']), 0, 6); ?>
      <details class="accordion-item">
        <summary id="<?= $id ?>"><?= e($f['q']) ?></summary>
        <div class="accordion-panel"><p><?= e($f['a']) ?></p></div>
      </details>
    <?php endforeach; ?>
  </div>
</section>
