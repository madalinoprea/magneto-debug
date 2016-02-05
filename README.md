
This repository represents an extension for Magento 1.x that offers a debug toolbar. The idea came from robhudson's [django-debug-toolbar](https://github.com/robhudson/django-debug-toolbar).  

![Toolbar](docs/images/toolbar.png)

# Features 
- **Request and Controller information**: lists request attributes and controller that handled the request
- **Models**: lists all models and collections loaded during the request; all executed SQL queries are listed
 when SQL Profiler is enabled
- **Module listing**: lists available Magento modules with their status and their version; 
 also offers the ability to enable/disable them
- **Design Objects**: lists layout handlers loaded during current request and adds ability to see layout files
updates to a specific handle
- **Blocks**: offers information about instantiated and rendered block
- **Logs**: shows log lines added to system and exception 'during' the request.
- **Utils**: contains quick links to flush cache, enable template hints, enable SQL profiler
- **Configuration** offers ability to search Magento configurations (review cronjobs, event observers, etc)

Don't forget to check out [screenshots gallery](docs/images.md)

# Installation 

## Using Modman

- Make sure you have [Modman](https://github.com/colinmollenhour/modman) installed
- Allow symlinks for the templates directory (required for installations via Modman)
    - For newer Magento versions (1.5.1.0 & above) you just need enable 'Allow Symlinks' from System - Configuration / Advanced / Developer / Template Settings
    - For older Magento versions you need to change some code http://www.tonigrigoriu.com/magento/magento-how-to-fix-template-path-errors-when-using-symlinks/
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

## Via Magento Connect

Extension is not updated regularly on Magento Connect. My recommendation is to use modman. 

```bash
cd [magento root folder]
sudo ./mage install community MagnetoDebug
```

Magento Connect extension package is available here: http://www.magentocommerce.com/magento-connect/sstoiana/extension/6714/magnetodebug

# Issues, Ideas or Feedback

Use [issue tracker on GitHub](https://github.com/madalinoprea/magneto-debug/issues) to report issues, ideas or any feedback.

# Common Issues

- 'Mage Registry key already exists' exception is raised after installation
    - `Mage registry key "_singleton/debug/observer" already exists` is reported when cache regeneration was corrupted. 
    Please try to flush Magento cache.
  
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
- Replace jQuery with prototype 
- Persist request info and add ability to view previous requests, including Ajax or API requests
- Reduce toolbar weight: simplify presented information, add separate request info view page
- Add request info listing (shows persisted request infos)
- Add unit tests
- Add Travis
- Add more detailed documentation
