
[![Build Status](https://travis-ci.org/madalinoprea/magneto-debug.svg?branch=master)](https://travis-ci.org/madalinoprea/magneto-debug) [![Coveralls](https://coveralls.io/repos/github/madalinoprea/magneto-debug/badge.svg?branch=master)](https://coveralls.io/github/madalinoprea/magneto-debug)

This repository represents an extension for Magento 1.x that offers a developer debug toolbar. The idea came from robhudson's [django-debug-toolbar](https://github.com/robhudson/django-debug-toolbar).  
Latest version is based on Symfony's WebProfilerBundle.

![Toolbar](docs/images/frontend_toolbar_request.png)

# Features 
- **Request and Controller information**: lists request attributes and controller that handled the request, including Ajax and POST requests
- **Execution Timeline**: shows execution timeline based on Varien Profiler timers
- **Logs**: shows log lines added to system and exception 'during' the request
- **Events**: shows all raised events and called observers
- **Database**: lists all models and collections loaded during the request; all executed SQL queries are listed
 when SQL Profiler is enabled
- **E-mails**: lists sent e-mails with preview
- **Layout**: outputs rendering tree, lists layout handlers loaded during current request and adds ability to see layout files
updates to a specific handle; offers information about instantiated and rendered block
- **Configuration**: lists available Magento modules with their status and their version; 
 also offers the ability to enable/disable them
- **Toolbar Tools**: contains quick links to flush cache, enable template hints, enable SQL profiler, enable Varien Profiler

Don't forget to check out [screenshots gallery](docs/images.md)

# Installation 

## Using Modman

- Make sure you have [Modman](https://github.com/colinmollenhour/modman) installed
- Allow symlinks for the templates directory (required for installations via Modman)
    - Use n98-magerun like pro: `n98-magerun.phar dev:symlinks`
    - Or just set 'Allow Symlinks' to 'Yes' from System - Configuration / Advanced / Developer / Template Settings

- Install Debug Toolbar module:
```bash
cd [magento root folder]
modman init
modman clone https://github.com/madalinoprea/magneto-debug.git
```
- Flush Magento's cache 

### How to update
I'm pretty lazy and I don't like to create Magento Connect packages. With modman you can effortlessly grab latest changes from github.
```
cd [magento root folder]
modman update magneto-debug
```
- Flush Magento's cache


## Using composer

Lately, I've been more into composer and thanks to these project https://github.com/Cotya/magento-composer-installer you can use it for your Magento 1.x websites.

```
# Add package as requirement to composer.json
composer require madalinoprea/magneto-debug
# Clear cache and voila..
```

# Compatibility

Extension is (hopefully) successfully unit tested against PHP 5.4, PHP 5.5 and Magento CE 1.9, Magento CE 1.8, Magento CE 1.7 and 
their related Magento Enterprise versions.

If you would like to support it on another version let us know.

[![Build Status](https://travis-ci.org/madalinoprea/magneto-debug.svg)](https://travis-ci.org/madalinoprea/magneto-debug)


# Issues, Ideas or Feedback

Use [issue tracker on GitHub](https://github.com/madalinoprea/magneto-debug/issues) to report issues, ideas or any feedback.

# Common Issues

- 'Mage Registry key already exists' exception is raised after installation
    - `Mage registry key "_singleton/debug/observer" already exists` is reported when cache regeneration was corrupted. 
    Please try to flush Magento cache.

- Doesn'work. I see these logs on `var/log/system.log`: `Not valid template file:adminhtml/default/default/template/sheep_debug/toolbar.phtml`
    - If you installed the module using modman you've missed an important step. Search this page after 'Allow symlinks for the templates directory' and complete that step.  	
  
- I can't see toolbar.
    - Toolbar is displayed in these conditions:
        - module is installed and enabled
        - toolbar is enabled from Admin / System / Configuration / Advanced - Developer Debug Toolbar (by default it's enabled)
        - Magento is running in developer mode (MAGE_IS_DEVELOPER_MODE) Or your ip is listed under under 'Developer Client Restrictions'
    - Check that module name Sheep_Debug is installed and enabled
    - Check that 'Allow Symlinks' configuration is enabled for Modman installation

- I can't see toolbar on specific page
    - Toolbar is added to all pages that have a structural block named `before_body_end`. By default this block is available on all Magento pages.
    Eliminate a possible cache problem by disabling all caches. Try to determine if there are any customizations that have removed `before_body_end`.

# Change Log
- **1.6.1**
   - Quick fix release for recursive block rendering #68 	
- **1.6.0**
    - Adds support for unexpected block rendering used by some common extension #66
    - Unit tests coverage improvements
- **1.5.1**
    - Adds support for Magento CE 1.7 and Magento EE 1.12
    - Fixes why for some people toolbar was not showing in admin (corrected modman links)
    - Fixes how log lines are fetched from logs (there is a good chance that is addressing some memory limit error)
- **1.5.0**
    - Adds nicer way to present rendering tree
    - Adds support to capture e-mails even when Aschroder_SMTPPro or Ebizmarts_Mandrill are installed
    - Minor UI tweaks for layout panel
    - Fixes listing of database layout updates
- **1.4.1**
    - Minor fixes for Magento Enterprise (website restrictions, Full Page Cache debug menu item)
    
- **1.4.0**:
    - Added unit tests and continuous integration via Travis CI
    - Fixes request headers collection for non Apache web servers (e.g Nginx)
    - Improves support to collect and render sent e-mails 
    - Other minor UI tweaks
    
- **1.3.0**:
    - UI reimplemented based on Symphony's web debug toolbar 
    - Ability to view POST and Ajax requests
    - Ability to view sent e-mails
    - Ability to view raised events and called observers
    - Ability to see an execution timeline based on Varien Profiler timers
    
- **1.2.0**: 
    - Fixes SELECT and DESCRIBE operations for long queries
    - Better way to identify what logging lines were added during request
    - Various minor UI improvements (order of the panel, panel titles)
    - Structural changes to improve stability and prepare new features

# Authors, contributors

- [Mario O](https://twitter.com/madalinoprea)
- [Other contributors](https://github.com/madalinoprea/magneto-debug/graphs/contributors)

# License

[MIT License](LICENSE.txt)
	
# Roadmap
- Re-add ability to search configuration
- UI tweaks and improvements
- Add more detailed documentation
