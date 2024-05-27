<!--main.html-->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DigiNotes</title>
    <link rel="stylesheet" href="./assets/style/main_style.css">
    <script src="https://kit.fontawesome.com/a147d6b708.js" crossorigin="anonymous"></script>
    <link rel="icon" href="./assets/images/app_logo.png" type="image/png">
</head>

<body>
    <header>
        <div class="header-content">
            <div class="logo-container">
                <img src="./assets/images/app_logo.png" id="logo" alt="Logo" class="logo">
                <img src="./assets/images/logo.png" id="name" alt="DigiNotes" class="name">
            </div>
        </div>
        <button id="logoutButton" onclick="logout()">Logout</button>
    </header>
    <div id="main">
        <div id="sidebar">
            <input type="text" id="search" placeholder="Search">
            <button id="deleteAll" class="delete-all-button"><i class="fa-sharp fa-solid fa-trash"></i> Delete All</button>
            <h2 id="savedNotesTitle">Notes</h2>
            <div id="notesList" class="notes-list"></div>
        </div>
        <div id="noteContent" style="display: block;">
            <div class="note">
                <div class="header">
                    <input type="text" id="title" placeholder="Enter title">
                    <div class="buttons">
                        <button class="button save"><i class="fa-sharp fa-solid fa-plus"></i></button>
                        <button class="button delete"><i class="fa-sharp fa-solid fa-eraser"></i></button>
                    </div>
                </div>
                <textarea id="content" style="height: 65vh;" placeholder="Enter content"></textarea>
            </div>
        </div>
    </div>
	<div id="toastContainer" class="toast-container"></div>
    <div id="modal" class="modal">
        <div class="modal-content">
            <p id="modalMessage"></p>
            <div class="modal-buttons">
                <button id="confirmDelete">Yes</button>
                <button id="cancelDelete">No</button>
            </div>
        </div>
    </div>
    <footer>
        <p style="color: #ffffff;">&copy; 2024 DigiNotes</p>
        <a href="https://github.com/haruchanz64d/DigiNotes" target="_blank">
            <i class="fab fa-github"></i>
        </a>
    </footer>
    <script src="js/main.js"></script>
</body>

</html>
