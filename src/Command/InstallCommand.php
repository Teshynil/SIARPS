<?php

namespace App\Command;

use App\Entity\Group;
use App\Entity\Project;
use App\Entity\Setting;
use App\Entity\Template;
use App\Entity\User;
use Doctrine\DBAL\Driver\SQLSrv\SQLSrvException;
use Doctrine\DBAL\Exception\TableNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaValidator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class InstallCommand extends Command {

    protected static $defaultName = 'app:install';
    protected $passwordEncoder;
    protected $em;
    protected $fileSystem;
    protected $targetDirectory;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $em, Filesystem $fileSystem, $targetDirectory) {
        $this->passwordEncoder = $passwordEncoder;
        $this->em = $em;
        $this->fileSystem = $fileSystem;
        $this->targetDirectory = $targetDirectory;
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


                try {
                    $command = $this->getApplication()->find('doctrine:migrations:execute');
                    $input = new ArrayInput(['version' => 'Sessions', '--down' => true]);
                    $input->setInteractive(false);
                    $returnCode = $command->run($input, $output);
                } catch (TableNotFoundException $ex) {
                    $io->warning('The database sessions table was not found, but this is not a problem');
                }

                $command = $this->getApplication()->find('doctrine:schema:drop');
                $input = new ArrayInput(['--force' => true]);
                $returnCode = $command->run($input, $output);

                $command = $this->getApplication()->find('doctrine:schema:create');
                $returnCode = $command->run(new ArrayInput([]), $output);

                $command = $this->getApplication()->find('doctrine:migrations:migrate');
                $input = new ArrayInput(['version' => 'Sessions']);
                $input->setInteractive(false);
                $returnCode = $command->run($input, $output);
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

                    $command = $this->getApplication()->find('doctrine:migrations:execute');
                    $input = new ArrayInput(['version' => 'Sessions', '--down' => true]);
                    $input->setInteractive(false);
                    $returnCode = $command->run($input, $output);

                    $command = $this->getApplication()->find('doctrine:schema:create');
                    $returnCode = $command->run(new ArrayInput([]), $output);

                    $command = $this->getApplication()->find('doctrine:migrations:migrate');
                    $input = new ArrayInput(['version' => 'Sessions']);
                    $input->setInteractive(false);
                    $returnCode = $command->run($input, $output);

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
        //
        $this->fileSystem->mkdir($this->targetDirectory, 0754);
        $adminGroup = new Group();
        $adminUser = new User();
        $guestGroup = new Group();
        $globalProject = new Project();
//        $nullTemplate = new Template();
//        $requiredTemplate = new Template();

        $_installStatus = new Setting("installStatus", null, null, $adminUser, $adminGroup);
        $_adminGroup = new Setting("adminGroup", null, null, $adminUser, $adminGroup);
        $_groupConfig = new Setting("groupConfig", null, null, $adminUser, $adminGroup);
        $_ldapGroupConfig = new Setting("ldapGroupConfig", null, null, $adminUser, $adminGroup);
        $_guestGroup = new Setting("guestGroup", null, null, $adminUser, $adminGroup);
        $_globalProject = new Setting("globalProject", null, null, $adminUser, $adminGroup);
//        $_nullTemplate = new Setting("nullTemplate", null, null, $adminUser, $adminGroup);
//        $_requiredTemplate = new Setting("requiredTemplate", null, null, $adminUser, $adminGroup);

        $_ldapHost = new Setting("ldapHost", null, null, $adminUser, $adminGroup);
        $_ldapPort = new Setting("ldapPort", null, null, $adminUser, $adminGroup);
        $_ldapEncryption = new Setting("ldapEncryption", null, null, $adminUser, $adminGroup);
        $_ldapBaseDN = new Setting("ldapBaseDN", null, null, $adminUser, $adminGroup);
        $_ldapReadUser = new Setting("ldapReadUser", null, null, $adminUser, $adminGroup);
        $_ldapReadUserPassword = new Setting("ldapReadUserPassword", null, null, $adminUser, $adminGroup);
        $_ldapUserGroupDN = new Setting("ldapUserGroupDN", null, null, $adminUser, $adminGroup);
        $_ldapAdminGroupDN = new Setting("ldapAdminGroupDN", null, null, $adminUser, $adminGroup);
        $_ldapOwnerGroupDn = new Setting("ldapOwnerGroupDN", null, null, $adminUser, $adminGroup);
        $_ldapGroupPrefix = new Setting("ldapGroupPrefix", null, null, $adminUser, $adminGroup);
        $_ldapLoginAttr = new Setting("ldapLoginAttr", null, null, $adminUser, $adminGroup);
        $_ldapFirstNameAttr = new Setting("ldapFirstNameAttr", null, null, $adminUser, $adminGroup);
        $_ldapLastNameAttr = new Setting("ldapLastNameAttr", null, null, $adminUser, $adminGroup);
        $_ldapEmailAttr = new Setting("ldapEmailAttr", null, null, $adminUser, $adminGroup);
        //$_=new Setting("",null);



        $io->section("Instalacion SIARPS");

        $io->section("Creacion de Grupo Administrativo");

        $groupName = $io->ask('Ingresa el nombre del grupo Administrativo: ', 'admin');

        $io->section("Creacion de Usuario Administrativo");

        $firstName = $io->ask("Ingresa primer nombre del SuperUsuario: ", "Administrador");
        $lastName = $io->ask("Ingresa los apellidos del SuperUsuario: ", "");
        $email = $io->ask("Ingresa el nombre de usuario del SuperUsuario: ", "admin");

        do {
            $password = $io->askHidden("Ingresa la contraseña del SuperUsuario: ");
        } while (empty($password));

        $io->section("Conexión LDAP");

        $groupsConfig = $io->choice("Como se usaran los grupos del SIARPS", ["Usar conexion con LDAP", "Usar grupos internos"]);


        $adminGroup->setName($groupName);
        $adminGroup->setPermissions(07, 06, 00);
        $adminGroup->setDescription("Grupo de Administración del Sistema");
        $adminGroup->setGroup($adminGroup);

        $adminUser->setFirstName($firstName)
                ->setLastName($lastName)
                ->setUsername($email)
                ->setPermissions(07, 04, 00)
                ->setOwner($adminUser)
                ->setGroup($adminGroup)
                ->setPassword($this->passwordEncoder->encodePassword($adminUser, $password));
        $adminGroup->setOwner($adminUser);

        $guestGroup->setName("Guest")
                ->setPermissions(07, 00, 00)
                ->setDescription("Grupo de Usuarios Temporales")
                ->setOwner($adminUser)
                ->setGroup($adminGroup);

        $globalProject->setName("Projecto Global")
                ->setPermissions(07, 07, 04)
                ->setDescription("Projecto para variables globales")
                ->setOwner($adminUser)
                ->setGroup($adminGroup);
        $_globalProject->setValue($globalProject);
        
        $_installStatus->setValue(true);
        $_adminGroup->setValue($adminGroup);

        if ($groupsConfig == 0) {
            $ldapHost = $io->ask('Ingresa el host del Directorio activo: ', '');
            $ldapEncryption = $io->choice('Ingresa el protocolo de conexion con el Directorio activo: ', ["tls" => "tls", "ssl" => "ssl", "none" => "none"], "tls");
            $ldapPort = $io->ask('Ingresa el puerto de conexion con el Directorio activo: ', $ldapEncryption == "ssl" ? 636 : 389);
            $ldapBaseDN = $io->ask('Ingresa el Nombre distinguido de la base de busqueda del Directorio Activo: ', '');
            $ldapReadUser = $io->ask('Ingresa el Nombre distinguido del usuario para lectura del Directorio Activo: ', '');
            $ldapReadUserPassword = $io->ask('Ingresa la contraseña del usuario para lectura del Directorio Activo ¡AVISO: SE GUARDA EN TEXTO PLANO!: ', '');
            $ldapUserGroupDN = $io->ask('Ingresa el Nombre distinguido del grupo asignado para los Usuarios normales: ', '');
            $ldapAdminGroupDN = $io->ask('Ingresa el Nombre distinguido del grupo asignado para los Usuarios administradores: ', '');
            $ldapOwnerGroupDn = $io->ask('Ingresa el Nombre distinguido del grupo asignado para los Lideres de los grupos: ', '');

            $ldapLoginAttr = $io->ask('Ingresa el atributo usado para identificar el login del usuario: ', 'sAMAccountName');
            $ldapFirstNameAttr = $io->ask('Ingresa el atributo usado para identificar los nombres del usuario: ', 'givenName');
            $ldapLastNameAttr = $io->ask('Ingresa el atributo usado para identificar los apellidos del usuario: ', 'sn');
            $ldapEmailAttr = $io->ask('Ingresa el atributo usado para identificar el email del usuario: ', 'email');

            $_groupConfig->setValue("LDAP");
            $ldapGroupConfig = $io->choice("Como se usaran los grupos del SIARPS", ["Usar Unidades Organizacionales como grupos", "Usar grupos con un prefijo"]);
            if ($ldapGroupConfig == 0) {
                $_ldapGroupConfig->setValue("OU");
            } else {
                $_ldapGroupConfig->setValue("PREFIX");
                $ldapGroupPrefix = $io->ask('Ingresa el prefijo del grupo usado para identificar el grupo del usuario: ', 'SIARPS.GROUPS.');
                $_ldapGroupPrefix->setValue($ldapGroupPrefix);
            }

            $_ldapHost->setValue($ldapHost);
            $_ldapPort->setValue($ldapPort);
            $_ldapEncryption->setValue($ldapEncryption);
            $_ldapBaseDN->setValue($ldapBaseDN);
            $_ldapReadUser->setValue($ldapReadUser);
            $_ldapReadUserPassword->setValue($ldapReadUserPassword);
            $_ldapUserGroupDN->setValue($ldapUserGroupDN);
            $_ldapAdminGroupDN->setValue($ldapAdminGroupDN);
            $_ldapOwnerGroupDn->setValue($ldapOwnerGroupDn);
            $_ldapLoginAttr->setValue($ldapLoginAttr);
            $_ldapFirstNameAttr->setValue($ldapFirstNameAttr);
            $_ldapLastNameAttr->setValue($ldapLastNameAttr);
            $_ldapEmailAttr->setValue($ldapEmailAttr);
        } else {
            $_groupConfig->setValue("INTERNAL");
        }
        $_guestGroup->setValue($guestGroup);

//        $nullTemplate->setPermissions(07, 07, 04)
//                ->setName("Archivo")
//                ->setType('File')
//                ->setSettings([
//                    'repeatable' => true
//                ])
//                ->setFile(null);
//        $_nullTemplate->setValue($nullTemplate);
//
//        $requiredTemplate->setPermissions(07, 07, 04)
//                ->setName("Resumen")
//                ->setType('Form')
//                ->setSettings(['fields' =>
//                    [
//                        ['name' => 'avance',
//                            'description' => 'Avance porcentual del proyecto',
//                            'type' => 'range',
//                            'settings' =>
//                            ['label' => 'Avance',
//                                'min' => 0,
//                                'max' => 100,
//                                'step' => 1,
//                                'required' => true,
//                                'choices' => NULL,
//                                'placeholder' => NULL,
//                                'multiple' => false,
//                                'constraints' => '- Range:\r\nmin: 0\r\nmax: 100',
//                            ],
//                        ],
//                    ],
//                    'page' =>
//                    ['size' =>
//                        ['name' => 'letter',
//                            'height' => 27.94,
//                            'width' => 21.59,
//                        ],
//                        'orientation' => 'landscape',
//                        'margin' =>
//                        ['header' => 4.5,
//                            'top' => 0,
//                            'left' => 1.8,
//                            'right' => 1.8,
//                            'bottom' => 0,
//                            'footer' => 1.8,
//                        ],
//                    ],
//        ]);
//        $_requiredTemplate->setValue($requiredTemplate);

        $this->em->persist($adminUser);
        $this->em->persist($adminGroup);
        $this->em->persist($guestGroup);
        $this->em->persist($globalProject);
//        $this->em->persist($nullTemplate);
//        $this->em->persist($requiredTemplate);
        $this->em->persist($_adminGroup);
        $this->em->persist($_groupConfig);
        $this->em->persist($_guestGroup);
        $this->em->persist($_installStatus);
        $this->em->persist($_globalProject);
        $this->em->persist($_ldapGroupConfig);
//        $this->em->persist($_nullTemplate);
//        $this->em->persist($_requiredTemplate);
        $this->em->persist($_ldapHost);
        $this->em->persist($_ldapPort);
        $this->em->persist($_ldapEncryption);
        $this->em->persist($_ldapBaseDN);
        $this->em->persist($_ldapReadUser);
        $this->em->persist($_ldapReadUserPassword);
        $this->em->persist($_ldapUserGroupDN);
        $this->em->persist($_ldapAdminGroupDN);
        $this->em->persist($_ldapOwnerGroupDn);
        $this->em->persist($_ldapGroupPrefix);
        $this->em->persist($_ldapLoginAttr);
        $this->em->persist($_ldapFirstNameAttr);
        $this->em->persist($_ldapLastNameAttr);
        $this->em->persist($_ldapEmailAttr);

        $this->em->flush();
    }

    protected function basicRequirements(SymfonyStyle $io, InputInterface $input, OutputInterface $output) {
        $this->fileSystem->mkdir($this->targetDirectory, 0754);
    }

    protected function reconfigure(SymfonyStyle $io, InputInterface $input, OutputInterface $output) {
        $io->section("Reconfiguración SIARPS");
        $io->section("");
    }

}
