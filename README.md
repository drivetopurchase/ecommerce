[![Gitter](https://badges.gitter.im/gbeep/magento1-plugin.svg)](https://gitter.im/gbeep/magento1-plugin?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge)

## GoBeep - Ecommerce module - Magento 1x

Gobeep’s extension for Magento 1.x is designed to help clients who use the Magento platform to quickly and seamlessly generate links to validate game codes distributed by Gobeep gaming solution.
And to (optionally) display the game on the frontend.

### Installation

#### Using github

First, have your developer download the plugin from GitHub (in the release section). Instructions on how to install the plugin are located in the GitHub repository’s README (this doc). *This code is all you need to have basic functionality working*.

*Note: Looking at `app/code/community/Gobeep_Ecommerce/etc/config.xml`, a developer can see which Magento blocks, helpers and models are used.*

#### Using modman

```modman clone https://github.com/gbeep/magento1-plugin```

### Setup

After the extension has been installed, log in to the Magento Account, Click `System`, and then `Configuration`.
Next, in the `left-nav` menu, under `Sales`, expand the `Gobeep Ecommerce` section.

### Inputs

| Name                    | Type             | Description                                                                   |  Default  | Required |
| ----------------------- | ---------------- | ----------------------------------------------------------------------------- | --------- | -------- |
| active                  | yes/no           | Whether extension is disabled or enabled                                      | No        | Yes      |
| environment             | text             | Gobeep Environment                                                            | stable    | No       |
| region                  | text             | Gobeep Region                                                                 | eu        | No       |
| campaign_id             | text             | Campaign ID (used in `Gobeep_Ecommerce_Block_Link` block)                     |           | Yes      |
| cashier_id              | text             | Cashier ID (used in `Gobeep_Ecommerce_Block_Link` block)                      |           | Yes      |
| secret                  | text             | Secret given by `GoBeep` for signing requests and verify incoming webhooks    |           | Yes      |
| from_date               | date             | Start date (Date will be checked to determine if module is enabled or not)    |           | No       |
| to_date                 | date             | End date (Date will be checked to determine if module is enabled or not)      |           | No       |
| eligible_days           | multiselect      | Days of the week when module is enabled                                       |           | No       |
| cashier_image           | image            | Cashier link image (used in `Gobeep_Ecommerce_Block_Link` block)              |           | Yes*     |
| cashier_external_image  | string           | Cashier link image URL (used in `Gobeep_Ecommerce_Block_Link` block)          |           | Yes*     |
| campaign_image          | image            | Campaign link image (used in `Gobeep_Ecommerce_Block_Link` block)             |           | Yes*     |
| campaign_external_image | string           | Campaign link image URL (used in `Gobeep_Ecommerce_Block_Link` block)         |           | Yes*     |
| notify                  | yes/no           | Whether we should notify users when they are winning or they are refunded     |           | No       |
| winning_email_template  | string           | Email Notification template (winning)                                         |           | No       |
| refund_email_template   | string           | Email Notification template (refund)                                          |           | No       |

<sub>(*) Use one or another (external or internal)</sub>

#### Blocks

##### Link block

We recommend using the `New Order` email to integrate the cashier/campaign links. The `Mage_Sales_Model_Order` object **MUST** be passed to the block when generating `cashier` links. The default template is located in `app/design/base/default/template/gobeep` directory.

Note: You **MUST** whitelist `gobeep_ecommerce/link` block, otherwise `Security problem: gobeep_ecommerce/link has not been whitelisted` error might pop up in logs and block will not be rendered.
You may fix it via entering `System > Permissions > Blocks` in Admin Panel and defining new block whitelist with `gobeep_ecommerce/link` as a name. 

###### cashier link

```{{block type="gobeep_ecommerce/link" order=$order for="cashier"}}```

###### campaign link

```{{block type="gobeep_ecommerce/link" for="campaign"}}```

##### Interstitial

The `gobeep_ecommerce/link` block can be used to display the game UI on `frontend`, this should be done on `cms_home` block ideally. Example below.

```
<reference name="content">
  <block type="gobeep_ecommerce/link" name="gobeep.insterstitial" alias="gobeep_interstitial" after="cms_page" template="..."></block>
</reference>
```

#### Transactional Email

If you want to use email notifications. Here's the list of templates.
:warning: You should create a new template for all these transactional emails in the `admin`, there's no default template for them. 

| Name                             | Type             |
| -------------------------------- | ---------------- |
| Gobeep Ecommerce Status Refunded | Refund email     |
| Gobeep Ecommerce Status Winning  | Winning email    |


### Support

For any technical issue with the module, please open an issue on `GitHub`.
