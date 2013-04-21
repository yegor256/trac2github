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

if (version_compare(PHP_VERSION, '5.3', '<')) {
    error_log('PHP version 5.3 or higher is required. Your version is ' . PHP_VERSION);
    die(-1);
}

$opts = getopt("ht:u:p:g:w:r:");
try {
    $migrations = new Migrations($opts);
} catch (Exception $e) {
    error_log($e->getMessage());
    $opts = array('h' => true);
}

if (array_key_exists('h', $opts)) {
    echo <<<EOT
Usage: php trac2github.php [options]
    -t=<trac-URL>           URL of Trac instance with enabled XmlRpcPlugin
    -u=<trac-user-name>     Trac user name, which has read permissions
    -p=<trac-password>      Trac password
    -g=<github-user>        GitHub user name
    -w=<github-password>    GitHub password
    -r=<repository>         GitHub repository name

EOT;
    die(0);
}

foreach ($migrations as $migration) {
    $migration->shoot();
}
