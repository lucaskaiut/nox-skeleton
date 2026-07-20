<?php

namespace App\Modules\Post\Http\Controllers;

use App\Modules\Post\Services\PostService;
use Illuminate\Http\Response;

class SitemapController
{
    public function __construct(private readonly PostService $service) {}

    public function __invoke(): Response
    {
        $posts = $this->service->getSitemapEntries();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";

        $xml .= '<url>'."\n";
        $xml .= '  <loc>'.e(config('app.url')).'</loc>'."\n";
        $xml .= '  <changefreq>weekly</changefreq>'."\n";
        $xml .= '  <priority>1.0</priority>'."\n";
        $xml .= '</url>'."\n";

        foreach ($posts as $post) {
            $xml .= '<url>'."\n";
            $xml .= '  <loc>'.e(config('app.url').'/blog/'.$post->slug).'</loc>'."\n";
            $xml .= '  <lastmod>'.$post->updated_at->toAtomString().'</lastmod>'."\n";
            $xml .= '  <changefreq>monthly</changefreq>'."\n";
            $xml .= '  <priority>0.8</priority>'."\n";
            $xml .= '</url>'."\n";
        }

        $xml .= '</urlset>';

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }
}
