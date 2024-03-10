<?php

namespace App\Api\Qdrant;

use App\Api\Qdrant\Vector\Vector;

final class PointStruct
{

    private int $id;
    private string $nameCollection;
    private Vector $vector;

    public function __construct(int $id, Vector $vector, string $nameCollection)
    {
        $this->id = $id;
        $this->vector = $vector;
        $this->nameCollection = $nameCollection;
    }

    public function getPayload(): array
    {
        return [
            'points' => [
                [
                    'id' => $this->id,
                    'payload' => $this->vector->getPayload(),
                    'vector' => $this->vector->getVector()
                ]
            ]
        ];
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getNameCollection(): string
    {
        return $this->nameCollection;
    }

    public function setNameCollection(string $nameCollection): void
    {
        $this->nameCollection = $nameCollection;
    }

    public function getVector(): Vector
    {
        return $this->vector;
    }

    public function setVector(Vector $vector): void
    {
        $this->vector = $vector;
    }
}
