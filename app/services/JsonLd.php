<?php
declare(strict_types=1);

namespace App\Services;

/**
 * Strukturierte Daten aus derselben Faktenbasis wie der sichtbare Inhalt (company.json, pages.json, jobs.json).
 */
final class JsonLd
{
    public static function orgId(): string
    {
        return url('/#organization');
    }

    public static function organization(): array
    {
        $c = Container::content()->company();
        $org = [
            '@context' => 'https://schema.org',
            '@type' => ['LocalBusiness', 'HomeAndConstructionBusiness', 'Plumber', 'HVACBusiness'],
            '@id' => self::orgId(),
            'name' => $c['legalName'],
            'url' => url('/'),
            'telephone' => $c['phoneE164'],
            'email' => $c['email'],
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => $c['address']['street'],
                'postalCode' => $c['address']['postalCode'],
                'addressLocality' => $c['address']['city'],
                'addressCountry' => 'DE',
            ],
            'areaServed' => $c['areaServedLabel'],
            'founder' => null,
            'employee' => array_map(fn($m) => [
                '@type' => 'Person',
                'name' => $m['name'],
                'jobTitle' => $m['role'],
            ], $c['management']),
            'parentOrganization' => array_map(fn($h) => [
                '@type' => 'Organization',
                'name' => $h['name'],
                'url' => $h['url'],
            ], $c['holdings']),
        ];
        unset($org['founder']);
        if (!empty($c['logoPath'])) {
            $org['logo'] = url($c['logoPath']);
        }
        return $org;
    }

    public static function breadcrumbs(array $trail): array
    {
        $items = [];
        foreach ($trail as $i => $b) {
            $items[] = ['@type' => 'ListItem', 'position' => $i + 1, 'name' => $b['name'], 'item' => url($b['path'])];
        }
        return ['@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => $items];
    }

    public static function service(array $page): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Service',
            'name' => $page['title'],
            'description' => $page['metaDescription'],
            'url' => url($page['path']),
            'provider' => ['@id' => self::orgId()],
            'areaServed' => Container::content()->company()['areaServedLabel'],
        ];
    }

    public static function faq(array $faq): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => array_map(fn($f) => [
                '@type' => 'Question',
                'name' => $f['q'],
                'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f['a']],
            ], $faq),
        ];
    }

    public static function jobPosting(array $job): array
    {
        $c = Container::content()->company();
        $data = [
            '@context' => 'https://schema.org',
            '@type' => 'JobPosting',
            'title' => $job['title'],
            'description' => $job['summary'],
            'datePosted' => $job['datePosted'],
            'employmentType' => $job['employmentTypeSchema'],
            'hiringOrganization' => ['@type' => 'Organization', 'name' => $c['legalName'], 'sameAs' => url('/')],
            'jobLocation' => [
                '@type' => 'Place',
                'address' => [
                    '@type' => 'PostalAddress',
                    'streetAddress' => $c['address']['street'],
                    'postalCode' => $c['address']['postalCode'],
                    'addressLocality' => $c['address']['city'],
                    'addressCountry' => 'DE',
                ],
            ],
            'directApply' => true,
            'identifier' => ['@type' => 'PropertyValue', 'name' => $c['legalName'], 'value' => $job['id']],
        ];
        if (!empty($job['validThrough'])) {
            $data['validThrough'] = $job['validThrough'];
        }
        return $data;
    }

    public static function article(array $guide, array $page): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $guide['title'],
            'description' => $guide['metaDescription'],
            'dateModified' => $guide['updated'],
            'datePublished' => $guide['published'],
            'author' => ['@id' => self::orgId()],
            'publisher' => ['@id' => self::orgId()],
            'mainEntityOfPage' => url($page['path']),
        ];
    }
}
