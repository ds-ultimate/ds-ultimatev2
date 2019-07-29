<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{ route('index') }}</loc>
        <changefreq>daily</changefreq>
    </url>
    <url>
        <loc>{{ route('legalPage') }}</loc>
        <changefreq>weekly</changefreq>
    </url>
    @foreach($servers as $key => $server)
        <url>
            <loc>{{ route('server', [$key]) }}</loc>
            <changefreq>daily</changefreq>
        </url>
        @foreach($server as $world)
            <url>
                <loc>{{ route('worldAlly', [$key, $world->name]) }}</loc>
                <lastmod>{{ $world->updated_at->format('c') }}</lastmod>
                <changefreq>hourly</changefreq>
            </url>
            <url>
                <loc>{{ route('worldPlayer', [$key, $world->name]) }}</loc>
                <lastmod>{{ $world->updated_at->format('c') }}</lastmod>
                <changefreq>hourly</changefreq>
            </url>
            <url>
                <loc>{{ route('world', [$key, $world->name]) }}</loc>
                <lastmod>{{ $world->updated_at->format('c') }}</lastmod>
                <changefreq>hourly</changefreq>
            </url>
        @endforeach
    @endforeach
</urlset>