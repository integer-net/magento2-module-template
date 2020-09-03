# integer_net Magento 2 Module Template

To automatically replace all placeholders with custom values, run the following script:

```
dev/init
```

**The `/dev/` directory can be removed after initialization!**

## Developing the template

### Adding placeholders

To add new placeholders, adjust the initialization script in `dev/bin/init`

Files where placeholders are replaced are defined in `getFilesToUpdate()`, placeholders with their default values in `getDefaultVariables()`
