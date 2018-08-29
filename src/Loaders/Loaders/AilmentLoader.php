<?php
	namespace App\Loaders\Loaders;

	use App\Entity\Ailment;
	use App\Entity\AilmentProtection;
	use App\Entity\Item;
	use App\Entity\Skill;
	use App\Loaders\Schemas\AilmentSchema;
	use App\Loaders\Type;
	use Doctrine\ORM\EntityManagerInterface;

	class AilmentLoader extends AbstractLoader {
		/**
		 * @var EntityManagerInterface
		 */
		protected $manager;

		/**
		 * AilmentLoader constructor.
		 *
		 * @param EntityManagerInterface $manager
		 * @param string                 $sourcePath
		 */
		public function __construct(EntityManagerInterface $manager, string $sourcePath) {
			parent::__construct(Type::AILMENTS, $sourcePath, AilmentSchema::class);

			$this->manager = $manager;
		}

		/**
		 * @param array $context
		 */
		public function load(array $context): void {
			$ailmentSchemas = $this->read();

			foreach ($ailmentSchemas as $ailmentSchema) {
				$ailment = $this->manager->getRepository(Ailment::class)->findOneBy([
					'name' => $ailmentSchema->getName(),
				]);

				if (!$ailment) {
					$ailment = new Ailment($ailmentSchema->getName(), $ailmentSchema->getDescription());

					$this->manager->persist($ailment);
				} else
					$ailment->setDescription($ailmentSchema->getDescription());

				$recovery = $ailment->getRecovery();
				$recovery->getItems()->clear();

				$actions = [];

				foreach ($ailmentSchema->getRecovery() as $recoverySchema) {
					switch ($recoverySchema->getType()) {
						case 'item':
							$item = $this->manager->getRepository(Item::class)->findOneBy([
								'name' => $name = $recoverySchema->getName(),
							]);

							if (!$item)
								throw $this->createTargetNotFoundException('item', $name, $ailment);

							$recovery->getItems()->add($item);

							break;

						case 'action':
							$actions[] = $recoverySchema->getName();

							break;

						default:
							throw $this->createUnrecognizedTypeException('recovery', $recoverySchema->getType(),
								$ailment);
					}
				}

				$recovery->setActions($actions);

				$protection = $ailment->getProtection();
				$protection->getSkills()->clear();
				$protection->getItems()->clear();

				foreach ($ailmentSchema->getProtection() as $protectionSchema) {
					switch ($protectionSchema->getType()) {
						case 'skill':
							$skill = $this->manager->getRepository(Skill::class)->findOneBy([
								'name' => $name = $protectionSchema->getName(),
							]);

							if (!$skill)
								throw $this->createTargetNotFoundException('skill', $name, $ailment);

							$protection->getSkills()->add($skill);

							break;

						case 'item':
							$item = $this->manager->getRepository(Item::class)->findOneBy([
								'name' => $name = $protectionSchema->getName(),
							]);

							if (!$item)
								throw $this->createTargetNotFoundException('item', $name, $ailment);

							$protection->getItems()->add($item);

							break;

						default:
							throw $this->createUnrecognizedTypeException('protection', $protectionSchema->getType(),
								$ailment);
					}
				}
			}

			$this->manager->flush();
		}

		/**
		 * @return AilmentSchema[]
		 */
		protected function read(): array {
			return parent::read();
		}

		/**
		 * @param string  $what
		 * @param string  $name
		 * @param Ailment $ailment
		 *
		 * @return \InvalidArgumentException
		 */
		protected function createTargetNotFoundException(
			string $what,
			string $name,
			Ailment $ailment
		): \InvalidArgumentException {
			return new \InvalidArgumentException(sprintf('Could not find %s named %s while loading ailment %s',
				$what, $name, $ailment->getName()));
		}

		/**
		 * @param string  $what
		 * @param string  $type
		 * @param Ailment $ailment
		 *
		 * @return \InvalidArgumentException
		 */
		protected function createUnrecognizedTypeException(
			string $what,
			string $type,
			Ailment $ailment
		): \InvalidArgumentException {
			return new \InvalidArgumentException(sprintf('Unrecognized %s type named %s while loading ailment %s',
				$what, $type, $ailment->getName()));
		}
	}