<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserCreateAdminCommand extends Command
{
    protected static $defaultName = 'user:create-admin';
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder)
    {
        parent::__construct(null);
        $this->em = $em;
        $this->passwordEncoder = $passwordEncoder;
    }

    protected function configure()
    {
        $this
            ->setDescription('Create a new admin user')
            ->addArgument('email', InputArgument::REQUIRED, 'Your email')
            ->addArgument('password', InputArgument::REQUIRED, 'Your password')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');
        $plainPassword = $input->getArgument('password');

        $user = new User();
        $user->setEmail($email);
        $hashPassword = $this->passwordEncoder->encodePassword($user, $plainPassword);
        $user->setPassword($hashPassword);

        $user->setRoles(['ROLE_ADMIN']);
        $this->em->persist($user);
        $this->em->flush();


        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');
    }
}
