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
     * Trac legacy site URL.
     */
    private $_trac_url;
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
    public function __construct(\XML_RPC2_Client $trac, $number, Github $github, $trac_url) {
        $this->_trac = $trac;
        $this->_number = $number;
        $this->_github = $github;
        $this->_trac_url = $trac_url;
    }
	/**
	 * Shoot the migration.
	 */
	public function shoot() {
        $details = $this->_trac->get($this->_number);
        $summary = $details[3]['summary'];
        $description = $details[3]['description'];

        if ($this->_github->exists($summary)) {
           log('Ticket #' . $this->_number . ' already exists in GitHub!');
           return;
        }
        log('Ticket #' . $this->_number . ': ' . $summary);
        $issue = $this->_github->create(
            $summary,
            $this->_format(
                $details[3]['reporter'],
                $details[1]->timestamp,
                $description,
                $this->_number
            )
        );
        $changes = $this->_trac->changeLog($this->_number);
        $comments = 0;
        foreach ($changes as $change) {
            if ($change[2] == 'comment' && !empty($change[4])) {
                $this->_github->post(
                    $issue,
                    $this->_format($change[1], $change[0]->timestamp, $change[4], $this->_number.'#comment:'.$change[3])
                );
            } else if ($change[2] == 'status' && $change[4] == 'closed') {
                $this->_github->close($issue);
            } else if ($change[2] == 'status' && $change[4] != 'closed') {//muliple different open states
                $this->_github->reopen($issue);
            }
            ++$comments;
        }
		log(
            'Ticket #' . $this->_number . ' migrated to GitHub issue #' . $issue
            . ' with ' . $comments . ' comment(s)'
        );
	}
    /**
     * Format a comment for github.
     * @param string $author Author from Trac
     * @param int $date When posted to Trac
     * @param string $text Comment text from Trac
     */
    private function _format($author, $date, $text, $number) {
        $regexps = array(
            '/\{{3}(.+?)\}{3}/' => '`\1`',
            '/\{{3}[^\n]*\n(.+?)\n\}{3}/s' => "```\n\\1\n```",
            '/\={4}\s(.+?)\s\={4}/' => '### \1',
            '/\={3}\s(.+?)\s\={3}/' => '## \1',
            '/\={2}\s(.+?)\s\={2}/' => '# \1',
            '/\=\s(.+?)\s\=[\s\n]*/' => '',
            '/\[(http[^\s\[\]]+)\s([^\[\]]+)\]/' => '[\2](\1)',
            '/\!(([A-Z][a-z0-9]+){2,})/' => '\1',
            '/\'{3}(.+)\'{3}/' => '*\1*',
            '/\'{2}(.+)\'{2}/' => '_\1_',
            '/^\s\*/' => '*',
            '/^\s\d\./' => '1.',
            '/@+?(\w+)/' => '**\1**',
        );
        $md = preg_replace(array_keys($regexps), $regexps, $text);
        $matches = array();
        if (preg_match('/^(.*?)@.*$/', $author, $matches)) {
            $author = $matches[1];
        }
        $body = $md . "\n\n"
						.'_migrated from [Trac issue '.$number.']('.$this->_trac_url.'/ticket/'.$number.'), where originally posted by '
						. '**' . $author . '** on '
            . date('j-M-o g:ia', $date)
            . "_";
        if (strlen($body) > 60000) {
            $body = substr($body, 0, 60000);
        }
        return $body;
    }
}
