<?php

namespace App\Api\Qdrant\Search;

use App\Api\Qdrant\Vector\Vector;

class SearchRequest
{
    private ?array $params = null;
    private int $limit = 4;
    private Vector $vector;
    private string $nameCollection;

    public function __construct(Vector $vector, string $nameCollection)
    {
        $this->vector = $vector;
        $this->nameCollection = $nameCollection;
    }

    public function getParams(): ?array
    {
        return $this->params ?? [
            'hnsw_ef' => 123,
            'exact' => false,
        ];
    }

    public function getPayload(){
        return [
            'vector' => $this->vector->getVector(),
            'params' => $this->getParams(),
            'limit' => $this->getLimit()
        ];
    }

    public function setParams(?array $params): self
    {
        $this->params = $params;
        return $this;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function setLimit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    public function getNameCollection(): string
    {
        return $this->nameCollection;
    }

    public function setNameCollection(string $nameCollection): self
    {
        $this->nameCollection = $nameCollection;
        return $this;
    }
}
