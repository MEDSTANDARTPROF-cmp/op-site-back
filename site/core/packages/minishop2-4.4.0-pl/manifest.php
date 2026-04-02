<?php return array (
  'manifest-version' => '1.1',
  'manifest-attributes' => 
  array (
    'changelog' => '# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [4.4.0-pl] - 2024-08-06

 ### Removed
 - fotorama\'s config #893

### Added
- Managing the cost of delivery of a completed order #895
- Ability to skip products when regenerating images  #918
- Missing rank equal to zero in msGetOrder snippet #904
- new lexicons

 ### Fixed
- Fix for deleting options #912
- change delimeter on delimiter in keys of parameters  #902
- Fix for options disappearing for products with multiple categories #913
- Syntax error in chunk.ms_cart_new.tpl
- Fix for correction price processing when creating a shipping method #919


 ### Changed
 - ReadMe  (added link in post about testing PR) #888

## [4.3.0-pl] - 2022-07-01

### Added
- New mgr section  Utilites
- Utilites\\Tab "Update previews" #868
- Utilites\\Tab "Import from CSV" #883
- Hash in URl for settings tabs #887

 ### Changed
 - ReadMe  (added link in post about testing PR) #888


## [4.2.2-pl] - 2022-06-01

### Added
 - New Crowdin updates

 ### Changed
 - Update plupload plugin

### Fixed
-  change visibility of method msCartHandler::getProductKey


## [4.2.1-pl] - 2022-05-07

### Fixed
-  A bug with lost field for msVendor.rank

 ### Changed
 - The regular expression for validating a phone number in an order has been moved to the system setting
 - All JS code in the new set of scripts is formatted according to eslint rules
 - Minishop2 Config renamed to config in main class of new JS bundle


## [4.2.0-pl] - 2022-05-01

### Added
- Additional Events for Vendors
- New section with information and useful links
- New field rank for msVendor
- Information about the customer in emails to the manager

### Fixed
- The problem of additional shipping costs if it is indicated as a percentage
- Sorting in payment options
- Improved use of return = json mode in msProducts

## [4.1.5-pl] - 2022-04-09

### Fixed
- Fixed a bug with creating cart key with options
- fix CustomInputNumber error in cart

## [4.1.4-pl] - 2022-03-31

### Added
 - New lexicons
 - Ability to attach TV fields to products inside an order letter
 - Ability to regenerate images from the product grid
 - Eslint JS Code Validator

 ### Changed
 - The regular expression for validating a phone number in an order has been moved to the system setting
 - All JS code in the new set of scripts is formatted according to eslint rules
 - Minishop2 Config renamed to config in main class of new JS bundle

 ### Fixed
 -  A bug with missing default value for msOrderAddress.order_id
 -  A bug with dragging goods in the grid

## [4.1.3-pl] - 2022-03-14

### Changed
- Fixed error with return data in snippet msGallery


## [4.1.2-pl] - 2022-02-03

### Changed
- Fixed incorrect preventDefault in vanilla js script

## [4.1.1-pl] - 2022-02-02

### Changed
- Update JS classes names in system settings
- Rename new JS method sendResponse to sendRequest

## [4.1.0-pl] - 2022-31-01

### Added
- New type of options - color selection
- Queue manager support
- Working with lexicons through the crowdln service, similar to MODX
- Additional data returned by the cart when it is changed
- New modes for returning data from msGallery
- System setting ms2_status_canceled


### Changed
- Fixed a bug with simultaneous operation of the basket with different contexts
- Added saving the description field to the gallery/upload processor
- Improved sorting and selection of uploaded files to save the first image to the msProductData table
- Updated the text and values of the setting_ms2_order_product_options system setting
- Fixed a bug with redirection after placing an order in new js
- Many minor syntax and code readability improvements

### Removed
- Setting for button icons in mgr category
- Phone length limitation during validation in the order

## [4.0.0-pl] - 2022-12-18

### Added
- Displaying custom parameters in msProducts.tpl #742
- Patch for error when installing and creating the ms2_options table  #747
- Patch for error with non-existent fields in the tables #749
- Creating a vendor when creating a product #748

## [4.0.0-beta] - 2022-09-16

### Added
- A new set of JS scripts
- Creating an order from the manager
- Ability to assign order statuses for various events (new order, payment, creating draft)

### Changed
- Relationship between msOrder and msOrderAddress
- Interfaces for controllers are moved to a separate file

### Removed
- Plugin fotorama
- Depricated controllers

## [3.0.7-pl] - 2022-09-06

### Added
- MODX and PHP version check before installation
- Snippet options are now available in wrapper chunks
- The ability to search for an order in the admin panel by phone number
- Email field to order address window

### Changed

- Css styles reserved for global layout classes (header, footer, title, count, weight, price, image) in msCart and msGetOrder chunks.

## [3.0.6-pl] - 2022-06-29

### Changed
Improved readability of the code without changing the logic
Fixed potential unknown variable error in  Order.submit method

### Removed
- PayPal payment module

## [3.0.5-pl] - 2022-06-16

### Changed
Fixed bug of linking order and address models
Fixed conflict of identical fields #696

## [3.0.4-pl] - 2022-06-09

### Added
Removal of empty directories of images #685

### Changed
Fix escaping rank field for msProductFile #690
Fix first thumbnail generator #691
load minishop2:default lexicon in registerFrontend #688

## [3.0.3-pl] - 2022-05-30

### Changed
- Fixed error with escaping of the rank on object msOrderStatus
- Added escaping of the rank in upload processor
- Returned the ability to add any fields to the order object

## [3.0.2-pl] - 2022-05-20

### Added
- The ability to update image previews in the product grid
- The ability to specify the role number when registering a user
- Lost system setting ms2_cart_max_count

### Changed
- msCartHandler, msOrderHandler, msDeliveryHandler, msPaymentHandler deprecated file notifications now listen to the log_deprecated system setting
- Escaping of the rank field to support mysql 8

## [3.0.1-pl] - 2022-05-04

### Changed
- Old controllers msCartHandler, msOrderHandler, msDeliveryHandler, msPaymentHandler are marked as deprecated with usage error logged
- The configuration required for JS controllers no longer depends on the included js files and is always available
- DB storage controllers got additional checks
- Update Copyrights

## [3.0.0-pl] - 2022-04-27

### Added
- New catalog handlers to store the main controllers: msCartHandler, msOrderHandler, msDeliveryHandler, msPaymentHandler
- System settings: ms2_tmp_storage, ms2_register_frontend
- Database fields ms2_orders.session_id, ms2_order_addresses.email ms2_order_products.properties
- New controllers for db storage, session storage
- New order status Processed
- Method RegisterFrontend

### Changed

- Composite msOrder.Address change owner from foreign to local
- Clear controllers msCartHandler, msOrderHandler, msDeliveryHandler, msPaymentHandler.  Content replaced by stub
- Resolver table.  New mechanism for managing database tables and fields
- Field\'s type  ms2_order_statuses.rank  changed from tinyint to int
- Plugin minishop2.  Added Method RegisterFrontend to section OnLoadWebDocument

### Removed
- Resolver Upgrade

## [2.9.3-pl] - 2021-10-16

### Added

- Added a class for the form of cleaning the trash [#630]

### Changed

- Fixed bug with adding text_address field [#628]

## [2.9.2-pl] - 2021-09-28

### Added

- Added the ability to set / remove product previews [#617]

### Changed

- Removed duplicate code [#619]
- Removed a broken check for resource changes [#618]
- Fixed correct http code 403 in case of unauthorized access to the file [#622]
- Default msProduct class when added to cart [#613]
- Improved overall code readability [#620]
-- Optimized dirname method when connecting files
-- Optimized definition of constants
-- Added PHPDOC
-- Removed commented code and some empty blocks
-- Added access specifier to methods

## [2.9.1-pl] - 2021-09-12

### Added

- Added msVendor fields in the list of order items [#612]
- Added frequently used fields to the address table [#581]

### Changed

- Bug fixed This image is already in the product gallery

## [2.9.0-pl] - 2021-08-25

### Added

- Added wiretapping of initiated context for work from API [#578]
- Added counting of the number of items in the cart [#579]
- Replace ssl certificate for PayPal [#573]
- Added delivery_cost parameter for msOnGetOrderCost event [#585]
- Added the ability to set week start in orders [#570]
- Added support for zero cost

### Changed

- Fixed the logic of registration of the order status label in the order history [#576], [#580]
- Bringing the code to the PSR12 standard [#574]
- Refactoring PSR-12 errors [#577]
- Added a name field for micro-markup [#588]
- Fixed error Cannot read property \'Search\' of undefined [#591]
- getCost method optimized [#602]
- Fixed error create ms2_options table

## [2.8.3-pl] - 2021-04-01

### Changed

- Regular expression update to validate phone number [#563]
- Legacy php backward support [#564]

### Removed

- Removed object references as legacy code. [#566]

## [2.8.2-pl] - 2021-03-15

### Added

- Added support for OR, AND operators in the optionFilters parameter in the msProducts snippet [#539]
- The concept of refactoring the connection of notification scripts [#542]
- Added "Save and close" button on links window [#533]
- Added support for the return parameter in msProducts [#553]

### Changed

- Fix Recoverable error: Object of class msProductData_mysql could not be converted to string [#555]
- Change variables in fenom chunk [#557]
- New keep a changelog format
- Fixed display of product option value on product category page [#547]
- Remove unnecessary type="text/javascript"

### Removed

- Removed unused library `jquery.form` [#544]
- Removed unused old.css file [#552]
- Removed deprecated backward compatibility method with ms1 [#537]
- Remove duplicate css [#558]

## [2.8.1-pl] - 2021-02-14

### Changed

- Fixed Recoverable error: Object of class msProductData_mysql could not be converted to string [PR #532]

## [2.8.0-pl] - 2021-01-27

### Added

- Improvement. Dividing the order amount by the purchase and delivery [PR #507]

### Changed

- Checking if the field free_delivery_amount is present [PR #520]
- Set checkbox value [PR #513]
- Minor English vernacular improvements [PR #512] [PR #511]
- Fixed. Correction of displaying a product category if it is not a container [PR #509]
- Fixed PHP warning: count() error [PR #508]
- Fixing the Notice: Undefined offset error when importing a picture with a Russian name through the \'gallery/upload\' processor [PR #504]
- Converting code to cross-browser ECMAScript 5.1 standard [PR #503]
- Fixed display of settings tabs and resource groups when editing a Category [PR #501]
- Fixed logic for free delivery calculation [PR #498]

### Removed

- Removing the console script for converting from ms1 to ms2 [PR #506]

## [2.7.0-pl - 2020-12-09

### Added

- Improvement. Discount calculation added to the status method in the cart class [PR #497]
- Improvement. Added order product\'s options interface [PR #491]
- Improvement. Added amount for free shipping [PR #490]
- Improvement. Changing any product fields by event msOnGetProductFields [PR #417]
- Improvement. Added an example of displaying product content on Fenom [PR #478]
- Improvement. `ignoreGroups` and `sortGroups` to `msProductOptions` snippet [PR #483]
- Improvement. Added `return` property to `msProductOptions` snippet [PR #484]
- Improvement. Added transparency to disabled grid lines [PR #485]

### Changed

- Fixed problem with "Cannot read property items of undefined" and corrected the order of tabs when creating an item [PR #486]
- Fixed PHP warning: count() [PR #482]
- Fixed baseUrl in media source [PR #489]
- Fixed visual bug [PR #480]

## [2.6.0-pl] - 2020-11-12

### Added

- Added micro-markup [PR #471]
- Added ms2_frontend_country lexicon [PR #470]
- Added displaying of the alt attribute and title in the gallery chunk [PR #469, #472]
- Added display of the gallery tab when creating a product [PR #455]
- Added sortOptions property for snippet msProductOptions [PR #447]
- Added multicontext cart settings [PR #436]
- Added buttons on orders and settings pages [PR #435]
- Added sorting sortOptions in msOptions, msProductOptions [PR #407, #433]
- Added renderBadge to statuses [PR #432]
- Added settings link in miniShop2 menu [PR #430]
- Added msorder_remove permission [PR #421]
- Added icon for image in vendor modal creation [PR #420]
- Added usable choice of chunk in the windows for creating/editing statuses [PR #419]
- Added clone msCategory options on subcategory creation [PR #412]
- Added ids to product tabs [PR #408]
- Added filter on modCategory for options [PR #405]
- Added settings for order numeration [PR #393]
- Added example for webp in media source [PR #385]
- Added reloading order object after changes in changeOrderStatus method [PR #384]
- Added icons for tree from system settings [PR #365]
- Added product cost in cart [PR #358]

### Changed

- Fixed sorting by customer field in the order list [PR #468]
- Updated snippets: msCart, msOrder, msGetOrder and msGallery [PR #461]
- Changed the display encoding of collector messages [PR# 460]
- Changed order of tabs for category [PR #459]
- Expanded functionality of windows for quick creation/updating of product/category [PR #439]
- Fixed clone media source on update if is renamed [PR #438]
- Fixed ui bug header in modal window [PR #434]
- Update lexicons [PR #414]
- Ext.Loader remove asynchronous loading of strftime-min-1.3.js [PR #411]
- Display field key if caption and lexicon entry is empty [PR #401]
- Fix miniShop2.combo.Options paging on local mode [PR #400]
- Fixed deleting link from one-to-one deleting all links [PR #389]
- Switch context if the order context does not match [PR #388]
- Fixed paypal error [PR #387]
- Fixed error 500 if pdoTools not installed [PR #386]
- Change class name to variable in sort processor  [PR #381]
- Moved product gallery tabs if enabled ticket comments [PR #377]
- Moved product tabs [PR #376]
- Fix options bug [PR #374]
- Fix if impossible to remove the "Vendor" field [PR #372]
- Output context name if it is [PR #364]
- Notify user on move product in grid [PR #359]
- Fixed miniShop2.combo.Options [PR #355]

## [2.5.0 pl]

### Added

- Added translation into Belarusian (thanks @dmse4050)
- Improvement. Adding Delete / Update buttons preview in the product gallery [PR #346]
- Improvement. Add drag\'n\'drop sortable for miniShop2.combo.Options [PR #345]
- Improvement. Getting parameters for multi-domain [PR #340]
- Added class small in chunk.ms_cart.tpl [PR #319]
- Added support for *webp images [PR #318]

### Changed

- Fix resetting user id value on expand combobox [PR #351]
- Fix hiding content field via FC [PR #343]
- Fix bug when uploading pictures to the gallery, files remain in the root [PR #349]
- Fix bug when saving order and changing status [PR #348]
- Fix Preventing the removal of goods with the wrong (empty) count [PR #344]
- Fix TV update bug if there are more than 10 during CSV import [PR #341]
- Fixed error "Incorrect decimal value" [PR #338]
- Change update product from grid [PR #337]
- Fix "formatPrice", "formatWeight" [PR #336]
- Optimization of the withdrawal of orders and fix for recounting an order [PR #335]
- Fix cutting Czech characters from username in order [PR #334]
- Fix defaultSortField [PR #333]
- Refactoring Relationship Methods [PR #331, #332]
- Fix set default media source on change resource class_key [PR #330]
- Fix some bugs on manager pages [PR #327]
- Fix romanian lexicon [PR #324]

## [2.4.18 pl]

### Added

- [#315] Added romanian lexicon.
- [#316] Added webp extension for mediaSource.

### Changed

- Update chunks for Bootstrap 4.
- Fixed display of manager tree in MODX 2.7.1
- [#314] Fixed missing categories for options in settings.

## [2.4.17 pl2]

### Changed

- Fixed load of lexicons in contexts.

## [2.4.16 pl]

### Changed

- Fixed possible SQL injections by Agel_Nash
- Change miniShop2::changeOrderStatus
- Change "msProductGetListProcessor"

## [2.4.15 pl]

### Added

- Added new events: "msOnBeforeGetOrderCustomer", "msOnGetOrderCustomer"

### Changed

- Change miniShop2::getCustomerId
- Change submit::msOrderHandler
- Change getDeliveryRequiresFields::msOrderHandler

## [2.4.14 pl]

### Added

- Added new events: "msOnBeforeSaveOrder" and "msOnSaveOrder"

### Changed

- Change miniShop2.combo.Options
- Fixed msCategoryOption permission
- Change receiver validate
- Fixed size gallery [js]
- Fixed height options tree [js]
- Change msProductData::saveProductOptions()
- Change key index prefix length to 191 from 255
- Change new installs to create tables with "InnoDB" engine

## [2.4.13 pl]

### Added

- Added "payment_link" in the snippet msGetOrder.
- Added new event: "msOnGetStatusCart".
- Added ukrainian lexicon.

### Changed

- Fixed "cultureKey" option
- Change Order submit js.
- Fixed msProductData::loadOptions().

## [2.4.12 pl]

### Added

- Added dutch lexicons by Sterc
- Added greek frontend lexicon

### Changed

- When you change the price of a product with msOnGetProductPrice, the old_price will be changed only if the new price is lower.
- Fixed method msProductData:rankProductImages()

## [2.4.11 pl]

### Added

- [mgr] Add contexts list into filters at the orders page.
- [mgr] Add "expand,collapse,check,uncheck" actions to the categories and options trees.

### Changed

- Fixed default path to loading services in the loadCustomClasses() method.
- Update the cost of an orders after product addition.
- Improved processing of products options.
- [mgr] Fixed possible error on get orders statuses list.
- [mgr] Fix "xcheckbox" in a Product options.

### Removed

- Removed call of ms2Gallery::syncFiles() from msProductData::updateProductImage().

## [2.4.10 pl]

### Added

- Added events in the processors of msOrderProduct.

### Changed

- Fixed possible E_WARNING in the snippet msOrder.

## [2.4.9 pl]

### Changed

- Improved loading of pdoTools in snippets

## [12.4.8 pl]

### Added

- Added support of ms2Gallery 2.0.

### Changed

- Media source option "thumbnails" now uses key of array with parameters as alias for thumbnail.
- Fixed bug of gallery with drag-over in Firefox.

### Removed

- Removed system setting "ms2_product_thumbnail_size".

## [2.4.7 pl]

### Changed

- Fixed the loading of product plugins when they can be loaded multiple times.
- Fixed fatal error on get classes in settings with some 3rd party payment methods.
- Fixed bug with incorrect rank of thumbnails after a sorting.

## [2.4.6 pl]

### Added

- Added the checking of users email when getting a customer id for new order.

### Changed

- Improved compatibility with MODX 2.5.2.

## [2.4.5 pl]

### Changed

- Fixed possible error in log on create a new product from cli mode.
- Updated jGrowl to version 1.4.5.

## [2.4.4 pl]

### Changed

- [#242] Ability to specify only needed options in the snippet msProductOptions.
- [#212, #241] Fixed handling of a product options with a dot in the name.
- [#240] Fixed the reset of date fields when product has been edited via category grid.
- [#239] Fixed duplicate and empty options in msProductData.
- [#229, #238] Categories tree now respects the "resource_tree_node_name_fallback" system setting.

## [2.4.3 pl]

### Changed

- [#237] Fixed msProductData::get(\'options\') method.
- [#236] Ability to display category columns of an ordered product.
- [#231] Snippet msOptions now transfers id of a product into a chunk.
- [#230] Fixed the inability to change vendor of a product more than 1 times.
- [#228] Possible fix for "empty file" in product gallery on some server configurations.
- [#227] Improved performance of the grid with options in settings.

## [2.4.2 pl]

### Changed

- Fixed error when system setting "ms2_product_tab_gallery" is disabled.

## [2.4.1 pl2]

### Changed

- Fixed error with overwrite product options when update from category grid.

## [2.4.0 pl]

### Changed

- [#222] Optimized some code in chunks.

## [2.4.0 rc11]

### Changed

- [msGallery] Improved fetching of thumbnails in the snippet.
- [#220] [mgr] Improved the total numbers in the form of orders.

## [2.4.0 rc10]

### Changed

- msPaymentHandler::getOrderHash() now includes order num.

## [2.4.0 rc9]

### Changed

- [#215] Ability to specify working context for cart.

## [1.4.0 rc8]

### Changed

- [#218] The product options are active immediately after assigning them to categories.

## [2.4.0 rc7]

### Changed

- Reverted back #216 due to issues.
- [#216] [msGetOrder] Need to update pdoTools to version 2.5.6-pl to fix this.
- [msGetOrder] Orders are show to all if parameter &id is set.
- [mgr] Fixed number of rows in orders panel.

## [2.4.0 rc6]

### Changed

- [#216] [msGetOrder] Fixed ability to &includeTVs.

## [2.4.0 rc5]

### Removed

- Removed wrong "requires" field in msPayment settings.

## [2.4.0 rc4]

### Changed

- Improved migration of system settings when upgrade from older versions.

## [2.4.0 rc3]

### Changed

- [msGallery] Improved snippet in case when product has no media source set.
- Fixed work with autocomplete product options.

## [2.4.0 rc2]

### Changed

- Fixed duplicate of order number in msOrderHandler.

## [2.4.0 rc1]

### Changed

- Fixed loop error on change class key from modResource to msProduct.

## [2.4.0 rc]

### Added

- [msOrder] New parameter &userFields.
- [msGetOrder] Added chunk tpl.msGetOrder.
- [msOptions] Added chunk tpl.msOptions.
- [msProductOptions] Added chunk tpl.msProductOptions.
- [msGallery] Added chunk tpl.msGallery.
- [msGallery] Added Fotorama script out from the box.

### Changed

- All snippets are completely rewritten.
- All chunks uses Fenom syntax.
- Improved compatibility with MySQL 5.7.
- Improved orders form panel.
- [msOrder] Chunk tpl.msOrder.outer renamed to tpl.msOrder.
- [msCart] Chunk tpl.msCart.outer renamed to tpl.msCart.
- [msGetOrder] Now acts as usual snippet and do not set placeholders to the page by default.
- Improved email chunks. Added common email template.

### Removed

- [msOrder] Removed chunks tpl.msOrder.delivery, tpl.msOrder.payment and tpl.msOrder.success.
- [msCart] Removed chunks tpl.msCart.row and tpl.msCart.empty.
- [msGetOrder] Removed chunk tpl.msGetOrder.row.
- [msOptions] Removed chunks tpl.msOptions.outer and tpl.msOptions.row.
- [msProductOptions] Removed chunks tpl.msProductOptions.outer and tpl.msProductOptions.row.
- [msGallery] Removed chunks tpl.msGallery.outer, tpl.msGallery.row and tpl.msGallery.empty.

## [2.4.0 beta3]

### Changed

- Small improvements of product gallery.
- Improved ExtJS settings panel.

## [2.4.0 beta2]

### Added

- Added system setting "ms2_template_category_default".
- New system to register 3rd party classes of extensions.
- New system to register 3rd party plugins for objects model.
- New logo.

### Changed

- Improved referrals registration.
- Improved ExtJS product panel.
- Emails chunks are now processed by pdoTools.
- Replaced modX::toJSON and modX::fromJSON calls to native functions.
- Improved duplicate of products and categories.
- The data fields of the product are added to the tags of the resource: [[*price]], [[*article]] etc.
- Improved registration of JS and CSS on frontend.
- Javascript callbacks now are arrays with functions. New functions to register and remove callbacks.
- Updated model for MySQL 5.7.
- Improved product gallery.

## [2.4.0 beta1]

### Changed

- Improved categories with products.
- Improved menu of categories in resources tree.
- Ability to change class_key of category.
- Many improvements of ExtJS category panel and products grid.
- Setting ms2_category_content_default is now empty by default.

## [2.4.0 beta0]

### Added

- Added icons for CRCs in managers tree.

### Changed

- Fixed php type of "article" in msProductData xPDO object.
- Improved orders panel.
- Updated builder.
- Minimum version is MODX 2.3.

## [2.2.0 pl2]

### Changed

- ID for Category\'s options tab
- Fix installation for MODX 2.4.0
- msProductOptions product property

## [2.2.0 beta4]

### Changed

- Category pagination fix.
- Fixed bug removal options when modifying and generating images of products
- Don\'t display tplOuter when all options are empty with hideEmpty=1

## [2.2.0 beta3]

### Changed

- Measure units for options.
- Restrictions for option names.
- Superboxselect and checkbox option types.
- Fixed bug with empty options after modResource save.

## [2.2.0 beta2]

### Changed

- Groups of options support.
- hideEmpty parameter in msProductOptions snippet.
- Fixed some bugs.

## [2.2.0 beta]

### Changed

- Custom product options support. More info at modx.pro.
- Fixed TV showing under content.
- Some code refactoring.

## [2.1.12 pl]

### Changed

- Improved installation script for MODX 2.4.

## [2.1.11 pl]

### Added

- Added new events: "msOnBeforeGetOrderCost" and "msOnGetOrderCost".

### Changed

- [mgr] Direct links to orders in manager.

## [2.1.10 pl1]

### Added

- Added support of Tickets 1.6.

### Changed

- Updated pdoTools version in the installer.

## [2.1.9 beta]

### Changed

- Fixed modification of order price by delivery and payment in default order handler.
- Fixed processing of decimal prices in PayPal payment class.
- Improved chunk tpl.msOrder.payment.

## [2.1.8 pl3]

### Changed

- Fixed controllers for MODX 2.3.
- Fixed routes to processors for MODX 2.3.
- Fixed product getlist processor for MODX 2.3.
- Fixed sorting products in category grid by clicking on column header.
- Disabled Bootstrap icons for MODX 2.3.
- Fixed product gallery for MODX 2.3.
- Fixed issue with deleting product files children in MODX 2.3.
- Fixed "autocomplete_err_noquery" error in MODX 2.3.
- [#148] Fixed duplicate connector.
- Fixed work of "minishop2-combo-user" in MODX 2.3.

## [2.1.8 beta]

### Added

- Added field "name" in object msOrderProduct for storing pagetitle of the product.
- Added integer field "type" to msOrder.

### Changed

- Fixed log level in PayPal method.
- Fixed CustomerProfile connection in schema.
- Improved price and weight formatting in orders table.
- Some UI improvements and fixes.

## [2.1.7 pl5]

### Changed

- [#131] Fixed update of product thumb when you update thumbnails.
- [#129] [msGetOrder] Fixed setting of placeholders.
- Fixed work with comments on product panel.
- [msOrder] Now snippet loads "building", "room" and "comment" from extended field of users profile.
- [mgr] Fixed parameter "maxUploadSize" in gallery.

## [2.1.7 pl1]

### Added

- [#113] Added Lithuanian lexicons.
- New system setting "ms2_category_remember_grid".

### Changed

- [#119] Improved compatibility with AjaxManager.
- [#118] Fixed negative cost in payment and delivery methods.
- [#112] Restrict cart items to specific contexts.
- [#111] Fixed remove of products links.
- [#107] Fixed empty customer field if fullname is not specified.
- [#106] Additional check of friendly filename in gallery.

## [2.1.6 pl4]

### Changed

- [#110] Fix generate thumbs on upload for Amazon S3.
- Fixed counting money spent in new customers.
- [#104] Fixed placeholder [[+cart_weight]] in emails.
- [#102] Improved checking of thumbnails url.
- [#98] Refactored permissions in processors.
- [#94] Formatted placeholder [[+cost]] in snippet msCart
- [#78] Fixed setting flag isfolder to false for old category of the product.
- Fixed handling of non-ajax requests.

### Removed

- Removed unnecessary ajax request on add to cart. Fixed possibly E_NOTICE.

## [2.1.5 pl]

### Added

- Added default media source in product "create" processor.
- Added default template in product "create" processor.
- Added buttons in orders management page.

### Changed

- Improved rename of files in gallery.

## [2.1.4 pl5]

### Added

- Added script for import products in core/components/minishop2/import/csv.php.

### Changed

- Fixed icons in chunks.
- [#95] Fixed price and weight format.
- Improved installator. Now you can update chunks on install.
- Fixed possible errors on thumbnail generation.
- Progressive thumbnails in gallery.

## [2.1.3 pl2]

### Added

- Support Bootstrap3.

### Changed

- Fixed E_WARNING in snippet msOptions.
- Fixed bug with php-apc on create order.

## [2.1.2 pl2]

### Changed

- Fixed possibly E_NOTICE in snippets.

## [2.1.2 pl]

### Added

- Added support of non-image files in gallery.
- Added ability to hide tabs of product page in manager. See new system settings.

### Changed

- [#77] Fixed change type of existing resource to "msProduct".
- Improved style of horizontal product tabs.
- Improved proportions in settings tab of category.
- Improved rename of product images.
- Improved retrieving of first thumbnail of image in gallery.
- If you specified wrong "ms2_product_thumbnail_size" parameter, gallery will still work.
- Improved snippet "msGallery" for display non-image files.
- [#84] Fixed displaying of  email of vendor in manager.

## [2.1.1 pl2]

### Changed

- Fixed parameter "&parents" in msProducts.

## [2.1.1 pl]

### Changed

- Snippet msProducts supports pdoTools 1.8.0. Parameter "&showHidden" enabled by default.
- Fixed fatal error with "clearCache" when msProduct created trough processor and it`s parent is not msCategory.
- Improved generation of thumbnails.

## [2.1.1 pl]

### Added

- Added support of component "msDiscount".
- Added counting of total spent sum by every customer in msCustomerProfile.

### Changed

- Fixed access permissions tab on product update.
- Fixed getPrice and getWeight calls in snippet msProducts.
- Improved xtype "minishop2-combo-user".

## [2.1.0 pl2]

### Changed

- Fixed placeholders on msProduct page due to issues with @INLINE chunks.

## [2.1.0 pl1]

### Added

- Added new events: "msOnGetProductPrice" and "msOnGetProductWeight".

### Changed

- Fixed wrong events in database from previous release.
- Changed "vendor_" to "vendor." in msProduct::toArray() for sameness with the snippet msProducts.
- Moved all logic from old action.php to new plugin. File leaved for compatibility with users modified javascript.

## [2.1.0 pl]

### Added

- Added method msPayment::getCost().
- New object "msCustomerProfile". It can be extended by plugins like "msProductData".
- Added new fields in "msProductFile": "hash" for sha1 of file and json field "properties".
- Added check for duplicate images in product gallery by checking hash of the content of file.
- Added new parameters for media source: "maxUploadWidth" and "maxUploadHeight" for frontend image resize.
- Added renaming files of product gallery together with thumbnails.
- [#73] Added virtual vendor fields. You can get it by $res->get(\'vendor_name\');
- [#65] Added new system events: "msOnBeforeValidateOrderValue" and "msOnValidateOrderValue".
- [#64] Added ability to stop switching order status from system plugin.
- [#63] Added new parameters for media source: "imageNameType". Switching to "friendly" will generate names for uploaded files by FURLs algorithm.
- [#61] Added ability to sort products in category by drag and drop.
- [#56] Added JSON field "properties" to "msProductFile".
- [#52] Added ability to customize orders in manager by 3 system setting: "ms2_order_grid_fields", "ms2_order_address_fields" and "ms2_order_product_fields".
- Added method PayPal::getPaymentLink() for continuing interrupted payment process. You will see [[+payment_link]] in emails.
- Added parameters "&tplSingle" and "&toSeparatePlaceholders" to snippet msGallery.

### Changed

- Changed default sort in goods grid on "menuindex, ASC".
- Improved method `msDelivery::getCost()`.
- Improved method msOrderHandler::getCost(). Now you can specify additional percent for delivery and payment in manager.
- Plugin "miniShop2" can automatic save referrers.
- In snippet "msProducts" you can override "where", "select", "leftJoin", "innerJoin" and "rightJoin" properties. Added pdoFetch 1.4.1 support.
- Improved other snippets.
- Changed uploader in product gallery to "Plupload" (Thanks to Alex Rahimov).
- Gallery upload processor now can receive parameters "id" and "file" for external images upload.
- [#77] Fixed changing type of existing resource to "msCategory".
- [#76] Fixed parameter "offset" in msGallery.
- [#75] Remove multi-category links when category removed.
- [#74] Refresh data in order from users profile if he is authenticated.
- [#66] Fixed saving payments methods when create a new delivery.
- [#62] Fixed work with "*.gif" files in products gallery.
- [#59] Improved regular expression for email verification.
- [#59] Improved validation of customer name and email when create order.
- [#59] msOrderHandler::add() now can return an error.
- [#59] Improved registration of javascript on frontend.
- [#59] Improved show\\hide of msMiniCart with css.
- [#59] Refactored default frontend javascript.
- [#59] Works with no javascript. (Thanks to Alexey Kartashov)
- [#58] Fixed PayPal non-USD currency.
- [#50] Fixed checking of the existence of the Tickets component.
- [#5] Possible fixed "Cannot execute queries while other unbuffered queries are active" on php < 5.3.
- Some fixes for MySql STRICT_TRANS_TABLES mode.
- Updated jQuery to version 1.10.2
- Gallery UI improvements for MODX 2.2.9
- Returned action.php for backward compatibility with the old javascript

## [2.0.1 pl3]

### Added

- Added german lexicon.

### Changed

- Now you can specify version of file in "ms2_frontend_js".
- [#60] Fixed type of field "index" in "msOrder".
- Improved listing of products in the categories of manager.

## [2.0.1 pl2]

### Changed

- Improved submit of order form.
- msProducts saves order of ids received through &resources=``. It need for smooth work with mSearch2.
- [mgr] Fixed search in vendors combo

## [2.0.1 pl1]

### Added

- Added loading of lexicons for 3rd party payment methods. They must be named in "msp.%name%.inc.php" format.
- Added placeholder [[+payment_link]] in the new user email. It will work only if payment method has function getPaymentLink(msOrder $order);
- [#45] Added system setting for specify default content of category.

### Changed

- [#53] Completely refactored call and processing plugin events.

### Removed

- Removed placeholder [[+id]] from product page because of issues.

## [2.0.1 pl]

### Added

- Added 2 new events on order remove: "msOnBeforeRemoveOrder" and "msOnRemoveOrder".

### Changed

- Improved classes: "msProduct" and "msCategory". Now `$modx->getCollection(\'msProduct\');` returns only products.

## [2.0.1 beta3]

### Changed

- Fixed msGallery

## [2.0.1 beta2]

### Added

- [#42] Added parameter "returnIds" to snippet msProducts.
- Added setting placeholder [[+idx]] to snippets msGallery and msProducts.
- Added parameter "where" in snippet msGallery, for specify JSON encoded string with additional query data.
- Added parameters "limit" and "offset" for pagination. msGallery can be used with getPage now.

### Changed

- [#44] Enabled duplicate of category.
- [#41] Fixed panel with tvs on product create.
- Parameter "resources" not suppressing "parents" in msProducts anymore. Now they working together: resources that are not in parents will not be returned.
- Snippet msGallery now can to display placeholders like [[+600x]] or [[+x600]].
- Fixed display an original image in msGallery. If upgrade, replace placeholders [[+image]] to [[+url]] in chunk "tpl.msGallery.row" manually.

## [2.0.1 beta1]

### Changed

- Maybe fixed bug with php-apc and sessions
- Fixed plugin params on events "msOnBeforeCreateOrder" and "msOnCreateOrder". Now you can change $msOrder by link.
- Fixed error with no changing status of order in manager.

## [2.0.1 beta]

### Added

- [#38] Added "menuindex" in product fields.
- [#35] Added 2 system setting to enable or disable the remembering of panel tabs.
- [#29] Added verification of object instance, when loading neighborhood resources in manager.
- New permissions for work with order: "save" and "view". New events for update order.
- Added ability to add\\update\\remove products in order.

### Changed

- [#37] Ability to override method miniShop2::changeOrderStatus() in custom order class.
- [#32] Enabled displaying of errors when uploading files in product gallery.
- [#30] "miniShopManagerPolicy" is automatically assigned to a group "Administrators".
- [#28] Returned the lost fields in images of msGallery.
- [#27] Improved ajax requests in default javascript.
- [#26] Improvements of categories tree.
- Fixed quick fields in snippet msOrder. Update your chunks for delivery and payment methods.
- Improved "totalVar" placeholder in snippet msGallery.
- Fixed model. Added proper primary keys for xPDOObjects.
- Ability to load only "miniShop2Config" object on frontend, without default javascript and jQuery.
- Fixed parameter "depth" in snippet "msProducts".

### Removed

- [#36] Removed filter_var(), because of issues on stupid hostings.
- [#25] Removed replacing of empty alias to id. Added 2 system setting for switching using resource id as alias for categories and products.

## [2.0.0 pl3]

### Changed

- Compatibility with pdoTools 1.2.0

## [2.0.0 pl2]

### Added

- Added ability to create system setting "ms2_cart_max_count" to override maximum number of products for one operation.
- Added system setting "ms2_price_snippet" for modification of product price.
- Added system setting "ms2_weight_snippet" for modification of product weight.

### Changed

- [mgr] Fixed formatting of dates in category grid.
- [mgr] Fixed decimals in product category grid.
- Fixed placeholders in emails subjects.
- Fixed generation of thumbnails in gallery for working with ImageMagick.
- Fixed link type "Many to many".
- Fixed requirement of non-existing permission "update_document" in processors.
- Product key in msCartHandler now generates with using "$price" and "$weight", because they can be modified when adding to cart.

## [2.0.0 pl1]

### Added

- Added selection of product links in snippet msProducts. See the new snippet properties.
- Added ability to use msProducts with any modResource class.
- Added property "tvPrefix" to msProducts for compatibility with getResources.
- Added property "outputSeparator" to snippets msOptions and msProducts.
- Added system parameter "ms2_price_format" for specifying how to format price of product.
- Added system parameter "ms2_price_format_no_zeros" for optional removing extra zeros at the end of price.
- Added system parameter "ms2_weight_format" for specifying how to format weight of product.
- Added system parameter "ms2_weight_format_no_zeros" for optional removing extra zeros at the end of weight.
- [mgr] New icons in tree

### Changed

- [mgr] Changing of product image now cleans its cache.
- Small improvements of the snippets at initialization.

## [2.0.0 rc6]

### Changed

- Fixed cleaning pdoTools in msGallery
- Improved saving payment method in order when quickly switch delivery.

## [2.0.0 rc5]

### Changed

- Integration of PayPal express-checkout payment method.

## [2.0.0 rc4]

### Changed

- Fixed bugs in snippets
- Fixed joining images by rank when includeThumbs.

## [2.0.0 rc]

### Added

- [mgr] Added displaying of nested products in categories. System setting "ms2_category_show_nested_products" can disable it.
- [mgr] Added the ability to link products together.
- [mgr] Added link to resource from ordered products.
- [mgr] Added ability to specify web document for Vendor by property "resource".
- [#10] Added checking for negative value of adding products in cart.
- [#12] Added regeneration site maps after new product create.
- [#14] Added check of "register_globals" on cart/add.
- Added system setting "ms2_product_thumbnail_size" for setting default size of product thumbnail.
- Added vendor placeholders on product page. Now you can use [[+vendor.name]], [[+vendor.logo]] etc.
- Added beta scripts for console converting miniShop1 to miniShop2.
- Added partial french translation.
- Added method miniShop2::getTagged() from MS1.
- Added ability to load plugins, that can add new or overload existing product fields in model and manager.
- Added parameters for joining thumbnails and tvs in snippets msProducts, msCart and msGetOrder.
- Improved save of product options.

### Changed

- [#6] Fixed error when order makes authenticated user without email.
- [#8] Fixed remove of order.
- [#9] Fixed date formatting in manager for working in Firefox.
- Fixed automatic install of pdoTools.
- Fixed fetching images in snippet msGallery
- Other fixes and improvements.

## [2.0.0 beta-1]

### Added

- Added manager pages
- Added create and manage orders

## [2.0.0 beta-0]

- Initial release.
',
    'license' => 'GNU GENERAL PUBLIC LICENSE
    Version 2, June 1991
--------------------------

Copyright (C) 1989, 1991 Free Software Foundation, Inc.
59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

Everyone is permitted to copy and distribute verbatim copies
of this license document, but changing it is not allowed.

Preamble
--------

The licenses for most software are designed to take away your
freedom to share and change it.  By contrast, the GNU General Public
License is intended to guarantee your freedom to share and change free
software--to make sure the software is free for all its users.  This
General Public License applies to most of the Free Software
Foundation\'s software and to any other program whose authors commit to
using it.  (Some other Free Software Foundation software is covered by
the GNU Library General Public License instead.)  You can apply it to
your programs, too.

When we speak of free software, we are referring to freedom, not
price.  Our General Public Licenses are designed to make sure that you
have the freedom to distribute copies of free software (and charge for
this service if you wish), that you receive source code or can get it
if you want it, that you can change the software or use pieces of it
in new free programs; and that you know you can do these things.

To protect your rights, we need to make restrictions that forbid
anyone to deny you these rights or to ask you to surrender the rights.
These restrictions translate to certain responsibilities for you if you
distribute copies of the software, or if you modify it.

For example, if you distribute copies of such a program, whether
gratis or for a fee, you must give the recipients all the rights that
you have.  You must make sure that they, too, receive or can get the
source code.  And you must show them these terms so they know their
rights.

We protect your rights with two steps: (1) copyright the software, and
(2) offer you this license which gives you legal permission to copy,
distribute and/or modify the software.

Also, for each author\'s protection and ours, we want to make certain
that everyone understands that there is no warranty for this free
software.  If the software is modified by someone else and passed on, we
want its recipients to know that what they have is not the original, so
that any problems introduced by others will not reflect on the original
authors\' reputations.

Finally, any free program is threatened constantly by software
patents.  We wish to avoid the danger that redistributors of a free
program will individually obtain patent licenses, in effect making the
program proprietary.  To prevent this, we have made it clear that any
patent must be licensed for everyone\'s free use or not licensed at all.

The precise terms and conditions for copying, distribution and
modification follow.


GNU GENERAL PUBLIC LICENSE
TERMS AND CONDITIONS FOR COPYING, DISTRIBUTION AND MODIFICATION
---------------------------------------------------------------

0. This License applies to any program or other work which contains
a notice placed by the copyright holder saying it may be distributed
under the terms of this General Public License.  The "Program", below,
refers to any such program or work, and a "work based on the Program"
means either the Program or any derivative work under copyright law:
that is to say, a work containing the Program or a portion of it,
either verbatim or with modifications and/or translated into another
language.  (Hereinafter, translation is included without limitation in
the term "modification".)  Each licensee is addressed as "you".

Activities other than copying, distribution and modification are not
covered by this License; they are outside its scope.  The act of
running the Program is not restricted, and the output from the Program
is covered only if its contents constitute a work based on the
Program (independent of having been made by running the Program).
Whether that is true depends on what the Program does.

1. You may copy and distribute verbatim copies of the Program\'s
source code as you receive it, in any medium, provided that you
conspicuously and appropriately publish on each copy an appropriate
copyright notice and disclaimer of warranty; keep intact all the
notices that refer to this License and to the absence of any warranty;
and give any other recipients of the Program a copy of this License
along with the Program.

You may charge a fee for the physical act of transferring a copy, and
you may at your option offer warranty protection in exchange for a fee.

2. You may modify your copy or copies of the Program or any portion
of it, thus forming a work based on the Program, and copy and
distribute such modifications or work under the terms of Section 1
above, provided that you also meet all of these conditions:

    a) You must cause the modified files to carry prominent notices
    stating that you changed the files and the date of any change.

    b) You must cause any work that you distribute or publish, that in
    whole or in part contains or is derived from the Program or any
    part thereof, to be licensed as a whole at no charge to all third
    parties under the terms of this License.

    c) If the modified program normally reads commands interactively
    when run, you must cause it, when started running for such
    interactive use in the most ordinary way, to print or display an
    announcement including an appropriate copyright notice and a
    notice that there is no warranty (or else, saying that you provide
    a warranty) and that users may redistribute the program under
    these conditions, and telling the user how to view a copy of this
    License.  (Exception: if the Program itself is interactive but
    does not normally print such an announcement, your work based on
    the Program is not required to print an announcement.)

These requirements apply to the modified work as a whole.  If
identifiable sections of that work are not derived from the Program,
and can be reasonably considered independent and separate works in
themselves, then this License, and its terms, do not apply to those
sections when you distribute them as separate works.  But when you
distribute the same sections as part of a whole which is a work based
on the Program, the distribution of the whole must be on the terms of
this License, whose permissions for other licensees extend to the
entire whole, and thus to each and every part regardless of who wrote it.

Thus, it is not the intent of this section to claim rights or contest
your rights to work written entirely by you; rather, the intent is to
exercise the right to control the distribution of derivative or
collective works based on the Program.

In addition, mere aggregation of another work not based on the Program
with the Program (or with a work based on the Program) on a volume of
a storage or distribution medium does not bring the other work under
the scope of this License.

3. You may copy and distribute the Program (or a work based on it,
under Section 2) in object code or executable form under the terms of
Sections 1 and 2 above provided that you also do one of the following:

    a) Accompany it with the complete corresponding machine-readable
    source code, which must be distributed under the terms of Sections
    1 and 2 above on a medium customarily used for software interchange; or,

    b) Accompany it with a written offer, valid for at least three
    years, to give any third party, for a charge no more than your
    cost of physically performing source distribution, a complete
    machine-readable copy of the corresponding source code, to be
    distributed under the terms of Sections 1 and 2 above on a medium
    customarily used for software interchange; or,

    c) Accompany it with the information you received as to the offer
    to distribute corresponding source code.  (This alternative is
    allowed only for noncommercial distribution and only if you
    received the program in object code or executable form with such
    an offer, in accord with Subsection b above.)

The source code for a work means the preferred form of the work for
making modifications to it.  For an executable work, complete source
code means all the source code for all modules it contains, plus any
associated interface definition files, plus the scripts used to
control compilation and installation of the executable.  However, as a
special exception, the source code distributed need not include
anything that is normally distributed (in either source or binary
form) with the major components (compiler, kernel, and so on) of the
operating system on which the executable runs, unless that component
itself accompanies the executable.

If distribution of executable or object code is made by offering
access to copy from a designated place, then offering equivalent
access to copy the source code from the same place counts as
distribution of the source code, even though third parties are not
compelled to copy the source along with the object code.

4. You may not copy, modify, sublicense, or distribute the Program
except as expressly provided under this License.  Any attempt
otherwise to copy, modify, sublicense or distribute the Program is
void, and will automatically terminate your rights under this License.
However, parties who have received copies, or rights, from you under
this License will not have their licenses terminated so long as such
parties remain in full compliance.

5. You are not required to accept this License, since you have not
signed it.  However, nothing else grants you permission to modify or
distribute the Program or its derivative works.  These actions are
prohibited by law if you do not accept this License.  Therefore, by
modifying or distributing the Program (or any work based on the
Program), you indicate your acceptance of this License to do so, and
all its terms and conditions for copying, distributing or modifying
the Program or works based on it.

6. Each time you redistribute the Program (or any work based on the
Program), the recipient automatically receives a license from the
original licensor to copy, distribute or modify the Program subject to
these terms and conditions.  You may not impose any further
restrictions on the recipients\' exercise of the rights granted herein.
You are not responsible for enforcing compliance by third parties to
this License.

7. If, as a consequence of a court judgment or allegation of patent
infringement or for any other reason (not limited to patent issues),
conditions are imposed on you (whether by court order, agreement or
otherwise) that contradict the conditions of this License, they do not
excuse you from the conditions of this License.  If you cannot
distribute so as to satisfy simultaneously your obligations under this
License and any other pertinent obligations, then as a consequence you
may not distribute the Program at all.  For example, if a patent
license would not permit royalty-free redistribution of the Program by
all those who receive copies directly or indirectly through you, then
the only way you could satisfy both it and this License would be to
refrain entirely from distribution of the Program.

If any portion of this section is held invalid or unenforceable under
any particular circumstance, the balance of the section is intended to
apply and the section as a whole is intended to apply in other
circumstances.

It is not the purpose of this section to induce you to infringe any
patents or other property right claims or to contest validity of any
such claims; this section has the sole purpose of protecting the
integrity of the free software distribution system, which is
implemented by public license practices.  Many people have made
generous contributions to the wide range of software distributed
through that system in reliance on consistent application of that
system; it is up to the author/donor to decide if he or she is willing
to distribute software through any other system and a licensee cannot
impose that choice.

This section is intended to make thoroughly clear what is believed to
be a consequence of the rest of this License.

8. If the distribution and/or use of the Program is restricted in
certain countries either by patents or by copyrighted interfaces, the
original copyright holder who places the Program under this License
may add an explicit geographical distribution limitation excluding
those countries, so that distribution is permitted only in or among
countries not thus excluded.  In such case, this License incorporates
the limitation as if written in the body of this License.

9. The Free Software Foundation may publish revised and/or new versions
of the General Public License from time to time.  Such new versions will
be similar in spirit to the present version, but may differ in detail to
address new problems or concerns.

Each version is given a distinguishing version number.  If the Program
specifies a version number of this License which applies to it and "any
later version", you have the option of following the terms and conditions
either of that version or of any later version published by the Free
Software Foundation.  If the Program does not specify a version number of
this License, you may choose any version ever published by the Free Software
Foundation.

10. If you wish to incorporate parts of the Program into other free
programs whose distribution conditions are different, write to the author
to ask for permission.  For software which is copyrighted by the Free
Software Foundation, write to the Free Software Foundation; we sometimes
make exceptions for this.  Our decision will be guided by the two goals
of preserving the free status of all derivatives of our free software and
of promoting the sharing and reuse of software generally.

NO WARRANTY
-----------

11. BECAUSE THE PROGRAM IS LICENSED FREE OF CHARGE, THERE IS NO WARRANTY
FOR THE PROGRAM, TO THE EXTENT PERMITTED BY APPLICABLE LAW.  EXCEPT WHEN
OTHERWISE STATED IN WRITING THE COPYRIGHT HOLDERS AND/OR OTHER PARTIES
PROVIDE THE PROGRAM "AS IS" WITHOUT WARRANTY OF ANY KIND, EITHER EXPRESSED
OR IMPLIED, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF
MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE.  THE ENTIRE RISK AS
TO THE QUALITY AND PERFORMANCE OF THE PROGRAM IS WITH YOU.  SHOULD THE
PROGRAM PROVE DEFECTIVE, YOU ASSUME THE COST OF ALL NECESSARY SERVICING,
REPAIR OR CORRECTION.

12. IN NO EVENT UNLESS REQUIRED BY APPLICABLE LAW OR AGREED TO IN WRITING
WILL ANY COPYRIGHT HOLDER, OR ANY OTHER PARTY WHO MAY MODIFY AND/OR
REDISTRIBUTE THE PROGRAM AS PERMITTED ABOVE, BE LIABLE TO YOU FOR DAMAGES,
INCLUDING ANY GENERAL, SPECIAL, INCIDENTAL OR CONSEQUENTIAL DAMAGES ARISING
OUT OF THE USE OR INABILITY TO USE THE PROGRAM (INCLUDING BUT NOT LIMITED
TO LOSS OF DATA OR DATA BEING RENDERED INACCURATE OR LOSSES SUSTAINED BY
YOU OR THIRD PARTIES OR A FAILURE OF THE PROGRAM TO OPERATE WITH ANY OTHER
PROGRAMS), EVEN IF SUCH HOLDER OR OTHER PARTY HAS BEEN ADVISED OF THE
POSSIBILITY OF SUCH DAMAGES.

---------------------------
END OF TERMS AND CONDITIONS
',
    'readme' => '--------------------
miniShop2
--------------------
Author: Vasiliy Naumkin <bezumkin@yandex.ru>
Owner: MODX RSC – Russian Speaking Community  https://github.com/modx-pro
Support and development  Nikolay Savin https://t.me/biz87
With questions and problems - contact the https://modx.pro  AND  https://t.me/ru_modx
--------------------

Feel free to suggest ideas/improvements/bugs on GitHub:
https://github.com/modx-pro/miniShop2


',
    'chunks' => 
    array (
      0 => 'msProduct.content',
      1 => 'msProduct.content.fenom',
      2 => 'tpl.msProducts.row',
      3 => 'tpl.msCart',
      4 => 'tpl.msCartNew',
      5 => 'tpl.msMiniCart',
      6 => 'tpl.msOrder',
      7 => 'tpl.msGetOrder',
      8 => 'tpl.msOptions',
      9 => 'tpl.msProductOptions',
      10 => 'tpl.msGallery',
      11 => 'tpl.msGalleryNew',
      12 => 'tpl.msEmail',
      13 => 'tpl.msEmail.new.user',
      14 => 'tpl.msEmail.new.manager',
      15 => 'tpl.msEmail.paid.user',
      16 => 'tpl.msEmail.paid.manager',
      17 => 'tpl.msEmail.sent.user',
      18 => 'tpl.msEmail.cancelled.user',
    ),
    'setup-options' => 'minishop2-4.4.0-pl/setup-options.php',
    'requires' => 
    array (
      'php' => '>=7.0.0',
      'modx' => '<3.0.0',
    ),
  ),
  'manifest-vehicles' => 
  array (
    0 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modNamespace',
      'guid' => 'e3167834a96a94fb3b935568191bc8ac',
      'native_key' => 'minishop2',
      'filename' => 'modNamespace/5e718544575b30c6a55f96eb6c95ac7c.vehicle',
      'namespace' => 'minishop2',
    ),
    1 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modSystemSetting',
      'guid' => 'b4a326f517679a151a249ea9fadf4f4e',
      'native_key' => 'mgr_tree_icon_mscategory',
      'filename' => 'modSystemSetting/156d3089f4dd91acd3e20924a42c1675.vehicle',
      'namespace' => 'minishop2',
    ),
    2 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modSystemSetting',
      'guid' => '72081b0485702fe6e7715f598bc96141',
      'native_key' => 'mgr_tree_icon_msproduct',
      'filename' => 'modSystemSetting/1d478d14f242764567f531615331dafc.vehicle',
      'namespace' => 'minishop2',
    ),
    3 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modSystemSetting',
      'guid' => 'cd2fa940cb8e6e21ed6cff5af0b35c0d',
      'native_key' => 'ms2_services',
      'filename' => 'modSystemSetting/5436d0cc8ff7efd69f1db5659cc20236.vehicle',
      'namespace' => 'minishop2',
    ),
    4 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modSystemSetting',
      'guid' => '6ce769ac384c40c89bf60470ffa192b6',
      'native_key' => 'ms2_plugins',
      'filename' => 'modSystemSetting/b1265205e9bc2feb61c8b1d97e7e2cca.vehicle',
      'namespace' => 'minishop2',
    ),
    5 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modSystemSetting',
      'guid' => 'a9600fc12da3581e32b822b2ad686f5d',
      'native_key' => 'ms2_chunks_categories',
      'filename' => 'modSystemSetting/20c30a829a4945d8c80bce930bc0d1fe.vehicle',
      'namespace' => 'minishop2',
    ),
    6 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modSystemSetting',
      'guid' => 'ca3e0ab6a1205f5db79e81fe26032058',
      'native_key' => 'ms2_tmp_storage',
      'filename' => 'modSystemSetting/5b024a8f282cac53655a06bd5210f7cd.vehicle',
      'namespace' => 'minishop2',
    ),
    7 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modSystemSetting',
      'guid' => 'c72442449a51589c8ee0f6c120aa5b7f',
      'native_key' => 'ms2_use_scheduler',
      'filename' => 'modSystemSetting/c32c2ca4f91508375b34aa9df8bfee95.vehicle',
      'namespace' => 'minishop2',
    ),
    8 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modSystemSetting',
      'guid' => 'e6ad12770f09dc1755d7c04be5b7a4f4',
      'native_key' => 'ms2_category_grid_fields',
      'filename' => 'modSystemSetting/26e22c994592273925a62374f3a9db57.vehicle',
      'namespace' => 'minishop2',
    ),
    9 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modSystemSetting',
      'guid' => '05d8b8e3b9f280a4687c4538eb33ea54',
      'native_key' => 'ms2_category_show_nested_products',
      'filename' => 'modSystemSetting/d59f4fc44c29eaccb6aa9eb604bcbdca.vehicle',
      'namespace' => 'minishop2',
    ),
    10 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modSystemSetting',
      'guid' => 'ea7714f7af1628f5c2d3a64aedf6b839',
      'native_key' => 'ms2_category_show_comments',
      'filename' => 'modSystemSetting/49de27393a28eac3f194d27083904bcb.vehicle',
      'namespace' => 'minishop2',
    ),
    11 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modSystemSetting',
      'guid' => 'fd6fd651e689f3ac809226f0ee50a823',
      'native_key' => 'ms2_category_show_options',
      'filename' => 'modSystemSetting/a8e535f7df1a2086b2999747d1812141.vehicle',
      'namespace' => 'minishop2',
    ),
    12 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modSystemSetting',
      'guid' => 'b5cd3894843fae1281fb4937bd723b77',
      'native_key' => 'ms2_category_remember_tabs',
      'filename' => 'modSystemSetting/1235ac0dc5cdca060da1711f13d63d72.vehicle',
      'namespace' => 'minishop2',
    ),
    13 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modSystemSetting',
      'guid' => '458e9db3edf08ca582933d595e45cc78',
      'native_key' => 'ms2_category_id_as_alias',
      'filename' => 'modSystemSetting/f3bd66c4d6bb0de4759b19ce7c22ffbb.vehicle',
      'namespace' => 'minishop2',
    ),
    14 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modSystemSetting',
      'guid' => '6f0ab8862d87f724c7c817406315ab0a',
      'native_key' => 'ms2_category_content_default',
      'filename' => 'modSystemSetting/3ec0125e488a5b9ec2a98e3ad5c94afb.vehicle',
      'namespace' => 'minishop2',
    ),
    15 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modSystemSetting',
      'guid' => '7f6d34aff7d855f9e24dc90efcdd1958',
      'native_key' => 'ms2_template_category_default',
      'filename' => 'modSystemSetting/8c1ca795a64a9cb31fb187af89be5e26.vehicle',
      'namespace' => 'minishop2',
    ),
    16 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modSystemSetting',
      'guid' => '051b543d09da63340544f4210a0519a5',
      'native_key' => 'ms2_product_extra_fields',
      'filename' => 'modSystemSetting/9b5011e18615efa18dcf67bd39a63a59.vehicle',
      'namespace' => 'minishop2',
    ),
    17 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modSystemSetting',
      'guid' => '01a7862db0757e2baa913c02abd34a5c',
      'native_key' => 'ms2_product_show_comments',
      'filename' => 'modSystemSetting/ae5fb04b1c2c02dd146fe57f81bc2a92.vehicle',
      'namespace' => 'minishop2',
    ),
    18 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modSystemSetting',
      'guid' => '6592a927100e9fe45ed755d9693130e8',
      'native_key' => 'ms2_template_product_default',
      'filename' => 'modSystemSetting/7bc5090e79e539e453286407f2446162.vehicle',
      'namespace' => 'minishop2',
    ),
    19 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modSystemSetting',
      'guid' => 'e6e7df56ee3dfcd523399393b80c408d',
      'native_key' => 'ms2_product_show_in_tree_default',
      'filename' => 'modSystemSetting/869657bd37b6f2c20e90c14b08886729.vehicle',
      'namespace' => 'minishop2',
    ),
    20 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modSystemSetting',
      'guid' => 'a634ea6c46da73f44c2ee6bc1b4ac6d4',
      'native_key' => 'ms2_product_source_default',
      'filename' => 'modSystemSetting/9a2139b0c4ec7539d100a81e0af345ca.vehicle',
      'namespace' => 'minishop2',
    ),
    21 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modSystemSetting',
      'guid' => '266823cb20fd07a37fcecfc311fe311a',
      'native_key' => 'ms2_product_thumbnail_default',
      'filename' => 'modSystemSetting/371ad15e55abfa91b782209cb9a91322.vehicle',
      'namespace' => 'minishop2',
    ),
    22 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modSystemSetting',
      'guid' => 'cd320927e788a0d6c460c2eed4222dcd',
      'native_key' => 'ms2_product_thumbnail_size',
      'filename' => 'modSystemSetting/e1c50687232780c948ab1cb01ca75fdf.vehicle',
      'namespace' => 'minishop2',
    ),
    23 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modSystemSetting',
      'guid' => 'dcddeef1c63955715af104d3bfb17a9f',
      'native_key' => 'ms2_product_remember_tabs',
      'filename' => 'modSystemSetting/10dd945a8fe7968a6ac6a3e7e6d000bb.vehicle',
      'namespace' => 'minishop2',
    ),
    24 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modSystemSetting',
      'guid' => 'bf98ccd53db51d4f76d46e6c07985502',
      'native_key' => 'ms2_product_id_as_alias',
      'filename' => 'modSystemSetting/403a79312be446074f9ea3eea61f0e98.vehicle',
      'namespace' => 'minishop2',
    ),
    25 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modSystemSetting',
      'guid' => 'abb7b0e5f29a706c901df857ef1e8025',
      'native_key' => 'ms2_price_format',
      'filename' => 'modSystemSetting/3dc5854f392c0c7757fe43981005a33f.vehicle',
      'namespace' => 'minishop2',
    ),
    26 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modSystemSetting',
      'guid' => '29c7c6b0f3db40c2872179d6ab4ae13c',
      'native_key' => 'ms2_weight_format',
      'filename' => 'modSystemSetting/e87a5d970f04897c8989f9ece2ada74b.vehicle',
      'namespace' => 'minishop2',
    ),
    27 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modSystemSetting',
      'guid' => '7bee596b55dfbf8af6ed57b44f97b54c',
      'native_key' => 'ms2_price_format_no_zeros',
      'filename' => 'modSystemSetting/c1f8d920f66763161dd08cd3885cdbdd.vehicle',
      'namespace' => 'minishop2',
    ),
    28 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modSystemSetting',
      'guid' => 'd372785e3a46ae235d0e97be96c75b46',
      'native_key' => 'ms2_weight_format_no_zeros',
      'filename' => 'modSystemSetting/52598527498d030dae16881615276055.vehicle',
      'namespace' => 'minishop2',
    ),
    29 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modSystemSetting',
      'guid' => '874fba157a6a8187ee785d49aeb9d5b7',
      'native_key' => 'ms2_product_tab_extra',
      'filename' => 'modSystemSetting/93554b7a8f40ab44eb40204fe6068bda.vehicle',
      'namespace' => 'minishop2',
    ),
    30 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modSystemSetting',
      'guid' => '1d15f4e4cc42d2de7d7c3b55eca70b6c',
      'native_key' => 'ms2_product_tab_gallery',
      'filename' => 'modSystemSetting/a39537e4d93009e299b0b159262483b5.vehicle',
      'namespace' => 'minishop2',
    ),
    31 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modSystemSetting',
      'guid' => '4ac90a8ad74d7061a823949b213f4caa',
      'native_key' => 'ms2_product_tab_links',
      'filename' => 'modSystemSetting/625f95963379a7ad6eb7bfeecae99490.vehicle',
      'namespace' => 'minishop2',
    ),
    32 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modSystemSetting',
      'guid' => '0bab992f010c0997e176d645f28e60ef',
      'native_key' => 'ms2_product_tab_options',
      'filename' => 'modSystemSetting/2b21acac46b5347af00f2d06a90b2f59.vehicle',
      'namespace' => 'minishop2',
    ),
    33 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modSystemSetting',
      'guid' => 'c96827172f890f97030187ce605d3553',
      'native_key' => 'ms2_product_tab_categories',
      'filename' => 'modSystemSetting/f895f2c641f66b89d297890bb13bf9aa.vehicle',
      'namespace' => 'minishop2',
    ),
    34 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modSystemSetting',
      'guid' => '38ff61442bf5f9371c053bc4431cc47d',
      'native_key' => 'ms2_cart_handler_class',
      'filename' => 'modSystemSetting/0f30b407c6a30d21b8ebe11043f3ed14.vehicle',
      'namespace' => 'minishop2',
    ),
    35 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modSystemSetting',
      'guid' => '24cc296707421648219b909183827830',
      'native_key' => 'ms2_cart_context',
      'filename' => 'modSystemSetting/6282ba4b91465387eaf937da33610fd3.vehicle',
      'namespace' => 'minishop2',
    ),
    36 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modSystemSetting',
      'guid' => 'dde879a1245fdf0821fdcf9707c6028b',
      'native_key' => 'ms2_cart_max_count',
      'filename' => 'modSystemSetting/633b80faab195b2bc4086e1a3c34b122.vehicle',
      'namespace' => 'minishop2',
    ),
    37 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modSystemSetting',
      'guid' => 'c70947f94f5be450bf92245e52206e41',
      'native_key' => 'ms2_cart_product_key_fields',
      'filename' => 'modSystemSetting/7cbd345f83401d4905e14b4467db6105.vehicle',
      'namespace' => 'minishop2',
    ),
    38 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modSystemSetting',
      'guid' => '61b980e1536008b0db9253682c86fed1',
      'native_key' => 'ms2_order_format_phone',
      'filename' => 'modSystemSetting/752c3bdfc7ddf77234815f50506a514b.vehicle',
      'namespace' => 'minishop2',
    ),
    39 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modSystemSetting',
      'guid' => '02e71929af252735bf371e251e0321bf',
      'native_key' => 'ms2_order_format_num',
      'filename' => 'modSystemSetting/d269b18b9c4cceee7638e9784ef1757b.vehicle',
      'namespace' => 'minishop2',
    ),
    40 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modSystemSetting',
      'guid' => '0269499b68203e69e172b46a65db3752',
      'native_key' => 'ms2_order_format_num_separator',
      'filename' => 'modSystemSetting/b09275f6b538e547c0075ca4c886b742.vehicle',
      'namespace' => 'minishop2',
    ),
    41 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modSystemSetting',
      'guid' => '867cf711b3ae366696a1616fd48f261d',
      'native_key' => 'ms2_order_grid_fields',
      'filename' => 'modSystemSetting/8b5edacbb28672c314d48488b5a21835.vehicle',
      'namespace' => 'minishop2',
    ),
    42 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modSystemSetting',
      'guid' => 'ca9eaa6d48c23c000aac8df5d97dbcfb',
      'native_key' => 'ms2_order_address_fields',
      'filename' => 'modSystemSetting/c8eff9361ecf75716b0b286714aaa6f7.vehicle',
      'namespace' => 'minishop2',
    ),
    43 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modSystemSetting',
      'guid' => 'b9d82dff8e44083cbe9e9f783c3ef589',
      'native_key' => 'ms2_order_product_fields',
      'filename' => 'modSystemSetting/a2695862eeab167b38251fd91c1b74c6.vehicle',
      'namespace' => 'minishop2',
    ),
    44 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modSystemSetting',
      'guid' => '8a0460b64388a253569cdbee85ea0172',
      'native_key' => 'ms2_order_product_options',
      'filename' => 'modSystemSetting/471e51fcb2da8d06d30f70e6681d610d.vehicle',
      'namespace' => 'minishop2',
    ),
    45 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modSystemSetting',
      'guid' => 'e337f17970c5d7d0e3e97101c66e3ab8',
      'native_key' => 'ms2_order_tv_list',
      'filename' => 'modSystemSetting/ae360cca9aae05c777655c00baafa958.vehicle',
      'namespace' => 'minishop2',
    ),
    46 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modSystemSetting',
      'guid' => 'a9395aa68e1f6bca27db28b01d2b65f8',
      'native_key' => 'ms2_order_handler_class',
      'filename' => 'modSystemSetting/3b2e4ed2fbab5a47f8814e0f695d9a0e.vehicle',
      'namespace' => 'minishop2',
    ),
    47 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modSystemSetting',
      'guid' => 'e290eb571f8691562473c5e6de781a3c',
      'native_key' => 'ms2_order_user_groups',
      'filename' => 'modSystemSetting/9b0982c5596b5fc91eb6c13ac683968e.vehicle',
      'namespace' => 'minishop2',
    ),
    48 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modSystemSetting',
      'guid' => '88addc9ac0ec7ac819a82417fa24db96',
      'native_key' => 'ms2_date_format',
      'filename' => 'modSystemSetting/978bf2c153c47c67dc857e9966eed3cb.vehicle',
      'namespace' => 'minishop2',
    ),
    49 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modSystemSetting',
      'guid' => '412957dbfbae773697a2248d4c24ef28',
      'native_key' => 'ms2_email_manager',
      'filename' => 'modSystemSetting/b4f8bf9c487dd2a66a3e260de650874f.vehicle',
      'namespace' => 'minishop2',
    ),
    50 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modSystemSetting',
      'guid' => '9e23c7725cbb0ccbd536b83fc3f58e89',
      'native_key' => 'ms2_frontend_css',
      'filename' => 'modSystemSetting/4e08e80b0cfc9b0fb71a7b63d392a4bd.vehicle',
      'namespace' => 'minishop2',
    ),
    51 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modSystemSetting',
      'guid' => '1ed9e46cd6bf05990ba9fa8e3a3caaeb',
      'native_key' => 'ms2_frontend_message_css',
      'filename' => 'modSystemSetting/21b75ac1f6344d703d8c7b8547729d30.vehicle',
      'namespace' => 'minishop2',
    ),
    52 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modSystemSetting',
      'guid' => 'f1a7f9c08fd5dfb4e032e71cd1c1498f',
      'native_key' => 'ms2_frontend_js',
      'filename' => 'modSystemSetting/7f24ebf83ea0bb5727705787cd912366.vehicle',
      'namespace' => 'minishop2',
    ),
    53 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modSystemSetting',
      'guid' => '92b9369f073dfa1110de7b8e7f35503a',
      'native_key' => 'ms2_frontend_message_js',
      'filename' => 'modSystemSetting/d56c6408a58f235b210ea3488a306ec3.vehicle',
      'namespace' => 'minishop2',
    ),
    54 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modSystemSetting',
      'guid' => '9f87fbcc00e7c5037927f776a6dad846',
      'native_key' => 'ms2_frontend_message_js_settings',
      'filename' => 'modSystemSetting/2991eca365420e352760e7a4f6f857f3.vehicle',
      'namespace' => 'minishop2',
    ),
    55 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modSystemSetting',
      'guid' => 'c519433397c85f9c9006ed17bdb2814c',
      'native_key' => 'ms2_register_frontend',
      'filename' => 'modSystemSetting/5cf8a803275ac1cea25f8a0afe8665d2.vehicle',
      'namespace' => 'minishop2',
    ),
    56 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modSystemSetting',
      'guid' => '62bfb47c56c6076875e8709d6f677a56',
      'native_key' => 'ms2_toggle_js_type',
      'filename' => 'modSystemSetting/38bd55b5c672679b30d60e5b133e8606.vehicle',
      'namespace' => 'minishop2',
    ),
    57 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modSystemSetting',
      'guid' => '32002e14fb96e07446019782995c2d83',
      'native_key' => 'ms2_vanila_js',
      'filename' => 'modSystemSetting/ea303ad4df62e64cd7e04515004e211c.vehicle',
      'namespace' => 'minishop2',
    ),
    58 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modSystemSetting',
      'guid' => 'b6a27a282acfd1988a358cc3c87dcbb8',
      'native_key' => 'ms2_frontend_notify_js_settings',
      'filename' => 'modSystemSetting/978ffa13cbd73f0e2ce8e1f9472c9d35.vehicle',
      'namespace' => 'minishop2',
    ),
    59 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modSystemSetting',
      'guid' => '19a5b230f36fa40788c7bc0008ef70fd',
      'native_key' => 'ms2_cart_js_class_path',
      'filename' => 'modSystemSetting/d9cc5ff42b7bad744f5c9e72fa992f01.vehicle',
      'namespace' => 'minishop2',
    ),
    60 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modSystemSetting',
      'guid' => 'acb83ff81c91b33618d6382805207de3',
      'native_key' => 'ms2_cart_js_class_name',
      'filename' => 'modSystemSetting/86e9cae1b848eae5c1e639bfdfc8eb9a.vehicle',
      'namespace' => 'minishop2',
    ),
    61 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modSystemSetting',
      'guid' => 'a068ee5a2e9b0453b8fa9d6336123a9a',
      'native_key' => 'ms2_order_js_class_path',
      'filename' => 'modSystemSetting/06a5db6fd987d07212604642abfae641.vehicle',
      'namespace' => 'minishop2',
    ),
    62 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modSystemSetting',
      'guid' => '56dcd5d3660aeb7d96a3d7876e312a20',
      'native_key' => 'ms2_order_js_class_name',
      'filename' => 'modSystemSetting/e6cb4d7aa1da68654cb68418128cb6b4.vehicle',
      'namespace' => 'minishop2',
    ),
    63 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modSystemSetting',
      'guid' => 'f755dd86e5a69a6b4e4d93b5e590903e',
      'native_key' => 'ms2_notify_js_class_path',
      'filename' => 'modSystemSetting/07bce3c468d0cba239ca4497c610b59e.vehicle',
      'namespace' => 'minishop2',
    ),
    64 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modSystemSetting',
      'guid' => '76b93db7b8d9f6f9574f988abb222c8b',
      'native_key' => 'ms2_notify_js_class_name',
      'filename' => 'modSystemSetting/fd9ca640a98eafd2c27cbbc52ea08220.vehicle',
      'namespace' => 'minishop2',
    ),
    65 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modSystemSetting',
      'guid' => 'de226c6209602e39a7f218aa941929fc',
      'native_key' => 'ms2_status_draft',
      'filename' => 'modSystemSetting/19fa0acb64886f233099368b5ad37609.vehicle',
      'namespace' => 'minishop2',
    ),
    66 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modSystemSetting',
      'guid' => '3af32a4f0c387d478bd581f7b892632f',
      'native_key' => 'ms2_status_new',
      'filename' => 'modSystemSetting/3e0e2028ecea25c07d4406e2b2295548.vehicle',
      'namespace' => 'minishop2',
    ),
    67 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modSystemSetting',
      'guid' => 'aaf260daa627f4785b08015f658783f4',
      'native_key' => 'ms2_status_paid',
      'filename' => 'modSystemSetting/4ce6cfdc57c465f1c2393b294620ab26.vehicle',
      'namespace' => 'minishop2',
    ),
    68 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modSystemSetting',
      'guid' => 'f0ea7849207cf076458f8109faea2c1b',
      'native_key' => 'ms2_status_canceled',
      'filename' => 'modSystemSetting/8e18c495d42c72142a3c5181db9c787a.vehicle',
      'namespace' => 'minishop2',
    ),
    69 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modSystemSetting',
      'guid' => '1410d7a38515e7982a5d8cb7c57d9c2f',
      'native_key' => 'ms2_status_for_stat',
      'filename' => 'modSystemSetting/8d2ee45c9e3ba5f5ffa16abc35aba868.vehicle',
      'namespace' => 'minishop2',
    ),
    70 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modSystemSetting',
      'guid' => 'c7741891f5af357ef0ed0125c381ef02',
      'native_key' => 'ms2_utility_import_fields',
      'filename' => 'modSystemSetting/abcaafe751ab0611a04910ff016ab4e1.vehicle',
      'namespace' => 'minishop2',
    ),
    71 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modSystemSetting',
      'guid' => '10e235ac88d3edd73a5d32bd8ee4b930',
      'native_key' => 'ms2_utility_import_fields_delimiter',
      'filename' => 'modSystemSetting/333d20591e30e92d5506ef285111dd3e.vehicle',
      'namespace' => 'minishop2',
    ),
    72 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modEvent',
      'guid' => '16045c6617e14e4d9fc9304c14344c71',
      'native_key' => 'msOnBeforeAddToCart',
      'filename' => 'modEvent/57f2989d5da064a36204eb8af5fb312a.vehicle',
      'namespace' => 'minishop2',
    ),
    73 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modEvent',
      'guid' => '4f4dae01b78a0b9a2abcdb08b4d0c484',
      'native_key' => 'msOnAddToCart',
      'filename' => 'modEvent/b950a077db0cc82263c1416ea790ab06.vehicle',
      'namespace' => 'minishop2',
    ),
    74 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modEvent',
      'guid' => 'b2f4a5aba5a59152f61251e4ec3e6e74',
      'native_key' => 'msOnBeforeChangeInCart',
      'filename' => 'modEvent/24844bdec8dd789262054ccb701f50a2.vehicle',
      'namespace' => 'minishop2',
    ),
    75 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modEvent',
      'guid' => '04d63319aac69b264be808b19cd5e3b3',
      'native_key' => 'msOnChangeInCart',
      'filename' => 'modEvent/2e10d61d96a6bf200798f6f47ac13625.vehicle',
      'namespace' => 'minishop2',
    ),
    76 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modEvent',
      'guid' => '7a16085111880c40db43f1f0754856da',
      'native_key' => 'msOnBeforeRemoveFromCart',
      'filename' => 'modEvent/3b642d28b59a07f8e58ce8a284cf5a0f.vehicle',
      'namespace' => 'minishop2',
    ),
    77 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modEvent',
      'guid' => 'd8ad151467242c1f26103af400fbdf82',
      'native_key' => 'msOnRemoveFromCart',
      'filename' => 'modEvent/d9f40699eb9d743a4cd0aec60a2121e4.vehicle',
      'namespace' => 'minishop2',
    ),
    78 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modEvent',
      'guid' => '7d25c219b791e1656c0189ad3fb9f0c5',
      'native_key' => 'msOnBeforeEmptyCart',
      'filename' => 'modEvent/aa0881ef5e8317a03efd2c91e79f24e3.vehicle',
      'namespace' => 'minishop2',
    ),
    79 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modEvent',
      'guid' => 'daa35915d2adedfe2e9d33bbb0885de9',
      'native_key' => 'msOnEmptyCart',
      'filename' => 'modEvent/71728021d9acd1be1782c14fea7f6c42.vehicle',
      'namespace' => 'minishop2',
    ),
    80 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modEvent',
      'guid' => '9a78a9ff76f492058cc661b958d5c270',
      'native_key' => 'msOnGetStatusCart',
      'filename' => 'modEvent/b50698c984c86a68242e6a1cf09c055d.vehicle',
      'namespace' => 'minishop2',
    ),
    81 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modEvent',
      'guid' => 'ea16fe8e2964541028806b555cd77f6b',
      'native_key' => 'msOnBeforeAddToOrder',
      'filename' => 'modEvent/b4ef76f5bb2faf7548a94e70d6f97a14.vehicle',
      'namespace' => 'minishop2',
    ),
    82 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modEvent',
      'guid' => 'f9a3feade2074273d9a7dec9de21c83f',
      'native_key' => 'msOnAddToOrder',
      'filename' => 'modEvent/895f5f7e0959e252d4f613fcca050457.vehicle',
      'namespace' => 'minishop2',
    ),
    83 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modEvent',
      'guid' => 'd2f5dd97ba79022073d814e740138485',
      'native_key' => 'msOnBeforeValidateOrderValue',
      'filename' => 'modEvent/3de7efb512deca3fc76d7cd40cde05af.vehicle',
      'namespace' => 'minishop2',
    ),
    84 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modEvent',
      'guid' => '80a3b31cccb23156aec01f0fe2f0330e',
      'native_key' => 'msOnValidateOrderValue',
      'filename' => 'modEvent/6caa0a4a61d0f7c5ee2e561fecebe06d.vehicle',
      'namespace' => 'minishop2',
    ),
    85 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modEvent',
      'guid' => '7f43724970083f3054332919aa5613ee',
      'native_key' => 'msOnBeforeRemoveFromOrder',
      'filename' => 'modEvent/20fdce4a6272dc20459c2b81d7c89448.vehicle',
      'namespace' => 'minishop2',
    ),
    86 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modEvent',
      'guid' => 'f7cfd7392aec7f818ad5c807a1e33f14',
      'native_key' => 'msOnRemoveFromOrder',
      'filename' => 'modEvent/6f939637237995fe99892ab5d3c7ac4b.vehicle',
      'namespace' => 'minishop2',
    ),
    87 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modEvent',
      'guid' => '41c37042ec3b97a2524a481a791d7ffd',
      'native_key' => 'msOnBeforeEmptyOrder',
      'filename' => 'modEvent/77288ee10c396274fb382b73009c41f5.vehicle',
      'namespace' => 'minishop2',
    ),
    88 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modEvent',
      'guid' => '0ecc4ddbade7543d5cc8de34786ee4a8',
      'native_key' => 'msOnEmptyOrder',
      'filename' => 'modEvent/a8bd1eeaa37c16275dc9ce8c6396f274.vehicle',
      'namespace' => 'minishop2',
    ),
    89 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modEvent',
      'guid' => '3685d4159d657958e335b39b86d0953a',
      'native_key' => 'msOnBeforeGetOrderCost',
      'filename' => 'modEvent/1fe3c7b72ca3f26ebea0119909b538b0.vehicle',
      'namespace' => 'minishop2',
    ),
    90 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modEvent',
      'guid' => '58ea239044edc1fe4ef5a087053cda38',
      'native_key' => 'msOnGetOrderCost',
      'filename' => 'modEvent/b02fe8be1f42c67755618d7288749ee1.vehicle',
      'namespace' => 'minishop2',
    ),
    91 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modEvent',
      'guid' => '783de1fe8be59958b1d5f852c5331ebc',
      'native_key' => 'msOnSubmitOrder',
      'filename' => 'modEvent/05c7a80a4dc81364c54f40f9e159be70.vehicle',
      'namespace' => 'minishop2',
    ),
    92 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modEvent',
      'guid' => '18894263c03ab1d3280f082a643a7dd1',
      'native_key' => 'msOnBeforeChangeOrderStatus',
      'filename' => 'modEvent/d798a5740e805352db154ae32d7fe927.vehicle',
      'namespace' => 'minishop2',
    ),
    93 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modEvent',
      'guid' => 'dae05de4486ba1b0707f6c5528d749b6',
      'native_key' => 'msOnChangeOrderStatus',
      'filename' => 'modEvent/44cba647e7d8689ad14ba2397f40f0fa.vehicle',
      'namespace' => 'minishop2',
    ),
    94 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modEvent',
      'guid' => '409e0b1fe4bc1e2474eefefd5dd5272b',
      'native_key' => 'msOnBeforeGetOrderCustomer',
      'filename' => 'modEvent/d80bef781692b94a460429c33c735f45.vehicle',
      'namespace' => 'minishop2',
    ),
    95 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modEvent',
      'guid' => '76f0e8620f8bd683a17a0a1e9cc74b4a',
      'native_key' => 'msOnGetOrderCustomer',
      'filename' => 'modEvent/b12ba7f5a0af918d8aec321d6729fda7.vehicle',
      'namespace' => 'minishop2',
    ),
    96 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modEvent',
      'guid' => '4a93c5b349149b086d0455a5daa6a0b6',
      'native_key' => 'msOnBeforeCreateOrder',
      'filename' => 'modEvent/3bac6fcc5f91ab86c900e9e43df49d3a.vehicle',
      'namespace' => 'minishop2',
    ),
    97 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modEvent',
      'guid' => 'b9cab061a112d6cba858fef86731723f',
      'native_key' => 'msOnBeforeMgrCreateOrder',
      'filename' => 'modEvent/61a3fbcbdd65dc8616c02c7ee8171424.vehicle',
      'namespace' => 'minishop2',
    ),
    98 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modEvent',
      'guid' => '73c30a36369be0b61ba0bef645080c01',
      'native_key' => 'msOnCreateOrder',
      'filename' => 'modEvent/afbdea986d3bcfd347307441c269b8ea.vehicle',
      'namespace' => 'minishop2',
    ),
    99 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modEvent',
      'guid' => '55bd7d032eb10a3c6317b4e3abb36a39',
      'native_key' => 'msOnMgrCreateOrder',
      'filename' => 'modEvent/a3ff5eb6bd0a5911fec2e8bcbbfa367d.vehicle',
      'namespace' => 'minishop2',
    ),
    100 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modEvent',
      'guid' => 'f9723cc725f18432813b5f9592d800f5',
      'native_key' => 'msOnBeforeUpdateOrder',
      'filename' => 'modEvent/77938792f850400c64098da848f917a1.vehicle',
      'namespace' => 'minishop2',
    ),
    101 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modEvent',
      'guid' => 'f35c6d6cbd3381a7b8f93222b52e1e3f',
      'native_key' => 'msOnUpdateOrder',
      'filename' => 'modEvent/74ef1e9ac02ea15f0f721d16880e72f1.vehicle',
      'namespace' => 'minishop2',
    ),
    102 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modEvent',
      'guid' => 'd79de9f23b5e05fd8dca572858bccf09',
      'native_key' => 'msOnBeforeSaveOrder',
      'filename' => 'modEvent/edde83307a8f318f10286beae3c98584.vehicle',
      'namespace' => 'minishop2',
    ),
    103 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modEvent',
      'guid' => '36946f71e9d40beef1559a535bad6cec',
      'native_key' => 'msOnSaveOrder',
      'filename' => 'modEvent/83c237de255b604e53d2a09aca322c78.vehicle',
      'namespace' => 'minishop2',
    ),
    104 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modEvent',
      'guid' => '8437f8b4b771316a99471898c34a7e0a',
      'native_key' => 'msOnBeforeRemoveOrder',
      'filename' => 'modEvent/b947d7631752df19544e2917fa806494.vehicle',
      'namespace' => 'minishop2',
    ),
    105 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modEvent',
      'guid' => 'bdc4bdb927916c4c33616bbbf5cd3c41',
      'native_key' => 'msOnRemoveOrder',
      'filename' => 'modEvent/66cc2ecb3f75fbdf504c9fbac3dd0540.vehicle',
      'namespace' => 'minishop2',
    ),
    106 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modEvent',
      'guid' => '7a97b2c7da7c8488f288b24b6263f594',
      'native_key' => 'msOnBeforeCreateOrderProduct',
      'filename' => 'modEvent/e6a6cf5e21d29b870873375931de1d7a.vehicle',
      'namespace' => 'minishop2',
    ),
    107 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modEvent',
      'guid' => '9318fa116c5f2b310ac52676011640e8',
      'native_key' => 'msOnCreateOrderProduct',
      'filename' => 'modEvent/9493b6aa8cdf848aeac0b391b202536b.vehicle',
      'namespace' => 'minishop2',
    ),
    108 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modEvent',
      'guid' => 'e33c53f77b5c93cbb8b1bc9a9f782933',
      'native_key' => 'msOnBeforeUpdateOrderProduct',
      'filename' => 'modEvent/c7e1e8090a7dc0484e55679315ddf7d6.vehicle',
      'namespace' => 'minishop2',
    ),
    109 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modEvent',
      'guid' => '0435ceb53a394c2e2ee5771a43c832e5',
      'native_key' => 'msOnUpdateOrderProduct',
      'filename' => 'modEvent/48b8051a13ab292c08e70c9d26549cb9.vehicle',
      'namespace' => 'minishop2',
    ),
    110 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modEvent',
      'guid' => '31db091695d66ef95b20e0d697d3fa6b',
      'native_key' => 'msOnBeforeRemoveOrderProduct',
      'filename' => 'modEvent/23cc1e291620cfb513c4c2d86e1cbe58.vehicle',
      'namespace' => 'minishop2',
    ),
    111 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modEvent',
      'guid' => '0ae6d53dd0803d113774fe917cf73598',
      'native_key' => 'msOnRemoveOrderProduct',
      'filename' => 'modEvent/8ff64cddc24851fbc94cab815a5adbb0.vehicle',
      'namespace' => 'minishop2',
    ),
    112 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modEvent',
      'guid' => 'b5b0bf0df80e2d9798a9874176c2fc3a',
      'native_key' => 'msOnGetProductPrice',
      'filename' => 'modEvent/e485eb1d8bfc16501037493ba5f8fe99.vehicle',
      'namespace' => 'minishop2',
    ),
    113 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modEvent',
      'guid' => '1fcb7da15774501bd546883381eca3ba',
      'native_key' => 'msOnGetProductWeight',
      'filename' => 'modEvent/fd579ef56e7cfc31e32bd2da37c7a72b.vehicle',
      'namespace' => 'minishop2',
    ),
    114 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modEvent',
      'guid' => '909d48b6e094b9e316ea12479aaf65b6',
      'native_key' => 'msOnGetProductFields',
      'filename' => 'modEvent/edbb35e734e58d0cd51494c269d9c95e.vehicle',
      'namespace' => 'minishop2',
    ),
    115 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modEvent',
      'guid' => '76efb25d9ae8b406538d28cf2d610663',
      'native_key' => 'msOnManagerCustomCssJs',
      'filename' => 'modEvent/cd1ef9deb8b5aa8f01d0af10d4b1e3a5.vehicle',
      'namespace' => 'minishop2',
    ),
    116 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modEvent',
      'guid' => '0ccb3891a38b034e8fefe7fbc1e433a2',
      'native_key' => 'msOnBeforeVendorCreate',
      'filename' => 'modEvent/559674bfe5a6edaa70ee98d630eef8b2.vehicle',
      'namespace' => 'minishop2',
    ),
    117 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modEvent',
      'guid' => '63aca8c5a7df3172e26fc13c51de9f12',
      'native_key' => 'msOnAfterVendorCreate',
      'filename' => 'modEvent/1d74132f8bd05f88945e9cb71e747f68.vehicle',
      'namespace' => 'minishop2',
    ),
    118 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modEvent',
      'guid' => '79a9f48283c1ae223a34a29ffed3b4dc',
      'native_key' => 'msOnBeforeVendorUpdate',
      'filename' => 'modEvent/d72893ee312c42abbb7a2eba93d4ed2e.vehicle',
      'namespace' => 'minishop2',
    ),
    119 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modEvent',
      'guid' => '3456c6e31516fdd5235557fe0ba2f33c',
      'native_key' => 'msOnAfterVendorUpdate',
      'filename' => 'modEvent/73fdafdacecd5efa72c8fb4988881ce7.vehicle',
      'namespace' => 'minishop2',
    ),
    120 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modEvent',
      'guid' => '61be8865e6859094ce03c62ed19d22fd',
      'native_key' => 'msOnBeforeVendorDelete',
      'filename' => 'modEvent/ad40030c1034a86dce241af6de058183.vehicle',
      'namespace' => 'minishop2',
    ),
    121 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modEvent',
      'guid' => '87552143bd281e6d72860f8c26c93e7b',
      'native_key' => 'msOnAfterVendorDelete',
      'filename' => 'modEvent/c8273424a94e64b98ab0a3fc8d5bb8b2.vehicle',
      'namespace' => 'minishop2',
    ),
    122 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modAccessPolicy',
      'guid' => 'bf01ad9d2b0edf41f7e4d94dd90c3e81',
      'native_key' => NULL,
      'filename' => 'modAccessPolicy/19a4b7fb6329252925cc88bc7a38af4c.vehicle',
      'namespace' => 'minishop2',
    ),
    123 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modAccessPolicyTemplate',
      'guid' => 'ff894ab86b37a0488a34cd7222bfb30d',
      'native_key' => NULL,
      'filename' => 'modAccessPolicyTemplate/a37bd43e3e2e90356f653c6a9fb4c3b7.vehicle',
      'namespace' => 'minishop2',
    ),
    124 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modMenu',
      'guid' => '6441876e34a76f30f6ae58000427ba03',
      'native_key' => 'minishop2',
      'filename' => 'modMenu/5185cb0be8799efb3365393bc9551945.vehicle',
      'namespace' => 'minishop2',
    ),
    125 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modMenu',
      'guid' => '0a06f8ce1caf4113384063b4217dd151',
      'native_key' => 'ms2_orders',
      'filename' => 'modMenu/813ab45166d06642b5472edb02e98cd6.vehicle',
      'namespace' => 'minishop2',
    ),
    126 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modMenu',
      'guid' => '37d71e2f3c84a87ce3f7d9f3b217241c',
      'native_key' => 'ms2_settings',
      'filename' => 'modMenu/44cd89c98ad01d1ed93d238fe72b4388.vehicle',
      'namespace' => 'minishop2',
    ),
    127 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modMenu',
      'guid' => '1db764627ca65a6102669a462810c9ea',
      'native_key' => 'ms2_system_settings',
      'filename' => 'modMenu/0b6c6a3a794481aa864ab802a52b3e85.vehicle',
      'namespace' => 'minishop2',
    ),
    128 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modMenu',
      'guid' => '9eec1ae095e2c023113f42b0f45ec60d',
      'native_key' => 'ms2_help',
      'filename' => 'modMenu/7fd4e98fe2fb8cf097c29de6668404fc.vehicle',
      'namespace' => 'minishop2',
    ),
    129 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modMenu',
      'guid' => '3acc3daf6f9951d9e377dda125b429f7',
      'native_key' => 'ms2_utilities',
      'filename' => 'modMenu/281f62ea817ab4073d0de023aee33b4d.vehicle',
      'namespace' => 'minishop2',
    ),
    130 => 
    array (
      'vehicle_package' => 'transport',
      'vehicle_class' => 'xPDOObjectVehicle',
      'class' => 'modCategory',
      'guid' => 'c1c7c32bde61d7910a392b55ab698ffa',
      'native_key' => 1,
      'filename' => 'modCategory/067bdbf9606426bf6c6576dcdfa40486.vehicle',
      'namespace' => 'minishop2',
    ),
  ),
);