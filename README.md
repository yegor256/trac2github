# Trac2Github

Simple migration script from Trac to GitHub. The goal of this
software tool is to enable fast and transparent migration from
Trac issue tracking system to GitHub. Clone it from GitHub first:

```
$ git clone git@github.com:jbs1/trac2github.git
```

Make sure you have PHP 5.3+ installed
([how to install PHP?](http://php.net/manual/en/install.php)):

Install required PEAR libraries
([how to install PEAR?](http://pear.php.net/manual/nl/installation.php)):

```
$ pear install --alldeps XML_RPC2
```

Make sure your Trac instance has [XmlRpcPlugin](http://trac-hacks.org/wiki/XmlRpcPlugin)
installed and your Trac user has read permissions.

Then, run the script and read its output for further instructions:

```
$ php trac2github.php
```

You can more or less safely restart the tool after accidental crash. However, for 
a clear migration I recommend to start from an empty Github project always. If the
tool crashes for some reason, delete the project from Github and create it again.

## Limitations

There are a few limitations of this script:

 * It sets one user as an assignee for all tickets;
 * It migrates only Trac tickets (milestones, versions, wiki pages, etc. are ignored);
 * It converts Trac formatting to GitHub using simple regular expressions (which may fail sometimes);
 * It DOESN'T convert Trac users to GitHub users;
 * It posts all comments to GitHub as one user and adds a head line
   to each of them explaining who was the original poster in Trac.

## License

The tool is licensed under the Apache License, Version 2.0.
