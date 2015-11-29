# phpBB 3.1 Extension - LMDI Glossary

## Install

1. Download the latest release.
2. Unzip the downloaded release, and change the name of the folder to `gloss`.
3. In the `ext` directory of your phpBB board, create a new directory named `lmdi` (if it does not already exist).
4. Copy the `gloss` folder to `/ext/lmdi/`.
5. Navigate in the ACP to `Customise -> Manage extensions`.
6. Look for `Delete Re:` under the Disabled Extensions list, and click its `Enable` link.

Enable the feature in the ACP (Extension tab).
Some users dislike the tagging of terms in the posts. Therefore, there is an option 
to disable it individually in the UCP.

For the time being, I can't manage to use the template from the extension folder. 
Copy the file glossaire.html from the extension root into styles/prosilver/templates.

## Uninstall

1. Navigate in the ACP to `Customise -> Extension Management -> Extensions`.
2. Look for `LMDI Glossary` under the Enabled Extensions list, and click its `Disable` link.
3. To permanently uninstall, click `Delete Data` and then delete the `/ext/lmdi/gloss` folder.

## License
[GNU General Public License v2](http://opensource.org/licenses/GPL-2.0)

## Data table
CREATE TABLE `phpbb3_glossary` (
  `term_id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `variants` varchar(80) NOT NULL DEFAULT '',
  `term` varchar(80) DEFAULT NULL,
  `description` varchar(512) NOT NULL DEFAULT '',
  `picture` varchar(80) DEFAULT NULL,
  `lang` varchar(15) NOT NULL DEFAULT 'fr',
  PRIMARY KEY (`term_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

## Dummy entries
INSERT INTO phpbb3_glossary (variants, term, description, picture, lang) 
VALUES('test, tests, tested', 'Test', 'Test definition', 'nopict', 'en');
INSERT INTO phpbb3_glossary (variants, term, description, picture, lang) 
VALUES('test2', 'Second test', 'Dummydefinition', 'nopict', 'en');