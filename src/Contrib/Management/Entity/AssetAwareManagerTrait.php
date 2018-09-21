<?php
	namespace App\Contrib\Management\Entity;

	use App\Contrib\Management\ContribGroup;
	use App\Entity\Asset;

	trait AssetAwareManagerTrait {
		/**
		 * @param ContribGroup $contribGroup
		 * @param Asset|null   $asset
		 *
		 * @return void
		 */
		protected function exportAsset(ContribGroup $contribGroup, ?Asset $asset): void {
			if (!$asset)
				return;

			$uri = $asset->getUri();
			$path = $contribGroup->getAssetPath($uri);

			if ($path)
				return;

			$imageData = file_get_contents($uri);

			if ($imageData === false)
				throw new \RuntimeException('Could not retrieve ' . $uri);

			$path = ltrim(parse_url($uri, PHP_URL_PATH), '/');

			if (($pos = strpos($path, '/')) !== false)
				$path = substr($path, $pos + 1);

			$contribGroup->putAsset($path, $imageData);
		}
	}