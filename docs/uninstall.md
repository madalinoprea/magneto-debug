# Uninstall

Steps to cleanly uninstall this extension for a non-production environment. I assume you never install it in production.

- Remove files added by the extension
```
modman remove magneto-debug
```
    - TODO: add details how to do this for a non modman installation

- Remove database references added by the extension
```sql
DROP TABLE IF EXISTS `sheep_debug_request_info`;
DELETE FROM `core_resource` WHERE `code`='sheep_debug_setup';
DELETE FROM `core_config_data` WHERE `path` LIKE 'sheep_debug/%';
```

- Flush Cache
