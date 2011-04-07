# Magento Debug Toolbar 
Based on robhudson's awesome work (<https://github.com/robhudson/django-debug-toolbar>) we've created a debug toolbar for Magento.
It is installed as a Magento module without hacking Magento's core.

Basic features are implemented and few others will come soon.

## INSTALLATION 

### Via Modman
 - Modman required: <http://code.google.com/p/module-manager/>
 - Magento patch to allow symlinks for templates dir: <http://www.tonigrigoriu.com/magento/magento-how-to-fix-template-path-errors-when-using-symlinks/> (required if you choose to use modman installation)
 - Install via modman (for details consult modman website):
    <code>
        cd <magento root folder>
        modman init
        modman magneto-debug clone https://github.com/madalinoprea/magneto-debug.git
    </code>
 - Make sure you've cleaned Magento's cache to enable the new module

### Via Magento Connect
Soon we'll make available a Magento extension package that can be installed via Admin.

## FEATURES 
 - Magento module listing; Toggle Magento modules on the fly
 - Display peak memory usage, script execution time
 - Request information (controller name, action name, cookies variables, session variables, GET and POST variables)
 - Models instantiated
 - SQL queries executed for current request; ability to see queries' result or queries' execution plan (EXPLAIN)
 - Print layout handles for current request
 - Created blocks, their associated templates; Preview templates' source code
 - Quick actions: 
    - Toggle template hints
    - Clear cache

## KNOWN ISSUES
We working to correct these.
 - `Disable SQL Profiler` is not working, but `Enable SQL Profiler` works like a charm
 - Design for configuration panel is not final; current usability is ...

