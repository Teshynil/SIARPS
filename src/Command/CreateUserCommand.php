<?php

namespace App\Command;

use App\Entity\Group;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CreateUserCommand extends Command {

    protected static $defaultName = 'app:create-user';
    protected $passwordEncoder;
    protected $em;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $em) {
        $this->passwordEncoder = $passwordEncoder;
        $this->em = $em;
        parent::__construct();
    }

    protected function configure() {
        $this
                ->setDescription('Add a short description for your command')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $io = new SymfonyStyle($input, $output);
        $helper = $this->getHelper('question');
        $q = new Question("Ingresa primer nombre del usuario:");
        $firstName = $helper->ask($input, $output, $q);
        $q = new Question("Ingresa los apellidos del usuario:");
        $lastName = $helper->ask($input, $output, $q);
        $q = new Question("Ingresa el correo del usuario:");
        $email = $helper->ask($input, $output, $q);

        $q = new Question("Ingresa la contraseña del usuario:");
        $password = $helper->ask($input, $output, $q);

        $groups = $this->em->getRepository(Group::class)->findAll();
        $groups = array_map(function($o) {
            return $o->getName();
        }, $groups);
        $q = new ChoiceQuestion("Ingresa el grupo:", $groups);
        $groupName = $helper->ask($input, $output, $q);
        $user = new User();
        $group = $this->em->getRepository(Group::class)->findOneByName($groupName);

        $user->setFirstName($firstName)
                ->setLastName($lastName)
                ->setEmail($email)
                ->setPermissions(0744)
                ->setGroup($group);
        $password = $this->passwordEncoder->encodePassword($user, $password);
        $user->setPassword($password);
        $this->em->persist($user);
        $this->em->flush();
    }

}
