<?php

namespace Pngx\Iterator;

use Iterator;
use SplFileObject;

/**
 * Class Lazy_CSV_Iterator
 *
 * @since 3.3.0
 *
 */
class Lazy_CSV_Iterator implements Iterator {

	/**
	 * The current pointer of the csv file.
	 *
	 * @since 3.3.0
	 *
	 * @var int
	 */
	private $pointer = 0;

	/**
	 * The current line of the csv file.
	 *
	 * @since 3.3.0
	 *
	 * @var mixed
	 */
	private $line;

	/**
	 * The csv file, or null when it could not be opened.
	 *
	 * @since 3.3.0
	 * @since TBD Nullable — a missing or unreadable file yields an empty
	 *            iterator instead of an uncaught RuntimeException.
	 *
	 * @var SplFileObject|null
	 */
	private $file;

	/**
	 * The csv file path.
	 *
	 * @since 3.3.0
	 *
	 * @var string
	 */
	public $file_path;

	/*
	 * Constructor of Lazy_CSV_Iterator.
	 *
	 * @since 3.3.0
	 *
	 * @param string $filePath The file path string.
	 * @param string $delimiter The delimited of the csv file.
	 * @param string $enclosure The enclosure of a field.
	 * @param string $escape The escaping of data.
	 */
	public function __construct( $file_path, $delimiter = ',', $enclosure = '"', $escape = '\\' ) {
		$this->file_path = $file_path;

		// A deleted upload must degrade to an empty iterator, never fatal a
		// front-end request — the attachment row can outlive the file on disk.
		try {
			$this->file = new \SplFileObject( $this->file_path, 'r' );
		} catch ( \RuntimeException | \LogicException $exception ) {
			error_log( "Pngx Lazy_CSV_Iterator: could not open csv file {$this->file_path}: " . $exception->getMessage() );

			return;
		}

		$this->file->setFlags(
		      \SplFileObject::READ_CSV
		      | \SplFileObject::READ_AHEAD
		      | \SplFileObject::SKIP_EMPTY
		      | \SplFileObject::DROP_NEW_LINE
	    );

		$this->file->setCsvControl($delimiter, $enclosure, $escape);
	}

	/**
	 * Whether the csv file was opened successfully.
	 *
	 * @since TBD
	 *
	 * @return bool True when the file is open and iterable.
	 */
	public function is_readable(): bool {
		return null !== $this->file;
	}

	/**
	 * Get the current line.
	 *
	 * @since 3.3.0
	 *
	 * @return mixed The current line of a csv file.
	 */
	#[\ReturnTypeWillChange]
	public function current() {
		return $this->line;
	}

	/**
	 * Get the next line.
	 *
	 * @since 3.3.0
	 */
	public function next(): void {
		if ( null === $this->file ) {
			return;
		}

		$this->file->next();
		$this->line = $this->file->current();
		$this->pointer ++;
	}

	/**
	 * Get the current key.
	 *
	 * @since 3.3.0
	 *
	 * @return int The current pointer
	 */
	public function key(): int {
		return $this->pointer;
	}

	public function valid(): bool {
		return null !== $this->file && ! empty( $this->line ) && $this->file->valid();
	}

	/**
	 * Reset the iterator.
	 *
	 * @since 3.3.0
	 */
	public function rewind(): void  {
		$this->pointer = 0;

		if ( null === $this->file ) {
			return;
		}

		// Use the SplFileObject iterator API throughout: mixing seek() with
		// fgetcsv() skips the first row since PHP 8.0.1 (fgetcsv after seek
		// returns the line following the sought one).
		$this->file->rewind();
		$this->line = $this->file->current();
	}
}
