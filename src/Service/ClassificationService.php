<?php

namespace App\Service;

use App\Entity\PostPhoto;
use App\Entity\PostPhotoCategory;
use Doctrine\ORM\EntityManagerInterface;

class ClassificationService
{

    public function __construct(
        private CloudVisionService $cloudVisionService,
        private EntityManagerInterface $em,
        private GeminiService $geminiService
    ) {}

    public function classifyAndLinkPhoto(PostPhoto $photo, String $imagePath)
    {
        // 1. EXTRACTION DES LABELS (Cloud Vision)
        // La fonction dans CloudVisionService doit retourner un tableau de chaînes (labels EN)
        $labelsEn = $this->cloudVisionService->getLabels($imagePath);

        if (empty($labelsEn)) {
            // Si aucun label n'est trouvé, on arrête le processus
            return;
        }

        // 2. IDENTIFICATION ET TRADUCTION/INSERTION DES CATÉGORIES

        // Récupère les catégories existantes par leur label_en
        $existingCategories = $this->em->getRepository(PostPhotoCategory::class)->createQueryBuilder('category')
            ->where('LOWER(category.name) IN (:names)')
            ->setParameter('names', array_map('strtolower', $labelsEn))
            ->getQuery()
            ->getResult();
        $existingLabelsMap = [];

        /** @var \App\Entity\Category $category */
        foreach ($existingCategories as $category) {
            $existingLabelsMap[$category->getName()] = $category;
        }

        $labelsToInsert = array_diff($labelsEn, array_keys($existingLabelsMap));
        dd($labelsToInsert);
        $categoriesToLink = $existingCategories;

        if (!empty($labelsToInsert)) {
            // Appeler la fonction groupée (à implémenter dans votre GeminiService)
            // Elle retourne : [ 'label_en' => 'label_fr', ... ]
            $bulkTranslations = $this->geminiService->bulkTranslate(
                array_values($labelsToInsert) // Assurez-vous d'envoyer uniquement les valeurs
            );

            // Traiter et insérer les nouveaux labels
            foreach ($labelsToInsert as $labelEn) {
                // Récupérer la traduction à partir du tableau groupé
                $labelFr = $bulkTranslations[$labelEn] ?? "Traduction manquante";

                $newCategory = new \App\Entity\Category();
                $newCategory->setLabelEn($labelEn);
                $newCategory->setLabelFr($labelFr);

                $this->entityManager->persist($newCategory);
                $categoriesToLink[] = $newCategory;
            }
        }
    }
}
