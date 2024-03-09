<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Shared File</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body, html {
      margin: 0;
      padding: 0;
      height: 100%;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    #shared-file-container {
      max-width: 100%;
      max-height: 100%;
    }

    #shared-file-container img,
    #shared-file-container video {
      max-width: 100%;
      max-height: 100%;
      display: block;
      margin: auto;
    }

    #shared-file-container pre {
      overflow-x: auto;
      white-space: pre-wrap;
      word-wrap: break-word;
    }
  </style>
</head>
<body class="bg-gray-900">
  <div id="shared-file-container"></div>
  <script src="https://www.gstatic.com/firebasejs/8.9.1/firebase-app.js"></script>
  <script src="https://www.gstatic.com/firebasejs/8.9.1/firebase-storage.js"></script>

  <script>
    const firebaseConfig = {
      
    };
    firebase.initializeApp(firebaseConfig);

    const params = new URLSearchParams(window.location.search);
    const fileName = params.get('f');
    const storageRef = firebase.storage().ref();
    const fileRef = storageRef.child(fileName);

    fileRef.getDownloadURL().then((url) => {
  let sharedFileElement;
  if (fileName.endsWith('.mp4')) {
    sharedFileElement = document.createElement('video');
    sharedFileElement.controls = true;
    sharedFileElement.src = url;
    sharedFileElement.type = 'video/mp4';
  } else if (fileName.endsWith('.txt')) {
    window.location.href = url;
  } else if (fileName.endsWith('.mp3')) { // Support for music files
    sharedFileElement = document.createElement('audio');
    sharedFileElement.controls = true;
    sharedFileElement.src = url;
    sharedFileElement.type = 'audio/mp3';
  } else {
    sharedFileElement = document.createElement('img');
    sharedFileElement.src = url;
    sharedFileElement.alt = fileName;
  }
  if (sharedFileElement) {
    const sharedFileContainer = document.getElementById('shared-file-container');
    sharedFileContainer.appendChild(sharedFileElement);
    
    if (params.get('from-dash') === 'true') {
      navigator.clipboard.writeText(url)
        .then(() => {
          console.log('URL copied to clipboard');
        })
        .catch((error) => {
          console.error('Failed to copy URL: ', error);
        });
    }
  }
}).catch((error) => {
  console.error(error);
});
  </script>
</body>
</html>
