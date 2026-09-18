<?php
use App\Services\Container;
use App\Services\View;
$funding = Container::funding();
$programs = $funding->programs();
$filter = $page['programsFilter'] ?? null;
if ($filter) {
    $programs = array_values(array_filter($programs, function ($p) use ($filter) {
        foreach ($filter as $f) {
            if (str_starts_with($f, 'region:')) { if ($p['region'] === substr($f, 7)) return true; }
            elseif (in_array($f, $p['measures'], true)) return true;
        }
        return false;
    }));
}
$statusClass = fn(string $s) => 'status-' . preg_replace('/[^a-z]/', '', strtolower(str_replace(['ü', 'ä'], ['ue', 'ae'], $s)));
?>
<div class="wrap">
<header class="page-head"><div><p class="eyebrow">Förderung</p>
  <h1><?= e($page['h1']) ?></h1>
  <p class="lead"><?= e($page['lead']) ?></p>
  <p class="btn-row"><a class="btn btn-primary" href="/foerderrechner/">Förderung prüfen</a> <a class="btn btn-ghost" href="#anfrage">Vorhaben besprechen</a></p>
</div></header>
<?php if (!empty($page['hubChildren'])): ?>
<section class="section">
  <ul class="card-grid">
    <?php foreach ($page['hubChildren'] as $cp): $c = $content->page($cp); if (!$c) continue; ?>
      <li class="card"><a href="<?= e($cp) ?>"><h3><?= e($c['title']) ?></h3><p><?= e(excerpt($c['metaDescription'] ?? '', 150)) ?></p></a></li>
    <?php endforeach; ?>
  </ul>
</section>
<?php endif; ?>
<?= View::component('sections', ['sections' => $page['sections'] ?? []]) ?>
<section class="section" aria-labelledby="prog-h">
  <h2 id="prog-h">Programme im Überblick</h2>
  <p class="hint">Stand des Regelwerks: Version <?= e($funding->all()['ruleSetVersion']) ?>. Die Angaben stammen aus dem zentralen Förderregelwerk, das auch der Fördercheck verwendet. Prüfdatum je Programm siehe Tabelle. Eine Förderzusage erteilt ausschließlich der Fördergeber.</p>
  <div class="table-wrap island">
  <table class="programs">
    <thead><tr><th scope="col">Programm</th><th scope="col">Art</th><th scope="col">Status</th><th scope="col">Kerninhalt</th><th scope="col">Geprüft am</th></tr></thead>
    <tbody>
    <?php foreach ($programs as $p): ?>
      <tr>
        <th scope="row"><?= e($p['name']) ?><br><small><?= e($p['provider']) ?>, <?= e($p['region']) ?></small></th>
        <td data-label="Art"><?= e($p['fundingType']) ?></td>
        <td data-label="Status"><span class="status <?= $statusClass($p['status']) ?>"><?= e($p['status']) ?></span><?php if ($p['calculationApproved']): ?><br><small>Automatische Berechnung freigegeben</small><?php else: ?><br><small>Nur Information, keine automatische Berechnung</small><?php endif; ?></td>
        <td data-label="Kerninhalt">
          <ul class="compact">
          <?php if (isset($p['rates']['basePoints'])): ?><li>Grundförderung <?= (int)$p['rates']['basePoints'] ?> %</li><?php endif; ?>
          <?php if (isset($p['caps']['perPersonCents'])): ?><li>Bis <?= fmt_eur((int)$p['caps']['perPersonCents']) ?> je Person, zusammen bis <?= fmt_eur((int)$p['caps']['totalCapCents']) ?></li><?php endif; ?>
          <?php if (isset($p['caps']['loanPerUnitCents'])): ?><li>Kredit bis <?= fmt_eur((int)$p['caps']['loanPerUnitCents']) ?> je Wohneinheit</li><?php endif; ?>
          <?php if (isset($p['caps']['loanCents'])): ?><li>Darlehen bis <?= fmt_eur((int)$p['caps']['loanCents']) ?></li><?php endif; ?>
          <?php if (isset($p['caps']['maxRepaymentWaiverPoints'])): ?><li>Tilgungsnachlass bis <?= (int)$p['caps']['maxRepaymentWaiverPoints'] ?> %</li><?php endif; ?>
          <?php if (isset($p['rates']['totalPoints'])): ?><li><?= (int)$p['rates']['totalPoints'] ?> % über <?= (int)$p['rates']['years'] ?> Jahre, höchstens <?= fmt_eur((int)$p['caps']['totalCapCents']) ?></li><?php endif; ?>
          <?php foreach (($p['eligibility']['notes'] ?? []) as $n): ?><li><?= e($n) ?></li><?php endforeach; ?>
          </ul>
          <?php foreach ($p['sourceUrls'] as $u): ?><a class="source" href="<?= e($u) ?>" rel="noopener">Quelle</a> <?php endforeach; ?>
        </td>
        <td data-label="Geprüft am"><?= e(fmt_date($p['checkedAt'])) ?><br><small>Nächste Prüfung: <?= e(fmt_date($p['reviewDueAt'])) ?></small></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  </div>
</section>
<?= View::component('related', ['related' => $page['related'] ?? [], 'content' => $content]) ?>
<?= View::component('form', ['type' => 'contact', 'topic' => $page['formTopic'] ?? 'Förderung', 'page' => $page, 'company' => $company, 'content' => $content, 'heading' => 'Vorhaben mit HSM Tec besprechen']) ?>
</div>
