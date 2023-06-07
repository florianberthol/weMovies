<?php

namespace App\Controller;

use App\Service\TMDB;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    #[Route('/', name: 'app_index', methods: ['GET'])]
    public function index(TMDB $tmdb): Response
    {
        $genres = $tmdb->getGenres();
        $bestMovie = $tmdb->getBestMovie();
        $bestMovieTrailer = $tmdb->getTrailer($bestMovie['id']);

        return $this->render('index/index.html.twig', [
            'controller_name' => 'IndexController',
            'genres' => $genres,
            'bestMovie' => $bestMovie,
            'bestMovieTrailer' => $bestMovieTrailer
        ]);
    }

    #[Route('/movies', name: 'app_index_movies', methods: ['GET'])]
    public function movies(TMDB $tmdb, Request $request): Response
    {
        $genreString = $request->query->get('genres');
        if (preg_match('/^[1-9|]+$/', $genreString)) {
            $movies = $tmdb->getMoviesByGenre($genreString);
            return new JsonResponse($movies);
        }

        return new JsonResponse([]);
    }

    #[Route('/trailer/{id}', name: 'app_index_trailer', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function trailer(TMDB $tmdb, int $id): Response
    {
        $tmdb->getTrailer($id);

        return new JsonResponse($tmdb->getTrailer($id));
    }

    #[Route('/search', name: 'app_index_search', methods: ['GET'])]
    public function search(TMDB $tmdb, Request $request): Response
    {
        $search = $request->query->get('search');
        $apiResponse = $tmdb->searchMovie($search);

        return new JsonResponse($apiResponse);
    }
}
