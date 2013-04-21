<?php namespace tg;
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

require_once './Github.php';
require_once './TicketMigration.php';
require_once 'XML/RPC2/Client.php';

/**
 * Iterator of all ticket migrations.
 */
final class Migrations implements \Iterator {
    /**
     * Trac client.
     * @var \XML_RPC2_Client
     */
    private $_trac;
    /**
     * Github client.
     * @var Github
     */
    private $_github;
    /**
     * All Trac ticket numbers.
     * @var array
     */
    private $_numbers;
    /**
     * Current number in the array.
     * @var int
     */
    private $_position;
    /**
     * Public ctor.
     * @param array $opts Options
     */
    public function __construct(array $opts) {
        if (!isset($opts['t'])) {
            throw new \Exception('Trac URL not defined by -t=...');
        }
        ini_set("default_socket_timeout", -1);
        log('PHP socket timeout reset to enable long-running queries');
        $this->_trac = \XML_RPC2_Client::create(
            $opts['t'],
            array(
                'prefix' => 'ticket.',
                'encoding' => 'utf-8',
            )
        );
        log('Trac XML RPC client configured at ' . $opts['t']);
        if (!isset($opts['u'])) {
            throw new \Exception('GitHub user name not defined by -u=...');
        }
        if (!isset($opts['p'])) {
            throw new \Exception('GitHub password not defined by -p=...');
        }
        if (!isset($opts['r'])) {
            throw new \Exception('GitHub repository name not defined by -r=...');
        }
        $this->_github = new Github($opts['u'], $opts['r'], $opts['p']);
        $existing = $this->_github->issues();
        if (!empty($existing)) {
            log('GitHub repository is not empty and contains ' . count($existing) . ' issue(s)!');
        }
    }
    /**
     * Rewind the iterator.
     */
    public function rewind() {
        $this->_position = 0;
        $this->_numbers = array();
        log('Loading Trac ticket numbers... (may take some time)');
        $this->_numbers = $this->_trac->query("max=0&order=id");
        log('Found ' . count($this->_numbers) . ' ticket number(s)');
    }
    /**
     * Get current element.
     * @return Migration Current migration
     */
    public function current() {
        return new TicketMigration(
            $this->_trac,
            $this->_numbers[$this->_position],
            $this->_github
        );
    }
    /**
     * Shift to the next migration.
     */
    public function next() {
        $this->_position++;
    }
    /**
     * Is it still valid?
     * @return bool TRUE if still in bounds
     */
    public function valid() {
        return $this->_position < count($this->_numbers);
    }
    /**
     * Key.
     */
    public function key() {
        throw new Exception('keys are not supported by migrations');
    }
}
