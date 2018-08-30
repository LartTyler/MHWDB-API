<?php
	namespace App\Scraping\Scrapers\Helpers;

	use App\Utility\StringUtil;
	use Symfony\Component\DomCrawler\Crawler;

	final class HtmlHelper {
		/**
		 * HtmlHelper constructor.
		 */
		private function __construct() {
		}

		/**
		 * @param Crawler       $nodes
		 * @param callable|null $keyExtractor
		 * @param callable|null $valueExtractor
		 *
		 * @return string[]
		 */
		public static function parseHtmlToKeyValuePairs(
			Crawler $nodes,
			callable $keyExtractor = null,
			callable $valueExtractor = null
		): array {
			$keyExtractor = $keyExtractor ?? function(Crawler $node): string {
					$key = StringUtil::camelize(StringUtil::clean($node->children()->first()->text()));

					return rtrim($key, ':');
				};

			$valueExtractor = $valueExtractor ?? function(Crawler $node): ?string {
					$value = StringUtil::clean($node->children()->last()->text());

					if (!$value || $value === 'N/A')
						return null;

					return $value;
				};

			$values = [];

			for ($i = 0, $ii = $nodes->count(); $i < $ii; $i++) {
				$node = $nodes->eq($i);
				$key = call_user_func($keyExtractor, $node);

				$values[$key] = call_user_func($valueExtractor, $node, $key);
			}

			return $values;
		}
	}