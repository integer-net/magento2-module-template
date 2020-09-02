# integer_net Magento 2 Module Template

To automatically replace all placeholders with custom values, run the following script:

```
dev/init
```

It takes the root directory as optional argument, so if you run the script from a different location, it can look as follows:

```
# in dev:
./init ..

# anywhere
/path/to/repo/dev/init /path/to/repo
```

**The `/dev/` directory can be removed after initialization!**

## Developing the template

### Adding placeholders

To add new placeholders, adjust the initialization script:

In `dev`, run:
```
composer install
``` 

Change the script in `dev/bin/init`

Files where placeholders are replaced are defined in `getFilesToUpdate()`, placeholders with their default values in `getDefaultVariables()`

To update the PHAR archive, run in `dev`:
```
composer build
```