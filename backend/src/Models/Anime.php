<?php

namespace App\Models;

class Anime extends Media
{
    private string $studio;

    public function __construct(
        string $title,
        int $year,
        string $studio
    ) {
        parent::__construct($title, "Anime", $year);
        $this->studio = $studio;
    }

    public function getInfo(): string
    {
        return parent::getInfo() . "\n"
             . "Studio      : {$this->studio}";
    }
}