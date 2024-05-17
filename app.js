let currentListId = 0;
let lists = [];

document.addEventListener('DOMContentLoaded', () => {
    document.addEventListener('click', handleDocumentClick);
    loadLists();
    window.actions = {
        add_list_to_db: AddToDoList,
        add_task_to_list: HandleAddTask,
        delete_list: ConfirmDeleteList,
        change_list_name: ChangeListName,
        sort_lists: SortLists,
        view_list: ViewList,
        toggle_task_status: ToggleTaskStatus,
        delete_task: ConfirmDeleteTask,
        show_data: ShowData
    };
});

function handleDocumentClick(e) {
    const actionKey = e.target.dataset.click;
    const action = window.actions[actionKey];
    if (action) {
        action(e);
    } else {
        console.warn(`No action found for ${actionKey}`);
    }
}

async function loadLists() {
    const response = await fetch('./api/helpers/data.json');
    lists = await response.json();
    displayLists();
}

function displayLists() {
    const listsContainer = document.getElementById('lists');
    listsContainer.innerHTML = '';
    lists.forEach(list => {
        const listItem = document.createElement('li');
        listItem.textContent = list.list;
        listItem.classList.add('clickable-list-item');
        listItem.dataset.click = 'view_list';
        listItem.dataset.listId = list.listid;
        listsContainer.appendChild(listItem);
    });
}

async function AddToDoList(event) {
    const listNameInput = document.getElementById('list-name');
    const listName = listNameInput.value;
    if (!listName) {
        alert('Please provide a list name.');
        return;
    }

    const newListId = lists.length ? Math.max(...lists.map(list => parseInt(list.listid, 10))) + 1 : 1;
    const timestamp = new Date().toISOString();

    const newList = {
        list: listName,
        listid: newListId.toString(),
        tasks: {},
        created: timestamp
    };

    lists.push(newList);
    await saveLists();
    listNameInput.value = '';
    displayLists();
}

async function saveLists() {
    await fetch('./api/helpers/data.json', {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(lists, null, 2)
    });
}

function ViewList(e) {
    const listId = e.target.dataset.listId;
    const list = lists.find(list => list.listid === listId);

    if (list) {
        currentListId = listId;
        document.getElementById('list-name-header').textContent = list.list;
        document.getElementById('list-details-container').style.display = 'block';
        const listDetailsContainer = document.getElementById('list-details');
        listDetailsContainer.innerHTML = '';

        for (const [taskId, taskDetails] of Object.entries(list.tasks)) {
            const taskItem = document.createElement('li');
            taskItem.classList.add('list-details-item');
            taskItem.innerHTML = `
                <input type="checkbox" data-click="toggle_task_status" data-task-id="${taskId}" ${taskDetails.completed ? 'checked' : ''}>
                ${taskDetails.name} <small>(${taskDetails.timestamp})</small>
                <button data-click="delete_task" data-task-id="${taskId}">Delete</button>
            `;
            listDetailsContainer.appendChild(taskItem);
        }
    }
}

async function HandleAddTask(event) {
    const taskDetailsInput = document.getElementById('task-details');
    const taskDetails = taskDetailsInput.value;
    if (!taskDetails) {
        alert('Please provide a task.');
        return;
    }

    const list = lists.find(list => list.listid === currentListId);
    if (list) {
        const newTaskId = `task${Object.keys(list.tasks).length + 1}`;
        const timestamp = new Date().toISOString();
        list.tasks[newTaskId] = { name: taskDetails, completed: false, timestamp: timestamp };
        await saveLists();
        ViewList({ target: { dataset: { listId: currentListId } } });
        taskDetailsInput.value = '';
    }
}

async function ToggleTaskStatus(e) {
    const taskId = e.target.dataset.taskId;
    const list = lists.find(list => list.listid === currentListId);

    if (list) {
        list.tasks[taskId].completed = e.target.checked;
        await saveLists();
    }
}

async function ConfirmDeleteTask(e) {
    const taskId = e.target.dataset.taskId;
    if (confirm("Are you sure you want to delete this task?")) {
        DeleteTask(taskId);
    }
}

async function DeleteTask(taskId) {
    const list = lists.find(list => list.listid === currentListId);

    if (list) {
        delete list.tasks[taskId];
        await saveLists();
        ViewList({ target: { dataset: { listId: currentListId } } });
    }
}

async function ConfirmDeleteList() {
    if (confirm("Are you sure you want to delete this list?")) {
        DeleteList();
    }
}

async function DeleteList() {
    lists = lists.filter(list => list.listid !== currentListId);
    await saveLists();
    document.getElementById('list-details-container').style.display = 'none';
    displayLists();
}

async function ChangeListName() {
    const newName = prompt('Enter new list name:');
    if (newName) {
        const list = lists.find(list => list.listid === currentListId);
        if (list) {
            list.list = newName;
            await saveLists();
            document.getElementById('list-name-header').textContent = newName;
            displayLists();
        }
    }
}

function SortLists() {
    const sortBy = document.getElementById('sort-by').value;
    const sortOrder = document.getElementById('sort-order').value;

    lists.sort((a, b) => {
        let comparison = 0;
        if (sortBy === 'name') {
            comparison = a.list.localeCompare(b.list);
        } else if (sortBy === 'created') {
            comparison = new Date(a.created) - new Date(b.created);
        }

        return sortOrder === 'asc' ? comparison : -comparison;
    });

    displayLists();
}

async function ShowData() {
    const response = await fetch('./api/helpers/data.json');
    const data = await response.json();
    document.getElementById('data-display').textContent = JSON.stringify(data, null, 2);
}
