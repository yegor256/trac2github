# Trac2Github

Simple migration script from Trac to GitHub. The goal of this
software tool is to enable fast and transparent migration from
Trac issue tracking system to GitHub. Clone it from GitHub first:

```shell
$ git clone https://github.com/Aeon/trac2github.git
```

Make sure you have PHP 5.3+ installed
([how to install PHP?](http://php.net/manual/en/install.php)):
```shell
$ php -v
```

Install prerequisites with Composer ([PEAR is required](http://pear.php.net/manual/nl/installation.php))

```shell
$ composer install
```

Make sure your Trac instance has [XmlRpcPlugin](http://trac-hacks.org/wiki/XmlRpcPlugin)
installed and your Trac user has read permissions.

Then, run the script and read its output for further instructions:

```shell
$ php trac2github.php
```

### Example Invocation

```shell
$ php trac2github.php \
  -t=https://tracUser:tracPassword@my-trac-instance.org/login/rpc \
  -u=githubUser \
  -r=githubRepository \
  -o=githubOrganization \
  -i=5617,1150,1502,1503,1504,2009
```

Github has NO WAY to delete issues. So, before running the migration, create a test 
repository and run the script against it first. Only once you're happy with the results,
delete the test repository and run it against the real target.

You can more or less safely restart the tool after accidental crash. However, for 
a clear migration I recommend to start from an empty Github project always. If the
tool crashes for some reason, delete the project from Github and create it again.

## Limitations

There are a few limitations of this script:

 * It DOESN'T convert Trac users to GitHub users;
 * It DOESN'T migrate Trac attachments;
 * It WILL import the same ticket multiple times if the Github issue id is not the same as Trac ticket id;
 * It sets one user as an assignee for all tickets;
 * It migrates only Trac tickets (milestones, versions, wiki pages, etc. are ignored);
 * It converts Trac formatting to GitHub using simple regular expressions (which may fail sometimes);
 * It posts all comments to GitHub as one user and adds a footer line
   to each of them explaining who was the original poster in Trac.

## License

The tool is licensed under the Apache License, Version 2.0.
