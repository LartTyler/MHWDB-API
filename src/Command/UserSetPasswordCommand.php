<?php
	namespace App\Command;

	use App\Entity\User;
	use Doctrine\ORM\EntityManagerInterface;
	use Symfony\Component\Console\Command\Command;
	use Symfony\Component\Console\Input\InputArgument;
	use Symfony\Component\Console\Input\InputInterface;
	use Symfony\Component\Console\Output\OutputInterface;
	use Symfony\Component\Console\Style\SymfonyStyle;
	use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

	class UserSetPasswordCommand extends Command {
		/**
		 * @var EntityManagerInterface
		 */
		protected $entityManager;

		/**
		 * @var UserPasswordEncoderInterface
		 */
		protected $encoder;

		/**
		 * UserSetPasswordCommand constructor.
		 *
		 * @param EntityManagerInterface $entityManager
		 */
		public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder) {
			parent::__construct();

			$this->entityManager = $entityManager;
			$this->encoder = $encoder;
		}

		/**
		 * {@inheritdoc}
		 */
		protected function configure(): void {
			$this
				->addArgument('email', InputArgument::REQUIRED)
				->addArgument('password', InputArgument::REQUIRED);
		}

		/**
		 * {@inheritdoc}
		 */
		protected function interact(InputInterface $input, OutputInterface $output): void {
			$io = new SymfonyStyle($input, $output);

			if (!$input->getArgument('password'))
				$input->setArgument('password', $io->askHidden('Enter a new password'));
		}

		/**
		 * {@inheritdoc}
		 */
		protected function execute(InputInterface $input, OutputInterface $output): int {
			$io = new SymfonyStyle($input, $output);

			/** @var User|null $user */
			$user = $this->entityManager->getRepository(User::class)->findOneBy(
				[
					'email' => $input->getArgument('email'),
				]
			);

			if (!$user) {
				$io->error('Could not find a user with the email address ' . $input->getArgument('email') . '.');

				return 1;
			}

			$password = $input->getArgument('password');

			if (strlen($password) === 0) {
				$io->error('You must provide a password.');

				return 1;
			}

			$user->setPassword($this->encoder->encodePassword($user, $password));

			$this->entityManager->flush();

			$io->success('Password updated.');

			return 0;
		}
	}