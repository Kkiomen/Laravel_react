<?php

namespace App\Console\Commands;

use App\GeneratorChatGpt\Service\LanguageModelGenerateContentDataDto;
use App\Jobs\GenerateCollectionContentQueue;
use App\Models\GenerateGptCollection;
use App\Models\GenerateGptCollectionRequest;
use Carbon\Carbon;
use Illuminate\Console\Command;

class OpenAiCollectionTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:collection-openai';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate collection openai';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $collection = GenerateGptCollection::where('status_generate', 0)->orderBy('lp_generate', 'asc')->orderBy('updated_at', 'desc')->first();
        if($collection){
            if($collection->updated_at->diffInMinutes(Carbon::now()) >= 1){
                $collection->status_generate = 1;
                $collection->save();

                $requestData = GenerateGptCollectionRequest::where('id', $collection->generate_gpt_request_id)->first();

                $collectionGenerateContent = new LanguageModelGenerateContentDataDto();
                $collectionGenerateContent->setCollectionRequestId($requestData->id)
                    ->setCollectionId($collection->id)
                    ->setIdExternal($collection->id_external)
                    ->setTemperature($requestData->temperature)
                    ->setType($requestData->type)
                    ->setModel($requestData->model)
                    ->setSort($collection->sort)
                    ->setWebhook($collection->webhook)
                    ->setWebhookType($collection->webhook_type)
                    ->setSystem($collection->system)
                    ->setPrompt($collection->prompt)
                    ->setAddLastMessage($collection->add_last_message);

                $this->dispatchToGenerateContent($collectionGenerateContent);
            }
        }

        return Command::SUCCESS;
    }

    private function dispatchToGenerateContent(LanguageModelGenerateContentDataDto $collectionGenerateContent)
    {
        GenerateCollectionContentQueue::dispatch($collectionGenerateContent);
    }
}
