<?php

namespace App\Commands;

use App\Abstract\Command;
use App\Helpers\RequestHelper;
use App\Models\Worklog;
use App\Services\WorklogService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;

class DailyCommand extends Command
{
    protected $name = 'daily';

    public function getDescription(): string
    {
        return 'Pobieranie informacje do daily: Zrealizowane zgłoszenia, pytania';
    }

    public function getParams(): array
    {
        return [
            '-d [-d 04.05.2023] - Data z którego pobrać dane',
            '-f [-f 01.05.2023] - Data poczatkowa od której pokazać wszystkie zgłoszenia',
            '-t [-t 06.05.2023] - Data końcowa do której pokazać wszystkie zgłoszenia',
        ];
    }

    public function getUsageExamples() {
        return [
            '!daily show   :Pokaze zgloszenia z ostatniego dnia',
            '!daily -d 17.02.2023    :Pokaze zgloszenia z konkretnego dnia',
            '!daily -f 11.06.2023 -t 14.06.2023    :Pokaze zgloszenia z przedzialu (from, to)',
        ];
    }


    public function execute($params, $content): array
    {
        $worklogs = $this->getWorklogs($params);
        $output = '';
//
//        <h2 className="text-2xl font-bold tracking-tight text-white sm:text-2xl mb-2">Pytania daily:</h2>
//                  <ol className="list-decimal ml-10">
//                    <li>Co robiłem poprzedniego dnia?</li>
//                    <li>Czy jest zadowolony z czegoś co robiłem?</li>
//                    <li>Co będę robił dzisiaj?</li>
//                    <li>Czy natrafiłem na jakieś przeszkody?</li>
//                  </ol>
//                  <h2 className="text-2xl font-bold tracking-tight text-white sm:text-2xl mt-5 mb-2">Co robiłem poprzedniego dnia?:</h2>
//                  <ol className="list-disc ml-10">
//                    <li><strong>
//    Co robiłem poprzedniego dnia?<br/>
//                      <small>LOB-304 - 12.10.2023    |   <span> 30m</span></small>
//                    </strong>
//                    </li>
//                    <li>Czy jest zadowolony z czegoś co robiłem?</li>
//                    <li>Co będę robił dzisiaj?</li>
//                    <li>Czy natrafiłem na jakieś przeszkody?</li>
//                  </ol>
//
//
        $output .= '<h2 class="text-2xl font-bold tracking-tight text-white sm:text-2xl mb-2">Pytania daily:</h2>';
        $output .= '
                  <ol class="list-decimal ml-10">
                    <li>Co robiłem poprzedniego dnia?</li>
                    <li>Czy jest zadowolony z czegoś co robiłem?</li>
                    <li>Co będę robił dzisiaj?</li>
                    <li>Czy natrafiłem na jakieś przeszkody?</li>
                  </ol>
        ';
        $output .= '<h2 class="text-2xl font-bold tracking-tight text-white sm:text-2xl mt-5 mb-2">Co robiłem poprzedniego dnia?:</h2>';

        $output .= '<ul class="list-disc ml-10">';
        if($worklogs->count() == 0){
            $output .= '<li>Niestety brak takich zgłoszeń</li>';
        }else{
            foreach ($this->getWorklogs($params) as $worklog){
                $output .= '<li>';
                    $output .= '<strong>'. $worklog->description . '</strong>';
                    $output .= '<br/>';
                    $output .= !empty($worklog->issue_name) ? $worklog->issue_name . ' - ' : '';
                    $output .= !empty($worklog->created_at) ? $worklog->created_at : '';
                    $output .= !empty($worklog->duration_minutes) ?  ' | '. $worklog->duration_minutes : '';
                $output .= '</li>';
            }
        }
        $output .= '</ul>';

        return [
            'message' => $output,
            'data' => json_decode(RequestHelper::getOpenApi(), true)
        ];
    }

    private function getWorklogs($params){
        $date = $params['d'] ?? null;
        $startDate = $params['f'] ?? null;
        $endDate = $params['t'] ?? null;

        if(empty($date) && empty($startDate) && empty($endDate)){
            $lastRecord = Worklog::where('user_id', Auth::user()->id)->orderBy('created_at', 'desc')->first();

            if (!$lastRecord) {
                $worklogs = collect([]);
            } else {
                $lastDate = $lastRecord->created_at->startOfDay();
                $worklogs = Worklog::where('user_id', Auth::user()->id)->where('created_at', '>=', $lastDate)
                    ->where('created_at', '<', $lastDate->copy()->addDay())
                    ->get();
            }
            return $worklogs;
        }

        // give from and to date
        if(!empty($startDate) && !empty($endDate)){
            $startDate = Carbon::parse($startDate)->startOfDay();
            $endDate = Carbon::parse($endDate)->endOfDay();
            return Worklog::where('user_id', Auth::user()->id)->where('created_at', '>=', $startDate)
                ->where('created_at', '<=', $endDate)
                ->get();
        }

        // give only from date
        if(!empty($startDate) && empty($endDate)){
            $startDate = Carbon::parse($startDate)->startOfDay();
            return Worklog::where('user_id', Auth::user()->id)->where('created_at', '>=', $startDate)->get();
        }

        // give only to date
        if(empty($startDate) && !empty($endDate)){
            $endDate = Carbon::parse($endDate)->endOfDay();
            return Worklog::where('user_id', Auth::user()->id)->where('created_at', '<=', $endDate)->get();
        }

        if(!empty($date)){
            $specificDate = Carbon::parse($date);

            $startDate = $specificDate->copy()->startOfDay();
            $endDate = $specificDate->copy()->endOfDay();

            return Worklog::where('user_id', Auth::user()->id)->where('created_at', '>=', $startDate)
                ->where('created_at', '<=', $endDate)
                ->get();
        }


        return collect([]);
//        /**
//         * @var WorklogService $worklogService
//         */
//        $worklogService = app(WorklogService::class);
    }
}
