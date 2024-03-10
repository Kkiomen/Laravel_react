<?php

namespace App\Api;

use Illuminate\Support\Facades\Http;

class PineconeApi
{
    const HTTPS = 'https://';
    const UPSERT_URL_ADDON = '/vectors/upsert';
    const QUERY_ADDON = '/query';

    private ?string $pineconeApiKey = null;
    private ?string $pineconeIndexUrl = null;

    public function __construct()
    {
        $this->pineconeApiKey = env('PINECONE_API_KEY');
        $this->pineconeIndexUrl = env('PINECONE_INDEX_URL');
    }

    /**
     * @throws \Exception
     */
    public function upsert(array $data): bool
    {
        $this->validateApiKey();
        $this->validateIndexUrl();
        $this->validateData($data);

        $url = $this->getUpsertUrl();
        $response = Http::withHeaders([
            'Api-Key' => $this->pineconeApiKey,
        ])->post($url, $data);

        return is_array($response->json()) && array_key_exists('upsertedCount', $response->json());
    }

    public function get(array $data){
        $this->validateApiKey();
        $this->validateIndexUrl();
        $this->validateData($data);

        $url = $this->getQueryUrl();
        $response = Http::withHeaders([
            'Api-Key' => $this->pineconeApiKey,
        ])->post($url, $data);
        return is_array($response->json()) && array_key_exists('matches', $response->json()) ? $response->json() : null;
    }


    /**
     * Sets the Pinecone API key and URL.
     *
     * @param array $apiKeys An array containing the Pinecone API key and URL.
     * @throws \Exception If the Pinecone API key or URL is not present in the array.
     */
    public function setApiKey(?array $apiKeys): void {
        if (is_null($apiKeys) || !isset($apiKeys['pinecone_api_key']) || !isset($apiKeys['pinecone_api_url'])) {
            throw new \Exception('Missing Pinecone API key or URL.');
        }
        $this->pineconeApiKey = $apiKeys['pinecone_api_key'];
        $this->pineconeIndexUrl = $apiKeys['pinecone_api_url'];
    }

    /**
     * Validates that a Pinecone API key has been set.
     *
     * @throws \Exception If the Pinecone API key is null.
     */
    private function validateApiKey(): void
    {
        if (is_null($this->pineconeApiKey)) {
            throw new \Exception('Add PINECONE_API_KEY in the .env file');
        }
    }

    /**
     * Validates that a Pinecone index URL has been set.
     *
     * @throws \Exception If the Pinecone index URL is null.
     */
    private function validateIndexUrl(): void
    {
        if (is_null($this->pineconeIndexUrl)) {
            throw new \Exception('Add PINECONE_API_KEY in the .env file');
        }
    }

    /**
     * Validates that data has been passed to the Pinecone upsert method.
     *
     * @param array $data The data to be validated.
     * @throws \Exception If the data array is empty.
     */
    private function validateData(array $data): void
    {
        if (empty($data)) {
            throw new \Exception('Data not passed to Pinecone upsert method');
        }
    }

    /**
     * Returns the URL for the Pinecone upsert endpoint.
     *
     * @return string The URL for the Pinecone upsert endpoint.
     */
    private function getUpsertUrl(): string
    {
        return self::HTTPS . $this->pineconeIndexUrl . self::UPSERT_URL_ADDON;
    }

    /**
     * Returns the URL for the Pinecone query endpoint.
     *
     * @return string The URL for the Pinecone query endpoint.
     */
    private function getQueryUrl(): string
    {
        return self::HTTPS . $this->pineconeIndexUrl . self::QUERY_ADDON;
    }
}
