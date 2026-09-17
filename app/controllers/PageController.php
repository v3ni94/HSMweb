<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Services\Container;
use App\Services\JsonLd;

final class PageController extends BaseController
{
    public function show(string $path): void
    {
        $content = Container::content();
        $page = $content->page($path);
        if ($page === null) {
            (new SystemController())->notFound();
            return;
        }
        $vars = [];
        $jsonLd = [JsonLd::breadcrumbs($this->breadcrumbs($page))];
        if ($path === '/') {
            $jsonLd[] = JsonLd::organization();
        }
        if (($page['template'] ?? '') === 'service') {
            $jsonLd[] = JsonLd::service($page);
        }
        if (!empty($page['faq'])) {
            $jsonLd[] = JsonLd::faq($page['faq']);
        }
        if (in_array($page['template'], ['kontakt', 'unternehmen', 'impressum', 'ki-openai'], true)) {
            $jsonLd[] = JsonLd::organization();
        }
        $vars['jsonLd'] = $jsonLd;
        $hasForm = !empty($page['form']) && $page['form'] !== 'none';
        // Formularseiten: kein gemeinsames Caching (CSRF-Token)
        $this->renderPage($page, $page['template'], $vars, 200, $hasForm);
    }

    public function job(string $slug): void
    {
        $content = Container::content();
        $job = $content->job($slug);
        if ($job === null) {
            (new SystemController())->notFound();
            return;
        }
        $open = $content->isJobOpen($job);
        $page = [
            'path' => '/karriere/' . $slug . '/',
            'parent' => '/karriere/',
            'title' => $job['title'],
            'navTitle' => $job['title'],
            'metaTitle' => $job['title'] . ' (m/w/d) in Düren | Karriere bei HSM Tec',
            'metaDescription' => $job['summary'],
            'template' => 'job',
            'noindex' => !$open && ($job['type'] ?? '') !== 'initiative',
            'lastmod' => $job['datePosted'],
            'form' => 'application',
        ];
        $jsonLd = [JsonLd::breadcrumbs($this->breadcrumbs($page))];
        if ($open && ($job['type'] ?? '') !== 'initiative') {
            $jsonLd[] = JsonLd::jobPosting($job);
        }
        $this->renderPage($page, 'job', ['job' => $job, 'open' => $open, 'jsonLd' => $jsonLd], 200, true);
    }

    public function guide(string $slug): void
    {
        $content = Container::content();
        $guide = $content->guide($slug);
        if ($guide === null) {
            (new SystemController())->notFound();
            return;
        }
        $page = [
            'path' => '/ratgeber/' . $slug . '/',
            'parent' => '/ratgeber/',
            'title' => $guide['title'],
            'navTitle' => $guide['title'],
            'metaTitle' => $guide['title'] . ' | Ratgeber HSM Tec',
            'metaDescription' => $guide['metaDescription'],
            'template' => 'guide',
            'lastmod' => $guide['updated'],
            'form' => 'contact',
            'formTopic' => $guide['formTopic'] ?? 'Allgemeine Anfrage',
        ];
        $jsonLd = [JsonLd::breadcrumbs($this->breadcrumbs($page)), JsonLd::article($guide, $page)];
        if (!empty($guide['faq'])) {
            $jsonLd[] = JsonLd::faq($guide['faq']);
        }
        $this->renderPage($page, 'guide', ['guide' => $guide, 'jsonLd' => $jsonLd], 200, true);
    }
}
