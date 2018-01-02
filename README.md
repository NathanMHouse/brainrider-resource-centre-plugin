# Brainrider Resource Centre Plugin

Turnkey B2b resource centre solution with Pardot integration. Developed to same project time and resource while still allowing for easy, out-of-the-box customization and future extensibility.

## Getting Started

Setup follows the same basic principles of any other WordPress plugin.

### Prerequisites

```
WordPress 
```

### Installing

Download the dist directory to your local machine (alternatively, clone the entire repo).

From within the WordPress dashboard, upload the plugin ZIP file contained within the dist directory.

Activate the plugin.

Plugin activation will create an additional menu item/icon in your WordPress dashboard (i.e. 'resources'). By clicking on this menu item, you can visit the plugin setting's page and begin customization.

Pardot integration requires an active account with Saleforce's (Pardot Marketing Automation Solution)[https://www.pardot.com/]. Specifically, you will need your Pardot account email, password, and user API key.

Finally, the plugin has been built to be extensible, using WordPress's existing templating functions to allow for customization of the front-end. Better documentation (i.e. repo wiki) is on the roadmap but in the interm, available filters and actions can be discovered by inspecting the core plugin files.


## Built With

* [Bootstrap](https://getbootstrap.com/)
* [Pardot API](http://developer.pardot.com/)
* [WordPress Plugin API](https://codex.wordpress.org/Plugin_API)

## Future Development
* Various bug fixes
* Better plugin documenation (i.e. wiki)
* Post type slug customization
* Dummy content on load
* Seperation of front-end and back-end styles
* Manual refresh of Pardot custom redirects
* Dynamic content integration
* Better Prospect tracking and delivery of custom site content
* More!

## Author

* **Nathan M. House** - [NathanMHouse](https://github.com/NathanMHouse)

## Acknowledgments

** Catia Rocha - strategy and planning