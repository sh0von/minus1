### Setup

1. First, set up XAMPP by starting Apache and MySQL services.
2. Next, access phpMyAdmin and create a new database using the provided `db.sql` file.
3. Then, navigate to `db.php` and update the following variables with your own database information:
    - `$servername = "localhost";`  <!-- Add your server name here -->
    - `$username = "root";`  <!-- Add your username here -->
    - `$password = "";`  <!-- Add your password here -->
    - `$database = "";`  <!-- Add your database name here -->
4. Once these details are updated, your setup is complete.

This setup ensures smooth functioning of your database connectivity.

### Feature Overview

#### 1. Login/Registration
- Users can register for an account or log in if they already have one.

#### 2. Personal Profile
- Each user has a personal profile where they can manage their information.

#### 3. DevBox (GitHub-Like Repository Page)
- A page resembling GitHub's repository view where users can see all their repositories.
  
#### 4. Search/Filter Options for DevBox
- Users can search for specific repositories or use filters to narrow down their results.

#### 5. Skill-Based Sorting of DevBins
- Utilizes code to sort DevBins based on user-defined skills using a provided algorithm.

```php
usort($posts, function ($a, $b) use ($user_skills) {
    $matches_a = array_intersect($user_skills, explode(',', $a['skills']));
    $matches_b = array_intersect($user_skills, explode(',', $b['skills']));

    return count($matches_b) - count($matches_a);
});
```
#### 6. DevBin (Individual Repository)

Users can create and manage individual repositories, termed as "DevBins," each supported solely by Markdown (MD) files. These DevBins serve as containers for specific projects or code snippets, providing users with a structured way to organize and showcase their work. Through the DevBin interface, users can:

- Create new DevBins to house their projects or code snippets.
- Delete existing DevBins that are no longer needed or relevant.
- View and edit the Markdown files within each DevBin to provide detailed documentation or descriptions.
  
This feature empowers users to curate and share their code projects efficiently, fostering collaboration and knowledge sharing within the developer community.

#### 7. DevBin Creation/Deletion

Users have the ability to create new DevBins to organize their projects or code snippets effectively. Additionally, users can delete existing DevBins that are no longer required, allowing for efficient management of their repositories.

#### 8. Commit History Tracking

The system automatically tracks the commit history of each DevBin entry, providing users with insights into the changes made over time. This feature enables users to monitor the evolution of their projects and collaborate more effectively with others.

#### 9. Dev Entry/Individual MD File Upload

Users can upload individual Markdown (MD) files to their DevBins, allowing them to provide detailed documentation, descriptions, or code snippets for each project. This feature enhances the usability of DevBins by enabling users to share their work in a structured and organized manner.

#### 10. Commenting System

Users can engage in discussions and provide feedback on DevBins through a built-in commenting system. This feature fosters collaboration and allows for constructive communication among users, enabling them to share insights, ask questions, and offer suggestions.

#### 11. Add Collaborator to DevBin

Users can invite collaborators to their DevBins, granting them access to contribute to the repository. Collaborators can make edits, add content, and participate in discussions, facilitating teamwork and enabling multiple developers to work together on projects.
