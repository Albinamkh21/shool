<?php

namespace App\Domain\DTO;

class LessonOutputDTO
{
    public int $id;
    public string $title;
    public string $description;
    public array $contents;

    public function __construct(int $id, string $title, string $description, array $contents)
    {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->contents = $contents;
    }
}