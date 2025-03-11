<?php

namespace App\Domain\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class CourseInputDTO
{
    #[Assert\NotBlank(message: "course.title.not_blank")]
    #[Assert\Length(max: 255, min:10,
                maxMessage: "course.title.max_length",
                minMessage: "course.title.less_than",
    )]

    public string $title;

    #[Assert\NotBlank(message: "course.description.not_blank")]
    public string $description;

/*    #[Assert\Type("array", message: "course.contents.type")]
    public array $contents;
*/
    public function __construct(string $title, string $description)
    {
        $this->title = $title;
        $this->description = $description;

    }
}
