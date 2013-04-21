<?php
/**
 * Copyright 2013 Yegor Bugayenko
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

final class Migrations implements Iterator {
	
	/**
	 * Array of all ticket numbers.
	 */
	private $_numbers;
	
	/**
	 * Public ctor.
	 * @param array $opts Options 
	 */
	public function __construct(array $opts) {
		
	}
	
	/**
	 * @see Iterator::current()
	 */
	public function current() {
	}

	/* (non-PHPdoc)
	 * @see Iterator::next()
	 */
	public function next() {
	}

	/* (non-PHPdoc)
	 * @see Iterator::key()
	 */
	public function key() {
	}

	/* (non-PHPdoc)
	 * @see Iterator::valid()
	 */
	public function valid() {
	}

	/* (non-PHPdoc)
	 * @see Iterator::rewind()
	 */
	public function rewind() {
		$this->_trac->call();
	}

}
