<?php

namespace App\Service;

use App\Entity\Highlight;
use App\Repository\CategoryRepository;
use App\Repository\HighlightRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class HighlightService {

    public function __construct(
        private EntityManagerInterface $entityManager,
        private HighlightRepository $highlightRepository,
        private CategoryRepository $categoryRepository,
        private ValidatorInterface $validator
    ) {}

    public function getAllHighlights(): array
    {
        return $this->highlightRepository->findBy([], ['date' => 'DESC']);
    }

    public function createHighlight(array $data): Highlight
    {
        $highlight = new Highlight();
        $highlight->setTitle($data['title'] ?? '');
        $highlight->setDescription($data['description'] ?? '');
        $highlight->setDate(new \DateTimeImmutable($data['date'] ?? 'now'));

        // Hämta kategori baserat på inskickat namn (eller ID)
        if (!empty($data['category'])) {
            $categoryIdentifier = $data['category'];

            // Kolla om det är ett ID (siffra) eller ett namn (sträng)
            if (is_numeric($categoryIdentifier)) {
                $category = $this->categoryRepository->find($categoryIdentifier);
            } else {
                $category = $this->categoryRepository->findOneBy(['name' => $categoryIdentifier]);
            }

            if (!$category) {
                throw new BadRequestHttpException("Category '{$categoryIdentifier}' not found.");
            }

            $highlight->setCategory($category);
        }

        // Execute built in Symfony validation based on assert-rules
        $errors = $this->validator->validate($highlight);

        if (count($errors) > 0) {
            $errorsString = (string)$errors;
            throw new BadRequestHttpException($errorsString);
        }

        $this->entityManager->persist($highlight);
        $this->entityManager->flush();

        return $highlight;
    }

    public function updateHighlight(int $id, array $data): Highlight {

        $highlight = $this->highlightRepository->find($id);

        if (!$highlight) {
            throw new NotFoundHttpException("Highlight not found.");
        }

        if (isset($data['title'])) {
            $highlight->setTitle($data['title']);
        }
        if (isset($data['description'])) {
            $highlight->setDescription($data['description']);
        }
        if (isset($data['date'])) {
            $highlight->setDate(new \DateTimeImmutable($data['date']));
        }
        if (isset($data['category'])) {
            $categoryIdentifier = $data['category'];
            if (is_numeric($categoryIdentifier)) {
                $category = $this->categoryRepository->find($categoryIdentifier);
            } else {
                $category = $this->categoryRepository->findOneBy(['name' => $categoryIdentifier]);
            }

            if (!$category) {
                throw new BadRequestHttpException("Category '{$categoryIdentifier}' not found.");
            }
            $highlight->setCategory($category);
        }

        // Validate updated object
        $errors = $this->validator->validate($highlight);
        if (count($errors) > 0) {
            $errorsString = (string)$errors;
            throw new BadRequestHttpException($errorsString);
        }
        $this->entityManager->flush();
        return $highlight;
    }

    public function deleteHighlight(int $id): void {

        $highlight = $this->highlightRepository->find($id);

        if (!$highlight) {
            throw new NotFoundHttpException("Highlight not found.");
        }
        $this->entityManager->remove($highlight);
        $this->entityManager->flush();
    }
}
