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

I began this assignment by thoroughly reviewing the lecture notes and the assignment specifications to understand the project requirements. From there, I created an Entity-Relationship Diagram in my notebook to map out the database structure, along with a brief outline of the web page structure that would guide my development. This planning phase helped me visualize the components of the application and ensured that I had a clear understanding of the relationships between different entities.

During the development phase, I followed the ERD diagram closely and referred back to my web page structure notes to stay on track. To ensure that my code was functioning as expected, I employed frequent testing. After every few lines of code, I ran tests to verify that each part of the application was working correctly.

When I encountered problems, I utilized several strategies to resolve them. Laravel's error messages were incredibly helpful in diagnosing issues, and I made extensive use of the dd() function to inspect variables and outputs at different stages of the code execution. Additionally, I ran SQLite queries directly in the terminal to ensure that the database was being updated correctly. These techniques, along with regular testing, helped me resolve issues efficiently and ensure that the application worked as intended.

## Efforts made to encourage reviewers to submit useful reviews

To motivate reviewers to submit high-quality and meaningful reviews, I implemented two key features. The first feature is a Top Reviewers Page, which highlights the top 10 reviewers based on their overall scores. These scores are calculated fairly by combining two factors: the marks students received from their teachers for their assessments, and the average rating they receive from their reviewees for each review they provide. This combination ensures that the rankings reflect both academic performance and the quality of peer reviews.

The second feature I implemented is a dynamic feedback mechanism on the review submission page. This feature displays personalized messages to students based on their current position in the reviewer rankings. For example, if a student is in the top 10% of the total reviewers, they are congratulated and encouraged to maintain their excellent performance. If they are in the top 50%, they are congratulated with encouragement to push further. For students in the bottom half of the rankings, the system provides motivational prompts to improve their ranking by writing better reviews. This real-time feedback encourages students to strive for improvement, not only to rank higher but also to contribute more valuable reviews.

This dual approach of publicly recognizing top reviewers and providing immediate, personalized feedback when submitting reviews can encourage reviewer to write quality reviews for their peers.

## ERD Diagram

![ERD Diagram](/ERD.png)
