# Magento Debug Toolbar 
Based on robhudson's awesome work (<https://github.com/robhudson/django-debug-toolbar>) we've created a debug toolbar for Magento.
It is installed as a Magento module without hacking Magento's core.

Basic features are implemented and few others will come soon. Check the screenshots for current features: <https://github.com/madalinoprea/magneto-debug/wiki>

## INSTALLATION 

### Via Modman
 - Modman required: <http://code.google.com/p/module-manager/>
<pre>
curl http://module-manager.googlecode.com/files/modman-1.1.5 > modman
chmod +x modman
sudo mv modman /usr/bin
</pre>

 - Magento patch to allow symlinks for templates dir: <http://www.tonigrigoriu.com/magento/magento-how-to-fix-template-path-errors-when-using-symlinks/> (required if you choose to use modman installation)
 - Install via modman (for details consult modman website):

    <code>
        cd <magento root folder>
        modman init
        modman magneto-debug clone https://github.com/madalinoprea/magneto-debug.git
    </code>

 - Make sure you've cleaned Magento's cache to enable the new module; hit refresh

### Via Magento Connect
Soon we'll make available a Magento extension package that can be installed via Admin.

## FEATURES 
 - Magento module listing; Toggle Magento modules on the fly
 - Display peak memory usage, script execution time
 - Request information (controller name, action name, cookies variables, session variables, GET and POST variables)
 - Models instantiated
 - SQL queries executed for current request; ability to see queries' result or queries' execution plan (EXPLAIN)
 - List Magento configuration
 - Print layout handles for current request
 - Created blocks, their associated templates; Preview templates' source code
 - Quick actions: 
    - Toggle template hints
    - Clear cache

## KNOWN ISSUES
We're working to correct these:

 - To enable SQL profiler manually you have to add in your local.xml, under connection the profiler tag like in the example below:

    <code>
            <default_setup>
                <connection>
                    <host><![CDATA[/var/run/mysqld/mysqld.sock]]></host>
                    <username><![CDATA[root]]></username>
                    <password><![CDATA[]]></password>
                    <dbname><![CDATA[magento]]></dbname>
                    <active>1</active>
                    <profiler>1</profiler>
                </connection>
            </default_setup>
    </code>
 - `Disable SQL Profiler` is not working, but `Enable SQL Profiler` works like a charm (or not)
