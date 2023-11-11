<?php
// src/Serializer/TopicNormalizer.php
namespace App\Serializer;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class EntityDenormalizer implements DenormalizerInterface
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    // la logique de dénormalization
    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        // quand supportDenormalization return true, le code ci-dessous est lancé
        // j'utilise l'entityManager pour find l'élément en question (type) en bdd avec son id (data)
        return $this->entityManager->find($type, $data);
    }

    // c'est la fonction qui determine si on utilise bien ce dénormalizer
    public function supportsDenormalization($data, string $type, string $format = null)
    {
        //  Dans quel cas je vais bien utiliser mon denormalizer custom
        // si c'est un entier et que c'est une entité symfo je vais chercher en bdd l'élément qui correspond
        if (is_int($data) && strpos($type, "App\Entity") === 0) {
            return true;
        }
    }
}
