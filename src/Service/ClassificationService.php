<?php

namespace App\Service;

use App\Entity\PostPhoto;

class ClassificationService
{

    public function __construct(
        private CloudVisionService $cloudVisionService
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
        $existingCategories = $this->categoryRepository->findBy(['label_en' => $labelsEn]);
        $existingLabelsMap = [];
        /** @var \App\Entity\Category $category */
        foreach ($existingCategories as $category) {
            $existingLabelsMap[$category->getLabelEn()] = $category;
        }

        $labelsToInsert = array_diff($labelsEn, array_keys($existingLabelsMap));
    }
}
