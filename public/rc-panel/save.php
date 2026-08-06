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
            'seoDefaultTitle' => trim((string) ($_POST['seoDefaultTitle'] ?? '')),
            'seoDefaultDescription' => trim((string) ($_POST['seoDefaultDescription'] ?? '')),
        ];
        manage_write_json('site.json', $data);
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

    default:
        header('Location: /rc-panel/?error=1');
        exit;
}

header('Location: /rc-panel/?saved=1');
exit;
