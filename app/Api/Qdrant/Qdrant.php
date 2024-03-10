<?php

namespace App\Api\Qdrant;

use App\Api\Qdrant\Search\SearchRequest;
use Illuminate\Support\Facades\Http;

final class Qdrant
{
    private ?string $host = null;
    private ?int $port = null;

    public function __construct()
    {
        $this->host = env('QDRANT_HOST');
        $this->port = env('QDRANT_PORT');

        if (is_null($this->host) || is_null($this->port)) {
            throw new \Exception('Dane do Qdrant są niepoprawne. Dodaj informacje do .env (QDRANT_HOST oraz QDRANT_PORT)');
        }
    }

    /**
     * @throws \Exception
     */
    public function addVector(PointStruct $pointStruct): bool
    {
        $this->createCollection($pointStruct->getNameCollection());
        $url = $this->getQdrantUrl() . '/collections/' . $pointStruct->getNameCollection() . '/points?wait=true';
        $response = Http::put($url, $pointStruct->getPayload());
        if(isset($response->json()['status']) && $response->json()['status'] === 'ok'){
            return true;
        }
        return false;
    }

    /**
     * @throws \Exception
     */
    public function search(SearchRequest $searchRequest): array
    {
        $this->createCollection($searchRequest->getNameCollection());
        $url = $this->getQdrantUrl() . '/collections/' . $searchRequest->getNameCollection() . '/points/search';
        $response = Http::post($url, $searchRequest->getPayload());
        if(isset($response->json()['status']) && $response->json()['status'] === 'ok'){
            return $response->json()['result'];
        }
        return [];
    }

    public function delete(PointStruct $pointStruct): bool{
        $this->createCollection($pointStruct->getNameCollection());
        $url = $this->getQdrantUrl() . '/collections/' . $pointStruct->getNameCollection() . '/points/delete';
        $response = Http::post($url, [
            'points' => [
                $pointStruct->getId()
            ]
        ]);
        if(isset($response->json()['status']) && $response->json()['status'] === 'ok'){
            return true;
        }

        return false;
    }



    /**
     * Adding new Collection
     * @param string $name
     * @return void
     * @throws \Exception
     */
    public function createCollection(string $name): void
    {
        $collections = $this->getCollections();
        if (!in_array($name, array_column($collections, 'name'))) {

            $collectionSchema = [
                'vectors' => [
                    'size' => 1536,
                    'distance' => 'Cosine',
                ]
            ];

            $response = Http::put($this->getQdrantUrl() . '/collections/' . $name, $collectionSchema);
            if (!$response->successful()) {
                throw new \Exception('Wystąpił problem podczas tworzenia kolekcji');
            }
        }
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getCollections(): array
    {
        $response = Http::get($this->getQdrantUrl() . '/collections');
        if ($response->json()['status'] === 'ok') {
            return $response->json()['result']['collections'];
        }
        throw new \Exception('Wystąpił błąd podczas pobierania kolekcji z Qdrant');
    }

    /**
     * @return string
     */
    private function getQdrantUrl(): string
    {
        return 'http://' . $this->host . ':' . $this->port;
    }
}
