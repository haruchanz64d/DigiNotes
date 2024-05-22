const NOTES_LIST_SELECTOR = '#notesList';
const TITLE_SELECTOR = '#title';
const CONTENT_SELECTOR = '#content';
const SAVE_BUTTON_SELECTOR = '.note .header .save';
const DELETE_BUTTON_SELECTOR = '.note .header .delete';
const SEARCH_SELECTOR = '#search';
const CANCEL_BUTTON_SELECTOR = '.note .header .cancel';
const DELETE_ALL_BUTTON_SELECTOR = '#deleteAll';
const TOAST_CONTAINER_SELECTOR = '#toastContainer';

const notesList = document.querySelector(NOTES_LIST_SELECTOR);
const title = document.querySelector(TITLE_SELECTOR);
const content = document.querySelector(CONTENT_SELECTOR);
const saveButton = document.querySelector(SAVE_BUTTON_SELECTOR);
const deleteButton = document.querySelector(DELETE_BUTTON_SELECTOR);
const search = document.querySelector(SEARCH_SELECTOR);
const cancelButton = document.querySelector(CANCEL_BUTTON_SELECTOR);
const deleteAllButton = document.querySelector(DELETE_ALL_BUTTON_SELECTOR);
const toastContainer = document.querySelector(TOAST_CONTAINER_SELECTOR);

const modal = document.getElementById('modal');
const cancelDeleteAllButton = document.getElementById('cancelDelete');
const confirmDeleteAllButton = document.getElementById('confirmDelete');
const closeButton = document.getElementById('closeModal');
const modalMessage = document.getElementById('modalMessage');

let notes = [];
let noteId;

function createToast(message, type) {
  const toast = document.createElement('div');
  toast.classList.add('toast', type);
  toast.innerHTML = `
      <span class="icon">${type === 'success' ? '<i class="fa-solid fa-check"></i>' : '<i class="fa-solid fa-x"></i>'}</span>
      <span>${message}</span>
  `;
  toastContainer.appendChild(toast);

  // Show the toast
  setTimeout(() => {
      toast.classList.add('show');
  }, 100);

  // Hide the toast after 3 seconds
  setTimeout(() => {
      toast.classList.remove('show');
      // Remove the toast from DOM after the transition ends
      setTimeout(() => {
          toast.remove();
      }, 500);
  }, 3000);
}

function createNoteElement(note) {
    const noteElement = document.createElement('div');
    noteElement.classList.add('note-card');
    noteElement.innerHTML = `
        <div class="title">${note.title}</div>
        <div class="content">${truncateText(note.content, 100)}</div>
    `;
    noteElement.dataset.id = note.id;
    noteElement.onclick = function () {
        const selectedNote = notes.find(n => n.id === note.id);
        if (selectedNote) {
            setNoteContentForEditing(selectedNote); // Set up for editing mode
        } else {
            console.error(`Note not found: ${note.id}`);
        }
    };
    return noteElement;
}

function truncateText(text, maxLength) {
    if (text.length <= maxLength) {
        return text;
    }
    return text.slice(0, maxLength) + '...';
}

function updateNotes(notesToUpdate = notes) {
    notesList.innerHTML = '';
    notesToUpdate.forEach(note => {
        const noteElement = createNoteElement(note);
        notesList.appendChild(noteElement);
    });
}
saveButton.onclick = function () {
    const noteTitle = title.value;
    const noteContent = content.value;
    const existingNoteIndex = notes.findIndex(note => note.id === noteId);

    const noteIsEmpty = !noteTitle.trim();
    const maxTitleLength = 50;

    if (noteTitle.length > maxTitleLength) {
        createToast(`Note title cannot exceed ${maxTitleLength} characters.`, 'error');
        return;
    }
    if (noteIsEmpty) {
        createToast('Note title cannot be empty.', 'error');
        return;
    }

    if (existingNoteIndex !== -1) {
        notes[existingNoteIndex].title = noteTitle;
        notes[existingNoteIndex].content = noteContent;
        createToast('Note updated successfully.', 'success');
    } else {
        const newNote = {
            id: Date.now(),
            title: noteTitle,
            content: noteContent
        };
        notes.push(newNote);
        createToast('Note added successfully.', 'success');
    }

    updateNotes();
    localStorage.setItem('notes', JSON.stringify(notes));

    setNoteContentForDefault(); // Switch back to default mode after saving
};
deleteAllButton.onclick = function () {
    if (notes.length === 0) {
        createToast("No notes to delete.");
    } else {
        showModal('Are you sure you want to delete all notes?', true);
    }
};

confirmDeleteAllButton.onclick = function () {
    // Clear all notes
    notes = [];
    updateNotes();
    localStorage.setItem('notes', JSON.stringify(notes));
    title.value = '';
    content.value = '';
    closeModal();
    createToast('All notes deleted successfully.', 'success');
};

cancelDeleteAllButton.onclick = function () {
    closeModal();
};

deleteButton.onclick = function () {
    if (noteId !== null) {
        notes = notes.filter(note => note.id !== noteId);
        createToast('Note deleted successfully.', 'success');
        updateNotes();
        localStorage.setItem('notes', JSON.stringify(notes));
    }
    setNoteContentForDefault(); // Switch back to default mode after deletion
};

window.onclick = function (event) {
    if (event.target == modal) {
        closeModal();
    }
};

closeButton.onclick = function () {
    closeModal();
};

search.oninput = function () {
    const searchText = search.value.toLowerCase();
    const filteredNotes = notes.filter(note => note.title.toLowerCase().includes(searchText));
    updateNotes(filteredNotes);
};

cancelButton.onclick = function () {
    setNoteContentForDefault(); // Switch back to default mode on cancel
    createToast('Note cleared.', 'success');
};

document.addEventListener('DOMContentLoaded', function () {
    const storedNotes = localStorage.getItem('notes');
    if (storedNotes) {
        notes = JSON.parse(storedNotes);
        updateNotes();
    }
    setupDefaultMode();
});

function showModal(message, confirmation = false, isOkButton = false) {
    modalMessage.textContent = message;

    if (isOkButton) {
        cancelDeleteAllButton.textContent = 'OK';
        cancelDeleteAllButton.style.backgroundColor = '#4CAF50'; // Green color
        cancelDeleteAllButton.style.display = 'block';
        confirmDeleteAllButton.style.display = 'none';
    } else {
        cancelDeleteAllButton.textContent = 'No';
        cancelDeleteAllButton.style.backgroundColor = '#f44336'; // Red color
        confirmDeleteAllButton.style.display = confirmation ? 'block' : 'none';
        cancelDeleteAllButton.style.display = 'block';
    }

    modal.style.display = 'block';
}

function closeModal() {
    modal.style.display = 'none';
}


function setupEditNoteMode() {
    saveButton.innerHTML = '<i class="fa-sharp fa-solid fa-floppy-disk"></i>';
    deleteButton.innerHTML = '<i class="fa-sharp fa-solid fa-trash"></i>';
}

function setupDefaultMode() {
    saveButton.innerHTML = '<i class="fa-sharp fa-solid fa-plus"></i>';
    cancelButton.innerHTML = '<i class="fa-sharp fa-solid fa-eraser"></i>';
}

// Function to set up the note content for editing mode
function setNoteContentForEditing(note) {
    title.value = note.title;
    content.value = note.content;
    noteId = note.id;
    document.getElementById('noteContent').style.display = 'block';
    setupEditNoteMode(); // Switch to edit mode icons
}

// Function to set up the note content for default mode
function setNoteContentForDefault() {
    title.value = '';
    content.value = '';
    noteId = null;
    document.getElementById('noteContent').style.display = 'block';
    setupDefaultMode(); // Switch to default mode icons
}