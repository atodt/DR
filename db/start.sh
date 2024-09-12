#!/bin/bash

# Wait for MariaDB to start
sleep 5

# Run the init script
mysql_upgrade -uroot -ppasswd

# Start MariaDB
exec docker-entrypoint.sh mysqld