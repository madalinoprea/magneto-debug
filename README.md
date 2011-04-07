# Magento Debug Toolbar 
Based on robhudson's awesome work (<https://github.com/robhudson/django-debug-toolbar>) we've created a debug toolbar for Magento.
It is installed as a Magento module without hacking Magento's core.

Basic features are implemented and few others will come soon.

## INSTALLATION 
 - modman required: http://code.google.com/p/module-manager/
 - Magento patch to allow symlinks for templates dir: <http://www.tonigrigoriu.com/magento/magento-how-to-fix-template-path-errors-when-using-symlinks/> (required if you choose to use modman installation)
 - Install via modman (for details consult modman website):
    <code>
        cd <magento root folder>
        modman init
        modman magento-debug clone git@github.com:madalinoprea/magento-debug.git
    </code>
 - Make sure you've cleaned Magento's cache to enable the new module

## FEATURES 
 - Controllers Information
 - Models, Collection and SQL queries
 - Block, templates and layout
 - Installed modules
 - Quick actions: toggle template hints, clear cache, more to come

