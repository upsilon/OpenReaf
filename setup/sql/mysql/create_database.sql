CREATE DATABASE your_db_name DEFAULT CHARACTER SET utf8;
GRANT ALL PRIVILEGES ON your_db_name.* TO your_db_user_name@localhost IDENTIFIED BY 'your_db_user_password';
GRANT ALL PRIVILEGES ON your_db_name.* TO your_db_user_name@"192.168.1.10" IDENTIFIED BY 'your_db_user_password';
FLUSH PRIVILEGES;
