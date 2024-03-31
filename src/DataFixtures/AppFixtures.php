<?php

namespace App\DataFixtures;

use App\Entity\Ability;
use App\Entity\Challenge;
use App\Entity\ChallengeComplete;
use App\Entity\ChallengeReview;
use App\Entity\Course;
use App\Entity\Persona;
use App\Entity\Response;
use App\Entity\Section;
use App\Entity\Technologie;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private Generator $faker;
    private $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->faker = Factory::create('fr_FR');
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $date = new \DateTime();
        $technologiesData = [
            ['JavaScript', true, false],
            ['PHP', true, false],
            ['VueJS', false, true],
            ['Python', true, false]
        ];
        $technologies = [];
        foreach ($technologiesData as $technologieData) {
            $technologie = new Technologie();
            $technologie->setName($technologieData[0])
                ->setIsExecutable($technologieData[1])->setIsFramework($technologieData[2])
                ->setStatus('on')->setCreatedAt($date)->setUpdatedAt($date);

            $manager->persist($technologie);
            $technologies[] = $technologie;
        }

        $users = [];
        for ($i = 0; $i < 10; $i++) {
            $persona = new Persona();
            $created = $this->faker->dateTimeBetween("-1 week", "now");
            $updated = $this->faker->dateTimeBetween($created, "now");
            $persona->setPhone($this->faker->e164PhoneNumber())
                ->setEmail($this->faker->email())
                ->setStatus("on")
                ->setCreatedAt($created)
                ->setUpdatedAt($updated);

            $manager->persist($persona);

            $user = new User();
            $user->setRoles(["USER"]);
            $user->setUsername($this->faker->userName());
            $user->setPassword($this->userPasswordHasher->hashPassword($user, 'user'));
            $createdAt = $this->faker->dateTimeBetween('-1 week', 'now');
            $user->setCreatedAt($createdAt);
            $user->setPersona($persona);
            $user->setUpdatedAt($this->faker->dateTimeBetween($createdAt, 'now'));

            foreach ($technologies as $technologie) {
                if (random_int(0, 1)) {
                    $date = $this->faker->dateTimeBetween($createdAt, 'now');
                    $ability = new Ability();
                    $ability->setLevel(random_int(1, 5))->setTechnologie($technologie)->setCreatedAt($date)->setUpdatedAt($date);
                    $manager->persist($ability);

                    $user->addAbility($ability);
                }
            }
            $users[] = $user;
            $manager->persist($user);
        }


        $date = new \DateTime();
        $courses = [];
        for ($i = 0; $i < 5; $i++) {
            $course = new Course();
            $technologie = $technologies[random_int(0, count($technologies) - 1)];
            $course->setTitle('Apprendre le ' . $technologie->getName() . ' partie ' . $i)
                ->setLevel(random_int(1, 5))->setStatus('on')
                ->setCreatedAt($date)->setUpdatedAt($date)
                ->setTechnologie($technologie);
            $courses[] = $course;
            $manager->persist($course);
        }

        $sections = [];
        foreach ($courses as $course) {
            $sectionNumber = random_int(2, 6);
            for ($i = 1; $i < $sectionNumber; $i++) {
                $section = new Section();
                $section->setType('text')->setPosition($i)->setContent($this->faker->sentence(random_int(40, 200)))
                    ->setCreatedAt($date)->setUpdatedAt($date);
                $section->setCourse($course);

                $manager->persist($section);
                $sections[] = $section;
            }
        }

        foreach ($sections as $section) {
            if (random_int(0, 4)) {
                $validIndex = random_int(1, 4);
                for ($i = 1; $i <= 4; $i++) {
                    $res = new Response();
                    $res->setContent($this->faker->sentence(random_int(3, 8)) . ($i === $validIndex ? '(vrai)' : '(faux)'))
                        ->setIsValid($i === $validIndex)->setCreatedAt($date)->setUpdatedAt($date);
                    $res->setSection($section);

                    $manager->persist($res);
                }
            }
        }

        $challenges = [];
        for ($i = 0; $i < 6; $i++) {
            $challenge = new Challenge();
            $challenge->setDescription($this->faker->sentence(random_int(40, 200)))->setLevel(random_int(1, 5))
                ->setStatus('on')
                ->setCreatedAt($date)->setUpdatedAt($date);
            $challenges[] = $challenge;
            $manager->persist($challenge);
        }

        for ($i = 0; $i < 15; $i++) {
            $challengeComplete = new ChallengeComplete();
            $challengeComplete->setResponse($this->faker->sentence(random_int(40, 200)))
                ->setCreatedAt($date)->setUpdatedAt($date);
            $challengeComplete->setTechnologie($technologies[random_int(0, count($technologies) - 1)]);
            $challengeComplete->setUser($users[random_int(0, count($users) - 1)]);
            $challengeComplete->setChallenge($challenges[random_int(0, count($challenges) - 1)]);

            $manager->persist($challengeComplete);

            if (random_int(0, 1)) {
                $challengeReview = new ChallengeReview();
                $challengeReview->setComment($this->faker->sentence(random_int(10, 25)))
                    ->setNeedRevaluation(!random_int(0, 3))
                    ->setCreatedAt($date)->setUpdatedAt($date);
                $challengeReview->setChallengeComplete($challengeComplete);

                $manager->persist($challengeReview);
            }
        }

        $manager->flush();
    }
}
