<?php

namespace classes;

class MediaTitle {
    public ?string $romaji;
    public ?string $english;
    public ?string $native;
    public ?string $userPreferred;

    /**
     * MediaTitle constructor.
     *
     * @param object $title
     */
    public function __construct(object $title)
    {
        $this->romaji = $title->romaji;
        $this->english = $title->english;
        $this->native = $title->native;
        $this->userPreferred = $title->userPreferred;
    }
}
