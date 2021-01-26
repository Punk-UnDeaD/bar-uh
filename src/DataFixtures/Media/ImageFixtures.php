<?php

declare(strict_types=1);

namespace App\DataFixtures\Media;

use App\Model\Media\Entity\DataType\FileInfo;
use App\Model\Media\Entity\DataType\Id;
use App\Model\Media\Entity\DataType\ImageInfo;
use App\Model\Media\Entity\Image;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ImageFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        for ($i = 0; $i < 1000000; ++$i) {
            /** @var string $ext */
            $ext = $faker->randomElement(['jpeg', 'png', 'gif']);
            $image = new Image(
                Id::next(),
                new FileInfo(
                    "public://img-{$i}.{$ext}",
                    "img-{$i}.{$ext}",
                    "image/$ext",
                    $faker->numberBetween(1024, 300 * 1024)
                ),
                new ImageInfo($faker->numberBetween(800, 1200), $faker->numberBetween(600, 1000), $faker->text(80)),
                DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-5 weeks'))
            );

            /** @var list<string> $tags */
            $tags = array_unique(array_filter(preg_split('/[^\w]+/', $faker->text(20)) ?: []));
            $image->setTags($tags);

            $manager->persist($image);
        }
        $manager->flush();
    }
}
