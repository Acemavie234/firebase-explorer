
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require __DIR__ . "/includes/functions.php";
require __DIR__ . "/includes/discord.php";
require __DIR__ . "/config.php";

if ($_SESSION['authenticated'] !== "true") {
    header('Location: https://acemavie.eu/firebase-explorer/index.php');
    exit();
}

$avatar_url = "https://cdn.discordapp.com/avatars/".$_SESSION['user_id']."/".$_SESSION['user_avatar'];

?>
<!DOCTYPE html>
<html>
<head>
  <title>Firebase Explorer</title>
  <script src="https://cdn.tailwindcss.com"></script>
<style>
loader {
	 position: relative;
	 width: 2.5em;
	 height: 2.5em;
	 transform: rotate(165deg);
}
 .loader:before, .loader:after {
	 content: '';
	 position: absolute;

	 display: block;
	 width: 0.5em;
	 height: 0.5em;
	 border-radius: 0.25em;
	 transform: translate(-50%, -50%);
}
 .loader:before {
	 animation: before 2s infinite;
}
 .loader:after {
	 animation: after 2s infinite;
}
 @keyframes before {
	 0% {
		 width: 0.5em;
		 box-shadow: 1em -0.5em rgba(225, 20, 98, 0.75), -1em 0.5em rgba(111, 202, 220, 0.75);
	}
	 35% {
		 width: 2.5em;
		 box-shadow: 0 -0.5em rgba(225, 20, 98, 0.75), 0 0.5em rgba(111, 202, 220, 0.75);
	}
	 70% {
		 width: 0.5em;
		 box-shadow: -1em -0.5em rgba(225, 20, 98, 0.75), 1em 0.5em rgba(111, 202, 220, 0.75);
	}
	 100% {
		 box-shadow: 1em -0.5em rgba(225, 20, 98, 0.75), -1em 0.5em rgba(111, 202, 220, 0.75);
	}
}
 @keyframes after {
	 0% {
		 height: 0.5em;
		 box-shadow: 0.5em 1em rgba(61, 184, 143, 0.75), -0.5em -1em rgba(233, 169, 32, 0.75);
	}
	 35% {
		 height: 2.5em;
		 box-shadow: 0.5em 0 rgba(61, 184, 143, 0.75), -0.5em 0 rgba(233, 169, 32, 0.75);
	}
	 70% {
		 height: 0.5em;
		 box-shadow: 0.5em -1em rgba(61, 184, 143, 0.75), -0.5em 1em rgba(233, 169, 32, 0.75);
	}
	 100% {
		 box-shadow: 0.5em 1em rgba(61, 184, 143, 0.75), -0.5em -1em rgba(233, 169, 32, 0.75);
	}
}
</style>
</head>
<body class="bg-gray-900">
<div class="container mx-auto bg-gray-900">
  <div class="flex justify-between items-center py-5 px-10">
    <div>
      <h1 class="text-3xl font-bold text-gray-200">Firebase Explorer</h1>
    </div>
    <div class="flex items-center">
      
      <p class="block rounded rounded-md text-gray-400 ml-10" id="totalFiles">Total Files: 5</p>
    </div>
  </div>

  <div class="flex items-center mb-4 px-10">
    <form id="file-upload-form">
      <div class="mb-4">
        <label for="file-upload" class="block rounded rounded-md text-gray-400">Select a file to upload:</label>
        <input type="file" id="file-upload" name="file-upload" class="rounded border bg-gray-800 transition duration-200 px-4 py-2 hover-bg-gray-700/50 border-gray-700 focus:ring-offset text-gray-300 hover:border-yellow-500 text-sm focus:z-10 focus:outline-none focus:ring-1 focus:border-yellow-500 sm:max-w-xs">
      </div>
      <button type="submit" class="rounded border bg-gray-800 transition duration-200 px-4 py-2 hover-bg-gray-700/50 border-gray-700 focus:ring-offset text-gray-300 hover:border-yellow-500 focus:border-yellow-500 text-sm focus:z-10 focus:outline-none focus:ring-1 focus:border-yellow-500 sm:max-w-xs" id="uploadButton">
        Upload
        
      </button>
      <p id="uploadLoader" class="loader hidden ml-28"></p>
      
    </form>
  </div>

  <hr class="my-4">

  <div class="image-list"></div>
</div>

<div id="image-modal" tabindex="-1" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-2xl max-h-full">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                   Image Content
                </h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="image-modal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <div class="p-4 md:p-5 space-y-4">
                <img id="modal-image" class="w-full max-h-full" alt="Modal Image">
            </div>
        </div>
    </div>
</div>
<!-- Modal for text files -->
<div id="text-modal" tabindex="-1" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-2xl max-h-full">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                    File Content
                </h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="text-modal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <div class="p-4 md:p-5 space-y-4">
                <textarea id="modal-text" class="w-full h-full bg-transparent border-none"></textarea>
            </div>
        </div>
    </div>
</div>

<!-- Modal for videos -->
<div id="video-modal" tabindex="-1" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-2xl max-h-full">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
         
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                    Video Content
                </h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="video-modal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <div class="p-4 md:p-5 space-y-4">
                <video id="modal-video" class="w-96 h-2/3" controls></video>
            </div>
        </div>
    </div>
</div>

<!-- Modal for audio files -->
<div id="audio-modal" tabindex="-1" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-2xl max-h-full">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Audio Content</h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="audio-modal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <div class="p-4 md:p-5 space-y-4">
                <audio id="modal-audio" class="w-full" controls></audio>
            </div>
        </div>
    </div>
</div>


<script src="https://www.gstatic.com/firebasejs/8.9.1/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.9.1/firebase-storage.js"></script>
<script>
  const firebaseConfig = {
   
  };
  firebase.initializeApp(firebaseConfig);

  const fileUploadForm = document.getElementById("file-upload-form");
  const fileUploadInput = document.getElementById("file-upload");
  const imageList = document.querySelector('.image-list');
  const uploadButton = document.getElementById("uploadButton");
  const uploadLoader = document.getElementById("uploadLoader");


  fileUploadForm.addEventListener("submit", (event) => {
    event.preventDefault();

    const file = fileUploadInput.files[0];
    if (file) {
      const storageRef = firebase.storage().ref();
      const fileRef = storageRef.child(file.name);

      // Display loader and change button text
      uploadButton.textContent = "Loading";
      uploadLoader.classList.remove("hidden");

      fileRef.put(file).then(() => {
        fileUploadForm.reset();
        uploadButton.textContent = "Upload";
        uploadLoader.classList.add("hidden");
      }).catch((error) => {
        console.error(error);
        uploadButton.textContent = "Upload";
        uploadLoader.classList.add("hidden");
      });
    }
  });

  function getDownloadURLs() {
    firebase.storage().ref().listAll().then((res) => {
        const imageContainer = document.querySelector('.image-list');
        // Clear previous content
        imageContainer.innerHTML = '';

        // Create a grid container to hold the image items
        const gridContainer = document.createElement('div');
        gridContainer.classList.add('grid', 'grid-cols-3', 'gap-4');
        res.items.forEach((itemRef) => {
            itemRef.getDownloadURL().then((url) => {
                const fileItem = document.createElement('div');
                fileItem.classList.add('rounded', 'overflow-hidden', 'shadow-lg', 'bg-gray-700');

                const fileMedia = document.createElement(itemRef.name.endsWith(".mp4") || itemRef.name.endsWith(".mov") ? 'video' : 'img');
                fileMedia.classList.add('w-full', 'h-64', 'object-cover');
                fileMedia.src = url;

                // Set autoplay for video
                if (itemRef.name.endsWith(".mp4")) {
                    fileMedia.autoplay = true;
                    fileMedia.loop = true;
                    fileMedia.muted = true; // Mute to avoid audio playback
                }

                fileItem.appendChild(fileMedia);

                const fileCardBody = document.createElement('div');
                fileCardBody.classList.add('p-4');
                fileItem.appendChild(fileCardBody);

                const fileTitle = document.createElement('h5');
                fileTitle.classList.add('text-xl', 'font-bold', 'mb-2', 'text-gray-300');
                fileTitle.textContent = itemRef.name;
                fileCardBody.appendChild(fileTitle);

                // Download button
                const downloadButton = document.createElement('a');
                downloadButton.classList.add('rounded', 'rounded-md', 'border', 'bg-gray-800', 'transition', 'duration-200', 'px-4', 'py-2', 'hover-bg-gray-700/50', 'border-gray-700', 'focus:ring-offset', 'text-gray-300', 'hover:border-yellow-500', 'focus:border-yellow-500', 'text-sm', 'focus:z-10', 'focus:outline-none', 'focus:ring-1', 'focus:border-yellow-500', 'sm:max-w-xs', 'btn', 'btn-primary', 'mr-2');
                downloadButton.href = url;
                downloadButton.download = itemRef.name; // Set download file name
                downloadButton.textContent = "Download";
                fileCardBody.appendChild(downloadButton);

                // Source button
                const sourceButton = document.createElement('a');
                sourceButton.classList.add('rounded', 'rounded-md', 'border', 'bg-gray-800', 'transition', 'duration-200', 'px-4', 'py-2', 'hover-bg-gray-700/50', 'border-gray-700', 'focus:ring-offset', 'text-gray-300', 'hover:border-yellow-500', 'focus:border-yellow-500', 'text-sm', 'focus:z-10', 'focus:outline-none', 'focus:ring-1', 'focus:border-yellow-500', 'sm:max-w-xs', 'btn', 'btn-primary', 'mr-2');
                sourceButton.href = url;
                sourceButton.textContent = "Source";
                fileCardBody.appendChild(sourceButton);

                // Add Enlarge button to open modal
                const enlargeButton = document.createElement('button');
                enlargeButton.classList.add('rounded', 'rounded-md', 'border', 'bg-gray-800', 'transition', 'duration-200', 'px-4', 'py-2', 'hover-bg-gray-700/50', 'border-gray-700', 'focus:ring-offset', 'text-gray-300', 'hover:border-yellow-500', 'focus:border-yellow-500', 'text-sm', 'focus:z-10', 'focus:outline-none', 'focus:ring-1', 'focus:border-yellow-500', 'sm:max-w-xs', 'btn', 'btn-primary', 'mr-2');
                enlargeButton.textContent = "Enlarge";
                enlargeButton.addEventListener("click", () => {
                    openModal(itemRef.name.endsWith(".mp4") || itemRef.name.endsWith(".mov") ? 'video' : (itemRef.name.endsWith(".txt") || itemRef.name.endsWith(".py")) ? 'text' : (itemRef.name.endsWith(".mp3")) ? 'audio' : 'image', url);
                });
                fileCardBody.appendChild(enlargeButton);

                // Add Share button to redirect to the share file with that file name
                const shareButton = document.createElement('a');
                shareButton.classList.add('rounded', 'rounded-md', 'border', 'bg-gray-800', 'transition', 'duration-200', 'px-4', 'py-2', 'hover-bg-gray-700/50', 'border-gray-700', 'focus:ring-offset', 'text-gray-300', 'hover:border-yellow-500', 'focus:border-yellow-500', 'text-sm', 'focus:z-10', 'focus:outline-none', 'focus:ring-1', 'focus:border-yellow-500', 'sm:max-w-xs', 'btn', 'btn-primary', 'mr-2');
                shareButton.href = `share.php?f=${itemRef.name}&from-dash=true`;
                shareButton.textContent = "Share";
                fileCardBody.appendChild(shareButton);

                // Add show text button for text files
              
                gridContainer.appendChild(fileItem);
            }).catch((error) => {
                console.error(error);
            });
        });
        // Append the grid container to the image container
        imageContainer.appendChild(gridContainer);
    }).catch((error) => {
        console.error(error);
    });
}
const totalFilesElement = document.getElementById('totalFiles');

function getTotalFiles() {
  firebase.storage().ref().listAll().then((res) => {
    const totalFiles = res.items.length;
    totalFilesElement.textContent = `Total Files: ${totalFiles}`;
  }).catch((error) => {
    console.error(error);
  });
}

getTotalFiles();

function openModal(type, url) {
    const modal = document.getElementById(type + '-modal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    if (type === 'image') {
        document.getElementById('modal-image').src = url;
    } else if (type === 'text') {
        fetch(url)
            .then(response => response.text())
            .then(data => document.getElementById('modal-text').textContent = data);
    } else if (type === 'video') {
        const video = document.getElementById('modal-video');
        video.src = url;
        video.load();
    } else if (type === 'audio') { // Corrected line
        const audioPlayer = document.getElementById('modal-audio');
        audioPlayer.src = url;
        audioPlayer.load(); // Load the audio file into the player
        audioPlayer.play(); // Start playing the audio
    }
}



const closeButtons = document.querySelectorAll('[data-modal-hide]');
closeButtons.forEach(button => {
  button.addEventListener('click', () => {
    const modalId = button.getAttribute('data-modal-hide');
    closeModal(modalId);
  });
});

function closeModal(modalId) {
  const modal = document.getElementById(modalId);
  modal.classList.remove('flex');
  modal.classList.add('hidden');
}

  getDownloadURLs();

  firebase.storage().ref().on('child_added', () => {
    getDownloadURLs();
    getTotalFiles();
  });
</script>
</body>
</html>
