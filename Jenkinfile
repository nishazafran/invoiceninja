pipeline {
    agent any

    environment {
        DB_HOST = '127.0.0.1'
        DB_PORT = '3306'
        DB_DATABASE = 'invoiceninja_test'
        DB_USERNAME = 'ninja'
        DB_PASSWORD = 'ninja'
    }

    options {
        // Keep last 10 builds
        buildDiscarder(logRotator(numToKeepStr: '10'))
        // Timestamps in logs
        timestamps()
    }

    stages {

        stage('Checkout Code') {
            steps {
                // Checkout the v5-stable branch
                git branch: 'v5-stable', url: 'https://github.com/invoiceninja/invoiceninja.git'
            }
        }

        stage('Setup PHP & Composer') {
            steps {
                // Install PHP & extensions if using self-hosted agent or Docker
                sh '''
                    sudo apt update
                    sudo apt install -y php8.2 php8.2-mbstring php8.2-intl php8.2-mysql php8.2-bcmath php8.2-gd php8.2-zip unzip curl
                    curl -sS https://getcomposer.org/installer | php
                    sudo mv composer.phar /usr/local/bin/composer
                '''
                
                // Install PHP dependencies
                sh 'composer install --no-interaction --prefer-dist'
            }
        }

        stage('Setup Node & Build Frontend') {
            steps {
                sh '''
                    curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
                    sudo apt-get install -y nodejs
                    npm ci
                    npm run production
                '''
            }
        }

        stage('Start MySQL') {
            steps {
                sh '''
                    sudo service mysql start || true
                    mysql -e "CREATE DATABASE IF NOT EXISTS ${DB_DATABASE};"
                    mysql -e "CREATE USER IF NOT EXISTS '${DB_USERNAME}'@'localhost' IDENTIFIED BY '${DB_PASSWORD}';"
                    mysql -e "GRANT ALL PRIVILEGES ON ${DB_DATABASE}.* TO '${DB_USERNAME}'@'localhost';"
                '''
            }
        }

        stage('Configure Laravel Environment') {
            steps {
                sh '''
                    cp .env.example .env
                    php artisan key:generate
                '''
            }
        }

        stage('Run Migrations') {
            steps {
                sh '''
                    php artisan migrate --force
                '''
            }
        }

        stage('Run Tests') {
            steps {
                sh '''
                    php artisan test --testsuite=Unit --testsuite=Feature
                '''
            }
        }
    }

    post {
        always {
            echo 'Cleaning up...'
            // Optional: remove temporary files, stop services, etc.
        }
        success {
            echo 'Build and tests succeeded!'
        }
        failure {
            echo 'Build or tests failed!'
        }
    }
}
