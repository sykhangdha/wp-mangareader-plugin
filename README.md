# wp-mangareader-plugin
a super simple manga reader that works with most wordpress themes using jQuery(js).
Check it out here: https://hasky.rf.gd/read/

# ShortCode
shortcode example: [manga_reader images="image1.jpg, image2.jpg, image3.jpg"] OR you can also use [manga_reader]  [/manga_reader]
Example of that shortcode: 


[<img src="http://i.epvpimg.com/qle7aab.png">]


# Functions
  * Select between Paged/List View
      * The reader will remember the last selected option
      * For list view: Clicking on the image will scroll down to the next one.
      * For paged view: Use arrow keys OR click on images to navigate
  * Works with MOST wordpress themes
      * You may need to add some .css changes to your theme to get it to work properly!
  * Lightweight
      * No unnecssary functions, just switching between views and navigation functions.
  * What's being worked on still?
      * Currently being worked on: A custom field to insert multiple image links and it will automatically insert the shortcode and links into the correct format.
             * You can test this out by downloading the zip of this repo and activating the plugin. Use classic editor and go to screen options and in custom_fields add one called image_links (it should be an option but if not type it in and press enter). Now any image links provied in the field(one image link per line) it will display it into the reader when you use the shortcode [manga_reader] 
     
     

