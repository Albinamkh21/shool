<?php

namespace App\Domain\Entity\LessonContent;

interface LessonContentInterface
{
    public const TYPE_TEXT = 'text';
    public const TYPE_VIDEO = 'video';
    public const TYPE_AUDIO = 'audio';
    public const TYPE_QUIZ = 'quiz';
    public function getId(): ?int;
    public function getType(): string;
    public function getContent(): ?string;
}