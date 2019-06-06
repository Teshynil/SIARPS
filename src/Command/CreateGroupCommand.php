<?php

namespace App\Command;

use App\Entity\Group;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CreateGroupCommand extends Command {

    protected static $defaultName = 'app:create-group';
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
        $q = new Question("Ingresa nombre del grupo:");
        $groupName = $helper->ask($input, $output, $q);
        $group = new Group();
        $group->setName($groupName);
        $group->setPermissions(0744);

        $this->em->persist($group);
        $this->em->flush();
        //$io->success('You have a new command! Now make it your own! Pass --help to see your options.');
    }

}
