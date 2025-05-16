document.addEventListener('DOMContentLoaded', function () {
    const quizTypeSelect = document.getElementById('type');
    const gradeSelect = document.getElementById('grade');
    const courseSelect = document.getElementById('course');
    const folderSelect = document.getElementById('folder');
    const subjectSelect = document.getElementById('subject');

    // Save initial course and folder options
    const initialCourseHTML = courseSelect.innerHTML;
    const initialFolderHTML = folderSelect.innerHTML;

    let isInitialized = true; // Track first-time initialization

    function initializeSelections() {
        const quizType = quizTypeSelect.value;

        // Disable all initially
        gradeSelect.disabled = true;
        courseSelect.disabled = true;
        subjectSelect.disabled = true;
        folderSelect.disabled = true;

        switch (quizType) {
            case 'Exercise':
                courseSelect.disabled = false;
                folderSelect.disabled = false;
                break;

            case 'Test':
                courseSelect.disabled = false;
                break;

            case 'Assessment':
                gradeSelect.disabled = false;
                subjectSelect.disabled = false;
                break;

            default:
                break;
        }
    }

    // Initialize on page load
    initializeSelections();

    // React to quiz type change
    quizTypeSelect.addEventListener('change', function () {
        initializeSelections();

        // Reset course and folder selects to placeholders after the first initialization
        if (!isInitialized) {
            courseSelect.innerHTML = '<option value="" disabled selected hidden>Select Course</option>' + initialCourseHTML;
            folderSelect.innerHTML = initialFolderHTML;
            folderSelect.disabled = true;
        }
        isInitialized = false; // Ensure placeholder logic only applies after the first change
    });

    // Handle courseSelect change for Exercise quiz type
    courseSelect.addEventListener('change', function () {
        if (quizTypeSelect.value === 'Exercise' && courseSelect.value) {
            folderSelect.disabled = false;
            folderSelect.innerHTML = '<option value="" disabled selected hidden>Select Folder</option>';

            // Fetch folders for the selected course
            fetch('/Assignment/get_folders.php?course_id=' + encodeURIComponent(courseSelect.value))
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
            folderSelect.innerHTML = initialFolderHTML;
        }
    });
});






// Add event listener for radio and checkbox input changes
document.addEventListener('change', function (event) {
if (event.target.type === 'radio' || event.target.type === 'checkbox') {
    const questionId = event.target.name.split('[')[0].replace('option', '');
    updateCorrectAnswer(questionId, event.target);
}
});


function selectqtype(questionId) {
const qtype = document.getElementById(`qtype${questionId}`).value;
const dynamicContent = document.getElementById(`dynamicContent${questionId}`);
if (qtype === "MCQ") {
    dynamicContent.innerHTML = `
        <div class="option-item">
            <input type="radio" name="MCQ${questionId}">
            <textarea class="option-label" placeholder="Enter option text" rows="1"
                      oninput="autoExpand(this)"></textarea>
            <button type="button" class="remove-option-btn" onclick="removeSpecificOption(this, ${questionId})">Remove</button>
        </div>
        <div class="option-item">
            <input type="radio" name="MCQ${questionId}">
            <textarea class="option-label" placeholder="Enter option text" rows="1"
                      oninput="autoExpand(this)"></textarea>
            <button type="button" class="remove-option-btn" onclick="removeSpecificOption(this, ${questionId})">Remove</button>
        </div>
        <button type="button" class="add-option-btn" onclick="addOption(${questionId}, 'MCQ')">Add Option</button>
    `;
} else if (qtype === "Checkboxes") {
    dynamicContent.innerHTML = `
        <div class="option-item">
            <input type="checkbox" name="Checkboxes${questionId}">
            <textarea class="option-label" placeholder="Enter option text" rows="1"
                      oninput="autoExpand(this)"></textarea>
            <button type="button" class="remove-option-btn" onclick="removeSpecificOption(this, ${questionId})">Remove</button>
        </div>
        <div class="option-item">
            <input type="checkbox" name="Checkboxes${questionId}">
            <textarea class="option-label" placeholder="Enter option text" rows="1"
                      oninput="autoExpand(this)"></textarea>
            <button type="button" class="remove-option-btn" onclick="removeSpecificOption(this, ${questionId})">Remove</button>
        </div>
        <button type="button" class="add-option-btn" onclick="addOption(${questionId}, 'Checkboxes')">Add Option</button>
    `;
} else if (qtype === "shortans") {
    dynamicContent.innerHTML = `
        <input type="text" placeholder="Your answer here">
    `;
} else {
    dynamicContent.innerHTML = '<p>Select a question type to see specific content here.</p>';
}
}

function addOption(questionId, type) {
const dynamicContent = document.getElementById(`dynamicContent${questionId}`);
const addButton = dynamicContent.querySelector(".add-option-btn"); // Locate the "Add Option" button

// Count the number of options already present
const optionItems = dynamicContent.querySelectorAll(".option-item");

// Only allow adding new option if there are less than 10 options (adjust limit as needed)
if (optionItems.length < 10) {
    // Create a new option item
    const optionItem = document.createElement("div");
    optionItem.classList.add("option-item");
    optionItem.innerHTML = `
        <input type="${type === 'MCQ' ? 'radio' : 'checkbox'}" name="${type}${questionId}">
        <textarea class="option-label" placeholder="Enter option text" rows="1" oninput="autoExpand(this)"></textarea>
        <button type="button" class="remove-option-btn" onclick="removeSpecificOption(this, ${questionId})">Remove</button>
    `;

    // Insert the new option item above the "Add Option" button
    dynamicContent.insertBefore(optionItem, addButton);
} else {
    alert("You can only add up to 10 options.");
}
}

function removeSpecificOption(button, questionId) {
const optionItems = document.querySelectorAll(`#dynamicContent${questionId} .option-item`);

// Only allow removal if there are more than 2 options
if (optionItems.length > 2) {
    const optionItem = button.parentElement;
    optionItem.remove();
} else {
    alert("You must have at least 2 options.");
}
}

// Update the DOM to add an options container in the PHP section

function resubmitQuiz(event, quiz_id) {
    event.preventDefault();

    const action = event.target.id === "saveQuizButton" ? "save" : "submit";
    const confirmation = confirm(`Are you sure you want to ${action} this quiz?`);

    if (!confirmation) {
        // If the user cancels, exit the function
        console.log(`${action} canceled by user.`);
        return;
    }
    

    const quizMetadata = {
        quiz_id: quiz_id,
        name: document.getElementById("quiz_name").value,
        description: document.getElementById("description").value,
        grade: document.getElementById("grade").value,
        quizType: document.getElementById("type").value,
        subject: document.getElementById("subject").value,
        folderName: document.getElementById("folder").value,
        course: document.getElementById("course").value,
        status: event.target.id === 'saveQuizButton' ? 0 : 1,
        question: []
    };

    console.log(quizMetadata);

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

    // Log the final quizMetadata
    console.log("Final quizMetadata:", quizMetadata);

    const quizData = JSON.stringify(quizMetadata);

    fetch('resubmit.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: quizData,  // Send the JSON body
    })
    .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.replace('sharedEditQuiz.php'); // Show success message
                     // Reload the page to reset the form
                } else {
                    window.location.replace('teachhome.php');
                }
            })
            
    // location.reload(true);
}


// Initialize questionCount from the current data in the container
// Initialize question count from existing question boxes
// Initialize questionCount from the existing question boxes in the DOM
// Initialize the global questionCount from the number of question boxes already in the DOM
// Initialize the global questionCount from the number of question boxes already in the DOM
// Get the number of existing question boxes from the database (initial question count)

function autoExpand(textarea) {
    textarea.style.height = "auto"; // Reset height to calculate scrollHeight accurately.
    if (textarea.scrollHeight > textarea.offsetHeight) {
        textarea.style.height = `${textarea.scrollHeight}px`; // Expand only if content overflows.
    }
}

function removeQuestionBox(questionId) {
const questionBox = document.getElementById(`questionBox${questionId}`);
if (questionBox) {
    questionBox.remove(); // Removes the question box from the DOM
}
}

function updateCorrectAnswer(questionId, selectedOption) {
    const options = document.querySelectorAll(`#dynamicContent${questionId} .option-item`);
    
    if (options.length === 0) {
        console.log('No options found for question:', questionId);
        return;
    }

    options.forEach(option => {
        const inputElement = option.querySelector('input[type="radio"], input[type="checkbox"]');
        if (inputElement === selectedOption) {
            inputElement.dataset.correct = 'true';
        } else {
            inputElement.dataset.correct = 'false';
        }
    });
}

