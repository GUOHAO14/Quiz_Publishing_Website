# Quiz Publishing Website - Overview
This project is an assignment for Responsive Web Design & Development module in semester 4 of my diploma program at APU. It is a website developed as an online assessment and learning platform that can be utilised by educational institutions. Since it is a relatively small project and our team is still beginners in web development, the website is not published with a live server, instead it is a locally-hosted project. The development has involved four programming / scripting languages - HTML, CSS, JavaScript and PHP. XAMPP (phpMyAdmin) is used as a localhost server. In order to retrieve data from XAMPP server, MySQL query language is used. 

The objective of the website is to:
1. Act as an alternative tool of teaching for teachers.
2. Eliminate rigid and traditional teaching methods.
3. Automate the answer verification process.
4. Analyse and summarise student performance.
5. To provide students with a reliable medium for online self-learning.


# Project Assumptions (And How It Works)
1. In contrast to regular online quiz and assessment websites, our project is designed specifically as a closed system for the use within an educational institution. Our intent is to provide a secure website for educational institutions, with which the quality of the quizzes are guaranteed. Details are as below:

    - In the website’s initialisation, there is intentionally no sign up feature for account creation. Only the login feature is available.
    - This is made so that the general public is not allowed to access the website. This is because by design, all accounts in the systems are pre-registered by website’s administrators.
    - Only people within the institution in-use of the website own their respective accounts and can log in to the website with them.
    - Each accounts is assigned with a specific role, such as student, teacher or admin. Different roles have their distinct user interface and functionalities. For example, exclusively the institution’s teachers and admins are certified to create online quizzes.

2. In our project, there are three types of quizzes – Ranked Assessment, Test, and Exercise. Exercise is a regular quiz; Tests is also identical to Exercise, but they are timed; Ranked Assessment is a special quiz category which will be recorded in the leaderboard and showcased to the whole student grade. This added competitiveness is aimed to provide extra fun and intensity in the user experience, aside from just testing student’s performance.

## How To Use
### 1. MySQL Database Import 
In the zip file, there is an sql file that you must download to import it into XAMPP (phpMyAdmin) database. The connection would then be established by opening XAMPP control panel and activating (click 'Start') on two modules - Apache and MySQL. The database import then can be done after typing 'localhost/phpmyadmin' to visit phpMyAdmin page (alternatively, click 'Admin' in XAMPP control panel).

### 2. Project Folder Placement in htdocs
By now, the files in the zip folder should have already been extracted. Assuming that XAMPP is successfully installed, all the extracted files (which is in a folder) should be moved (or copy, paste) to the htdocs folder, which is child folder of xampp folder. The xampp folder should have been automatically created and usually located in C drive. This, and alongside step 1, ensure that the php files can connect to XAMPP's MySQL server. 

Note: there is a *conn.php* file among the files that serves the purpose of establishing a server connection. Make sure the database name in the file matches with the name of the database imported in phpMyAdmin. 

### 3. Password Hashing
As a security implementation, all user passwords are hashed (encrypted) in the database. You can visit the user table in the database, and you will find that the values of the password columns are all giberrish. This is completely normal and intended. This means that even if the database is somehow intruded with brute force, the password are hidden, and impersonation attempts will not be successful. 

This is a list of usernames and passwords that can be used to log into the system:

**Admin Login:** 
- King, Masterkey123.
- Queen, Masterkey123.

**Teacher Login:**
- Anwar, Anwar02.
- Joshua, Joshie9099.

**Student Login:**
- hogrider, Hogrider123.
- Najib, Najib123.
- marshall, Marshall123.

Passwords can only be reset by an admin account. And as a callback to the assumptions above, all types of accounts can only be created by admin accounts. These implementation are intended for maximum security and authorised access the website. 

Thank you
