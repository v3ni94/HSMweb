<?php /** @var array $sections */ foreach ($sections ?? [] as $s): ?>
<section class="section" data-reveal>
  <?php if (!empty($s['heading'])): ?><h2><?= e($s['heading']) ?></h2><?php endif; ?>
  <?php if (!empty($s['paragraphs'])): ?><?= paragraphs($s['paragraphs']) ?><?php endif; ?>
  <?php if (!empty($s['list'])): ?><ul class="checklist"><?php foreach ($s['list'] as $li): ?><li><?= e($li) ?></li><?php endforeach; ?></ul><?php endif; ?>
</section>
<?php endforeach; ?>
