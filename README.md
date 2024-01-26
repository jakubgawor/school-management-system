# School Management System
An application that enables efficient management of users, teaching processes, and grading, with an advanced panel for teachers and administrators.

## Description

### Authentication
- **Authentication** is done through JWT tokens.

### Account Management
- **Registration and account activation:** A user can create an account, which remains inactive until confirmed via a token sent in an email.
- **Password reset:** In case of forgotten password, a user can reset the password via a dedicated endpoint, using an email address to obtain a password reset token.

### User Management
- **Creation and management of Students and Teachers:** Both Administrator and Teacher can create a new Student entity by assigning them to a specific user. The Administrator can do the same for a Teacher. It's also possible to delete entities and edit them.

### Grade Management
- **Adding a new grade:** A Teacher can issue a weighted grade for a specific student in a subject, and remove a grade.
- **Information about grades:** Students can check their grades in a given subject and the average of weighted grades.

### School Class Management
- **Creating and editing classes:** The Administrator can create a new class and delete an existing one.
- **Managing Students:** Both Administrator and Teacher can assign a student to a class, if the student doesn't belong to any, and remove a student from a class.
- **Information about classes:** Teachers and administrators can view grouped information about classes, including data on students and assigned school subjects.

### School Subject Management
- **Creating and editing school subjects:** The Administrator can create a new subject, and teachers can edit and delete subjects.
- **Assigning a school subject to a school class:** The Administrator can assign a class to a particular school subject and remove the connection.
- **Information about school subjects:** Students can check information about school subjects, including data on which classes are taught in a given subject and which teacher is the subject teacher.

## Technologies used in the project:
- PHP 8.2
- Symfony 7.0
- API Platform 3.2
- Doctrine
- Mysql
- Swagger
- Composer
- Phpunit
- Mockery
- XDebug
- Docker
- Git

## Installation
1. Clone the repository
```bash
git clone https://github.com/jakubgawor/school-management-system.git
```

2. Change directory to the project
```bash
cd school-management-system
```

3. Install dependencies using Composer
```bash
composer install
```

4. Rename the environment configuration file
```bash
mv .env.example .env
```

5. Manually configure the necessary environment variables in .env
```bash
DATABASE_URL
MESSENGER_TRANSPORT_DSN
MAILER_DSN
```

6. Use Docker Compose to build and start the containers.
```bash
docker compose up  
```

7. Create the database
```bash
php bin/console doctrine:database:create
```

8. Migrate the database schema:
```bash
php bin/console doctrine:migrations:migrate
```

## Endpoints
![endpoints](https://github.com/jakubgawor/school-management-system/assets/126957667/57979fe6-e7b8-40a6-a8b5-ce68c320deb2)

## Tests coverage
![tests-coverage](https://github.com/jakubgawor/school-management-system/assets/126957667/dd52aaf1-53f2-4a6a-8e93-b75b2ffdde36)

