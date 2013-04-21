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

require_once 'HTTP/Request2.php';
require_once 'Net/URL2.php';

/**
 * GitHub client.
 * @see http://developer.github.com/v3/issues/
 */
final class Github {
    /**
     * User name.
     * @var string
     */
    private $_user;
    /**
     * Repository name.
     * @var string
     */
    private $_repo;
    /**
     * Github password.
     * @var string
     */
    private $_password;
    /**
     * Public ctor.
     * @param string $user User name
     * @param string $repo Repository
     * @param string $password Password
     */
    public function __construct($user, $repo, $password) {
        $this->_user = $user;
        $this->_repo = $repo;
        $this->_password = $password;
        log('GitHub client configured for ' . $this->_url());
    }
    /**
     * List all issues.
     * @return array Decoded JSON
     * @see http://developer.github.com/v3/issues/#list-issues-for-a-repository
     */
    public function issues() {
        $request = new \HTTP_Request2();
        $request->setUrl($this->_url('/repos/' . $this->_user . '/' . $this->_repo . '/issues'));
        $json = json_decode($request->send()->getBody(), true);
        log('Found ' . count($json) . ' issues in GitHub');
        return $json;
    }
    /**
     * Issue exists with this title?
     * @param int $issue Issue number
     * @param string $title Title of the new issue
     * @return bool TRUE if it already exists
     */
    public function exists($issue, $title) {
        $request = new \HTTP_Request2();
        $request->setUrl($this->_url('/repos/' . $this->_user . '/' . $this->_repo . '/issues/' . $issue));
        $attempt = 0;
        while (true) {
            try {
                $response = $request->send();
                if ($response->getStatus() == 404) {
                    return false;
                }
                log('failed to check: ' . $response->getBody());
            } catch (\HTTP_Request2_Exception $e) {
                log('failed to check for issue existence');
            }
            if (++$attempt > 5) {
                throw new \HTTP_Request2_Exception(
                    'failed to check issue #' . $issue . ' in Github'
                );
            }
        }
    }
    /**
     * Create an issue.
     * @param string $title Title of the new issue
     * @param string $body Body to post as a description
     * @return int Issue number
     */
    public function create($title, $body) {
        $request = new \HTTP_Request2();
        $request->setUrl($this->_url('/repos/' . $this->_user . '/' . $this->_repo . '/issues'));
        $request->setMethod('POST');
        $request->setBody(
            json_encode(
                array(
                    'title' => $title,
                    'body' => $body,
                    'assignee' => $this->_user,
                )
            )
        );
        $attempt = 0;
        while (true) {
            try {
                $response = $request->send();
                if ($response->getStatus() != 201) {
                    throw new \HTTP_Request2_Exception(
                        'failed to create an issue in Github: ' . $response->getBody()
                    );
                }
                break;
            } catch (\HTTP_Request2_Exception $e) {
                if (++$attempt > 5) {
                    throw $e;
                }
                log('failed to create an issue, will try again');
            }
        }
        $json = json_decode($response->getBody(), true);
        log('Issue #' . $json['number'] . ' created in Github');
        return $json['number'];
    }
    /**
     * Post a comment to the issue.
     * @param int $issue Issue number
     * @param string $comment Comment to post
     */
    public function post($issue, $comment) {
        $request = new \HTTP_Request2();
        $request->setUrl($this->_url('/repos/' . $this->_user . '/' . $this->_repo . '/issues/' . $issue . '/comments'));
        $request->setMethod('POST');
        $request->setBody(json_encode(array('body' => $comment)));
        $attempt = 0;
        while (true) {
            try {
                $response = $request->send();
                if ($response->getStatus() != 201) {
                    throw new \HTTP_Request2_Exception(
                        'failed to post a comment to Github issue #' . $issue
                        . ': ' . $response->getBody()
                    );
                }
                break;
            } catch (\HTTP_Request2_Exception $e) {
                if (++$attempt > 5) {
                    throw $e;
                }
                log('failed to post to issue #' . $issue . ', will try again');
            }
        }
        log('Comment posted to Github issue #' . $issue);
    }
    /**
     * Close an issue.
     * @param int $issue Issue number
     */
    public function close($issue) {
        $request = new \HTTP_Request2();
        $request->setUrl($this->_url('/repos/' . $this->_user . '/' . $this->_repo . '/issues/' . $issue));
        $request->setMethod('PATCH');
        $request->setBody(json_encode(array('state' => 'closed')));
        $response = $request->send();
        if ($response->getStatus() != 200) {
            throw new \Exception(
                'failed to close a Github issue #' . $issue
                . ': ' . $response->getBody()
            );
        }
        log('Closed Github issue #' . $issue);
    }
    /**
     * Reopen an issue.
     * @param int $issue Issue number
     */
    public function reopen($issue) {
        $request = new \HTTP_Request2();
        $request->setUrl($this->_url('/repos/' . $this->_user . '/' . $this->_repo . '/issues/' . $issue));
        $request->setMethod('PATCH');
        $request->setBody(json_encode(array('state' => 'open')));
        $response = $request->send();
        if ($response->getStatus() != 200) {
            throw new \Exception(
                'failed to reopen a Github issue #' . $issue
                . ': ' . $response->getBody()
            );
        }
        log('Re-opened Github issue #' . $issue);
    }
    /**
     * Make a URL.
     * @param string $path Path to append
     * @return Net_URL The URL
     */
    private function _url($path = '') {
        $url = new \Net_URL2('https://api.github.com/');
        $url->setPath($path);
        $url->setUserinfo($this->_user, $this->_password);
        return $url;
    }
}
