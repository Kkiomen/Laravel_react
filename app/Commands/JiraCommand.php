<?php

namespace App\Commands;

use App\Abstract\Command;
use App\Services\WorklogService;
use Illuminate\Support\Facades\Lang;

class JiraCommand extends Command
{
    protected $name = 'jira';

    public function getDescription(): string
    {
        return 'Komenda służąca do obsługi zadań Jira';
    }

    public function getParams(): array
    {
        return [
            '-d [-d 30m] - ile czasu zajeło na realizacje zgłoszenie',
            '-n [-n LOB-311] - nazwa zgłoszenia nad którym się pracowało',
            '-t [-t 2023-04-17] - ustawienie daty kiedy zostało zrealizowane zgłoszenie',
        ];
    }

    public function getUsageExamples() {
        return [
            '!jira -d 30m dodanie nowego pola w LOB-306',
            '!jira -n LOB-394 -d 30m -t 2023-04-17 aktualizacja komponentu X',
        ];
    }

    public function execute($params, $content): array {

        if($this->createNewWorkLog($params, $content)){
            return [
                'message' => Lang::get('messages.success_add_worklog', ['message' => $content])
            ];
        }
        return [
            'message' => Lang::get('messages.error')
        ];
    }

    private function createNewWorkLog($params, $content){
        $name = $params['n'] ?? null;
        $durationMinutes = $params['d'] ?? null;
        $date = $params['t'] ?? null;
        $worklogService = app(WorklogService::class);
        return $worklogService->createWorklog($content, $durationMinutes, $name, $date);
    }

    private function processJiraAction($time, $priority, $label, $content) {

        return [
            'time' => $time,
            'priority' => $priority,
            'label' => $label,
            'content' => $content,
            'message' => $content
        ];
    }
}
