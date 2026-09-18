<?php
/**
 * Inline-SVG-Icons (Linienstil, currentColor). $name: heizung|bad|wasser|sanierung|arrow|phone|mail|check|foerderung|karriere|verwaltung|gewerbe|dokument|uhr
 */
$icons = [
    'heizung' => '<path d="M12 3c2.5 3 5 5.5 5 9a5 5 0 0 1-10 0c0-1.5.5-2.8 1.3-4 .3 1.4 1 2.4 1.9 3 .2-2.7 1-5.4 1.8-8Z"/><path d="M6 21h12"/>',
    'bad' => '<path d="M4 12h16v3a4 4 0 0 1-4 4H8a4 4 0 0 1-4-4v-3Z"/><path d="M6 12V6a2 2 0 0 1 4 0"/><path d="M7 19l-1 2M17 19l1 2"/>',
    'wasser' => '<path d="M12 3s6 6.5 6 11a6 6 0 0 1-12 0c0-4.5 6-11 6-11Z"/><path d="M9 14a3 3 0 0 0 3 3"/>',
    'sanierung' => '<path d="M3 21h18"/><path d="M5 21V10l7-6 7 6v11"/><path d="M10 21v-6h4v6"/><path d="M14 4h3v4"/>',
    'arrow' => '<path d="M5 12h14"/><path d="m13 6 6 6-6 6"/>',
    'phone' => '<path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2Z"/>',
    'mail' => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>',
    'check' => '<path d="m5 12 5 5L20 7"/>',
    'foerderung' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v10M9.5 9.5h3.5a1.75 1.75 0 0 1 0 3.5H10.5a1.75 1.75 0 0 0 0 3.5H15"/>',
    'karriere' => '<rect x="3" y="7" width="18" height="13" rx="2"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><path d="M3 12h18"/>',
    'verwaltung' => '<path d="M3 21h18"/><path d="M5 21V4h9v17"/><path d="M14 9h5v12"/><path d="M8 8h3M8 12h3M8 16h3"/>',
    'gewerbe' => '<path d="M3 9l1-5h16l1 5"/><path d="M3 9a3 3 0 0 0 6 0 3 3 0 0 0 6 0 3 3 0 0 0 6 0"/><path d="M5 11v10h14V11"/><path d="M10 21v-6h4v6"/>',
    'dokument' => '<path d="M7 3h7l5 5v13H7z"/><path d="M14 3v5h5"/><path d="M9 13h6M9 17h6"/>',
    'uhr' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
];
$name = $name ?? 'arrow';
$path = $icons[$name] ?? $icons['arrow'];
$class = $class ?? '';
?><svg class="icon <?= e($class) ?>" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><?= $path ?></svg>
