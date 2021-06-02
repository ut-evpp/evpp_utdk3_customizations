# EVPP UTDK 3 Customizations
This Composer packages provides a common set of configuration and customized functionality for EVPP sites based on [UT Drupal Kit version 3](https://drupalkit.its.utexas.edu/).

### Contents
- [Setup](#setup)
- [Current functionality](#current-functionality)
- [Adding new functionality](#adding-new-functionality)
  * [Example: Add a new content type, "Data visualizations"](#example-add-a-new-content-type-data-visualizations)
- [Deploying updates to sites](#deploying-updates-to-sites)
## Setup
1. Add this package to any UT Drupal Kit 3 site via the following command:

```
composer require evpp/evpp_utdk3_customizations
```

This package will be installed to the `web/modules/custom/` directory, but should not be committed to version control on individual sites (the default `.gitignore` should ignore it).

2. After site installation, enable the `evpp_utdk3_customizations` module.

## Current functionality
Functionality for this package will grow over time. This section lists its current functionality.
1. Enterprise Login integration (also requires [a request to ITS](https://drupalkit.its.utexas.edu/docs/getting_started/pantheon_setup.html#request-integration)).

## Adding new functionality
The base module `evpp_utdk3_customizations` does not provide functionality on its own. Each additional functional feature should be added as as a sub-module, and installed on new and existing sites. An example follows.

### Example: Add a new content type, "Data visualizations"

1. Spin up a local instance of an EVPP site that uses this package.
1. Replace the github.com-based 'dist' version of this package with a 'source' based version from Enterprise Github (e.g., delete the directory, then `git clone git@github.austin.utexas.edu:evpp-web/evpp_utdk3_customizations.git`)
1. Create a new Git branch for staging the new feature.
1. In the codebase, add a new sub-module, `evpp_content_type_data_visualization` in the `modules` directory of this repository, with a standard `.info.yml` file and nothing else.
1. Enable the bare module (`drush en evpp_content_type_data_visualization`) so that it is registered in the Drupal system.
1. Use the Drupal UI to create the content type and its fields. Field machine names should be prefixed with `evpp` to prevent naming collisions. For example, a field for the visualization metadata could be machine-named `field_evpp_viz_metadata`.
1. Enable `features_ui` if it is not already enabled, go to `/admin/config/development/features`, and click "Create new feature."
1. Title the feature "Content Type: Data Visualization", with machine name `evpp_content_type_data_visualization`
1. Choose the bundle "EVPP Customizations"
1. Set the path for the configuration to be written to as `modules/custom/evpp_utdk3_customizations/evpp_content_type_data_visualization`.
1. From the selection panel on the right, select the node type that you created. You may need to explicitly select the "Field storage" config related to the content type, too.
1. Export the configuration by pressing "Write." (Verify it is located in the `config/install` directory of this new sub-module).
1. Continue to build out the content type by adding any needed templates, libraries, and assets to the module itself.
1. In `evpp_utdk3_customizations.install`, add logic to programmatically enable the new module on new and existing sites (i.e., enable it in both the `hook_install()` implementation and in a new `hook_update()` function.
1. Test the workflow by pulling the site's live database (effectively erasing your locally-staged changes) and running `drush updb`. This should enable the new sub-module, which will install the new content type.
1. Commit the changes to the branch & push them to Enterprise Github, using whatever internal review process is desired (e.g. pull request).
1. Once merged into the default branch on Enterprise Github, create a new tag (e.g. `1.1.0`) and push that to https://github.com/ut-evpp/evpp_utdk3_customizations . This should automatically update the information on Packagist.
## Deploying updates to sites
1. For all sites that use this repository, you can now receive the pending update via Composer. Assuming the sites are hosted on Pantheon, navigate to each site's dashboard & click "Check for updates." This should detect a new version of `evpp/evpp_utdk3_customizations`, which you can apply via the dashboard and deploy up to the live environment (the update hook taking care of deploying the actual functionality).
