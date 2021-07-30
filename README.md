# integration-test

# VERSION USED
1. Php 7.4.21
2. MySQL 5.6
3. Composer 2.0.14


# INITIAL INSTALLATION

1. cd project_folder
2. run -> composer install
3. copy ".env.example" to ".env"
4. import database located at database/integration_test.sql
5. edit below from .env
	DB_DATABASE=integration_test
	DB_USERNAME=your_username
	DB_PASSWORD=your_password
6. run -> php artisan key:generate
7. run -> php artisan serve

# HOW TO USE

1. Enter API KEY to "http://127.0.0.1:8000/" , if API KEY is valid page will redirect to "http://127.0.0.1:8000/list", error notification will show it not
2. Adding a subscriber
	- Click Add Subscriber button from "http://127.0.0.1:8000/list"
	- Submit required values
	- On Successful registration a success notification will appear on the same page
	- On Failed registration a failed notification will appear on the same page
3. Updating a subscriber 	
	- Click Email Address from "http://127.0.0.1:8000/list"
	- Submit required values
	- On Successful registration page will redirect to  "http://127.0.0.1:8000/list"
	- On Failed registration a failed notification will appear on the same page
4. Deleting a subscriber
	- Click Delete button on "http://127.0.0.1:8000/list"
	- Table will refresh without page reload
5. Listing
	- Search textbox will search for subscriber email via API
	- Datables List & Pagination

#Contact me at contact.wendellalvarez@gmail.com for more info.
Thank You :)
