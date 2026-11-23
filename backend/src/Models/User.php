<?php

namespace App\Models;

class User
{
    private string $name;
    private array $collection = [];

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function addToCollection(Media $media): void
    {
        foreach ($this->collection as $item) {
            if ($item->getTitle() === $media->getTitle()) {
                return;
            }
        }

        $this->collection[] = $media;
    }

    public function getCollection(): array
    {
        return $this->collection;
    }
}
