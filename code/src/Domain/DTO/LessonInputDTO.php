<?php

namespace App\Domain\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class LessonInputDTO
{
    #[Assert\NotBlank(message: "lesson.title.not_blank")]
    #[Assert\Length(max: 255, min:10,
                maxMessage: "lesson.title.max_length",
                minMessage: "lesson.title.less_than",
    )]
    public string $title;


    #[Assert\NotBlank(message: "lesson.description.not_blank")]
    #[Assert\GreaterThan(
        value: 0,
        message: "lesson.description.greater_than"

    )]
    public string $description;


    #[Assert\Type("int", message: "lesson.order.type")]
    public  int $order;

    #[Assert\Type("int", message: "lesson.courseId.type")]
    #[Assert\NotBlank(message: "lesson.course.not_blank")]
    #[Assert\GreaterThan(
        value: 0,
        message: "lesson.course.greater_than"
    )]
    public  int $courseId;


    #[Assert\Type("array", message: "lesson.contents.type")]
    public array $contents;

    public function __construct(string $title, string $description,  int $courseId, int $order, array $contents)
    {
        $this->title = $title;
        $this->courseId = $courseId;
        $this->order = $order;
        $this->description = $description;
        $this->contents = $contents;
    }
}
