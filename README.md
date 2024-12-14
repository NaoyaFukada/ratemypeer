# Peer Review Web Application (RateMyPeer)

## Project Overview

This project was developed as part of **Assignment 2** for the **2703ICT Web Application Development** course at **Griffith University**. The purpose of this web application is to enable students to share and submit peer reviews for a course. It implements a simplified peer review process tailored for educational use. The application supports two user types (teachers and students) and provides features for managing courses, assessments, and reviews.

## Instructions for Demo

The demo application is available at **[naoyafukada.com/ratemypeer/](https://naoyafukada.com/ratemypeer/)**.

### 1. Login as a Student:

- **Username:** `S1001`
- **Password:** `password`

### 2. Login as a Teacher:

- **Username:** `T1001`
- **Password:** `password`

## Features

- **User Authentication**:

  - Students can register with their name, email, and S-number.
  - Both students and teachers can log in and log out securely.

- **Course Management**:

  - Students can view a list of courses they are enrolled in.
  - Teachers can manage courses they teach and manually enroll students.

- **Assessment Management**:

  - Teachers can create and update peer review assessments with the following details:
    - Title, instructions, due date, number of required reviews, maximum score, and type (student-select or teacher-assign).
  - Teachers can only update assessments if no submissions have been made.
  - Teachers can take attendance for each assessment, randomly assigning attended students to groups to minimize the teacher's workload.

- **Peer Review Process**:

  - **Student-Select Reviews**: Students select their reviewees from a dropdown list of enrolled students and submit reviews.
  - **Teacher-Assign Reviews**: Teachers assign students to peer review groups, which are determined randomly based on attendance. This reduces the manual effort required for assigning students to groups.

- **Review Tracking**:

  - Students can:
    - Submit reviews.
    - View reviews they’ve submitted.
    - See and rate reviews they’ve received (e.g., rate out of 5).
  - Teachers can:
    - View students' review statistics, including the number of reviews submitted and received, and their scores for each assessment.

- **File Upload**:

  - Teachers can upload a text file to bulk-create courses with the following:
    - Associated teachers, assessments, and enrolled students.
  - The system ensures that courses with duplicate course codes are not created.

- **Top Reviewers**:
  - The application includes a leaderboard showcasing the top reviewers based on:
    - The average ratings of their reviews and assessments submitted.

## Key Technologies Used:

- **Laravel**: The application leverages Laravel's robust features for building scalable web applications, including:

  - **Routing**: Used to define and manage routes, ensuring seamless navigation between pages.
  - **Blade Templating**: Provides HTML sanitization and facilitates clean, organized, and reusable views.
  - **Migrations**: Simplifies database schema creation and modification using PHP.
  - **Seedings**: Automatically inserts default data into the database for testing purposes.
  - **Models**: Acts as a bridge between the frontend and backend by representing database tables as classes and records as instances. Eloquent ORM, Laravel's built-in ORM, makes it easier to interact with the database.
  - **Controllers**: Handles the logic for processing user input, interacting with models, and returning appropriate views.
  - **Pagination**: Enables the efficient handling and display of large datasets by breaking them into manageable chunks, with navigation controls for better user experience.

- **Bootstrap**: CSS Library used for responsive and intuitive UI design.

- **SQlite**: Relational database management system that uses SQL to interact with the database.

- **Font Awesome**: Used for icons.

## Development Reflection

This project followed a structured development process:

### Planning:

- Reviewed the assignment specifications and lecture notes to understand the requirements.
- Created an Entity-Relationship Diagram (ERD) and a web page structure outline to guide development.

### Development:

- Built the database schema using migrations and populated it with seeders.
- Implemented routing, controllers, and models in Laravel to connect the frontend and backend seamlessly.
- Created reusable Blade templates for consistency across views.

### Testing:

- Conducted frequent tests after implementing each feature to ensure functionality.
- Used Laravel's `dd()` and database queries to debug and verify data integrity.

### Problem-Solving:

- Resolved issues using Laravel's error messages and debugging tools.
- Regularly tested interactions between components to maintain a smooth workflow.

## Efforts to Encourage Useful Reviews

To motivate reviewers to submit high-quality reviews, two key features were implemented:

### Top Reviewers Leaderboard:

- Highlights the top 10 reviewers based on a combination of:
  - Marks received from teachers for assessments.
  - Average ratings received from reviewees for their reviews.
- This approach balances academic performance with the quality of peer reviews.

### Dynamic Feedback on Review Submission Page:

- Displays personalized messages to students based on their ranking:
  - **Top 10%:** Encouraged to maintain excellent performance.
  - **Top 50%:** Motivated to push for improvement.
  - **Bottom 50%:** Prompted with motivational messages to write better reviews.
- This real-time feedback inspires students to improve their contributions to the peer review process.

## ERD Diagram

![ERD Diagram](/ERD.png)
