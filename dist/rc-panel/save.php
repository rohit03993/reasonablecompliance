<?php
require __DIR__ . '/auth.php';
manage_require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /rc-panel/');
    exit;
}

$type = (string) ($_POST['type'] ?? '');

function lines_to_array(string $text): array
{
    $lines = preg_split("/\r\n|\n|\r/", trim($text)) ?: [];
    $out = [];
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line !== '') {
            $out[] = $line;
        }
    }
    return $out;
}

switch ($type) {
    case 'site':
        $current = manage_read_json('site.json');
        $data = [
            'siteName' => trim((string) ($_POST['siteName'] ?? '')),
            'tagline' => trim((string) ($_POST['tagline'] ?? '')),
            'logo' => trim((string) ($_POST['logo'] ?? '')),
            'email' => trim((string) ($_POST['email'] ?? '')),
            'phone' => trim((string) ($_POST['phone'] ?? '')),
            'whatsapp' => trim((string) ($_POST['whatsapp'] ?? '')),
            'web3formsAccessKey' => $current['web3formsAccessKey'] ?? '',
            'ctaPrimary' => trim((string) ($_POST['ctaPrimary'] ?? '')),
            'ctaSecondary' => trim((string) ($_POST['ctaSecondary'] ?? '')),
            'footerBlurb' => trim((string) ($_POST['footerBlurb'] ?? '')),
            'seoDefaultTitle' => trim((string) ($_POST['seoDefaultTitle'] ?? '')),
            'seoDefaultDescription' => trim((string) ($_POST['seoDefaultDescription'] ?? '')),
        ];
        manage_write_json('site.json', $data);
        manage_bump_cache();
        break;

    case 'homepage':
        $steps = [];
        $titles = $_POST['step_title'] ?? [];
        $descs = $_POST['step_description'] ?? [];
        if (is_array($titles)) {
            foreach ($titles as $i => $title) {
                $title = trim((string) $title);
                $desc = trim((string) ($descs[$i] ?? ''));
                if ($title !== '' || $desc !== '') {
                    $steps[] = ['title' => $title, 'description' => $desc];
                }
            }
        }
        $stats = [];
        foreach (lines_to_array((string) ($_POST['stats'] ?? '')) as $line) {
            $parts = array_map('trim', explode('|', $line, 2));
            if (($parts[0] ?? '') === '') {
                continue;
            }
            $stats[] = [
                'value' => $parts[0],
                'label' => $parts[1] ?? '',
            ];
        }
        $data = [
            'hero' => [
                'headline' => trim((string) ($_POST['headline'] ?? '')),
                'subheadline' => trim((string) ($_POST['subheadline'] ?? '')),
                'intro' => trim((string) ($_POST['intro'] ?? '')),
            ],
            'stats' => $stats,
            'trustTitle' => trim((string) ($_POST['trustTitle'] ?? '')),
            'trustBullets' => lines_to_array((string) ($_POST['trustBullets'] ?? '')),
            'whyChooseTitle' => trim((string) ($_POST['whyChooseTitle'] ?? '')),
            'whyChooseBullets' => lines_to_array((string) ($_POST['whyChooseBullets'] ?? '')),
            'industriesTitle' => trim((string) ($_POST['industriesTitle'] ?? '')),
            'industriesIntro' => trim((string) ($_POST['industriesIntro'] ?? '')),
            'industries' => lines_to_array((string) ($_POST['industries'] ?? '')),
            'processTitle' => trim((string) ($_POST['processTitle'] ?? '')),
            'processSteps' => $steps,
            'finalCta' => [
                'headline' => trim((string) ($_POST['finalHeadline'] ?? '')),
                'text' => trim((string) ($_POST['finalText'] ?? '')),
            ],
        ];
        manage_write_json('homepage.json', $data);
        break;

    case 'about':
        $data = [
            'title' => trim((string) ($_POST['title'] ?? '')),
            'seoTitle' => trim((string) ($_POST['seoTitle'] ?? '')),
            'seoDescription' => trim((string) ($_POST['seoDescription'] ?? '')),
            'paragraphs' => lines_to_array((string) ($_POST['paragraphs'] ?? '')),
            'whyChooseTitle' => trim((string) ($_POST['whyChooseTitle'] ?? '')),
            'whyChooseBullets' => lines_to_array((string) ($_POST['whyChooseBullets'] ?? '')),
        ];
        manage_write_json('about.json', $data);
        break;

    case 'contact':
        $data = [
            'headline' => trim((string) ($_POST['headline'] ?? '')),
            'supportingText' => trim((string) ($_POST['supportingText'] ?? '')),
            'formSuccessMessage' => trim((string) ($_POST['formSuccessMessage'] ?? '')),
            'seoTitle' => trim((string) ($_POST['seoTitle'] ?? '')),
            'seoDescription' => trim((string) ($_POST['seoDescription'] ?? '')),
        ];
        manage_write_json('contact.json', $data);
        break;

    case 'faqs':
        $items = [];
        $questions = $_POST['question'] ?? [];
        $answers = $_POST['answer'] ?? [];
        if (is_array($questions)) {
            foreach ($questions as $i => $q) {
                $q = trim((string) $q);
                $a = trim((string) ($answers[$i] ?? ''));
                if ($q !== '' || $a !== '') {
                    $items[] = ['question' => $q, 'answer' => $a, 'order' => $i + 1];
                }
            }
        }
        manage_write_json('faqs.json', ['items' => $items]);
        break;

    case 'services':
        $existing = manage_read_json('services.json');
        $existingBySlug = [];
        foreach (($existing['items'] ?? []) as $ex) {
            if (!empty($ex['slug'])) {
                $existingBySlug[$ex['slug']] = $ex;
            }
        }
        $items = [];
        $count = (int) ($_POST['count'] ?? 0);
        for ($i = 0; $i < $count; $i++) {
            $title = trim((string) ($_POST["title_$i"] ?? ''));
            if ($title === '') {
                continue;
            }
            $slug = trim((string) ($_POST["slug_$i"] ?? ''));
            $items[] = [
                'slug' => $slug,
                'title' => $title,
                'summary' => trim((string) ($_POST["summary_$i"] ?? '')),
                'seoTitle' => trim((string) ($_POST["seoTitle_$i"] ?? '')),
                'seoDescription' => trim((string) ($_POST["seoDescription_$i"] ?? '')),
                'order' => (int) ($_POST["order_$i"] ?? ($i + 1)),
                'icon' => $existingBySlug[$slug]['icon'] ?? 'spark',
                'bullets' => lines_to_array((string) ($_POST["bullets_$i"] ?? '')),
            ];
        }
        usort($items, fn($a, $b) => ($a['order'] <=> $b['order']));
        manage_write_json('services.json', ['items' => $items]);
        break;

    case 'social':
        manage_write_json('social.json', [
            'facebook' => trim((string) ($_POST['facebook'] ?? '')),
            'instagram' => trim((string) ($_POST['instagram'] ?? '')),
            'linkedin' => trim((string) ($_POST['linkedin'] ?? '')),
            'twitter' => trim((string) ($_POST['twitter'] ?? '')),
        ]);
        break;

    case 'gallery':
        $gItems = [];
        $titles = $_POST['item_title'] ?? [];
        $images = $_POST['item_image'] ?? [];
        $orders = $_POST['item_order'] ?? [];
        if (is_array($titles)) {
            foreach ($titles as $i => $title) {
                $title = trim((string) $title);
                $image = trim((string) ($images[$i] ?? ''));
                if ($title === '' && $image === '') {
                    continue;
                }
                $gItems[] = [
                    'title' => $title !== '' ? $title : 'Gallery photo',
                    'image' => $image,
                    'order' => (int) ($orders[$i] ?? ($i + 1)),
                ];
            }
        }
        usort($gItems, fn($a, $b) => ($a['order'] <=> $b['order']));
        manage_write_json('gallery.json', [
            'title' => trim((string) ($_POST['title'] ?? 'Gallery')),
            'intro' => trim((string) ($_POST['intro'] ?? '')),
            'items' => $gItems,
        ]);
        break;

    case 'testimonials':
        $tItems = [];
        $quotes = $_POST['quote'] ?? [];
        $names = $_POST['name'] ?? [];
        $roles = $_POST['role'] ?? [];
        if (is_array($quotes)) {
            foreach ($quotes as $i => $quote) {
                $quote = trim((string) $quote);
                $name = trim((string) ($names[$i] ?? ''));
                $role = trim((string) ($roles[$i] ?? ''));
                if ($quote === '' && $name === '') {
                    continue;
                }
                $tItems[] = [
                    'quote' => $quote,
                    'name' => $name !== '' ? $name : 'Client',
                    'role' => $role,
                ];
            }
        }
        manage_write_json('testimonials.json', [
            'title' => trim((string) ($_POST['title'] ?? 'What our clients say')),
            'items' => $tItems,
        ]);
        break;

    case 'blog':
        // Legacy bulk save kept for safety — prefer blog_post / blog_settings
        $bItems = [];
        $count = (int) ($_POST['count'] ?? 0);
        for ($i = 0; $i < $count; $i++) {
            $title = trim((string) ($_POST["title_$i"] ?? ''));
            if ($title === '') {
                continue;
            }
            $slug = trim((string) ($_POST["slug_$i"] ?? ''));
            if ($slug === '') {
                $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $title) ?? '');
                $slug = trim($slug, '-');
            }
            $bItems[] = [
                'slug' => $slug,
                'title' => $title,
                'excerpt' => trim((string) ($_POST["excerpt_$i"] ?? '')),
                'date' => trim((string) ($_POST["date_$i"] ?? date('Y-m-d'))),
                'author' => trim((string) ($_POST["author_$i"] ?? 'Reasonable Compliance')),
                'image' => trim((string) ($_POST["image_$i"] ?? '')),
                'status' => trim((string) ($_POST["status_$i"] ?? 'published')) === 'draft' ? 'draft' : 'published',
                'seoTitle' => trim((string) ($_POST["seoTitle_$i"] ?? '')),
                'seoDescription' => trim((string) ($_POST["seoDescription_$i"] ?? '')),
                'body' => trim((string) ($_POST["body_$i"] ?? '')),
            ];
        }
        usort($bItems, fn($a, $b) => strcmp($b['date'], $a['date']));
        manage_write_json('blog.json', [
            'title' => trim((string) ($_POST['title'] ?? 'Our Blog')),
            'intro' => trim((string) ($_POST['intro'] ?? '')),
            'items' => $bItems,
        ]);
        break;

    case 'blog_settings':
        $current = manage_ensure_content('blog', 'blog.json');
        $items = manage_blog_valid_items($current['items'] ?? []);
        if (count($items) === 0) {
            manage_restore_seed('blog', 'blog.json');
            $current = manage_read_json('blog.json');
            $items = manage_blog_valid_items($current['items'] ?? []);
        }
        manage_write_json('blog.json', [
            'title' => trim((string) ($_POST['title'] ?? 'Our Blog')),
            'intro' => trim((string) ($_POST['intro'] ?? '')),
            'items' => $items,
        ]);
        manage_bump_cache();
        header('Location: /rc-panel/edit-blog.php?saved=1');
        exit;

    case 'blog_post':
        $current = manage_ensure_content('blog', 'blog.json');
        $items = manage_blog_valid_items($current['items'] ?? []);
        $originalSlug = trim((string) ($_POST['original_slug'] ?? ''));
        $title = trim((string) ($_POST['title'] ?? ''));
        if ($title === '') {
            header('Location: /rc-panel/edit-blog.php?error=1');
            exit;
        }
        $slug = trim((string) ($_POST['slug'] ?? ''));
        if ($slug === '') {
            $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $title) ?? '');
            $slug = trim($slug, '-');
        }
        $slug = strtolower(preg_replace('/[^a-z0-9\-]+/i', '-', $slug) ?? '');
        $slug = trim($slug, '-');

        $newPost = [
            'slug' => $slug,
            'title' => $title,
            'excerpt' => trim((string) ($_POST['excerpt'] ?? '')),
            'date' => trim((string) ($_POST['date'] ?? date('Y-m-d'))),
            'author' => trim((string) ($_POST['author'] ?? 'Reasonable Compliance')),
            'image' => trim((string) ($_POST['image'] ?? '')),
            'status' => trim((string) ($_POST['status'] ?? 'published')) === 'draft' ? 'draft' : 'published',
            'seoTitle' => trim((string) ($_POST['seoTitle'] ?? '')),
            'seoDescription' => trim((string) ($_POST['seoDescription'] ?? '')),
            'body' => trim((string) ($_POST['body'] ?? '')),
        ];

        $next = [];
        foreach ($items as $item) {
            $s = (string) ($item['slug'] ?? '');
            if ($originalSlug !== '' && $s === $originalSlug) {
                continue;
            }
            if ($s === $slug) {
                continue;
            }
            $next[] = $item;
        }
        $next[] = $newPost;

        manage_blog_normalize_and_save([
            'title' => $current['title'] ?? 'Our Blog',
            'intro' => $current['intro'] ?? '',
            'items' => $next,
        ]);
        manage_bump_cache();
        header('Location: /rc-panel/edit-blog.php?saved=1');
        exit;

    case 'blog_delete':
        $current = manage_read_json('blog.json');
        $slug = trim((string) ($_POST['slug'] ?? ''));
        $items = array_values(array_filter(
            manage_blog_valid_items($current['items'] ?? []),
            fn($item) => ($item['slug'] ?? '') !== $slug
        ));
        manage_blog_normalize_and_save([
            'title' => $current['title'] ?? 'Our Blog',
            'intro' => $current['intro'] ?? '',
            'items' => $items,
        ]);
        manage_bump_cache();
        header('Location: /rc-panel/edit-blog.php?deleted=1');
        exit;

    case 'blog_repair':
    case 'content_reload':
        $key = trim((string) ($_POST['key'] ?? ''));
        if ($type === 'blog_repair') {
            $key = 'blog';
        }
        $map = [
            'blog' => 'blog.json',
            'services' => 'services.json',
            'faqs' => 'faqs.json',
            'testimonials' => 'testimonials.json',
            'gallery' => 'gallery.json',
            'homepage' => 'homepage.json',
            'about' => 'about.json',
            'contact' => 'contact.json',
            'site' => 'site.json',
            'social' => 'social.json',
        ];
        if (!isset($map[$key])) {
            header('Location: /rc-panel/?error=1');
            exit;
        }
        if (!manage_restore_seed($key, $map[$key])) {
            $fail = $key === 'blog' ? '/rc-panel/edit-blog.php?error=restore' : '/rc-panel/?error=1';
            header('Location: ' . $fail);
            exit;
        }
        $redirects = [
            'blog' => '/rc-panel/edit-blog.php?repaired=1',
            'services' => '/rc-panel/edit-services.php?saved=1',
            'faqs' => '/rc-panel/edit-faqs.php?saved=1',
            'testimonials' => '/rc-panel/edit-testimonials.php?saved=1',
            'gallery' => '/rc-panel/edit-gallery.php?saved=1',
            'homepage' => '/rc-panel/edit-homepage.php?saved=1',
            'about' => '/rc-panel/edit-about.php?saved=1',
            'contact' => '/rc-panel/edit-contact.php?saved=1',
            'site' => '/rc-panel/edit-site.php?saved=1',
            'social' => '/rc-panel/edit-social.php?saved=1',
        ];
        header('Location: ' . ($redirects[$key] ?? '/rc-panel/?saved=1'));
        exit;

    default:
        header('Location: /rc-panel/?error=1');
        exit;
}

manage_bump_cache();
header('Location: /rc-panel/?saved=1');
exit;
