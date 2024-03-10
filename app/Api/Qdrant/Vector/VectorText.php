<?php

namespace App\Api\Qdrant\Vector;

use App\Api\OpenAiApi;

 final class VectorText extends Vector
{
    private string $text;
    private OpenAiApi $openAiApi;

    public function __construct(OpenAiApi $openAiApi, string $text, array $payload = null)
    {
        $this->openAiApi = $openAiApi;
        $this->text = $text;
        $embedding = $this->makeEmbeddingFromText($text);
        parent::__construct($embedding, $payload);
    }

    public function getPayload(): ?array
    {
        return [
            'message' => $this->getText()
        ];
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }

    private function makeEmbeddingFromText(string $content): array
    {
        return $this->openAiApi->embedding($content);
    }


}
