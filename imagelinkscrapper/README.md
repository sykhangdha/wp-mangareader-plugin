# NOT PART OF THE WORDPRESS PLUGIN! PYTHON IS REQUIRED ON WINDOWS/LINUX TO WORK! WORKS WITH MANGAKAKALOT.TV ONLY!


To use the python script please install the required libs using this command **pip install requests beautifulsoup4 selenium**

open PowerShell or Terminal in the directory you downloaded the script and run 

* 1)python manga_scraper.py
* 2)After hitting enter it will ask to enter the starting chapter url. For example: https://ww5.mangakakalot.tv/chapter/manga-wd951838/chapter-1
* 3)Hit enter again and it will then ask to Enter the number of chapters to scrape:
          * For example if you enter 10 it will scrape chapters 1-10 if your url was chapter 1, if you start from chapter 10 and input 20 it will start from chapter 10-30.
* 4)Image links will then be inserted into a .txt file and separated by chapter headings!

Please note that the script is only meant to grab image links and not save the images! Again this is not part of the wordpress plugin, but it will grab image links so you can copy and paste them into the custom field provided by the plugin!
Currently looking for different method to lazy load images on the webpage, some mangas will probably not work with the script if there are too many images.

# CURRENTLY WORKING ON UPDATING MYMANGAREADER CMS SCRIPT FROM CODECANYON! development for the wordpress plugin will be completely separate!
