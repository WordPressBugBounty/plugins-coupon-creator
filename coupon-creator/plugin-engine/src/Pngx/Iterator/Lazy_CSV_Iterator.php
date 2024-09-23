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
	 * The csv file.
	 *
	 * @since 3.3.0
	 *
	 * @var SplFileObject
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

		$this->file = new \SplFileObject( $this->file_path, 'r' );
		$this->file->setFlags(
		      \SplFileObject::READ_CSV
		      | \SplFileObject::READ_AHEAD
		      | \SplFileObject::SKIP_EMPTY
		      | \SplFileObject::DROP_NEW_LINE
	    );

		$this->file->setCsvControl($delimiter, $enclosure, $escape);
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
		$this->line = $this->file->fgetcsv();
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
		return ! empty( $this->line ) && $this->file->valid();
	}

	/**
	 * Reset the iterator.
	 *
	 * @since 3.3.0
	 */
	public function rewind(): void  {
		$this->pointer = 0;
		$this->file->seek( 0 );
		$this->line = $this->file->fgetcsv();
	}
}
