# wp-mangareader-plugin

# Upcoming Changes
- rewrite of mangastarter theme with implementaiton of wp-manga-reader in progress(no ETA). Updates will move back to MangaStarter(Reworked). The Original/Magnific version of the wp-mangareader will be the final updates for the php script. Modify the code however you wish as I do not plan to continue working on the reader for awhile. The reader itself works perfectly fine[Use the magnific release for best results]
- Preview: [SkyManga](http://skymanga.42web.io/)
- Download(Still in beta and no guide yet): [MangaStarter-Revival Wordpress Theme](https://github.com/sykhangdha/mangareader-wp/releases/tag/Revival1)


# Original Version: [MR2 - Download + Changelog](http://github.com/sykhangdha/wp-mangareader-plugin/releases/tag/Maintenance/ "MR4 - Download + Changelog") // Original edition before magnific release

# Magnific Release: [MR4 - Download + Changelog](http://github.com/sykhangdha/wp-mangareader-plugin/releases/tag/MR4/ "MR4 - Download + Changelog") // Uses lightbox gallery(better preloading)
- View the new reader demo [HERE](https://skyha.rf.gd/choujin-x-6/ "New Reader DEMO")
	- [Changelog](https://skyha.rf.gd/project-releases/#tab-5791 "Changelog")
	- [Installation Guide](https://skyha.rf.gd/project-releases/#tab-5792 "Installation Guide") <--**NEW**
	- [Reader page setup Guide](https://skyha.rf.gd/project-releases/#tab-5793 "Reader page setup Guide")

a super simple manga reader that works with most wordpress themes using jQuery(js).
https://skyha.rf.gd/reader/

* The page linked uses this php code to display the manga list. You can add the php code to a wordpress page using the WP editor or the plugin called "Insert PHP Code Snippet". [Manga List PHP code](https://github.com/sykhangdha/wp-mangareader-plugin/blob/main/reader-example.php)
* Note: If for some reason the changes are not working just clear your cache for your wordpress website to update/refresh the manga-reader.js file!
* The reader plugin has now been implented to the latest release of mangareader-wp for users that want a basic wordpress theme: https://github.com/sykhangdha/mangareader-wp
* Recommended themes: Any wordpress theme that uses next_post function. These themes by Alx work the best with the reader. https://alx.media/themes/
* Plugin now maintenance only which can be downloaded here: https://github.com/sykhangdha/wp-mangareader-plugin/releases/



# IMPORTANT

While the reader should work with most themes, please include this code in single-post(usually called single.php in themes) for the reader to work properly.
In the github repo there should be a file called single.php. Copy and paste the code where you want the links to be(recommended: usually anything under posts will work fine) https://github.com/sykhangdha/wp-mangareader-plugin/blob/main/single.php

# ShortCode
The reader, when activated, will add a custom_field named "image_links". Add image links(one image link per line) and the reader will detect all images inside the field.
[<img src="http://i.epvpimg.com/t1RIcab.png">]

AFTER INPUTTING THE IMAGE LINKS PLEASE INSERT [manga_reader] shortcode into your post and it will display all the images from the custom field!

Note: if you want an option to use upload images from a wordpress gallery you can modify the manga-reader.php and manga-reader.js files to do this. Since the site uses external hosts to grab image links this function is not included in this reader plugin. But you are free to modify this plugin however you want! 

# Functions
  * Paged/List view modes
      * List view is always on by default, clicking on an image will open up Paged view mode(reader view)
      * MR3 Introduces the lightbox javascript code from [Magnific Popup - JS](https://dimsemenov.com/plugins/magnific-popup/ "Magnific Popup - JS")
      * For paged view: Use arrow keys OR click on images to navigate
      * Instructions on how to use reader-template.php is provided in the new WP-MangaReader settings introduced in the MR3 release(4.19.24)

  * Works with MOST wordpress themes
      * You may need to add some .css changes to your theme to get it to work properly!
  * Lightweight
      * No unnecssary functions, just switching between views and navigation functions.
      

Info about image link scrapper here: https://github.com/sykhangdha/wp-mangareader-plugin/tree/main/imagelinkscrapper
     
     

