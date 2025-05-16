
let questionCount = 0; // Fallback to 0 if not defined

document.addEventListener("DOMContentLoaded", () => {
    // Trigger addQuestionBox only once when the page loads
    addQuestionBox();
});

function addQuestionBox() {
    questionCount++;
    const questionContainer = document.getElementById("questionContainer");

    // Create a new question box div with a unique ID
    const questionBox = document.createElement("div");
    questionBox.classList.add("question-box");
    questionBox.id = `questionBox${questionCount}`;
    questionBox.innerHTML = `
    <table class="questionbox">
    <tr>
        <td>
            <textarea id="qname${questionCount}" name="qname${questionCount}" placeholder="Question Name" class="qname" autocomplete="off" required oninput="autoExpand(this)"></textarea><br><br>
            <select id="qtype${questionCount}" name="qtype${questionCount}" class="dropdown" required onchange="selectqtype(${questionCount})">
                <option value="" disabled selected hidden>Select Question Type</option>
                <option value="MCQ">Multiple Choice Question</option>
                <option value="shortans">Short Answer</option>
                <option value="Checkboxes">Checkboxes</option>
            </select>
        </td>
        <td width="5%">
            <button class="remove" type="button" onclick="removeQuestionBox(${questionCount})">
                <span>&#10005;</span>
            </button>
        </td>
    </tr>
    <tr>
        <td colspan="2" id="dynamicContent${questionCount}">
            <p>Select a question type to see specific content here.</p>
        </td>
    </tr>
</table>

    `;

    questionContainer.appendChild(questionBox);

    // Trigger animation after adding to DOM
    setTimeout(() => {
        questionBox.classList.add("animate-in");
    }, 10); // Slight delay to ensure the class is applied after insertion
}


function removeQuestionBox(questionId, event) {
    if (event) event.preventDefault(); // Prevents form submission if the button defaults to type="submit"

    const questionBox = document.getElementById(`questionBox${questionId}`);
    if (questionBox) {
        questionBox.classList.add("animate-out");
        questionBox.addEventListener(
            "transitionend",
            () => {
                questionBox.remove();
            },
            { once: true }
        );
    }
}


function selectqtype(questionId) {
    const dropdown = document.getElementById(`qtype${questionId}`);
    const selectedValue = dropdown.value;
    const dynamicContent = document.getElementById(`dynamicContent${questionId}`);

    dynamicContent.innerHTML = '';

    if (selectedValue === "MCQ") {
        dynamicContent.innerHTML = `
            <div class="option-item">
                <input type="radio" name="mcq${questionId}">
                <textarea class="option-label" placeholder="Enter text" rows="1"
                          oninput="autoExpand(this)"></textarea>
                <button type="button" class="remove-option-btn" onclick="removeSpecificOption(this)">Remove</button>
            </div>
            <div class="option-item">
                <input type="radio" name="mcq${questionId}">
                <textarea class="option-label" placeholder="Enter text" rows="1"
                          oninput="autoExpand(this)"></textarea>
                <button type="button" class="remove-option-btn" onclick="removeSpecificOption(this)">Remove</button>
            </div>
            <button type="button" class="add-option-btn" onclick="addOption(${questionId})">Add Option</button>
        `;
    } else if (selectedValue === "shortans") {
        dynamicContent.innerHTML = `<input type="text" placeholder="Your answer here">`;
    } else if (selectedValue === "Checkboxes") {
        dynamicContent.innerHTML = `
            <div class="option-item">
                <input type="checkbox" name="checkbox${questionId}">
                <textarea class="option-label" placeholder="Enter option text" rows="1"
                          oninput="autoExpand(this)"></textarea>
                <button type="button" class="remove-option-btn" onclick="removeSpecificOption(this)">Remove</button>
            </div>
            <div class="option-item">
                <input type="checkbox" name="checkbox${questionId}">
                <textarea class="option-label" placeholder="Enter option text" rows="1"
                          oninput="autoExpand(this)"></textarea>
                <button type="button" class="remove-option-btn" onclick="removeSpecificOption(this)">Remove</button>
            </div>
            <button type="button" class="add-option-btn" onclick="addOption(${questionId}, true)">Add Option</button>
        `;
    }
}

function clearText(input) {
    if (input.value === input.defaultValue) {
        input.value = '';
    }
}

function restoreText(input, defaultText) {
    if (input.value === '') {
        input.value = defaultText;
    }
}

function addOption(questionId, isCheckbox = false) {
    const dynamicContent = document.getElementById(`dynamicContent${questionId}`);

    const optionDiv = document.createElement("div");
    optionDiv.classList.add("option-item");
    optionDiv.innerHTML = `
        <input type="${isCheckbox ? 'checkbox' : 'radio'}" name="${isCheckbox ? 'checkbox' : 'mcq'}${questionId}">
            <textarea class="option-label" placeholder="Enter option text" rows="1"
            oninput="autoExpand(this)"></textarea>
        <button type="button" class="remove-option-btn" onclick="removeSpecificOption(this)">Remove</button>
    `;

    const addButton = dynamicContent.querySelector(".add-option-btn");
    dynamicContent.insertBefore(optionDiv, addButton);
}

function removeSpecificOption(button) {
    const optionDiv = button.parentElement;
    const dynamicContent = optionDiv.parentElement;

    const option = dynamicContent.querySelectorAll('.option-item');
    if (option.length > 2) {
        dynamicContent.removeChild(optionDiv);
    } else {
        alert("You must have at least 2 option.");
    }
}

function autoExpand(textarea) {
    textarea.style.height = "auto"; // Reset height to calculate scrollHeight accurately.
    if (textarea.scrollHeight > textarea.offsetHeight) {
        textarea.style.height = `${textarea.scrollHeight}px`; // Expand only if content overflows.
    }
}


function submitQuiz(event) {
    event.preventDefault();

    // Determine if it's a save or submit action based on the button clicked
    const action = event.target.id === "saveQuizButton" ? "save" : "submit"; // Fixed the button ID for "Save"
    const confirmation = confirm(`Are you sure you want to ${action} this quiz?`);

    if (!confirmation) {
        // If the user cancels, exit the function
        console.log(`${action} canceled by user.`);
        return;
    }

    const quizMetadata = {
        name: document.getElementById("qname").value,
        description: document.getElementById("description").value,
        grade: document.getElementById("grade").value,
        quizType: document.getElementById("type").value,
        subject: document.getElementById("subject").value,
        folderName: document.getElementById("folder").value,
        course: document.getElementById("course").value,
        status: action === "save" ? 0 : 1, // Set status based on the action (save = 0, submit = 1)
        question: []
    };

    // Collect question data
    for (let i = 1; i <= questionCount; i++) {
        const questionBox = document.getElementById(`questionBox${i}`);
        if (questionBox) {
            const questionData = {
                questionName: document.getElementById(`qname${i}`).value,
                questionType: document.getElementById(`qtype${i}`).value,
                option: [],
                answer: null
            };

            const dynamicContent = document.getElementById(`dynamicContent${i}`);
            if (questionData.questionType === "MCQ" || questionData.questionType === "Checkboxes") {
                const optionElements = dynamicContent.querySelectorAll('.option-item');
                optionElements.forEach((optionElement) => {
                    const optionText = optionElement.querySelector('textarea').value;
                    const isCorrect = optionElement.querySelector('input[type="radio"], input[type="checkbox"]').checked ? 1 : 0;
                    questionData.option.push({
                        name: optionText,
                        accuracy: isCorrect
                    });
                });
            } else if (questionData.questionType === "shortans") {
                const answerInput = dynamicContent.querySelector('input[type="text"]');
                if (answerInput) {
                    questionData.option.push({ name: answerInput.value, accuracy: 1 });
                    questionData.answer = answerInput.value;
                }
            }

            quizMetadata.question.push(questionData);
        }
    }

    const quizData = JSON.stringify(quizMetadata);

    // Send data to PHP
    fetch('submit_quiz.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: quizData
    })
    window.location.replace('teachhome.php');
}
    

document.addEventListener('DOMContentLoaded', function () {
    const quizTypeSelect = document.getElementById('type');
    const gradeSelect = document.getElementById('grade');
    const courseSelect = document.getElementById('course');
    const subjectSelect = document.getElementById('subject');
    const folderSelect = document.getElementById('folder');

    // Store the initial state of the select elements
    const initialState = {
        grade: gradeSelect.innerHTML,
        course: courseSelect.innerHTML,
        subject: subjectSelect.innerHTML,
        folder: folderSelect.innerHTML,
    };

    // Disable all select elements initially
    gradeSelect.disabled = true;
    courseSelect.disabled = true;
    subjectSelect.disabled = true;
    folderSelect.disabled = true;

    quizTypeSelect.addEventListener('change', function () {
        // Reset all select elements to their original state
        gradeSelect.innerHTML = initialState.grade;
        courseSelect.innerHTML = initialState.course;
        subjectSelect.innerHTML = initialState.subject;
        folderSelect.innerHTML = initialState.folder;

        gradeSelect.disabled = true;
        courseSelect.disabled = true;
        subjectSelect.disabled = true;
        folderSelect.disabled = true;

        switch (quizTypeSelect.value) {
            case 'Exercise':
                // Enable the course and folder selects
                courseSelect.disabled = false;
                break;

            case 'Test':
                // Enable only the course select
                courseSelect.disabled = false;
                break;

            case 'Assessment':
                // Enable the grade and subject selects
                gradeSelect.disabled = false;
                subjectSelect.disabled = false;
                break;

            default:
                break;
        }
    });

    // Handle courseSelect change for Exercise quiz type
    courseSelect.addEventListener('change', function () {
        if (quizTypeSelect.value === 'Exercise' && courseSelect.value) {
            folderSelect.disabled = false;
            folderSelect.innerHTML = '<option value="" disabled selected hidden>Select Folder</option>';

            // Fetch folders for the selected course
            fetch('/teacherphp/get_folders.php?course_id=' + encodeURIComponent(courseSelect.value))
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok ' + response.statusText);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data && Array.isArray(data)) {
                        data.forEach(folder => {
                            const option = document.createElement('option');
                            option.value = folder.folder_id;
                            option.textContent = folder.folder_name;
                            folderSelect.appendChild(option);
                        });
                    } else {
                        folderSelect.innerHTML = '<option value="">No folders available</option>';
                    }
                })
                .catch(error => {
                    console.error('Error fetching folders:', error);
                    folderSelect.innerHTML = '<option value="">Error fetching folders</option>';
                });
        } else {
            folderSelect.disabled = true;
            folderSelect.innerHTML = initialState.folder;
        }
    });
});


    
    function editQuiz(quizId) {
        window.location.href = `edit_quiz.php?quiz_id=${quizId}`;
    }

