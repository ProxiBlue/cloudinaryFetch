ProxiBlue CloudinaryFetch Extension
=====================

Integrate [Cloudinary](https://cloudinary.com) into Magento 1.

Cloudinary supplies their own Magento module, however, I had issues with this module, and decided to roll my own (simpler) module

So, whats wrong (in my opinion) with their 'official' module:

1. It is way to complex. The module extends Catalog product / Category and Images subsystems way to deep.
2. Extends ADMIN image management.
3. Works in ADMIN, so all admin images/thumbnails etc also goes to CDN, which is not needed in most usage cases (never want admin stuff in CDN, it is meant for frontend)
4. No composer based install available. ref: https://github.com/cloudinary/cloudinary_magento/issues/27
5. Major bug when using a symlinked media folder (due to cluster of frontends) ref: https://github.com/cloudinary/cloudinary_magento/issues/27 
   I further discovered a similar report frm 2016, so this bug has been present for some time!
6. They extended the Product/Catalog/Categories image subsystem, to allow pushing these images to CDN/Loading from CDN, however, and additional custom image attributes/functionality would not 'just work'
   You had to now specifically code your custom image 'thing' to use the CDN.
   So, basically, any custom/3rd party image functionality will be ignored, and will require additional work to extend teh cloudinary module to also incorporate them

My needs for cloudinary was simple: 

I needed a CDN which allows allowed image manipulation, specifically to decrease image size.

1. All images needs to be pushed to CDN, without constant/additional work to include them when doing custom work
2. Exclude ADMIN - no need to CDN those images
3. Easy to enable/disable

Setup
-----

1. Install via composer
 
       "require": {
                "ProxiBlue/CloudinaryFetch":"*"
           },
           "repositories": [
              {
                    "type": "vcs",
                        "url": "https://github.com/ProxiBlue/cloudinaryFetch.git"
                }
            ],

2. Clear Cache
3. Configure settings (System->Configuration->Web)

   ![Config](./docs/Selection_307.png?raw=true "Configuration")

    The 'Fetch URL' will be the 'Secure delivery URL' supplied by cloudinary. Login to your cloudinary console, and on the main dashboard, Account Details
    click the small 'more' link, located at the bottom right of the panel. This will reveal your 'Secure delivery URL'
    PostFix it with '/image/upload'
    
    ![fetch url](./docs/Selection_308.png?raw=true "Fetch URL")
    
4. Configure Cloudinary to setup a callback URL, where cloudinary will fetch the image if not on CDN 
   
   Open cloudinary console, and enter the settings section, then swap to the 'upload' tab
   You will see a section that designates 'Auto upload mapping'
   
   Add two mappings: One for /media, and one for /skin
   
   ![callback url](./docs/Selection_309.png?raw=true "Callback URL")
   

Basically, you are done, and ready to use the CDN. 

How it works
------------

Not rocket science. The URL for images will be changed to cloudinary. 
On first load, cloudinary will find the image is not yet on CDN.
Cloudanary will then make a 'callback' to your site (using the previously setup 'Auto upload mapping' and fetch the image from your site, and serve it.

Simple. Easy. 

Gotchas
-------

We use CSS/JS merging (and minification)
The prodblem with merging the assets is that magento places the files under /media/css|jscss_secure folders. 
This then attempted to load those from CDN, which of course fails.

Cloudinary only supports js/css files if they are manually uploaded to the CDN via their console (WTF?)
I accommodated this issue by adjusting merged assets to use the normal BASE URL, not the usual MEDIA url


SKIN!
----

A lot of your theme images would be part of css, and most likely using relative urls.
If using relative URL's, you will need to move away from those, so you can use a CDN URL

Depending on how you manage your .css resources, you would need to adjust the guide below to your needs:
We use SASS, and compass to compile (our runner is ant (yikes, old school!)), but that is really irrelevant)

The key is in how you setup compass (via config.rb) to build the css from sass, setting either relative (for dev) or CDN URLs (which you can split between UAT/LIVE)

My config.rb file:

```
theme = "default"
cdn_base_url = "https://res.cloudinary.com"
cdn_options = "q_auto"
uat_cdn_area = "UAT"
live_cdn_area = "LIVE"
image_path = "skin/frontend/enjo"

http_path = "/"
css_dir = "../css"
sass_dir = "../scss"
images_dir = "../images"
javascripts_dir = "../js"
fonts_dir = "fonts"

add_import_path "../../../rwd/default/scss"


output_style = (environment == :production) ? :compressed : :expanded
sourcemap = (environment == :production) ? false : true
relative_assets = (environment == :production) ? false : true

branch = ENV['bamboo_planRepository_branch']

http_images_path = (branch == "uat") ? "#{cdn_base_url}/#{uat_cdn_area}/image/upload/#{cdn_options}/#{image_path}/#{theme}/images/" : "#{cdn_base_url}/#{live_cdn_area}/image/upload/#{cdn_options}/#{image_path}/#{theme}/images/"

```

The basics:

a check to define if we must use relative assets:  ```relative_assets = (environment == :production) ? false : true``` In development we always build using the development flag, so our development works with relative assests, no CDN
Check for branch that is being built. ```branch = ENV['bamboo_planRepository_branch']``` as you have noticed, we use [bamboo](https://www.atlassian.com/software/bamboo) for our build process. When building bamboo sets ENV variables, of which one is the branch. If the branch is UAT, I set the UAT CDN url, else use live CDN URL 
The default is live CDN URL, which is a safe fallback, in case anything goes wrong.

Image Manipulation
------------------

One core reason Cloudinary was selected: The image processing.
The module allows you to set params in teh fetch URL.

One such param is q_auto (quality_auto) - setting this in the calling url can reduce images over 50% in size!
All depends on each image, and how well they can transform.

Specific Image Settings
-----------------------

I have considered a way, but since it is not (yet) needed, not (yet) implemented

Compatibility
-------------
- Magento 1 (tested in 1.9.3.8)

Support
-------
If you have any issues with this extension, open an issue on [GitHub](https://github.com/proxiblue/ProxiBlue_CloudinaryFetch/issues).

Contribution
------------
Any contribution is highly appreciated. The best way to contribute code is to open a [pull request on GitHub](https://help.github.com/articles/using-pull-requests).

Licence
-------
[OSL - Open Software Licence 3.0](http://opensource.org/licenses/osl-3.0.php)

Copyright
---------
(c) 2018 ProxiBlue
