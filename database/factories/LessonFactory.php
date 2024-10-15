<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lesson>
 */
class LessonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = $this->faker->randomElement(['video', 'text']);
        $video_url = null;
        $content = null;
        if($type === 'video') {
            $videoId = $this->faker->unique()->regexify('[A-Za-z0-9_-]{11}');
            $video_url = "https://www.youtube.com/watch?v={$videoId}";
        } else {
            $content = $this->faker->text();
        }
        return [
            'chapter_id' => $this->faker->numberBetween(1, 10),
            'type' => $type,
            'status' => $this->faker->randomElement([1, 0]),
            'priority' => $this->faker->numberBetween(1, 10),
            'lesson_title' => $this->faker->text(),
            'video_url' => $video_url,
            'duration' => $this->faker->numberBetween([1,10]),
            'content' => $content,
            'is_public' => $this->faker->randomElement([1, 0])
        ];
    }
}
