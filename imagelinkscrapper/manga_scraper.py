from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.chrome.service import Service as ChromeService
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.common.keys import Keys
from selenium.webdriver.common.action_chains import ActionChains
import time
import datetime  # Added datetime module for timestamp

# Prompt user for input
start_chapter_url = input("Enter the starting chapter URL: ")
num_chapters = int(input("Enter the number of chapters to scrape: "))

# Extract the chapter number from the starting chapter URL
start_chapter_num = int(start_chapter_url.split("chapter-")[1])

# Set up Chrome driver
chrome_service = ChromeService(executable_path="path/to/chromedriver.exe") # Update with path to your chromedriver executable
driver = webdriver.Chrome(service=chrome_service)

# Navigate to starting chapter URL
driver.get(start_chapter_url)

# Create a dictionary to store chapter headings and image links
chapter_data = {}

# Get current timestamp
timestamp = datetime.datetime.now().strftime("%Y%m%d_%H%M%S")

# Scroll down the webpage using JavaScript to trigger lazy-loading of images
def scroll_down():
    # Use ActionChains to simulate scrolling down with a delay
    actions = ActionChains(driver)
    actions.send_keys(Keys.END)  # Scroll to the bottom of the page
    actions.perform()
    time.sleep(0.5)  # Adjust delay as needed to scroll faster

# Loop through chapters and scrape image links
for i in range(num_chapters):
    chapter_num = start_chapter_num + i
    chapter_url = start_chapter_url.replace("chapter-{}".format(start_chapter_num), "chapter-{}".format(chapter_num))
    driver.get(chapter_url)
    print("Scraping images from chapter: ", chapter_url)

    try:
        # Wait for the lazy-loading of images
        WebDriverWait(driver, 10).until(EC.presence_of_all_elements_located((By.XPATH, "//div[@class='vung-doc']/img")))
    except TimeoutException:
        print("No images found in chapter: ", chapter_url)
        continue

    # Scroll down the page to trigger lazy-loading of images
    while True:
        prev_height = driver.execute_script("return document.body.scrollHeight")
        scroll_down()
        new_height = driver.execute_script("return document.body.scrollHeight")
        if new_height == prev_height:
            break

    # Wait for images to load
    time.sleep(5)  # Increased delay to allow images to load properly

    # Scraping image links
    img_elements = driver.find_elements(By.XPATH, "//div[@class='vung-doc']/img")
    image_links = []
    for img_element in img_elements:
        img_url = img_element.get_attribute("src")
        if img_url:
            print(img_url)
            image_links.append(img_url)

    # Store chapter heading and image links in dictionary
    chapter_heading = driver.find_element(By.XPATH, "//h1").text
    chapter_data[chapter_heading] = image_links

    # Delay for 7 seconds before navigating to the next chapter
    time.sleep(7)

# Save chapter headings and image links to a single .txt file with timestamp appended to the file name
with open("chapter_image_links_{}.txt".format(timestamp), "w") as file:
    for chapter_heading, image_links in chapter_data.items():
        file.write("Chapter {}\n".format(chapter_heading))
        for img_url in image_links:
            if img_url:
                file.write("{}\n".format(img_url))