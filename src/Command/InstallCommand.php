<?php

namespace App\Command;

use App\Entity\Group;
use App\Entity\Setting;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaValidator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class InstallCommand extends Command {

    protected static $defaultName = 'app:install';
    protected $passwordEncoder;
    protected $em;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $em) {
        $this->passwordEncoder = $passwordEncoder;
        $this->em = $em;
        parent::__construct();
    }

    protected function configure() {
        $this
                ->setDescription('Inicia el proceso de instalación de SIARPS')
                ->addOption('force', null, InputOption::VALUE_NONE, 'Forza la ejecución de la instalación sin importar el estado de instalación')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $io = new SymfonyStyle($input, $output);
        $force = false;
        if ($input->getOption('force')) {
            $force = true;
        }

        $validator = new SchemaValidator($this->em);

        $io->section('Verificación');
        $verificacion = 0;
        if ($errors = $validator->validateMapping()) {
            foreach ($errors as $className => $errorMessages) {
                $io->text(
                        sprintf(
                                '<error>[FAIL]</error> The entity-class <comment>%s</comment> mapping is invalid:',
                                $className
                        )
                );

                $io->listing($errorMessages);
                $io->newLine();
            }

            ++$verificacion;
        } else {
            $io->success('Mapeo Correcto.');
        }
        $io->section('Database');
        $databaseValidator = true;
        try {
            $databaseValidator = $validator->schemaInSyncWithMetadata();
        } catch (\Doctrine\DBAL\Driver\SQLSrv\SQLSrvException $exc) {
            $io->error('No se pudo realizar la conexion con la base de datos.');
            if ($force) {
                $io->section('Intentando crear base de datos');
                try {
                    $command = $this->getApplication()->find('doctrine:database:create');
                    $returnCode = $command->run(new ArrayInput([]), $output);

                    $command = $this->getApplication()->find('doctrine:schema:create');
                    $returnCode = $command->run(new ArrayInput([]), $output);
                } catch (\Doctrine\DBAL\Driver\SQLSrv\SQLSrvException $exc) {
                    $io->error('No se pudo realizar la conexion con la base de datos.\n Revise la cadena de conexión en el archivo .env');
                }
            }
        }
        if (!$databaseValidator) {
            $io->error('The database schema is not in sync with the current mapping file.');
            ++$verificacion;
            if ($force) {
                $command = $this->getApplication()->find('doctrine:database:drop');
                $returnCode = $command->run(new ArrayInput(['--force' => true]), $output);

                $command = $this->getApplication()->find('doctrine:database:create');
                $returnCode = $command->run(new ArrayInput([]), $output);

                $command = $this->getApplication()->find('doctrine:schema:create');
                $returnCode = $command->run(new ArrayInput([]), $output);
            } else {
                $io->error('Es necesario asegurarse que la base de datos este correctamente configurada y sincronizada con el mapeo.');
                $io->text('Puede usar la opcion --force para que el comando realize las correcciones, NO se debe usar en produccion.');
            }
        } else {
            $io->success('The database schema is in sync with the mapping files.');
        }

        $installStatus = $this->em->find(Setting::class, "installStatus");
        if ($installStatus == null) {
            $this->install($io, $input, $output);
        } elseif ($installStatus->getValue() != false) {
            if ($force) {
                $command = $this->getApplication()->find('doctrine:database:drop');
                $returnCode = $command->run(new ArrayInput(['--force' => true]), $output);

                $command = $this->getApplication()->find('doctrine:database:create');
                $returnCode = $command->run(new ArrayInput([]), $output);

                $command = $this->getApplication()->find('doctrine:schema:create');
                $returnCode = $command->run(new ArrayInput([]), $output);

                $this->install($io, $input, $output);
            } else {
                $io->warning('Se encontro informacion en la base de datos, Puede usar la opcion --force para ignorar y continuar.');
            }
        } else {
            if ($force) {
                $this->install($io, $input, $output);
            }
        }
    }

    protected function install(SymfonyStyle $io, InputInterface $input, OutputInterface $output) {
        $helper = $this->getHelper('question');
        $io->section("Instalacion SIARPS");

        $io->section("Creacion de Grupo Administrativo");

        $groupName = $io->ask('Ingresa el nombre del grupo Administrativo: ', 'admin');
        $group = new Group();
        $group->setName($groupName);
        $group->setPermissions(0760);
        $group->setDescription("Grupo de Administración del Sistema");
        $group->setGroup($group);

        $io->section("Creacion de Usuario Administrativo");


        $firstName = $io->ask("Ingresa primer nombre del SuperUsuario: ", "Administrador");
        $lastName = $io->ask("Ingresa los apellidos del SuperUsuario: ", "");
        $email = $io->ask("Ingresa el correo del SuperUsuario: ", "admin@siarps.local");

        do {
            $password = $io->askHidden("Ingresa la contraseña del SuperUsuario: ");
        } while (empty($password));


        $io->section("Guardando cambios a la Base de datos");
        $user = new User();
        $user->setFirstName($firstName)
                ->setLastName($lastName)
                ->setEmail($email)
                ->setPermissions(0740)
                ->setOwner($user)
                ->setGroup($group);
        $group->setOwner($user);

        $password = $this->passwordEncoder->encodePassword($user, $password);
        $user->setPassword($password);

        $this->em->persist(new Setting("installStatus", false, true, $user, $group, 0770));
        $this->em->persist($user);
        $this->em->persist($group);
        $this->em->flush();
        $this->em->persist(new Setting("adminGroup", true, $group->getId(), $user, $group, 0770));
        $this->em->flush();
    }

}
