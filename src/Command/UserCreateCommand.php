<?php
	namespace App\Command;

	use App\Entity\User;
	use App\Security\Role;
	use Doctrine\ORM\EntityManagerInterface;
	use Symfony\Component\Console\Command\Command;
	use Symfony\Component\Console\Input\InputArgument;
	use Symfony\Component\Console\Input\InputInterface;
	use Symfony\Component\Console\Input\InputOption;
	use Symfony\Component\Console\Output\OutputInterface;
	use Symfony\Component\Console\Question\ChoiceQuestion;
	use Symfony\Component\Console\Style\SymfonyStyle;
	use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

	/**
	 * Class UserCreateCommand
	 *
	 * @package App\Command
	 */
	class UserCreateCommand extends Command {
		/**
		 * @var EntityManagerInterface
		 */
		protected $entityManager;

		/**
		 * @var UserPasswordEncoderInterface
		 */
		protected $passwordEncoder;

		/**
		 * UserCreateCommand constructor.
		 *
		 * @param EntityManagerInterface       $entityManager
		 * @param UserPasswordEncoderInterface $passwordEncoder
		 */
		public function __construct(
			EntityManagerInterface $entityManager,
			UserPasswordEncoderInterface $passwordEncoder
		) {
			parent::__construct();

			$this->entityManager = $entityManager;
			$this->passwordEncoder = $passwordEncoder;
		}

		/**
		 * @return void
		 */
		protected function configure(): void {
			$this
				->setName('app:user:create')
				->addArgument('email', InputArgument::REQUIRED)
				->addArgument('displayName', InputArgument::REQUIRED)
				->addOption('password', 'p', InputOption::VALUE_REQUIRED)
				->addOption('role', 'r', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY);
		}

		/**
		 * @param InputInterface  $input
		 * @param OutputInterface $output
		 *
		 * @return void
		 */
		protected function interact(InputInterface $input, OutputInterface $output): void {
			$io = new SymfonyStyle($input, $output);

			if (!$input->getArgument('email'))
				$input->setArgument('email', $io->ask('Enter an email address'));

			if (!$input->getArgument('displayName'))
				$input->setArgument('displayName', $io->ask('Enter a display name (may be changed later)'));

			if (!$input->getOption('role')) {
				$question = new ChoiceQuestion('Choose one or more roles for the user', array_values(Role::all()));
				$question->setMultiselect(true);

				$input->setOption('role', $io->askQuestion($question));
			}

			if (!$input->getOption('password'))
				$input->setOption('password', $io->askHidden('Enter a password (leave blank to send registration email)'));
		}

		/**
		 * @param InputInterface  $input
		 * @param OutputInterface $output
		 *
		 * @return void
		 */
		protected function execute(InputInterface $input, OutputInterface $output): void {
			$io = new SymfonyStyle($input, $output);

			$errors = [];

			if (!preg_match('/^.+@.+\\..+$/', $email = $input->getArgument('email')))
				$errors[] = 'The email address must follow the pattern "*@*.*"';

			if (strlen($displayName = $input->getArgument('displayName')) < 3)
				$errors[] = 'A displayName must contain at least 3 characters';

			if (strlen($password = $input->getOption('password')) < 5)
				$errors[] = 'A password must contain at least 5 characters (activation email not yet supported, sorry)';

			if ($errors) {
				$io->error('Could not create user. Please correct the following error(s), then try again.');
				$io->listing($errors);

				return;
			}

			/** @var User|null $user */
			$user = $this->entityManager->createQueryBuilder()
				->from(User::class, 'u')
				->select('u')
				->where('u.email = :email')
				->orWhere('u.displayName = :displayName')
				->setParameter('email', $email)
				->setParameter('displayName', $displayName)
				->setMaxResults(1)
				->getQuery()
					->getOneOrNullResult();

			if ($user) {
				$matched = [];

				if ($user->getEmail() === $email)
					$matched[] = 'email address';

				if ($user->getDisplayName() === $displayName)
					$matched[] = 'display name';

				$io->error('A user already exists with that ' . implode(' and ', $matched));

				return;
			}

			$user = new User($email, $displayName);
			$user->setPassword($this->passwordEncoder->encodePassword($user, $password));

			foreach ($input->getOption('role') as $role)
				$user->grantRole($role);

			$this->entityManager->persist($user);
			$this->entityManager->flush();

			$io->success('Done!');
		}
	}