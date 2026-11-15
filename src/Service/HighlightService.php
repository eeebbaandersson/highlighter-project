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

        $this->validateInputTypes($data);

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

    public function updateHighlight(int $id, array $data): Highlight
    {
        $highlight = $this->highlightRepository->find($id);

        if (!$highlight) {
            throw new NotFoundHttpException("Highlight not found.");
        }

        $this->validateInputTypes($data);

        if (isset($data['title'])) {
            $highlight->setTitle($data['title']);
        }

        if (array_key_exists('description', $data)) {
            $highlight->setDescription($data['description']);
        }

        if (isset($data['date'])) {
            $highlight->setDate(new \DateTimeImmutable($data['date']));
        }

        if (array_key_exists('category', $data)) {
            $categoryIdentifier = $data['category'];

            if ($categoryIdentifier === null) {
                $highlight->setCategory(null);
            } else {
                if (is_numeric($categoryIdentifier)) {
                    $category = $this->categoryRepository->find($categoryIdentifier);
                } else {
                    $category = $this->categoryRepository->findOneBy([
                        'name' => $categoryIdentifier,
                    ]);
                }

                if (!$category) {
                    throw new BadRequestHttpException(
                        "Category '{$categoryIdentifier}' not found."
                    );
                }

                $highlight->setCategory($category);
            }
        }

        $errors = $this->validator->validate($highlight);

        if (count($errors) > 0) {
            throw new BadRequestHttpException((string) $errors);
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

    private function validateInputTypes(array $data): void
    {
        foreach (['title', 'description', 'date'] as $field) {
            if (isset($data[$field]) && !is_string($data[$field])) {
                throw new BadRequestHttpException("$field must be a string.");
            }
        }

        if (isset($data['category'])
            && !is_int($data['category'])
            && !is_string($data['category'])) {
            throw new BadRequestHttpException(
                'category must be an integer ID or a string.'
            );
        }
    }
}
