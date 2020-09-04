# integer_net Magento 2 Module Template

To automatically replace all placeholders with custom values, run the following script:

```
dev/init
```

## Placeholders explained

| Placeholder    | Purpose |
| -------------- | ------- |
| Vendor | Vendor name for composer package, should match the name of your GitHub user or organization |
| Package | Package name for composer package |
| Description | A short description what the module does and for whom it is useful. Placed at the top of the README and as description in `composer.json` |
| Author name | Main author name, used in composer.json and README |
| Author email | Main author email, used in composer.json and README |
| Author github | GitHub username of main author, used in README to link to the profile |
| Module namespace | Top level PHP namespace (e.g. company name) |
| Module name | Second level PHP namespace (module name) |
| Company | Full company name, used in LICENSE |
| Year | Current year, used in LICENSE |



**The `/dev/` directory can be removed after initialization!**

## Developing the template

### Adding placeholders

To add new placeholders, adjust the initialization script in `dev/bin/init`

Files where placeholders are replaced are defined in `getFilesToUpdate()`, placeholders with their default values in `getDefaultVariables()`
