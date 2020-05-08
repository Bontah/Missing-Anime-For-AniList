<?php

namespace classes;

class Media
{
    public ?int $id;
    public ?int $idMal;
    public ?MediaTitle $title;
    public ?string $type; //MediaType
    public ?string $format; //MediaFormat
    public ?string $status; //MediaStatus
    public ?string $description;
    public ?string $startDate;
    public ?string $endDate;
    public ?string $season; //MediaSeason
    public ?int $seasonYear;
    public ?int $seasonInt;
    public ?int $episodes;
    public ?int $duration;
    public ?string $source; //MediaSource
    public ?MediaCoverImage $coverImage;
    public ?string $bannerImage;
    public ?array $genres;
    public ?int $averageScore;
    public ?int $meanScore;
    public ?int $popularity;
    public ?int $favourites;

//relations: MediaConnection
//Other media in the same or connecting franchise
    /**
     * Media constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->id = isset($data['id']) ? $data['id'] : null;
        $this->idMal = isset($data['idMal']) ? $data['idMal'] : null;
        $this->title = isset($data['title']) ? new MediaTitle($data['title']) : null;
        $this->type = isset($data['type']) ? $data['type'] : null;
        $this->format = isset($data['format']) ? $data['format'] : null;
        $this->status = isset($data['status']) ? $data['status'] : null;
        $this->description = isset($data['description']) ? $data['description'] : null;
        $this->startDate = isset($data['startDate']) ? $data['startDate'] : null;
        $this->endDate = isset($data['endDate']) ? $data['endDate'] : null;
        $this->season = isset($data['season']) ? $data['season'] : null;
        $this->seasonYear = isset($data['seasonYear']) ? $data['seasonYear'] : null;
        $this->seasonInt = isset($data['seasonInt']) ? $data['seasonInt'] : null;
        $this->episodes = isset($data['episodes']) ? $data['episodes'] : null;
        $this->duration = isset($data['duration']) ? $data['duration'] : null;
        $this->source = isset($data['source']) ? $data['source'] : null;
        $this->coverImage = isset($data['coverImage']) ? new MediaCoverImage($data['coverImage']) : null;
        $this->bannerImage = isset($data['bannerImage']) ? $data['bannerImage'] : null;
        $this->genres = isset($data['genres']) ? $data['genres'] : null;
        $this->averageScore = isset($data['averageScore']) ? $data['averageScore'] : null;
        $this->meanScore = isset($data['meanScore']) ? $data['meanScore'] : null;
        $this->popularity = isset($data['popularity']) ? $data['popularity'] : null;
        $this->favourites = isset($data['favourites']) ? $data['favourites'] : null;
    }


}
