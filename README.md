# Trac2Github

Simple migration script from Trac to GitHub. The goal of this
software tool is to enable fast and transparent migration from
Trac issue tracking system to GitHub. Clone it from GitHub first:

```
$ git clone git@github.com:yegor256/trac2github.git
```

Make sure you have PHP 5.3+ installed
([how to install PHP?](http://php.net/manual/en/install.php)):

```
$ php -v
PHP 5.4.13 (cli) (built: Apr 10 2013 12:02:56)
Copyright (c) 1997-2013 The PHP Group
Zend Engine v2.4.0, Copyright (c) 1998-2013 Zend Technologies
```

Install required PEAR libraries:

```
$ pear install --alldeps XML_RPC2 HTTP_Request2 Net_URL2
```

Make sure your Trac instance has [XmlRpcPlugin](http://trac-hacks.org/wiki/XmlRpcPlugin)
installed and your Trac user has read permissions.

Then, run the script and read its output for further instructions:

```
$ php trac2github.php
```

## Limitations

There are a few limitations of this script:

 * It migrates only Trac tickets (milestones, versions, wiki pages, etc. are ignored);
 * It converts Trac formatting to GitHub using simple regular expressions (which may fail sometimes);
 * It DOESN'T convert Trac users to GitHub users;
 * It posts all comments to GitHub as one user and adds a head line
   to each of them explaining who was the original poster in Trac.

## Questions?

If you have any ideas, problems, suggestions, or comments - don't
hesitate [to submit them to me](https://github.com/yegor256/trac2github/issues).
I'll do my best to respond timely.

## License

The tool is licensed under the Apache License, Version 2.0.
