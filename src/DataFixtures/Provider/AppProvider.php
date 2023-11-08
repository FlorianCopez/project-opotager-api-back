<?php

namespace App\DataFixtures\Provider;

class AppProvider
{
    private $cities = [
        'Paris',
        'Versailles',
        'Poissy',
        'Orgeval',
        'Les Mureaux',
        'Gargenville',
        'Mantes la Jolie',
        'Limay',
        'Rouen',
        'Caen',
        'Lille',
        'Marseille',
        'Bordeaux',
        'Rennes',
        'Toulouse',
        'Montpellier',
        'Castres',
        'Annecy',
        'Lyon',
        'Strasbourg',
        'Dieppe',
        'Le mans',
        'Nantes',
        'Rennes',
        'Biarritz',
        'Sete',
        'Le Havre'
    ];

    /**
     * Get a random cities
     * @return string random cities
     */
    public function cities(): string
    {
        return $this->cities[array_rand($this->cities)];
    }
}
