<?php
/**
 * Wiederverwendbares Formular. $type: contact | damage | application
 * $topic: vorausgewähltes Thema; $data: vorherige Eingaben (bei Fehler); $errors: Feldfehler; $job: Stelle (application)
 */
$type = $type ?? 'contact';
$data = $data ?? [];
$errors = $errors ?? [];
$topic = $topic ?? 'Allgemeine Anfrage';
$csrf = \App\Services\Container::csrf()->token();
$sec = \App\Services\Container::config('security');
$uploads = \App\Services\Container::uploads();
$company = $company ?? \App\Services\Container::content()->company();
$action = match ($type) { 'damage' => '/wasserschaden/melden/', 'application' => '/karriere/bewerben/', default => '/anfrage/senden/' };
$heading = $heading ?? match ($type) { 'damage' => 'Wasserschaden melden', 'application' => 'Kurzbewerbung', default => 'Projekt anfragen' };
$fid = 'f' . substr(md5($type . $topic), 0, 6);
$val = fn(string $k) => e($data[$k] ?? '');
$err = fn(string $k) => isset($errors[$k]) ? '<p class="field-error" id="' . $fid . '-' . $k . '-err">' . e($errors[$k]) . '</p>' : '';
$aria = fn(string $k) => isset($errors[$k]) ? ' aria-invalid="true" aria-describedby="' . $fid . '-' . $k . '-err"' : '';
$contactTopics = ['Allgemeine Anfrage', 'Heizung', 'Wärmepumpe', 'Heizungsmodernisierung', 'Heizungswartung', 'Heizungsreparatur', 'Fußbodenheizung', 'Hydraulischer Abgleich', 'Heizkörper', 'Bad und Sanitär', 'Badsanierung', 'Barrierearmes Bad', 'Trinkwasserinstallation', 'Warmwasserbereitung', 'Rohrreparatur', 'Wasseraufbereitung', 'Sanierung', 'Komplettsanierung', 'Wohnungssanierung', 'Altbausanierung', 'Hausverwaltung', 'Gewerbekunden', 'Förderung', 'Fördercheck', 'Wasserschaden'];
$damageTopics = ['Leckortung', 'Rohrbruch', 'Feuchtigkeit', 'Technische Trocknung', 'Wiederherstellung', 'Unklare Ursache'];
$damageTypes = ['Rohrbruch / Leitungswasser', 'Feuchte Wand oder Decke', 'Wasser im Boden / Estrich', 'Undichte Stelle im Bad', 'Nasser Keller', 'Schaden nach Unwetter', 'Unklare Ursache'];
$selTopic = $data['topic'] ?? $topic;
?>
<section class="section form-section" id="anfrage" aria-labelledby="<?= $fid ?>-h">
  <h2 id="<?= $fid ?>-h"><?= e($heading) ?></h2>
  <?php if ($type === 'damage'): ?>
    <p class="notice">Bei akutem Wasseraustritt: Hauptabsperrhahn schließen und anrufen: <a href="tel:<?= e($company['phoneE164']) ?>"><?= e($company['phoneDisplay']) ?></a>. Dieses Formular ist keine laufend besetzte Notrufstelle.</p>
  <?php elseif ($type === 'application'): ?>
    <p>Name und ein Kontaktweg genügen für den ersten Schritt. Anschreiben und Lebenslauf sind nicht erforderlich. Bitte keine Geburtsdaten, Gesundheitsdaten oder Fotos.</p>
  <?php else: ?>
    <p>Beschreiben Sie Ihr Anliegen kurz. Wir melden uns zu unseren Geschäftszeiten. Alternativ: <a href="tel:<?= e($company['phoneE164']) ?>"><?= e($company['phoneDisplay']) ?></a> oder <a href="mailto:<?= e($company['email']) ?>"><?= e($company['email']) ?></a>.</p>
  <?php endif; ?>
  <?php if (!empty($errors)): ?>
    <div class="form-errors" role="alert" tabindex="-1" data-form-errors>
      <p><strong>Bitte prüfen Sie Ihre Eingaben:</strong></p>
      <ul><?php foreach ($errors as $k => $m): ?><li><?= e($m) ?></li><?php endforeach; ?></ul>
    </div>
  <?php endif; ?>
  <form class="form" method="post" action="<?= e($action) ?>" enctype="multipart/form-data" novalidate data-form="<?= e($type) ?>">
    <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>" data-csrf>
    <input type="hidden" name="form_started" value="<?= time() ?>">
    <input type="hidden" name="return_to" value="<?= e($page['path'] ?? '/') ?>">
    <div class="hp" aria-hidden="true"><label for="<?= $fid ?>-hp">Bitte leer lassen</label><input id="<?= $fid ?>-hp" type="text" name="<?= e($sec['spam']['honeypotField']) ?>" tabindex="-1" autocomplete="off" value=""></div>

    <?php if ($type === 'application'): ?>
      <div class="field">
        <label for="<?= $fid ?>-job">Tätigkeit</label>
        <select id="<?= $fid ?>-job" name="job_slug" required<?= $aria('jobSlug') ?>>
          <?php foreach ($content->jobs(true) as $j): ?>
            <option value="<?= e($j['slug']) ?>"<?= (($data['jobSlug'] ?? ($job['slug'] ?? '')) === $j['slug']) ? ' selected' : '' ?>><?= e($j['title']) ?></option>
          <?php endforeach; ?>
        </select>
        <?= $err('jobSlug') ?>
      </div>
    <?php elseif ($type === 'damage'): ?>
      <div class="field">
        <label for="<?= $fid ?>-dt">Schadenart</label>
        <select id="<?= $fid ?>-dt" name="damage_type" required<?= $aria('damageType') ?>>
          <option value="">Bitte wählen</option>
          <?php foreach ($damageTypes as $d): ?><option<?= ($data['damageType'] ?? '') === $d ? ' selected' : '' ?>><?= e($d) ?></option><?php endforeach; ?>
        </select>
        <?= $err('damageType') ?>
      </div>
      <div class="field">
        <label for="<?= $fid ?>-topic">Gewünschte Leistung</label>
        <select id="<?= $fid ?>-topic" name="topic">
          <?php foreach ($damageTopics as $t): ?><option<?= $selTopic === $t ? ' selected' : '' ?>><?= e($t) ?></option><?php endforeach; ?>
        </select>
      </div>
      <div class="field">
        <label for="<?= $fid ?>-obj">Objekt (Adresse oder Bezeichnung, bei Verwaltungen mit Wohnung)</label>
        <input id="<?= $fid ?>-obj" type="text" name="object_ref" required maxlength="200" value="<?= $val('objectRef') ?>"<?= $aria('objectRef') ?>>
        <?= $err('objectRef') ?>
      </div>
      <div class="field-row">
        <div class="field">
          <label for="<?= $fid ?>-not">Festgestellt am (optional)</label>
          <input id="<?= $fid ?>-not" type="text" name="noticed_at" maxlength="40" placeholder="TT.MM.JJJJ" value="<?= $val('noticedAt') ?>">
        </div>
        <div class="field">
          <label for="<?= $fid ?>-plz">Objekt-PLZ (optional)</label>
          <input id="<?= $fid ?>-plz" type="text" inputmode="numeric" name="plz" maxlength="5" pattern="[0-9]{5}" value="<?= $val('plz') ?>"<?= $aria('plz') ?>>
          <?= $err('plz') ?>
        </div>
      </div>
    <?php else: ?>
      <div class="field">
        <label for="<?= $fid ?>-topic">Anliegen</label>
        <select id="<?= $fid ?>-topic" name="topic" required<?= $aria('topic') ?>>
          <?php foreach ($contactTopics as $t): ?><option<?= $selTopic === $t ? ' selected' : '' ?>><?= e($t) ?></option><?php endforeach; ?>
        </select>
        <?= $err('topic') ?>
      </div>
    <?php endif; ?>

    <div class="field">
      <label for="<?= $fid ?>-name">Name</label>
      <input id="<?= $fid ?>-name" type="text" name="name" required autocomplete="name" maxlength="120" value="<?= $val('name') ?>"<?= $aria('name') ?>>
      <?= $err('name') ?>
    </div>
    <fieldset class="field-row">
      <legend>Rückkontakt (mindestens eine Angabe)</legend>
      <div class="field">
        <label for="<?= $fid ?>-email">E-Mail</label>
        <input id="<?= $fid ?>-email" type="email" name="email" autocomplete="email" maxlength="200" value="<?= $val('email') ?>"<?= $aria('email') ?>>
        <?= $err('email') ?>
      </div>
      <div class="field">
        <label for="<?= $fid ?>-phone">Telefon</label>
        <input id="<?= $fid ?>-phone" type="tel" name="phone" autocomplete="tel" maxlength="40" value="<?= $val('phone') ?>"<?= $aria('phone') ?>>
        <?= $err('phone') ?>
      </div>
      <?= $err('contact') ?>
    </fieldset>
    <div class="field">
      <label for="<?= $fid ?>-pref">Bevorzugte Rückmeldung</label>
      <select id="<?= $fid ?>-pref" name="preferred"<?= $aria('preferred') ?>>
        <?php foreach (['Egal', 'E-Mail', 'Telefon'] as $p): ?><option<?= ($data['preferred'] ?? 'Egal') === $p ? ' selected' : '' ?>><?= e($p) ?></option><?php endforeach; ?>
      </select>
      <?= $err('preferred') ?>
    </div>

    <?php if ($type === 'application'): ?>
      <div class="field">
        <label for="<?= $fid ?>-exp">Erfahrung oder Qualifikation (optional)</label>
        <input id="<?= $fid ?>-exp" type="text" name="experience" maxlength="1000" value="<?= $val('experience') ?>">
      </div>
      <div class="field">
        <label for="<?= $fid ?>-start">Möglicher Beginn (optional)</label>
        <input id="<?= $fid ?>-start" type="text" name="start_date" maxlength="60" value="<?= $val('startDate') ?>">
      </div>
      <div class="field">
        <label for="<?= $fid ?>-msg">Ein paar Sätze zu Ihnen (optional)</label>
        <textarea id="<?= $fid ?>-msg" name="message" rows="4" maxlength="5000"><?= $val('message') ?></textarea>
      </div>
    <?php else: ?>
      <?php if ($type === 'contact'): ?>
      <div class="field-row">
        <div class="field">
          <label for="<?= $fid ?>-comp">Unternehmen oder Verwaltung (optional)</label>
          <input id="<?= $fid ?>-comp" type="text" name="company" autocomplete="organization" maxlength="120" value="<?= $val('company') ?>">
        </div>
        <div class="field">
          <label for="<?= $fid ?>-plz">Objekt-PLZ (optional)</label>
          <input id="<?= $fid ?>-plz" type="text" inputmode="numeric" name="plz" maxlength="5" pattern="[0-9]{5}" value="<?= $val('plz') ?>"<?= $aria('plz') ?>>
          <?= $err('plz') ?>
        </div>
      </div>
      <?php endif; ?>
      <div class="field">
        <label for="<?= $fid ?>-msg"><?= $type === 'damage' ? 'Schadenbeschreibung' : 'Nachricht' ?></label>
        <textarea id="<?= $fid ?>-msg" name="message" rows="5" required maxlength="5000"<?= $aria('message') ?>><?= $val('message') ?></textarea>
        <?= $err('message') ?>
      </div>
      <div class="field">
        <label for="<?= $fid ?>-app">Terminpräferenz (optional, keine Buchung)</label>
        <input id="<?= $fid ?>-app" type="text" name="appointment" maxlength="200" value="<?= $val('appointment') ?>">
      </div>
    <?php endif; ?>

    <?php if ($uploads->enabled()): $ucfg = $sec['upload']; ?>
      <div class="field">
        <label for="<?= $fid ?>-files"><?= $type === 'damage' ? 'Fotos (optional)' : ($type === 'application' ? 'Unterlagen (optional)' : 'Dateien (optional)') ?></label>
        <input id="<?= $fid ?>-files" type="file" name="attachments[]" multiple accept=".pdf,.jpg,.jpeg,.png,.webp,application/pdf,image/jpeg,image/png,image/webp">
        <p class="hint">Bis zu <?= (int)$ucfg['maxFiles'] ?> Dateien, je höchstens <?= intdiv((int)$ucfg['maxFileBytes'], 1048576) ?> MiB. PDF, JPG, PNG oder WebP.</p>
      </div>
    <?php endif; ?>

    <div class="field field-check">
      <input id="<?= $fid ?>-priv" type="checkbox" name="privacy" value="1" required<?= !empty($data['privacy']) ? ' checked' : '' ?><?= $aria('privacy') ?>>
      <label for="<?= $fid ?>-priv">Ich habe die <a href="/datenschutz/">Datenschutzerklärung</a> gelesen. Meine Angaben werden zur Bearbeitung meiner <?= $type === 'application' ? 'Bewerbung' : 'Anfrage' ?> per E-Mail an HSM Tec übermittelt.</label>
      <?= $err('privacy') ?>
    </div>
    <p class="btn-row">
      <button class="btn btn-primary" type="submit" data-submit><?= $type === 'damage' ? 'Schaden melden' : ($type === 'application' ? 'Bewerbung senden' : 'Anfrage senden') ?></button>
    </p>
    <p class="hint">Mit dem Absenden entsteht kein Vertrag und keine Terminbuchung. Wir bestätigen den Eingang und melden uns.</p>
  </form>
</section>
