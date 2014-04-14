Tagging (plugin for Omeka)
==========================

About
-----

[Tagging] is a plugin for [Omeka] that allows visitors to add tags to create a
folksonomy.

Tags can be added with or without captcha and approbation. Once approved, users
tags become normal tags. Tag creation and approbation can be managed via roles.


Installation
------------

Uncompress files and rename plugin folder "Tagging".

Then install it like any other Omeka plugin and follow the config instructions.

The Tagging plugin can use Omeka ReCaptchas. You need to get keys to this
service and set them in the general preferences.

The plugin can use [GuestUser] if it is installed.


Displaying Tagging Form
-----------------------

The plugin will add tagging form automatically on items/show page if the current
user has right to use it:
```php
fire_plugin_hook('public_items_show', array('view' => $this, 'item' => $item));
```

If you need more flexibility, you can use view helpers:
```php
echo $this->getTaggingForm();
```
Rights are automatically managed.


Warning
-------

Use it at your own risk.

It's always recommended to backup your files and database so you can roll back
if needed.


Troubleshooting
---------------

See online issues on the [Tagging issues] page on GitHub.


License
-------

This plugin is published under the [CeCILL v2.1] licence, compatible with
[GNU/GPL] and approved by [FSF] and [OSI].

In consideration of access to the source code and the rights to copy, modify and
redistribute granted by the license, users are provided only with a limited
warranty and the software's author, the holder of the economic rights, and the
successive licensors only have limited liability.

In this respect, the risks associated with loading, using, modifying and/or
developing or reproducing the software by the user are brought to the user's
attention, given its Free Software status, which may make it complicated to use,
with the result that its use is reserved for developers and experienced
professionals having in-depth computer knowledge. Users are therefore encouraged
to load and test the suitability of the software as regards their requirements
in conditions enabling the security of their systems and/or data to be ensured
and, more generally, to use and operate it in the same conditions of security.
This Agreement may be freely reproduced and published, provided it is not
altered, and that no provisions are either added or removed herefrom.


Contact
-------

Current maintainers:
* Daniel Berthereau (see [Daniel-KM] on GitHub)

First version of this plugin has been built for [Mines ParisTech].


Copyright
---------

* Copyright Daniel Berthereau, 2013-2014


[Omeka]: https://omeka.org "Omeka.org"
[Tagging]: https://github.com/Daniel-KM/Tagging
[Tagging issues]: https://github.com/Daniel-KM/Tagging/issues
[GuestUser]: https://github.com/omeka/plugin-GuestUser
[CeCILL v2.1]: http://www.cecill.info/licences/Licence_CeCILL_V2.1-en.html "CeCILL v2.1"
[GNU/GPL]: https://www.gnu.org/licenses/gpl-3.0.html "GNU/GPL v3"
[FSF]: https://www.fsf.org
[OSI]: http://opensource.org
[Daniel-KM]: https://github.com/Daniel-KM "Daniel Berthereau"
[Mines ParisTech]: http://bib.mines-paristech.fr "Mines ParisTech / ENSMP"
