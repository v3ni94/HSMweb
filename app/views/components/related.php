<?php /** @var array $related @var \App\Services\Content $content */
$items = [];
foreach ($related ?? [] as $path) {
    if ($p = $content->page($path)) { $items[] = ['path' => $path, 'title' => $p['title'], 'text' => $p['metaDescription'] ?? '']; continue; }
    if (preg_match('~^/ratgeber/([a-z0-9-]+)/$~', $path, $m) && ($g = $content->guide($m[1]))) { $items[] = ['path' => $path, 'title' => $g['title'], 'text' => $g['metaDescription']]; }
}
if (!$items) { return; } ?>
<section class="section" aria-labelledby="related-heading">
  <h2 id="related-heading"><?= e($heading ?? 'Passende Themen') ?></h2>
  <ul class="card-grid">
    <?php foreach ($items as $it): ?>
      <li class="card"><a href="<?= e($it['path']) ?>"><h3><?= e($it['title']) ?></h3><p><?= e(mb_strimwidth($it['text'], 0, 140, '…')) ?></p></a></li>
    <?php endforeach; ?>
  </ul>
</section>
