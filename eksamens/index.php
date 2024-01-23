<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guest Book</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>GUEST BOOK</h1>

    <form id="guestBookForm">
        <label for="name">Vārds:</label>
        <input type="text" name="name" id="name" required>
        <p id="nameError" class="error"></p>

        <label for="email">E-pasts:</label>
        <input type="email" name="email" id="email" required>
        <p id="emailError" class="error"></p>

        <label for="message">Ziņojums:</label>
        <textarea name="message" id="message" required></textarea>
        <p id="messageError" class="error"></p>

        <button type="button" onclick="validateAndSubmit()" id="submitButton">Pievienot ziņojumu</button>
    </form>

    <!-- Sorting dropdown -->
    <div id="sortingContainer">
        <label for="sortSelect">Kārtošana:</label>
        <select id="sortSelect" onchange="updateEntries()">
            <option value="created_at">Pēc datuma</option>
            <option value="name">Pēc vārda</option>
        </select>
    </div>

    <!-- Search bar -->
    <div id="searchContainer">
        <label class="searchInputH" for="searchInput">Meklēt:</label>
        <input type="text" id="searchInput" oninput="updateEntries()">
    </div>

    <div id="entriesContainer">
        <?php
            require_once 'GuestBook.php';

            $guestBook = new GuestBook();
            $entries = $guestBook->getEntries();  // Default sorting by date

            if (!empty($entries)) {
                echo '<h2>Visi ieraksti</h2>';
                foreach ($entries as $entry) {
                    echo '<p><strong>' . $entry['name'] . '</strong> (' . $entry['email'] . '): ' . $entry['message'] . ' - ' . $entry['created_at'] . '</p>';
                }
            } else {
                echo '<p>Nav ierakstu.</p>';
            }
        ?>
    </div>

    <div id="cooldownMessage" class="hidden">Jums jāgaida, pirms iesniegšanas atkārtotas.</div>

    <script>
        var isCooldown = false;

        function validateAndSubmit() {
            if (isCooldown) {
                document.getElementById('cooldownMessage').style.display = 'block';
                setTimeout(function () {
                    document.getElementById('cooldownMessage').style.display = 'none';
                }, 5000); // Hide the message after 5 seconds
                return;
            }

            var name = document.getElementById('name').value;
            var email = document.getElementById('email').value;
            var message = document.getElementById('message').value;

            var emailRegex = /^[^\s@]+@[^\s@]+\.(com|lv)$/;

            document.getElementById('nameError').innerText = name === '' ? 'Lūdzu, ievadiet vārdu.' : '';
            document.getElementById('emailError').innerText = (email === '' || !emailRegex.test(email)) ? 'Lūdzu, ievadiet derīgu e-pasta adresi ar .com vai .lv domēnu.' : '';
            document.getElementById('messageError').innerText = (message === '' || message.includes('<div></div>')) ? 'Lūdzu, ievadiet derīgu ziņojumu.' : '';

            // If no errors, submit the form asynchronously
            if (name !== '' && email !== '' && emailRegex.test(email) && message !== '' && !message.includes('<div></div>')) {
                submitForm();
            }
        }

        function submitForm() {
            var formData = new FormData(document.getElementById('guestBookForm'));

            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'process.php', true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    // Handle the response
                    console.log(xhr.responseText);
                    // Update the entries on the page
                    updateEntries();

                    // Set a cooldown of 10 seconds
                    isCooldown = true;
                    setTimeout(function () {
                        isCooldown = false;
                    }, 2000);

                    // Disable the submit button during cooldown
                    document.getElementById('submitButton').disabled = true;
                    setTimeout(function () {
                        document.getElementById('submitButton').disabled = false;
                    }, 2000);
                }
            };

            xhr.send(formData);
        }

        function updateEntries() {
            var entriesContainer = document.getElementById('entriesContainer');
            var sortOption = document.getElementById('sortSelect').value;
            var searchQuery = document.getElementById('searchInput').value;

            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'get_entries.php?sort=' + sortOption + '&search=' + encodeURIComponent(searchQuery), true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    entriesContainer.innerHTML = xhr.responseText;
                }
            };

            xhr.send();
        }

        // JavaScript, lai apstrādātu pogas paslēpšanu/parādīšanu
        document.getElementById('seeEntriesBtn').addEventListener('click', function() {
            var entriesContainer = document.getElementById('entriesContainer');
            entriesContainer.classList.toggle('hidden');
        });
    </script>
</body>
</html>
