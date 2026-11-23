<?php

namespace App\Models;

class Media
{
    protected string $title;
    protected string $type;
    protected int $year;

    public function __construct(string $title, string $type, int $year)
    {
        $this->title = $title;
        $this->type = $type;
        $this->year = $year;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getYear(): int
    {
        return $this->year;
    }

    public function getInfo(): string
    {
        return "Judul       : {$this->title}\n"
             . "Jenis       : {$this->type}\n"
             . "Tahun       : {$this->year}";
    }
}
