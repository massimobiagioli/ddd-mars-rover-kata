<?php
declare(strict_types=1);

namespace MarsRoverKata\UI\Controller;

use MarsRoverKata\Application\Query\MarsRover\MarsRoverRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route("/dashboard", name: "get_dashboard", methods: ["GET"])]
    public function getDashboard(MarsRoverRepository $marsRoverRepository): JsonResponse
    {
        try {
            return new JsonResponse($marsRoverRepository->getAll());
        } catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), 500);
        }
    }
}