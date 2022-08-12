###### REQUIREMENTS #####
You must have prior knowledge, at least intermediate, of the laravel framework and php.


##### RUNNING SIMPLE #####
1. Download and unpack the Simple package from the following link: https://github.com/oderman/spot2-api-m2 by pressing the green link: "Clone or download".
2. Run the composer install command to install all project dependencies.
2. We must create a database with the name apispot2 or with the name of our preference.
3. We must go to the .env file to change the connection and database values.
4. The migrations command must be executed to create the zones table with its respective fields.
5. At the root of the project there is a file called seeders.sql. We must import this into our table in the database to have the test records.
6. We start the server with the php artisan serve command and we are ready to test our api.