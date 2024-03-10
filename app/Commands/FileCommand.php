<?php

namespace App\Commands;

use App\Abstract\Command;
use App\Models\FileCloud;
use Illuminate\Support\Facades\Auth;

class FileCommand extends Command
{
    protected $name = 'file';

    public function getDescription(): string
    {
        return 'Wyświetlanie listy plików zapisanych w chmurze';
    }

    public function getParams(): array
    {
        return [
            '-l [-l 6] - Pobiera ostatnie [x] plików',
            '-a [-a] - Pobiera wszystkie pliki',
        ];
    }

    public function getUsageExamples() {
        return [
            '!file show   :Pokaze ostatnie 10 plików',
            '!file -l 6 show    :Pokaze liste 6 ostatnich dodanych plików',
            '!file -a show    :Pokaze liste wszystkich dodanych plików',
        ];
    }

    public function execute($params, $content): array
    {
        $all = $params['a'] ?? null;
        $output = '';
        $files = $this->getFiles($params);

        if($files->count() == 0){
            $output .= '<li>No files available</li>';
        }else{
            $output .= '<h2 className="text-2xl font-bold tracking-tight text-white sm:text-2xl mb-2">File list:</h2>';
            $output .= '<div className="flex flex-col">';
            foreach ($files as $file){
                $url = asset('/cloud/'. $file->uuid);
                $output .= '<div className="flex items-center mt-3 justify-between  border-b pb-5 border-white/5">';
                    $output .= '<div className="pr-6 text-xs sm:text-sm">
                                  <a href="'.$url.'">'.$url.'</a>
                                </div>';
                    $output .= '<img src=" '.$url.'" className="rounded-xl mt-3 sm:mt-0 shadow ' . (is_null($all) ? 'w-20' : 'w-12') .' "/>';
                $output .= '</div>';
            }
            $output .= '</div>';
        }

        /*
         * <h2 className="text-2xl font-bold tracking-tight text-white sm:text-2xl mb-2">Lista plików:</h2>

                <div className="flex flex-col">
                  <div className="flex items-center mt-3 justify-between  border-b pb-5 border-white/5">
                    <div className="pr-6 text-sm">
                      http://localhost:215/cloud/rxkVfbXNR0CD125707
                    </div>
                  </div>

                  <div className="flex items-center mt-3 justify-between  border-b pb-5 border-white/5">
                    <div className="pr-6 text-sm">
                      <a href="http://localhost:215/cloud/rxkVfbXNR0CD125707">http://localhost:215/cloud/rxkVfbXNR0CD125707</a>
                    </div>
                    <img src="http://localhost:215/cloud/rxkVfbXNR0CD125707" className="rounded-xl shadow w-32 "/>
                  </div>
                </div>
         */

        return [
            'message' => $output
        ];
    }

    private function getFiles(array $params){
        $last = $params['l'] ?? null;
        $all = $params['a'] ?? null;
        if(!empty($all)){
            return FileCloud::where('user_id', Auth::user()->id)->orderBy('created_at', 'desc')->get();
        }else if(!empty($last)){
            return FileCloud::where('user_id', Auth::user()->id)->orderBy('created_at', 'desc')->limit($last)->get();
        }else{
            return FileCloud::where('user_id', Auth::user()->id)->orderBy('created_at', 'desc')->limit(10)->get();
        }
    }
}
