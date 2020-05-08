<?php

namespace classes;

class User
{
    public ?int $id = null;
    public ?string $name = null;

    /**
     * User constructor.
     * @param int|null $id
     * @param string|null $name
     */
    public function __construct(?int $id, ?string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }
}
