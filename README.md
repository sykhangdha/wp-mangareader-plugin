# wp-mangareader-plugin

a super simple manga reader that works with most wordpress themes using jQuery(js).
https://skyha.rf.gd/reader/

* The page linked uses this php code to display the manga list. You can add the php code to a wordpress page using the WP editor or the plugin called "Insert PHP Code Snippet". [Manga List PHP code](https://github.com/sykhangdha/wp-mangareader-plugin/blob/main/reader-example.php)
* Note: If for some reason the changes are not working just clear your cache for your wordpress website to update/refresh the manga-reader.js file!
* The reader plugin has now been implented to the latest release of mangareader-wp for users that want a basic wordpress theme: https://github.com/sykhangdha/mangareader-wp
* Recommended themes: Any wordpress theme that uses next_post function. These themes by Alx work the best with the reader. https://alx.media/themes/
* A modified version of innuity by alx is available here: https://github.com/sykhangdha/wp-mangareader-plugin/blob/main/innuity-modified/inunity.zip
* Plugin now maintenance only which can be downloaded here: https://github.com/sykhangdha/wp-mangareader-plugin/releases/tag/Maintenance

# Upcoming Changes for MR3 // 4.18.24 (NOT YET AVAILABLE)
  * Reader Example page template from https://skyha.rf.gd/reader/
  * Documentation for how to use the reader plugin

# IMPORTANT

While the reader should work with most themes, please include this code in single-post(usually called single.php in themes) for the reader to work properly.
In the github repo there should be a file called single.php. Copy and paste the code where you want the links to be(recommended: usually anything under posts will work fine) https://github.com/sykhangdha/wp-mangareader-plugin/blob/main/single.php

# ShortCode
The reader, when activated, will add a custom_field named "image_links". Add image links(one image link per line) and the reader will detect all images inside the field.
[<img src="http://i.epvpimg.com/t1RIcab.png">]

AFTER INPUTTING THE IMAGE LINKS PLEASE INSERT [manga_reader] shortcode into your post and it will display all the images from the custom field!

Note: if you want an option to use upload images from a wordpress gallery you can modify the manga-reader.php and manga-reader.js files to do this. Since the site uses external hosts to grab image links this function is not included in this reader plugin. But you are free to modify this plugin however you want! 

# Functions
  * Select between Paged/List View
      * The reader will remember the last selected option
      * For list view: Clicking on the image will scroll down to the next one.
      * For paged view: Use arrow keys OR click on images to navigate
      * Next and Previous chapter using arrow keys!(will add an option when clicking on image as well, probably will add in the next few days)
      * Note: This assumes you are using the Category as your taxonomy! You can update the code if you are using a custom taxonomy
  * Works with MOST wordpress themes
      * You may need to add some .css changes to your theme to get it to work properly!
  * Lightweight
      * No unnecssary functions, just switching between views and navigation functions.
  * What's being worked on still?
      * ~~Currently being worked on: A custom field to insert multiple image links and it will automatically insert the shortcode and links into the correct format.~~ Added in V2
      * ~~You can test this out by downloading the zip of this repo and activating the plugin. Use classic editor and go to screen options and in custom_fields add one called image_links (it should be an option but if not type it in and press enter). Now any image links provied in the field(one image link per line) it will display it into the reader when you use the shortcode [manga_reader]~~  Added in Maintenance Release 1
      

Info about image link scrapper here: https://github.com/sykhangdha/wp-mangareader-plugin/tree/main/imagelinkscrapper
     
     

