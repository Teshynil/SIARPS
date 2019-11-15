<?php

namespace App\Command;

use App\Entity\Group;
use App\Entity\Setting;
use App\Entity\Template;
use App\Entity\User;
use Doctrine\DBAL\Driver\SQLSrv\SQLSrvException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaValidator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
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
                ->addOption('reconfigure', null, InputOption::VALUE_NONE, 'Permite cambiar la configuración inicial sin alterar el estado de la instalación')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $io = new SymfonyStyle($input, $output);
        $force = false;
        $reconfigure = false;
        if ($input->getOption('force')) {
            $force = true;
        }

        if ($input->getOption('reconfigure')) {
            $reconfigure = true;
            if ($force) {
                $io->text('No se puede usar la opcion --reconfigure con la opcion --force.');
                return;
            }
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
        } catch (SQLSrvException $exc) {
            $io->error('No se pudo realizar la conexion con la base de datos.\n Revise la cadena de conexión en el archivo .env');
            return;
        }
        if (!$databaseValidator) {
            $io->error('The database schema is not in sync with the current mapping file.');
            ++$verificacion;
            if ($force) {

                $command = $this->getApplication()->find('doctrine:schema:drop');
                $returnCode = $command->run(new ArrayInput(['--force' => true]), $output);

                $command = $this->getApplication()->find('doctrine:schema:create');
                $returnCode = $command->run(new ArrayInput([]), $output);
            } else {
                $io->error('Es necesario asegurarse que la base de datos este correctamente configurada y sincronizada con el mapeo.');
                $io->text('Puede usar la opcion --force para que el comando realize las correcciones, NO se debe usar en produccion.');
                return;
            }
        } else {
            $io->success('The database schema is in sync with the mapping files.');
        }

        $installStatus = $this->em->find(Setting::class, "installStatus");

        if ($reconfigure) {
            if ($installStatus == null) {
                $io->error('No se encontro la base de datos de la instalacion.');
            } elseif ($installStatus->getValue() == true) {
                $this->reconfigure($io, $input, $output);
            } else {
                $io->error('Hay un error en la instalación, favor de reinstalar.');
            }
        } else {
            if ($installStatus == null) {
                $this->install($io, $input, $output);
            } elseif ($installStatus->getValue() != false) {
                if ($force) {

                    $command = $this->getApplication()->find('doctrine:schema:drop');
                    $returnCode = $command->run(new ArrayInput(['--force' => true]), $output);

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
    }

    protected function install(SymfonyStyle $io, InputInterface $input, OutputInterface $output) {
        $io->section("Instalacion SIARPS");

        $io->section("Creacion de Grupo Administrativo");

        $groupName = $io->ask('Ingresa el nombre del grupo Administrativo: ', 'admin');
        $group = new Group();
        $group->setName($groupName);
        $group->setPermissions(07, 06, 00);
        $group->setDescription("Grupo de Administración del Sistema");
        $group->setGroup($group);

        $io->section("Creacion de Usuario Administrativo");

        $firstName = $io->ask("Ingresa primer nombre del SuperUsuario: ", "Administrador");
        $lastName = $io->ask("Ingresa los apellidos del SuperUsuario: ", "");
        $email = $io->ask("Ingresa el nombre de usuario del SuperUsuario: ", "admin");

        do {
            $password = $io->askHidden("Ingresa la contraseña del SuperUsuario: ");
        } while (empty($password));

        $io->section("Conexión LDAP");
        $groupsConfig = $io->choice("Como se usaran los grupos del SIARPS", ["Usar conexion con LDAP", "Usar grupos internos"]);

        $user = new User();
        $user->setFirstName($firstName)
                ->setLastName($lastName)
                ->setUsername($email)
                ->setPermissions(07, 04, 00)
                ->setOwner($user)
                ->setGroup($group);
        $group->setOwner($user);

        $password = $this->passwordEncoder->encodePassword($user, $password);
        $user->setPassword($password);

        $this->em->persist(new Setting("installStatus", null, true, $user, $group, 07, 07, 00));
        $this->em->persist($user);
        $this->em->persist($group);
        $this->em->flush();
        $this->em->persist(new Setting("adminGroup", Group::class, $group->getId(), $user, $group, 07, 07, 00));
        $this->em->flush();

        if ($groupsConfig == 0) {
            $this->em->persist(new Setting("groupConfig", null, "LDAP", $user, $group, 07, 07, 00));
            $this->em->persist(new Setting("guestGroup", Group::class, null, $user, $group, 07, 07, 00));
            $ldapGroupConfig = $io->choice("Como se usaran los grupos del SIARPS", ["Usar Unidades Organizacionales como grupos", "Usar grupos con un prefijo"]);
            if ($ldapGroupConfig == 0) {
                $this->em->persist(new Setting("ldapGroupConfig", null, "OU", $user, $group, 07, 07, 00));
            } else {
                $this->em->persist(new Setting("ldapGroupConfig", null, "PREFIX", $user, $group, 07, 07, 00));
            }
        } else {
            $this->em->persist(new Setting("ldapGroupConfig", null, null, $user, $group, 07, 07, 00));
            $this->em->persist(new Setting("groupConfig", null, "INTERNAL", $user, $group, 07, 07, 00));
            $ggroup = new Group();
            $ggroup->setName("Guest");
            $ggroup->setPermissions(07, 00, 00);
            $ggroup->setDescription("Grupo de Usuarios Temporales");
            $ggroup->setOwner($user);
            $ggroup->setGroup($group);
            $this->em->persist($ggroup);
            $this->em->flush();
            $this->em->persist(new Setting("guestGroup", Group::class, $ggroup->getId(), $user, $group, 07, 07, 00));
        }
        
        $nullTemplate = new Template();
        $nullTemplate->setPermissions(07, 07, 04)
                ->setName("Archivo")
                ->setType('File')
                ->setSettings([
                    'repeatable'=>true
                ])
                ->setFile(null);
        $this->em->persist($nullTemplate);
        $this->em->flush();
        $this->em->persist(new Setting("nullTemplate", Template::class, $nullTemplate->getId(), $user, $group, 07, 07, 00));
        $this->em->flush();
    }

    protected function reconfigure(SymfonyStyle $io, InputInterface $input, OutputInterface $output) {
        $io->section("Reconfiguración SIARPS");
        $io->section("");
    }

}
