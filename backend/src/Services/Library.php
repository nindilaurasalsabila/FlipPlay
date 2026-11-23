<?php

namespace App\Services;

use App\Models\Media;

class Library
{
    private array $mediaList = [];

    public function addMedia(Media $media): bool
    {
        foreach ($this->mediaList as $item) {
            if (strtolower($item->getTitle()) === strtolower($media->getTitle())) {
                return false;
            }
        }

        $this->mediaList[] = $media;
        return true;
    }

    public function getAllMedia(): array
    {
        return $this->mediaList;
    }

    public function findMedia(string $keyword): ?Media
    {
        foreach ($this->mediaList as $media) {
            if (stripos($media->getTitle(), $keyword) !== false) {
                return $media;
            }
        }

        return null;
    }

    public function getMediaByNumber(int $number): ?Media
    {
        return $this->mediaList[$number - 1] ?? null;
    }
}
