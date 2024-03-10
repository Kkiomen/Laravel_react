<?php

namespace App\Jobs;

use App\CoreAssistant\Adapter\LLM\LanguageModel;
use App\CoreAssistant\Adapter\LLM\LanguageModelSettings;
use App\CoreAssistant\Adapter\LLM\LanguageModelType;
use App\Enum\ConfigurationMessage;
use App\GeneratorChatGpt\Service\LanguageModelGenerateContentDataDto;
use App\Models\GenerateGptCollection;
use App\Models\HistoryGenerateGptCollection;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class GenerateCollectionContentQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private LanguageModelGenerateContentDataDto $contentDataDto;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($contentDataDto)
    {
        $this->contentDataDto = $contentDataDto;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(LanguageModel $languageModel): void
    {
        $lastGeneratedCollection = GenerateGptCollection::where('id_external', $this->contentDataDto->getIdExternal())->whereNotNull('generated_content')->orderBy('updated_at', 'desc')->first();
        if($lastGeneratedCollection){

            if($this->contentDataDto->getSort() == null){
                $this->contentDataDto->setSort($lastGeneratedCollection->sort);
            }

            if($this->contentDataDto->getPrompt() == ConfigurationMessage::LAST_MESSAGE_CONTENT->value){
                $this->contentDataDto->setPrompt($lastGeneratedCollection->generated_content);
            }
        }

        $generateGptCollection = GenerateGptCollection::where('id', $this->contentDataDto->getCollectionId())->first();
        $generateGptCollection->status_generate = 2;
        $generateGptCollection->save();

        $lastMessages = $this->contentDataDto->isAddLastMessage() ? $this->tryFindMessages($this->contentDataDto) : [];

        if(in_array($this->contentDataDto->getPrompt(), ConfigurationMessage::getAllConfigurationArray())){
            $generateGptCollection->update([
                'prompt' => 'FINISH_LAST_MESSAGE_:' . $generateGptCollection->prompt
            ]);
            return;
        }

        $generatedContent = $languageModel->generateWithConversation(
            prompt: $this->contentDataDto->getPrompt(),
            systemPrompt: $this->contentDataDto->getSystem(),
            settings: (new LanguageModelSettings())->setLanguageModelType(LanguageModelType::from($this->contentDataDto->getModel()))->setTemperature($this->contentDataDto->getTemperature()),
            messagesUser: $lastMessages
        );

        $generateGptCollection->generated_content = $generatedContent;
        $generateGptCollection->status_generate = 3;

        $generateGptCollection->save();

        // Send callback information
        $this->sendWebhook($generatedContent);

        $this->saveHistory($generatedContent);
    }

    private function tryFindMessages(LanguageModelGenerateContentDataDto $collectionGenerateContent): array
    {
        $lastCollections = GenerateGptCollection::where('generate_gpt_request_id', $collectionGenerateContent->getCollectionRequestId())->where('status_generate', 1)->orderBy('sort', 'desc')->orderBy('created_at', 'desc')->get();

        $sortedData = [];

        foreach ($lastCollections as $collection){
            if(key_exists($collection->id_external, $sortedData)){
                continue;
            }

            $sortedData[$collection->id_external] = [
                'id' => $collection->id,
                'created_at' => $collection->created_at,
                'prompt' => $collection->prompt,
                'system' => $collection->system,
                'sort' => $collection->sort,
                'generated_content' => $collection->generated_content
            ];

        }

        if(empty($sortedData)){
            return [];
        }

        $currentSort = $collectionGenerateContent->getSort();
        $preparedLastMessage = [];


        foreach ($sortedData as $data){
            if($data['sort'] >= $currentSort){
                continue;
            }

            $preparedLastMessage[] = [
                'role' => 'user',
                'content' => $data['prompt']
            ];


            $preparedLastMessage[] = [
                'role' => 'assistant',
                'content' => $data['generated_content']
            ];
        }

        return $preparedLastMessage;
    }

    private function saveHistory(?string $content): void
    {
        HistoryGenerateGptCollection::create([
            'collection_request_id' => $this->contentDataDto->getCollectionRequestId(),
            'model' => $this->contentDataDto->getModel(),
            'id_external' => $this->contentDataDto->getIdExternal(),
            'temperature' => $this->contentDataDto->getTemperature(),
            'type' => $this->contentDataDto->getType(),
            'sort' => $this->contentDataDto->getSort(),
            'webhook' => $this->contentDataDto->getWebhook(),
            'webhook_type' => $this->contentDataDto->getWebhookType(),
            'prompt' => $this->contentDataDto->getPrompt(),
            'system' => $this->contentDataDto->getSystem(),
            'add_last_message' => $this->contentDataDto->isAddLastMessage(),
            'generated_content' => $content
        ]);
    }

    private function sendWebhook(?string $content): void
    {
        if(!empty($this->contentDataDto->getWebhook())){
            try{
                Http::post($this->contentDataDto->getWebhook(), [
                    'type' => $this->contentDataDto->getWebhookType(),
                    'data' => [
                        'content' => $content
                    ]
                ]);
            }catch (\Exception $exception){}
        }
    }
}
