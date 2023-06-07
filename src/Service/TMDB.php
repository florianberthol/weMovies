<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class TMDB
{
    private const TMDP_VIDEO_TRAILLER = 'Trailer';
    private const TMDP_YOUTUBE_URL = 'https://www.youtube.com/embed/';

    public function __construct(private string $apiKey, private string $apiLang, private HttpClientInterface $httpClient)
    {}

    public function getGenres(): array
    {
        $apiResponse = $this->get('https://api.themoviedb.org/3/genre/movie/list');

        return $this->decodeResponse($apiResponse)['genres'];
    }

    public function getBestMovie(): ?array
    {

        $apiResponse = $this->get('https://api.themoviedb.org/3/discover/movie', ['sort_by' => 'popularity.desc']);

        $bestMovies = $this->decodeResponse($apiResponse);
        if (!empty($bestMovies)) {
            return $bestMovies['results'][0];
        }

        return null;
    }

    public function getMoviesByGenre(string $genres): array
    {
        $apiResponse = $this->get('https://api.themoviedb.org/3/discover/movie', ['with_genres' => $genres]);

        return $this->decodeResponse($apiResponse)['results'];
    }

    public function getTrailer(int $movieId): ?array
    {
        $apiResponse = $this->get('https://api.themoviedb.org/3/movie/' . $movieId . '/videos');

        $videos = $this->decodeResponse($apiResponse)['results'];
        foreach ($videos as $video) {
            if ($video['type'] === self::TMDP_VIDEO_TRAILLER) {
                return [
                    'youtube_url' => self::TMDP_YOUTUBE_URL .  $video['key'],
                    'name' => $video['name']
                ];
            }
        }

        return null;
    }

    public function searchMovie(string $search): array
    {
        $apiResponse = $this->get('https://api.themoviedb.org/3/search/movie', ['query' => $search]);

        return $this->decodeResponse($apiResponse)['results'];
    }

    private function decodeResponse(ResponseInterface $response): ?array
    {
        if ($response->getStatusCode() === 200) {
            return json_decode($response->getContent(), true);
        }

        return null;
    }

    private function get(string $url, array $params = []) {
        $params = ['query' => array_merge(
            $params, ['api_key' => $this->apiKey,'language' => $this->apiLang]
        )];

        return $this->httpClient->request('GET', $url, $params);
    }
}