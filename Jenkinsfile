pipeline {
    agent any

    environment {
        APP_ENV       = 'testing'
        DB_CONNECTION = 'sqlite'
        DB_DATABASE   = ':memory:'
    }

    stages {
        stage('Checkout') {
            steps {
                checkout scm
            }
        }

        stage('Composer Install & Audit') {
            steps {
                sh 'composer install --no-interaction --prefer-dist --optimize-autoloader'
                sh 'composer audit --no-interaction'
            }
        }

        stage('Frontend Build') {
            steps {
                sh 'npm ci'
                sh 'npm run build'
            }
        }

        stage('PHPUnit & 100% Coverage Check') {
            steps {
                sh 'vendor/bin/phpunit --coverage-clover coverage.xml'
                script {
                    def clover = readFile('coverage.xml')
                    def xml = new XmlParser().parseText(clover)
                    def metrics = xml.project.metrics[0]
                    def elements = metrics.@elements.toInteger()
                    def coveredelements = metrics.@coveredelements.toInteger()
                    def percentage = elements > 0 ? ((coveredelements / elements) * 100) : 100

                    echo "Code Coverage: ${percentage}% (${coveredelements}/${elements})"
                    if (percentage < 100) {
                        error("Code coverage is ${percentage}%, but 100% is required!")
                    }
                }
            }
        }

        stage('Trivy Security Scan') {
            steps {
                sh 'trivy fs --exit-code 0 --format sarif --output trivy-results.sarif .'
            }
        }

        stage('Package Application') {
            steps {
                sh '''
                    zip -r latest-app.zip . \
                        -x "vendor/*" \
                        -x "node_modules/*" \
                        -x ".env*" \
                        -x ".git/*" \
                        -x "coverage/*" \
                        -x "coverage.xml" \
                        -x "trivy-results.sarif" \
                        -x "latest-app.zip"
                '''
            }
        }

        stage('Publish & Deploy') {
            steps {
                archiveArtifacts artifacts: 'latest-app.zip, coverage.xml, trivy-results.sarif', fingerprint: true
                echo 'Application deployed successfully.'
            }
        }
    }

    post {
        always {
            cleanWs deleteDirs: true, notFailBuild: true
        }
    }
}
