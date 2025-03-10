//event div movement
window.onload = function() {
    const div = document.getElementById('dok-event-container')
    const dokContent = document.getElementById('dok-container')

    const contentTopPosition = dokContent.getBoundingClientRect().top + window.scrollY
    div.style.top = contentTopPosition + 'px'

    window.addEventListener('scroll', function() {
        const scrollPosition = window.scrollY
        let smoothTop = contentTopPosition - scrollPosition

        smoothTop = Math.max(smoothTop, 0)
        div.style.top = smoothTop + 'px'
    })
}

//event date 
document.addEventListener('DOMContentLoaded', function() {
    const eventDate = document.getElementById('event-date')
    const today = new Date().toISOString().split('T')[0]
    const dateInput = document.getElementById('dok-event-date')
    dateInput.setAttribute('min', today)

    document.querySelectorAll('input[name="add-event"]').forEach((radio) => {
        radio.addEventListener('change', function() {
            eventDate.hidden = document.querySelector('input[name="add-event"]:checked').value != "igen"
        })
    })
})

//vote popup
const popup = document.getElementById('voteWindow')

function Vote() {
    popup.showModal()
}

document.getElementById('closeVote').addEventListener('click', function () {
    popup.close()
})

document.getElementById('addVote').addEventListener('click', function() {
    var newInput = document.createElement('input')
    newInput.type = 'text'
    newInput.maxLength = '19'
    newInput.name = 'input' + (document.querySelectorAll('#votes-container input').length)
    document.getElementById('votes-container').appendChild(newInput)
    document.getElementById('votes-container').appendChild(document.createElement('br'))
})

//file display
const fileInput = document.getElementById('post-file');
const fileList = document.getElementById('file-list');
let filesArray = [];

fileInput.addEventListener('change', function(event) {
    const newFiles = Array.from(event.target.files);
    filesArray = [...filesArray, ...newFiles];

    renderFileList();
});

function renderFileList() {
    fileList.innerHTML = '';
    filesArray.forEach((file, index) => {
        const li = document.createElement('li');
        li.textContent = file.name + ' ';
        const removeBtn = document.createElement('span');
        removeBtn.textContent = '❌';
        removeBtn.style.cursor = 'pointer';
        removeBtn.onclick = () => {
            filesArray.splice(index, 1);
            renderFileList();
        };
        li.appendChild(removeBtn);
        fileList.appendChild(li);
    });

    updateFileInput();
}

function updateFileInput() {
    const dataTransfer = new DataTransfer();
    filesArray.forEach(file => dataTransfer.items.add(file));
    fileInput.files = dataTransfer.files;
}

//post management
function Menu(e, postid) {
    let manageMenu = document.getElementById("postManageMenu"+postid)

    if (e.style.border !== "1px solid grey") {
        e.style.border = "1px solid grey"
        e.style.backgroundColor = "rgba(0, 0, 0, 0.2)"
        manageMenu.style.display= "block"

        document.addEventListener("click", function closeMenu(event) {
            if (!manageMenu.contains(event.target) && event.target !== e) {
                e.style.backgroundColor = ""
                e.style.border = "none"
                manageMenu.style.display = "none"
                document.removeEventListener("click", closeMenu)
            }
        })
    } else {
        e.style.backgroundColor = "";
        e.style.border = "none"
        manageMenu.style.display= "none"
    }
}
