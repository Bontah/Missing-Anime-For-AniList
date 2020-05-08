<?php

use GuzzleHttp\Client;

chdir(dirname(__DIR__));
require 'vendor/autoload.php';

class missingAnimeApp
{
    protected ?string $accessToken = null;
    protected ?\classes\User $user = null;
    protected array $completedAnimeList = [];
    protected array $missingAnimeList = [];

    public function init()
    {
        $this->accessToken = $_POST['accessToken'];

        $this->getUser();
        $this->getUsersCompletedList();
        $this->getUsersMissingAnimes();

        return $this->generateHtml();
    }

    private function generateHtml()
    {
        $tableStart = '
            <table id="table">
                <thead>
                    <tr>
                        <td></td>
                        <td>ID</td>
                        <td>TITLE</td>
                        <td>FORMAT</td>
                        <td>STATUS</td>
                    </tr>
                </thead>
                <tbody>
        ';
        $tableEnd = '
                </tbody>
            </table>
        ';
        $rows = '';

        foreach ($this->missingAnimeList as $media) {
            $img = "<img src='" . $media->coverImage->medium . "'>";
            $id = "<a target='_blank' href='https://anilist.co/anime/" . $media->id . "'>" . $media->id . "</a>";
            $title = $media->title->userPreferred;
            $format = $media->format;
            $status = $media->status;
            $rows .= "<tr><td>$img</td><td>$id</td><td>$title</td><td>$format</td><td>$status</td></tr>";
        }

        return $tableStart . $rows . $tableEnd;
    }

    private function getUser()
    {
        if ($this->accessToken === null) {
            die('ERROR: accessToken is Missing');
        }

        $query = '{
            Viewer {
                id,
                name
            }
        }';

        $response = $this->sendApiRequest($query);
        $jsonDecoded = json_decode($response);
        $userId = $jsonDecoded->data->Viewer->id;
        $userName = $jsonDecoded->data->Viewer->name;

        $user = new classes\User($userId, $userName);
        $this->user = $user;
    }

    private function getUsersCompletedList($page = 1)
    {
        $query = '
            query ($page: Int, $id: Int) {
              Page(page: $page, perPage: 50) {
                pageInfo {
                  total
                  perPage
                  currentPage
                  lastPage
                  hasNextPage
                }
                mediaList(userId: $id, status: COMPLETED) {
                  media {
                    id
                    title {
                      romaji
                      english
                      native
                      userPreferred
                    }
                  }
                }
              }
            }';

        $variables = [
            "page" => $page,
            "id" => $this->user->id,
        ];

        $response = $this->sendApiRequest($query, $variables);
        $jsonDecoded = json_decode($response);
        $pageInfo = $jsonDecoded->data->Page->pageInfo;
        $mediaList = $jsonDecoded->data->Page->mediaList;

        foreach ($mediaList as $mediaObject) {
            $media = new classes\Media([
                'id' => $mediaObject->media->id,
                'title' => $mediaObject->media->title,
            ]);

            $this->completedAnimeList[] = $media;
        }

        if ($pageInfo->hasNextPage === true) {
            $this->getUsersCompletedList($page + 1);
        }
    }

    private function getUsersMissingAnimes(int $page = 1, array $missingIdList = [])
    {
        $completedAnimeIds = [];

        foreach ($this->completedAnimeList as $completed) {
            $completedAnimeIds[] = $completed->id;
        }

        $query = '
            query ($page: Int, $ids: [Int]) {
              Page(page: $page, perPage: 50) {
                pageInfo {
                  total
                  perPage
                  currentPage
                  lastPage
                  hasNextPage
                }
                media(id_in: $ids) {
                  relations {
                    nodes {
                      id
                      idMal
                      title {
                        romaji
                        english
                        native
                        userPreferred
                      }
                      type
                      format
                      status
                      description
                      coverImage {
                        extraLarge
                        large
                        medium
                        color
                      }
                      bannerImage
                    }
                  }
                }
              }
            }';

        $variables = [
            "page" => $page,
            "ids" => $completedAnimeIds,
        ];

        $response = $this->sendApiRequest($query, $variables);
        $jsonDecoded = json_decode($response);
        $pageInfo = $jsonDecoded->data->Page->pageInfo;
        $mediaList = $jsonDecoded->data->Page->media;

        foreach ($mediaList as $mediaParentObject) {
            $nodes = $mediaParentObject->relations->nodes;

            foreach ($nodes as $node) {

                //Only Anime
                if ($node->type === 'MANGA') {
                    continue;
                }

                //Ignore duplicates in relations
                if (in_array($node->id, $missingIdList)) {
                    continue;
                }

                //Ignore already completed animes
                if (in_array($node->id, $completedAnimeIds)) {
                    continue;
                }

                $missingIdList[] = $node->id;
                $this->missingAnimeList[] = new classes\Media([
                    'id' => $node->id,
                    'title' => $node->title,
                    'type' => $node->type,
                    'format' => $node->format,
                    'status' => $node->status,
                    'description' => $node->description,
                    'coverImage' => $node->coverImage,
                    'bannerImage' => $node->bannerImage,
                ]);
            }
        }

        if ($pageInfo->hasNextPage === true) {
            $this->getUsersMissingAnimes($page + 1, $missingIdList);
        }
    }

    private function sendApiRequest(string $query, array $variables = [])
    {
        $client = new Client(['verify' => false]);
        try {
            $repsonse = $client->request('POST', 'https://graphql.anilist.co', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->accessToken,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'json' => [
                    'query' => $query,
                    'variables' => $variables,
                ]
            ]);
        } catch (\Exception $exception) {
            die($exception->getMessage());
        }

        if ($repsonse->getStatusCode() === 429) {
            die('Too Many Requests');
        }

        return $repsonse->getBody()->getContents();
    }
}

$application = new missingAnimeApp();
echo $application->init();
