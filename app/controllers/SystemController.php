<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Services\Container;
use App\Services\Response;

final class SystemController extends BaseController
{
    public function notFound(): void
    {
        $page = ['path' => '/404', 'title' => 'Seite nicht gefunden', 'metaTitle' => 'Seite nicht gefunden | HSM Tec GmbH', 'metaDescription' => '', 'template' => 'error', 'noindex' => true, 'form' => 'none'];
        $this->renderPage($page, 'error', ['code' => 404], 404, true);
    }

    public function gone(?string $to = null): void
    {
        $page = ['path' => '/410', 'title' => 'Inhalt nicht mehr verfügbar', 'metaTitle' => 'Inhalt nicht mehr verfügbar | HSM Tec GmbH', 'metaDescription' => '', 'template' => 'error', 'noindex' => true, 'form' => 'none'];
        $this->renderPage($page, 'error', ['code' => 410], 410, true);
    }

    public function serverError(): void
    {
        try {
            $page = ['path' => '/500', 'title' => 'Technischer Fehler', 'metaTitle' => 'Technischer Fehler | HSM Tec GmbH', 'metaDescription' => '', 'template' => 'error', 'noindex' => true, 'form' => 'none'];
            $this->renderPage($page, 'error', ['code' => 500], 500, true);
        } catch (\Throwable) {
            http_response_code(500);
            header('Content-Type: text/plain; charset=utf-8');
            echo "Technischer Fehler. Bitte rufen Sie uns an: 02421 8899975";
        }
    }

    public function methodNotAllowed(): void
    {
        http_response_code(405);
        header('Allow: GET, HEAD, POST');
        header('Content-Type: text/plain; charset=utf-8');
        echo 'Methode nicht erlaubt';
    }

    public function redirect(?string $to): void
    {
        Response::redirect($to ?: '/', 301);
    }

    public function redirectToForm(string $form): void
    {
        $map = ['contact' => '/kontakt/', 'damage' => '/wasserschaden/', 'application' => '/karriere/'];
        Response::redirect($map[$form] ?? '/', 302);
    }

    public function csrfToken(): void
    {
        Response::json(['token' => Container::csrf()->token()]);
    }

    public function sitemap(): void
    {
        $content = Container::content();
        $site = Container::config('site');
        $base = rtrim($site['baseUrl'], '/');
        $urls = [];
        foreach ($content->pages() as $p) {
            if (!empty($p['noindex']) || !empty($p['excludeFromSitemap'])) {
                continue;
            }
            $urls[] = ['loc' => $base . $p['path'], 'lastmod' => $p['lastmod'] ?? null];
        }
        foreach ($content->jobs(true) as $j) {
            $urls[] = ['loc' => $base . '/karriere/' . $j['slug'] . '/', 'lastmod' => $j['dateModified'] ?? $j['datePosted']];
        }
        foreach ($content->guides() as $g) {
            $urls[] = ['loc' => $base . '/ratgeber/' . $g['slug'] . '/', 'lastmod' => $g['updated']];
        }
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($urls as $u) {
            $xml .= '  <url><loc>' . htmlspecialchars($u['loc'], ENT_XML1) . '</loc>';
            if ($u['lastmod']) {
                $xml .= '<lastmod>' . htmlspecialchars($u['lastmod'], ENT_XML1) . '</lastmod>';
            }
            $xml .= "</url>\n";
        }
        $xml .= '</urlset>';
        if ($site['noindexAll']) {
            header('X-Robots-Tag: noindex');
        }
        Response::xml($xml);
    }

    public function llms(): void
    {
        $content = Container::content();
        $site = Container::config('site');
        $base = rtrim($site['baseUrl'], '/');
        $company = $content->company();
        $out = "# HSM Tec GmbH\n\n> Heizung, Bad und Sanitär, Wasserschaden und Sanierung aus Düren. Ein Ansprechpartner, der die beteiligten Gewerke koordiniert.\n\n";
        $out .= "Stand der Inhalte: " . $company['factsUpdated'] . ". Diese Datei ist ein Inhaltsverzeichnis, kein Ersatz für die HTML-Seiten oder die Sitemap.\n\n";
        $groups = [
            'Unternehmensfakten' => ['/ki/openai/', '/ki/anthropic/', '/unternehmen/', '/kontakt/', '/impressum/'],
            'Leistungen' => ['/leistungen/', '/heizung/', '/sanitaer/', '/wasserschaden/', '/sanierung/'],
            'Förderung' => ['/foerderung/', '/foerderrechner/'],
            'Karriere' => ['/karriere/'],
        ];
        foreach ($groups as $label => $paths) {
            $out .= "## {$label}\n\n";
            foreach ($paths as $p) {
                $pg = $content->page($p);
                if ($pg) {
                    $out .= "- [{$pg['title']}]({$base}{$p}): {$pg['metaDescription']}\n";
                }
            }
            $out .= "\n";
        }
        Response::text($out);
    }
}
