<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use App\Entity\Booking;
use App\Entity\Image;
use App\Entity\Role;
use App\Entity\User;
use Faker\Factory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    public function __construct(UserPassWordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    //to execute :php bin/console doctrine:fixtures:load
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('FR-fr');

        $adminRole = new Role();
        $adminRole->setTitle('ROLE_ADMIN');
        $manager->persist($adminRole);

        $adminUser = new User();
        $adminUser->setFirstName('Moyan')
                  ->setLastName('Lei')
                  ->setEmail('moyan.lei@gmail.com')
                  ->setHash($this->encoder->encodePassword($adminUser, 'password'))
                  ->setPicture('https://placehold.it/64x64')
                  ->setIntroduction('gdsvsdvefs,kldv dflngvozelf,vsfmv,fdv')
                  ->setDescription('dfnlsdkvcnsdfoihsnvlsdv,posefaa')
                  ->addUserRole($adminRole);
        $manager->persist($adminUser);            
        //users
        $users = [];
        $genders = ['male', 'female'];
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $gender = $faker->randomElement($genders);
            $pictureId = $faker->numberBetween(1, 99) .'.jpg';
            $picture = "https://randomuser.me/api/portraits/";    
            $picture = $gender == "male" ? "{$picture}men/{$pictureId}" : "{$picture}women/{$pictureId}";
            $hash = $this->encoder->encodePassword($user, 'password');

            $user->setFirstName($faker->firstName($gender))
                 ->setLastName($faker->lastName)
                 ->setEmail($faker->email)
                 ->setIntroduction($faker->sentence())
                 ->setDescription('<p>' . join('</p><p>', $faker->paragraphs(5)) . '</p>')
                 ->setHash($hash)
                 ->setPicture($picture);     
            $manager->persist($user);
            $users[] = $user;     
        }

        //ads
        for ($i = 0; $i < 30; $i++) {
            $ad = new Ad();
            $title = $faker->sentence(5);
            $user = $users[mt_rand(0, count($users)-1)];   
            $ad->setTitle($title)
                ->setCoverImage($faker->imageUrl(1000, 350))
                ->setIntroduction($faker->paragraph(2))
                ->setContent('<p>' . join('</p><p>', $faker->paragraphs(5)) . '</p>')
                ->setPrice(mt_rand(40, 200))
                ->setRooms(mt_rand(1, 5))
                ->setAuthor($user);

             

            for ($j = 1; $j <= mt_rand(2, 5); $j++) {
                $image = new Image();
                $image->setUrl($faker->imageUrl())
                    ->setCaption($faker->sentence())
                    ->setAd($ad);
                $manager->persist($image);
            }

            //booking    
            for ($j= 1; $j< mt_rand(0,10); $j++) {
                $booking = new Booking();
                $createdAt = $faker->dateTimeBetween('-6 months');
                $startDate = $faker->dateTimeBetween('-3 months');
                $duration = mt_rand(3,10);
                $endDate = (clone $startDate)->modify("+{$duration} days");
                $amount = $ad->getPrice() * $duration;
                $booker = $users[mt_rand(0, count($users) -1)];
                $booking->setCreatedAt($createdAt)
                        ->setStartDate($startDate)
                        ->setAd($ad)
                        ->setBooker($booker)
                        ->setEndDate($endDate)
                        ->setAmount($amount)
                        ->setComment($faker->paragraph());

                $manager->persist($booking);        
            }

            $manager->persist($ad);
        }

        $manager->flush();
    }
}
