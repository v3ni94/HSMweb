<?php /** @var array $steps */ if (empty($steps)) { return; } ?>
<section class="section" aria-labelledby="ablauf-heading">
  <h2 id="ablauf-heading"><?= e($heading ?? 'So läuft es ab') ?></h2>
  <ol class="steps">
    <?php foreach ($steps as $s): ?>
      <li><h3><?= e($s['title']) ?></h3><p><?= e($s['text']) ?></p></li>
    <?php endforeach; ?>
  </ol>
</section>
