<?php

namespace App\Commands;

use App\Abstract\Command;

class TestWiseB2BCommand extends Command
{
    protected $name = 'wiseb2bTest';

    public function getDescription(): string
    {
        return 'Wygenerowanie testów dla enpointów WiseB2B';
    }

    public function getParams(): array
    {
        return [
            'brak',
        ];
    }

    public function getUsageExamples() {
        return [
            '!wiseb2bTest show   :Wyświetlenie listy endpointów do wygenerowania testów',
        ];
    }

    public function execute($params, $content): array
    {
        $all = $params['a'] ?? null;
        $output = 'Endpointy zostaly załadowane. Wybierz jeden, który Cię interesuje';

        $jsonApi = file_get_contents('http://demo.wiseb2b.eu:8001/admin-api/doc.json');
        $openApi = json_decode($jsonApi, true);

        return [
            'message' => $output,
            'data' => $openApi['paths']
        ];
    }
}
