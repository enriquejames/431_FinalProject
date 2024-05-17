let current_id = 0
document.addEventListener('DOMContentLoaded', () => { // Corrected here
    document.addEventListener('click', handleDocumentClick);

    window.actions = {
        add_list: AddToDoList,
        add_list_to_db: AskForTasks
        // Removed the delete action since sortAction is undefined
    };
});

function handleDocumentClick(e) {
    // Prevent default action if needed
    e.preventDefault();
    
    // Determine the action based on the data-click attribute
    const actionKey = e.target.dataset.click;
    const action = window.actions[actionKey];
    
    // Execute the action if it exists
    if (action) {
        action(e); // Pass the event object if needed
    } else {
        console.warn(`No action found for ${actionKey}`);
    }
}

// Declare loadView in the global scope
async function loadView(viewUrl, className) {
    try {
        const response = await fetch(viewUrl);
        const html = await response.text();
        const elements = document.getElementsByClassName(className);
        if (elements.length > 0) {
            elements[0].innerHTML = html;
        } else {
            console.error(`No elements found with class name "${className}".`);
        }
    } catch (error) {
        console.error('Error loading view:', error);
    }
}


async function AddToDoList(event) {
    event.preventDefault(); // Prevent the default form submission behavior
    await loadView('./views/add_to_do_list.html', 'todo-list');
    const listNameInput = document.getElementById('list-name');
    const listIdInput = document.getElementById('list-id');

    // Read the JSON file
    const response = await fetch('./api/helpers/data.json');
    const data = await response.json();

    // Find the highest listid and increment it by one
    let highestId = 0;
    data.forEach(item => {
        const currentId = parseInt(item.listid, 10);
        if (currentId > highestId) {
            highestId = currentId;
        }
    });
    const newListId = highestId + 1;

    // Assign the new ID to the input field
    listIdInput.value = newListId;

    //saved so can add tasks
    current_id = newListId
}

function AskForTasks() {
    const listNameInput = document.getElementById('list-name');
    const listIdInput = document.getElementById('list-id'); // Correctly gets the input element
    console.log(`Adding list: ${listNameInput.value}, ID: ${listIdInput.value}`); // Logs the values of both inputs
}
