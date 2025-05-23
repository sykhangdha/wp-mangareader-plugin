# wp-mangareader-plugin

The final release of the wp-mangareader-plugin: [DOWNLOAD NOW](https://github.com/sykhangdha/wp-mangareader-plugin/releases/tag/FinalRelease)

# PLEASE READ

Users that want to use an alternative method(grab manga images and chapters from a folder) can use my new MangaViewer plugin [Check it out now!](https://github.com/sykhangdha/MangaViewer)


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
     
     

