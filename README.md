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

## Uninstall

1. Navigate in the ACP to `Customise -> Extension Management -> Extensions`.
2. Look for `LMDI Glossary` under the Enabled Extensions list, and click its `Disable` link.
3. To permanently uninstall, click `Delete Data` and then delete the `/ext/lmdi/gloss` folder.

## License
[GNU General Public License v2](http://opensource.org/licenses/GPL-2.0)

## Collation scheme of data table
If you have accented initials, they are not sorted correctly with the default utf8_bin collation option used by phpBB when creating a table. The correct collation should be 'utf8_general_ci'.
This command, to be used in phpMyAdmin or Adminer, will change the collation scheme:
alter table phpbb3_glossary convert to character set utf8 collate utf8_unicode_ci;