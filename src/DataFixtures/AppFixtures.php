<?php

namespace App\DataFixtures;

use App\Entity\Application;
use App\Entity\Donnes;
use App\Entity\OS;
use App\Entity\Responsable;
use App\Entity\Source;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;


class AppFixtures extends Fixture
{

    public function __construct()

    {
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {

        $this->manager = $manager;

        //$this->oSFixture();
        //$this->responsableFixture();

        //$this->applicationFixture();


        // a lancé deux fois en modifiant l'id
        //$this->donnesFixture();
        //$this->sourceFixture();
    }

    private function oSFixture()
    {
        //OS (ajout android et iOS )
        $lstOS = ['android', 'iOS'];
        for ($i = 0; $i < 2; $i++) {
            $os = new OS();
            $os->setNom($lstOS[$i]);
            $this->manager->persist($os);
        }
        //flush
        $this->manager->flush();

    }

    private function sourceFixture()
    {
        //Source (ajout de fake url equivalent au nombre d'applications a surveiller 60 pour android et 60 pour iOS)

        //1 à 3 car l'id en base de données commence à 1 et qu'on aura deux choix dans la base de données (android et ios)
        for ($i = 1; $i < 3; $i++) {
            for ($j = 0; $j < 20; $j++) {

                $source = new Source();
                $source->setUrl($this->faker->url);
                //iOS
                $os = $this->manager->getRepository(OS::class)->find($i);
                $source->setOs($os);
                $lstApplication = $this->manager->getRepository(Application::class)->findAll();
                $source->setApplication($lstApplication[$j]);
                $this->manager->persist($source);
            }
        }
        //flush
        $this->manager->flush();
    }

    private function responsableFixture()
    {
        //Responsable
        $lstNom = ["Duck"];
        $lstPrenom = ["Riri", "Fifi", "Loulou", "Donald"];
        for ($i = 0; $i < 4; $i++) {

            $responsable = new Responsable();
            $responsable->setNom($lstNom[0]);
            $responsable->setPrenom($lstPrenom[$i]);
            $this->manager->persist($responsable);
        }
        $this->manager->flush();
    }

    private function donnesFixture()
    {
        $lstApplication = $this->manager->getRepository(Application::class)->findAll();
        for ($i = 0; $i < 20; $i++) {
            for ($j = 0; $j < 365; $j++) {
                $donnes = new Donnes();
                $donnes->setApplication($lstApplication[$i]);
                //on boucle sur les jours de l'année
                $donnes->setDateCollect(new \DateTime($this->getDayOfYear()[$j]));
                //premier chiffre = nb chiffre apres la virgule.
                // deuxieme et troisième chiffres la range
                $os = $this->manager->getRepository(OS::class)->findAll();
                $donnes->setOs($os[0]);
                $donnes->setRating($this->faker->randomFloat(2, 1, 5));
                $donnes->setVote($this->faker->randomNumber(4, false));
                $this->manager->persist($donnes);
            }
        }
        $this->manager->flush();
    }
    private function getDayOfYear(): ArrayCollection{
        $from = new \DateTime('-1 year');
        $to = new \DateTime('now');
        $interval = \DateInterval::createFromDateString('1 day');
        $days = new \DatePeriod($from, $interval, $to);
        $lstDate = new ArrayCollection();
        /** @var \DateTimeInterface $day */
        foreach ($days as $day) {
            $lstDate->add($day->format('Y-m-d'));
        }
        return $lstDate;
    }

    private function applicationFixture()
    {
        for ($i = 1; $i <= 20; $i++) {
            $application = new Application();
            $application->setNom($this->faker->word());
            $administrateur = $this->manager->getRepository(Responsable::class)->find($this->faker->numberBetween(1, 4));

            $application->setAdministrateur($administrateur);
            $this->manager->persist($application);
        }
        $this->manager->flush();
    }
}
