<?php /** @var array $steps */ if (empty($steps)) { return; } ?>
<section class="section" aria-labelledby="ablauf-heading">
  <div class="section-head" data-reveal><p class="eyebrow">Ablauf</p><h2 id="ablauf-heading"><?= e($heading ?? 'So läuft es ab') ?></h2></div>
  <ol class="steps<?= count($steps) <= 5 ? ' is-horizontal' : '' ?>" data-reveal>
    <?php foreach ($steps as $s): ?><li><h3><?= e($s['title']) ?></h3><p><?= e($s['text']) ?></p></li><?php endforeach; ?>
  </ol>
</section>
