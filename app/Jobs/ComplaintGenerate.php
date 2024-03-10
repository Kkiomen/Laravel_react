<?php

namespace App\Jobs;

use App\Api\OpenAiApi;
use App\Models\ComplaintList;
use App\Prompts\ComplaintPromptHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ComplaintGenerate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private ?string $summaryComplaint;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($summaryComplaint)
    {
        $this->summaryComplaint = $summaryComplaint;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        /**
         * @var OpenAiApi $openAiApi
         */
        $openAiApi = app(OpenAiApi::class);
        $json = $openAiApi->completionChat($this->summaryComplaint, ComplaintPromptHelper::generateResultJSON());
        $data = json_decode($json, true);

        ComplaintList::create([
            'subject' => $data['subject_complaint'],
            'description' => $data['description_complaint'],
            'time' => $data['time_complaint'],
            'result' => $data['result_complaint']
        ]);
    }
}
