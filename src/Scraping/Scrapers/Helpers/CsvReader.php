<?php
	namespace App\Scraping\Scrapers\Helpers;

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
		 * @var int|null
		 */
		protected $rowCount = null;

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

			$next = fgetcsv($this->file);

			if (!$next)
				return null;

			foreach ($next as $index => $cell) {
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

		/**
		 * @return int
		 */
		public function getRowCount(): int {
			if ($this->rowCount !== null)
				return $this->rowCount;

			if (!stream_get_meta_data($this->file)['seekable'])
				throw new \RuntimeException('Cannot get row count: file is not seekable');

			$pos = ftell($this->file);

			fseek($this->file, 0);

			$lines = 0;

			while (fgetcsv($this->file))
				++$lines;

			fseek($this->file, $pos);

			return $this->rowCount = $lines - 1;
		}
	}