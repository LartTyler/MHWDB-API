<?php
	namespace App\Scraping\Scrapers\Helpers\Csv;

	class CsvReader {
		/**
		 * @var resource
		 */
		protected $file;

		/**
		 * @var callable[]
		 */
		protected $transformers;

		/**
		 * @var string[]
		 */
		protected $headers;

		/**
		 * CsvReader constructor.
		 *
		 * @param string|resource $contentOrResource
		 * @param callable[]      $transformers
		 */
		public function __construct($contentOrResource, array $transformers = []) {
			if (is_string($contentOrResource)) {
				$this->file = tmpfile();

				fwrite($this->file, $contentOrResource);
				fseek($this->file, 0);
			} else if (is_resource($contentOrResource))
				$this->file = $contentOrResource;
			else {
				throw new \InvalidArgumentException('Cannot create a CSV reader from a(n) ' .
					(is_object($contentOrResource) ? get_class($contentOrResource) : gettype($contentOrResource)));
			}

			$this->transformers = $transformers;

			$this->headers = fgetcsv($this->file);
		}

		/**
		 * @return bool
		 */
		public function eof(): bool {
			return feof($this->file);
		}

		/**
		 * @return array|null
		 */
		public function read(): ?array {
			if ($this->eof())
				return null;

			$row = [];
			$unknown = 0;

			foreach (fgetcsv($this->file) as $index => $cell) {
				$key = $this->headers[$index] ?? 'uknown_column_' . $unknown++;

				if (isset($this->transformers[$key]))
					$cell = call_user_func($this->transformers[$key], $cell, $index);

				$row[$key] = $cell;
			}

			return $row;
		}

		/**
		 * {@inheritdoc}
		 */
		public function __destruct() {
			$this->close();
		}

		/**
		 * @return void
		 */
		public function close(): void {
			fclose($this->file);
		}
	}