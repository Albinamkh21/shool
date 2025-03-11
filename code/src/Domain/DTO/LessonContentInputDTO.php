<?php
/*
Author
*/

namespace App\Domain\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class LessonContentInputDTO
{
    #[Assert\NotBlank(message: "lesson_content.type.required")]
    #[Assert\Choice(choices: ['text', 'video'], message: "lesson_content.type.invalid")]
    public string $type;

    #[Assert\NotBlank(message: "lesson_content.content.required")]
    #[Assert\Length(
        min: 3,
        max: 5000,
        minMessage: "lesson_content.content.too_short",
        maxMessage: "lesson_content.content.too_long"
    )]
    public string $content;

    public function __construct(string $type, string $content)
    {
        $this->type = $type;
        $this->content = $content;
    }
}