# DreamRobot REST API Project

## Overview

DreamRobot is a Docker-based application designed to help you build a PHP application with a REST API. This project involves creating a MariaDB container, developing a PHP application with a REST API, packaging the PHP application in a container, and implementing a rate-limit feature.

## Tasks

### 1. Create a MariaDB Container
Set up a MariaDB container using Docker to serve as the database for the application.

### 2. Develop a PHP Application with REST API
Create a PHP application that provides a REST API for interacting with the MariaDB database.

### 3. Package the PHP Application in a Container
Package the PHP application into a Docker container for easy deployment and scalability.

### 4. Implement Rate-Limit Feature
Add a rate-limit feature to the REST API.

### 5. Version Control with GitHub
Use GitHub for version control to manage and track changes to the project.

## Installation

To get started with this project, you need to have Docker installed on your system. 
This project uses docker compose to start multiple containers at once.
Follow the steps below to set up the project:

1. **Clone the repository:**
    ```sh
    git clone https://github.com/atodt/DR.git app
    cd app
    ```

2. **Run the application:** 
   ```sh
   # This builds images if they don't exist and starts the containers in the docker stack after.
   docker compose up -d
   ```

3. **To start containers with rebuilding images**
   ```sh
   # This builds images if they don't exist and starts the containers in the docker stack after.
   docker compose up -d --build
   ```