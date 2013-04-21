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

require_once './Migration.php';
require_once 'XML/RPC2/Client.php';

final class TicketMigration implements Migration {
    /**
     * Trac client.
     * @var XML_RPC2_Client
     */
    private $_trac;
    /**
     * Ticket number.
     */
    private $_number;
    /**
     * Github.
     * @var Github
     */
    private $_github;
    /**
     * Public ctor.
     * @param XML_RPC2_Client $trac Trac client
     * @param int $number Ticket number
     */
    public function __construct(\XML_RPC2_Client $trac, $number, Github $github) {
        $this->_trac = $trac;
        $this->_number = $number;
        $this->_github = $github;
    }
	/**
	 * Shoot the migration.
	 */
	public function shoot() {
        $details = $this->_trac->get($this->_number);
        $summary = $details[3]['summary'];
        $description = $details[3]['description'];
        log('Ticket #' . $this->_number . ' goes to GitHub issue #'. $this->_number . ': ' . $summary);
        $changes = $this->_trac->changeLog($this->_number);
		log('Ticket #' . $this->_number . ' migrated to GitHub');
	}

}
