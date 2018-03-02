<?php
	namespace App\Command;

	use App\Game\ArmorType;
	use App\Scraper\Kiranico\Scrapers\KiranicoArmorScraper;
	use Symfony\Component\Console\Command\Command;
	use Symfony\Component\Console\Input\InputInterface;
	use Symfony\Component\Console\Output\OutputInterface;

	class TestCommand extends Command {
		private const HIGH_RANK_SUFFIX_TRANSLATIONS = [
			'Alpha' => 'α',
			'Beta' => 'β',
		];

		private const ARMOR_TYPE_PHRASES = [
			// -- Standard --
			'Headgear' => ArmorType::HEAD,
			'Headpiece' => ArmorType::HEAD,
			'Helm' => ArmorType::HEAD,
			'Hood' => ArmorType::HEAD,
			'Vertex' => ArmorType::HEAD,
			'Goggles' => ArmorType::HEAD,
			'Hat' => ArmorType::HEAD,
			'Mask' => ArmorType::HEAD,
			'Brain' => ArmorType::HEAD,
			'Lobos' => ArmorType::HEAD,
			'Crown' => ArmorType::HEAD,
			'Glare' => ArmorType::HEAD,
			'Horn' => ArmorType::HEAD,
			'Circlet' => ArmorType::HEAD,
			'Gorget' => ArmorType::HEAD,
			'Spectacles' => ArmorType::HEAD,
			'Eyepatch' => ArmorType::HEAD,
			'Mail' => ArmorType::CHEST,
			'Vest' => ArmorType::CHEST,
			'Thorax' => ArmorType::CHEST,
			'Muscle' => ArmorType::CHEST,
			'Suit' => ArmorType::CHEST,
			'Jacket' => ArmorType::CHEST,
			'Hide' => ArmorType::CHEST,
			'Cista' => ArmorType::CHEST,
			'Armor' => ArmorType::CHEST,
			'Gloves' => ArmorType::GLOVES,
			'Vambraces' => ArmorType::GLOVES,
			'Guards' => ArmorType::GLOVES,
			'Braces' => ArmorType::GLOVES,
			'Brachia' => ArmorType::GLOVES,
			'Grip' => ArmorType::GLOVES,
			'Longarms' => ArmorType::GLOVES,
			'Claws' => ArmorType::GLOVES,
			'Belt' => ArmorType::WAIST,
			'Coil' => ArmorType::WAIST,
			'Elytra' => ArmorType::WAIST,
			'Bowels' => ArmorType::WAIST,
			'Hoop' => ArmorType::WAIST,
			'Spine' => ArmorType::WAIST,
			'Cocoon' => ArmorType::WAIST,
			'Trousers' => ArmorType::LEGS,
			'Greaves' => ArmorType::LEGS,
			'Boots' => ArmorType::LEGS,
			'Crura' => ArmorType::LEGS,
			'Heel' => ArmorType::LEGS,
			'Heels' => ArmorType::LEGS,
			'Leg Guards' => ArmorType::LEGS,
			'Spurs' => ArmorType::LEGS,
			'Crus' => ArmorType::LEGS,
			'Pants' => ArmorType::LEGS,

			// -- Special --
			'Faux Felyne' => ArmorType::HEAD,
		];
		public function configure() {
			$this->setName('test');
		}

		protected function execute(InputInterface $input, OutputInterface $output) {
			$rawName = 'Death Stench Heel Alpha';

			$cleanedName = str_replace([
				'Apha',
				'Barchia',
			], [
				'Alpha',
				'Brachia',
			], preg_replace('/\s+/', ' ', $rawName));

			$parts = array_filter(array_map(function(string $part): string {
				return trim($part);
			}, explode(' ', $cleanedName)));

			$partCount = sizeof($parts);

			if (isset(self::HIGH_RANK_SUFFIX_TRANSLATIONS[$parts[$partCount - 1]])) {
				$rank = self::HIGH_RANK_SUFFIX_TRANSLATIONS[array_pop($parts)];

				--$partCount;
			} else
				$rank = '';

			$armorType = null;
			$partOffsetMax = $partCount - 1;

			foreach (self::ARMOR_TYPE_PHRASES as $phrase => $type) {
				$consumeCount = substr_count($phrase, ' ');

				// If we'd need to consume more of the array than there are pieces, this can't possibly be our match
				if ($consumeCount > $partCount)
					continue;

				$candidate = implode(' ', array_slice($parts, $partOffsetMax - $consumeCount));

				if ($candidate === $phrase) {
					$armorType = $type;

					break;
				}
			}

			if ($armorType === null)
				throw new \RuntimeException('Could not determine armor type from name: ' . $rawName);

			$output->writeln([
				'',
				'Type is ' . $armorType,
				'',
				implode(' ', $parts) . ' ' . $rank,
				'',
			]);
		}
	}