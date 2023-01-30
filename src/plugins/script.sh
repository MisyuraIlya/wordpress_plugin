#!/bin/bash

echo "what is the name of project ?"

read project

echo "what is the token of project?"

read token

echo "wich ERP this project used?"

read erp

echo "insert domain api"

read domain

echo "api db name?"

read db

echo "username of api?"

read username

echo "password of api?"

read apipassword


php /var/www/july/data/www/digitrade.host/wordpress_plugin/src/plugins/php_script.php "$project" "$token" "$erp" "$domain" "$db" "$username" "$apipassword"
