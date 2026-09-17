<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Services\Container;
use App\Services\Response;
use App\Services\View;

abstract class BaseController
{
    /**
     * Rendert eine Seite im Standardlayout.
     * @param array $page   Seitendaten (path, title, metaTitle, metaDescription, noindex, ...)
     * @param string $template Template unter views/pages
     */
    protected function renderPage(array $page, string $template, array $vars = [], int $status = 200, bool $private = false): void
    {
        $site = Container::config('site');
        $content = Container::content();
        $company = $content->company();
        $vars += ['page' => $page, 'company' => $company, 'content' => $content];
        $body = View::render($template, $vars);
        $html = View::layout('base', [
            'page' => $page,
            'company' => $company,
            'content' => $content,
            'site' => $site,
            'body' => $body,
            'breadcrumbs' => $this->breadcrumbs($page),
            'jsonLd' => $vars['jsonLd'] ?? [],
            'noindex' => ($page['noindex'] ?? false) || $site['noindexAll'] || $status !== 200,
            'status' => $status,
        ]);
        Response::html($html, $status, $private);
    }

    protected function breadcrumbs(array $page): array
    {
        $content = Container::content();
        $trail = [];
        $cur = $page;
        $guard = 0;
        while ($cur && $guard++ < 8) {
            array_unshift($trail, ['name' => $cur['navTitle'] ?? $cur['title'], 'path' => $cur['path']]);
            $parent = $cur['parent'] ?? null;
            $cur = $parent ? $content->page($parent) : null;
        }
        if (($page['path'] ?? '') !== '/') {
            array_unshift($trail, ['name' => 'Startseite', 'path' => '/']);
        }
        return $trail;
    }
}
