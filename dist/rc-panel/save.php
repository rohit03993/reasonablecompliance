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
        $data = [
            'hero' => [
                'headline' => trim((string) ($_POST['headline'] ?? '')),
                'subheadline' => trim((string) ($_POST['subheadline'] ?? '')),
                'intro' => trim((string) ($_POST['intro'] ?? '')),
            ],
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
        $items = [];
        $count = (int) ($_POST['count'] ?? 0);
        for ($i = 0; $i < $count; $i++) {
            $title = trim((string) ($_POST["title_$i"] ?? ''));
            if ($title === '') {
                continue;
            }
            $items[] = [
                'slug' => trim((string) ($_POST["slug_$i"] ?? '')),
                'title' => $title,
                'summary' => trim((string) ($_POST["summary_$i"] ?? '')),
                'seoTitle' => trim((string) ($_POST["seoTitle_$i"] ?? '')),
                'seoDescription' => trim((string) ($_POST["seoDescription_$i"] ?? '')),
                'order' => (int) ($_POST["order_$i"] ?? ($i + 1)),
                'bullets' => lines_to_array((string) ($_POST["bullets_$i"] ?? '')),
            ];
        }
        usort($items, fn($a, $b) => ($a['order'] <=> $b['order']));
        manage_write_json('services.json', ['items' => $items]);
        break;

    default:
        header('Location: /rc-panel/?error=1');
        exit;
}

header('Location: /rc-panel/?saved=1');
exit;
