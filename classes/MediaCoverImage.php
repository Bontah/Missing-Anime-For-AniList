<?php

namespace classes;

class MediaCoverImage
{
    public ?string $extraLarge;
    public ?string $large;
    public ?string $medium;
    public ?string $color;

    /**
     * MediaTitle constructor.
     *
     * @param object $coverImages
     */
    public function __construct(object $coverImages)
    {
        $this->extraLarge = $coverImages->extraLarge;
        $this->large = $coverImages->large;
        $this->medium = $coverImages->medium;
        $this->color = $coverImages->color;
    }
}
