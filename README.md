LEVEL UP





Installation & Setup

1) Clone the respository 

git clone https://github.com/Krishi2004/student-reflection-tool.git
cd student-reflection-tool

2) Install PHP dependencies 

composer install

3) Install frontend assets

npm install
npm run build

4) Enviroment Config

Copy the env.example file
Generate a new application key: php artisan key:generate
update the .env file with your local database details

5) Database Migration and Seeding

php artisan migrate --seed

6) Start the application

php artisan serve
