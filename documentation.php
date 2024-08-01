<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentation</title>
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
    <h2>Documentation</h2>
    <h3>How to use</h3>
    <div class="container">
        <h1>How to Use</h1>
        
        <h2>Sign Up and Login</h2>
        <p>Start by signing up on the registration page.</p>
        <p>After signing up, log in using your credentials to be redirected to the home page.</p>

        <h2>Creating a New Group</h2>
        <p>On the home page, click the "Create a New Group" button.</p>
        <p>Enter the necessary details to create a new group.</p>

        <h2>Managing Groups</h2>
        <p>You can enter any group you have created or are a member of by clicking on it from the list of groups on the home page.</p>
        <p>Inside the group, you can invite registered users via their email addresses.</p>

        <h2>Inviting Users to a Group</h2>
        <p>Once in a group, click the "Invite a User" button.</p>
        <p>Enter the email address of the user you want to invite and submit the form.</p>
        <p>If the email corresponds to a registered user, they will be added to the group.</p>

        <h2>Adding Items to the Group List</h2>
        <p>Inside the group, you can add items to a shared list.</p>
        <p>Click the "Add Item" button, enter the item details, and submit the form.</p>

        <h2>Completing Items</h2>
        <p>You can mark items as completed by selecting them from the list and clicking the corresponding button.</p>

        <h2>Adding Expenses</h2>
        <p>Click the "Add Expense" button.</p>
        <p>Select the user who paid, the users who are sharing the expense, the amount, and a description.</p>
        <p>Submit the form to add the expense.</p>

        <h2>Viewing and Managing Expenses</h2>
        <p>The expenses are listed in a table, displaying the amount, payer, description, amount due, and actions.</p>
        <p>You can view details and manage these expenses as necessary.</p>
    </div>
    <h3>Organization/Architecture</h3>   
    <p>The project has a well-organized structure with clear separation of concerns. The key files are organized as follows:</p>
    <ul>
        <li><strong>expense_details.php:</strong> Handles the display and management of expense details.</li>
        <li><strong>group.php:</strong> Manages group-related functionalities.</li>
        <li><strong>navbar.php:</strong> Contains the navigation bar for the application, which follows DRY principle and makes code much easier to implement without repeating it.</li>
        <li><strong>home.php:</strong> Contains the list of groups made and access button to those.</li>
        <li><strong>login.php:</strong> A form handling input of user email and password by verifying it.</li>
        <li><strong>update_payments.php:</strong> Handles the updating of user payment statuses.</li>
    </ul>
    
    <h3>Dynamic Content</h3>
    <p>Expenses and groups are dynamically stored in and fetched from the database. The PHP files interact with the database to retrieve and display this content. You may go through the given database SQL file for further structure analysis of the database.</p>

    <h3>Forms and Validation</h3>
    <p>The project includes forms for adding expenses and groups. These forms include validation to ensure data integrity. Login and signup include basic validation for incorrect email formatting or invalid password for a registered user. These tasks have been implemented with the files in the includes folder having validation PHP files for each page.</p>

    <h3>Database Updates</h3>
    <p>Users can add new expenses and groups, which updates the database accordingly. This functionality ensures that the application remains dynamic and interactive.</p>

    <h3>Documentation and Sources Pages</h3>
    <p>The documentation page serves as comprehensive documentation for the project. It includes an overview, features, checkpoints, usage guidelines.</p>

    <h3>Animations</h3>
    <p>Subtle animations are included to enhance the user experience. These animations make the interface more engaging and interactive.</p>

    <h3>Grid/Flexbox Layout</h3>
    <p>The layout of the application is created using CSS Grid and Flexbox, ensuring a flexible and responsive design. Some elements like the navbar and tables in group.php have been implemented using Flexbox.</p>

    <h3>JavaScript/jQuery Functionality</h3>
    <p>Various functionalities are implemented using JavaScript and jQuery. These include toggle functions to get a tab by clicking on "add expense," making the user experience quite clean and easy to understand and use.</p>

    <h3>Accessibility and Responsiveness</h3>
    <p>The site is designed to be accessible and responsive. It adapts to different screen sizes and provides a user-friendly interface for all users.</p>

    <h3>Testing and Validation</h3>
    <p>The application is well-tested and free from errors. Screenshots of validation and testing processes are included in the documentation.</p>

    <h3>Bonus</h3>
    <p>Sessions are being used for creating local tokens or to store user IDs, which might be suitable for bonus.</p>

    <h3>Features</h3>
    <ul>
        <li>User authentication</li>
        <li>Create and manage groups</li>
        <li>Add and track expenses</li>
        <li>Split expenses among group members</li>
        <li>Dynamic content fetched from the database</li>
        <li>Form validation</li>
        <li>Responsive design</li>
        <li>Accessible interface</li>
        <li>Animations</li>
        <li>Grid/Flexbox layout</li>
        <li>JavaScript/jQuery functionality</li>
    </ul>
</body>
</html>
