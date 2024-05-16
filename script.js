const NOTES_LIST_SELECTOR = '#notesList';
const NEW_NOTE_SELECTOR = '#newNote';
const TITLE_SELECTOR = '#title';
const CONTENT_SELECTOR = '#content';
const SAVE_BUTTON_SELECTOR = '.note .header .save';
const DELETE_BUTTON_SELECTOR = '.note .header .delete';
const SEARCH_SELECTOR = '#search';
const CANCEL_BUTTON_SELECTOR = '.note .header .cancel';

const notesList = document.querySelector(NOTES_LIST_SELECTOR);
const newNote = document.querySelector(NEW_NOTE_SELECTOR);
const title = document.querySelector(TITLE_SELECTOR);
const content = document.querySelector(CONTENT_SELECTOR);
const saveButton = document.querySelector(SAVE_BUTTON_SELECTOR);
const deleteButton = document.querySelector(DELETE_BUTTON_SELECTOR);
const search = document.querySelector(SEARCH_SELECTOR);
const cancelButton = document.querySelector(CANCEL_BUTTON_SELECTOR);

let notes = [];
let noteId;

function createNoteElement(note) {
  const noteElement = document.createElement('div');
  noteElement.classList.add('note-card');
  noteElement.innerHTML = `
    <div class="title">${note.title}</div>
    <div class="content">${truncateText(note.content, 100)}</div>
  `;
  noteElement.dataset.id = note.id;
  noteElement.onclick = function (e) {
    if (e.target.tagName === 'DIV' && e.target.classList.contains('title')) {
      const noteTitle = e.target.textContent;
      const selectedNote = notes.find(note => note.title === noteTitle);
      if (selectedNote) {
        title.value = selectedNote.title;
        content.value = selectedNote.content;
        noteId = selectedNote.id;
        document.getElementById('noteContent').style.display = 'block';
      } else {
        console.error(`Note not found: ${noteTitle}`);
      }
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


newNote.onclick = function () {
  document.getElementById('noteContent').style.display = 'block';
  title.value = '';
  content.value = '';
  noteId = null;
};

deleteAll.onclick = function () {
  const confirmDelete = confirm('Are you sure you want to delete all notes?');
  if (confirmDelete) {
    if (notes.length === 0) {
      alert('There are no notes to delete.');
      return;
    }
    else {
      notes = [];
    alert('All notes have been deleted.');
    updateNotes();
    localStorage.setItem('notes', JSON.stringify(notes));
    }
  }
};

saveButton.onclick = function () {
  const noteTitle = title.value;
  const noteContent = content.value;
  const existingNoteIndex = notes.findIndex(note => note.id === noteId);

  const noteIsEmpty = !noteTitle.trim();

  if (noteIsEmpty) {
    alert('Error: Note title cannot be empty.');
    return;
  }

  if (existingNoteIndex !== -1) {
    notes[existingNoteIndex].title = noteTitle;
    notes[existingNoteIndex].content = noteContent;
  } else {
    const newNote = {
      id: Date.now(),
      title: noteTitle,
      content: noteContent
    };
    notes.push(newNote);
    title.value = '';
    content.value = '';
  }

  updateNotes();
  document.getElementById('noteContent').style.display = 'none';
  localStorage.setItem('notes', JSON.stringify(notes));
};

deleteButton.onclick = function () {
  const noteId = notes.find(note => note.title === title.value).id;
  notes = notes.filter(note => note.id !== noteId);
  updateNotes();
  document.getElementById('noteContent').style.display = 'none';
  localStorage.setItem('notes', JSON.stringify(notes));
};

search.oninput = function () {
  const searchText = search.value.toLowerCase();
  const filteredNotes = notes.filter(note => note.title.toLowerCase().includes(searchText));
  updateNotes(filteredNotes);
};

cancelButton.onclick = function () {
  document.getElementById('noteContent').style.display = 'none';
};


document.addEventListener('DOMContentLoaded', function () {
  const storedNotes = localStorage.getItem('notes');
  if (storedNotes) {
    notes = JSON.parse(storedNotes);
    updateNotes();
  }
});
