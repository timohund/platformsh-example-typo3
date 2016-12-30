TYPO3 CMS on Platform.sh
=====
This is the offical example for running TYPO3 CMS version 8 on platform.sh.

Refer to [typo3.org](https://typo3.org/) for information on TYPO3 CMS.

This example is maintained by the TYPO3 Community Interest Group (CIG) Platform.sh, which is headed [dkd Internet Service Gmbh](https://dkd.de).

In the news:
* [strategic integration announcement](https://typo3.org/news/article/typo3-and-platformsh-announce-cloud-readiness-and-tech-preview-of-strategic-integration-ahead-of-t3/)
* [release at T3CON](https://typo3.org/news/article/typo3-conference-in-munich-typo3-cms-8-starting-today-with-platformsh-in-the-cloud/)

Installation
-----
### One-Click-Button
https://accounts.platform.sh/platform/trial/typo3/setup?branch=master
### Manual Push
* `git clone https://github.com/platformsh/platformsh-example-typo3.git`
* `cd platformsh-example-typo3`
* Go to http://platform.sh and create a new project, select *import your existing code* when asked
* Copy the git remote config from the follow-up form and run it - looks like
  * `git remote add platform XYZ@git.eu.platform.sh:XYZ.git`
* Add your SSH key if necessary
  * https://accounts.platform.sh/user/
  * `cat ~/.ssh/id_rsa.pub`
  * *Account Settings* *SSH Keys*
* `git push -u platform master`
* Click *finish* in the platformsh dialog

### Backend Access
* Click *access site*
* Click the url
* Add `/typo3` to your browser adress bar [ctrl-l \<end\> /typo3]
* Login with *admin* *password* [Change your password]


Adding TYPO3 Extensions/composer packages
-----
* Either open the `composer.json` file and add your dependencies or use `composer require PACKAGE`
* Run `composer update --ignore-platform-reqs` (Not referring to platform.sh, but your local platform, so e.g. PHP version)
* `git add -a && git push`

Example Development
-----
*master* contains stable tested versions and *develop* is the target for PRs


Solr Integration
-----
Currently there are three cores configured for the solr extension "core_da", "core_de" and "core_en":

The connection settings are added to the TYPO3_CONF_VARS from the platform.sh relation array. By convension the solr relations from .platform.app.yml have the name `solr-<languagekey>`:

They are exposed by the languagekey to TYPO3_CONF_VARS|EXTCONF|solr|Connections|<languagekey> and can be used with TypoScript for configuration:

```
plugin.tx_solr.solr {
   scheme = http
   port = TEXT
   port {
      value = 8080
      override.data = global:TYPO3_CONF_VARS|EXTCONF|solr|Connections|en|port
   }
   host = TEXT
   host {
      value = solr-en.internal
      override.data = global:TYPO3_CONF_VARS|EXTCONF|solr|Connections|en|host
   }
   path = TEXT
   path {
      value = /solr/core_en/
      override.data = global:TYPO3_CONF_VARS|EXTCONF|solr|Connections|en|path
   }
}

[globalVar = GP:L = 1]
plugin.tx_solr.solr.port.override.data = global:TYPO3_CONF_VARS|EXTCONF|solr|Connections|de|port
plugin.tx_solr.solr.host.override.data = global:TYPO3_CONF_VARS|EXTCONF|solr|Connections|de|host
plugin.tx_solr.solr.path.override.data = global:TYPO3_CONF_VARS|EXTCONF|solr|Connections|de|path
[end]

[globalVar = GP:L = 2]
plugin.tx_solr.solr.port.override.data = global:TYPO3_CONF_VARS|EXTCONF|solr|Connections|da|port
plugin.tx_solr.solr.host.override.data = global:TYPO3_CONF_VARS|EXTCONF|solr|Connections|da|host
plugin.tx_solr.solr.path.override.data = global:TYPO3_CONF_VARS|EXTCONF|solr|Connections|da|path
[end]
```

