<?php

namespace App\Controller;

use App\Entity\Highlight;
use App\Service\HighlightService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
class HighlightController extends AbstractController
{
    #[Route('/highlights', name: 'api_highlight_index', methods: ['GET'])]
    public function index(HighlightService $highlightService): JsonResponse
    {
        $highlights = $highlightService->getAllHighlights();

        return $this->json($highlights);
    }

    #[Route('/highlights', name: 'api_highlight_create', methods: ['POST'])]
    public function create(Request $request, HighlightService $highlightService): JsonResponse
    {
      try {
          $body = $request->getContent();

          if (!is_object(json_decode($body))) {
              return $this->json(
                  ['error' => 'Request body must be a valid JSON object'],
                  JsonResponse::HTTP_BAD_REQUEST
              );
          }

          $data = json_decode($body, true);
          $highlight = $highlightService->createHighlight($data);

          return $this->json($highlight, JsonResponse::HTTP_CREATED);
      } catch (\Exception $exception) {
          return $this->json(['error' => $exception->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
      }
    }

    #[Route('/highlights/{id}', name: 'api_highlight_update', methods: ['PUT', 'PATCH'])]
    public function update(Request $request, int $id, HighlightService $highlightService): JsonResponse
    {
        try {
            $body = $request->getContent();

            if (!is_object(json_decode($body))) {
                return $this->json(
                    ['error' => 'Request body must be a valid JSON object'],
                    JsonResponse::HTTP_BAD_REQUEST
                );
            }

            $data = json_decode($body, true);
            $highlight = $highlightService->updateHighlight($id, $data);

            return $this->json($highlight, JsonResponse::HTTP_OK);
        } catch (NotFoundHttpException $exception) {
            return $this->json(['error' => $exception->getMessage()], JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $exception) {
            return $this->json(['error' => $exception->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/highlights/{id}', name: 'api_highlight_delete', methods: ['DELETE'])]
    public function delete(int $id, HighlightService $highlightService ): JsonResponse
    {
        try {
          $highlightService->deleteHighlight($id);

            return $this->json(null, JsonResponse::HTTP_NO_CONTENT);
        } catch (NotFoundHttpException $exception) {
            return $this->json(['error' => $exception->getMessage()], JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $exception) {
            return $this->json(['error' => $exception->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

}
