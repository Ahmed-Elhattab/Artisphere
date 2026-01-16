(function () {
  const fileInput = document.getElementById("eventImage");
  const avatar = document.getElementById("eventAvatar");
  const thumbs = document.getElementById("eventThumbs");

  if (!fileInput || !avatar || !thumbs) return;

  console.log("evenement_image_preview.js chargé ✅");

  const objectUrls = [];

  function cleanupUrls() {
    while (objectUrls.length) {
      URL.revokeObjectURL(objectUrls.pop());
    }
  }

  function showPlaceholder() {
    cleanupUrls();
    avatar.innerHTML = '<span class="event-avatar__placeholder">📷</span>';
    thumbs.innerHTML = "";
  }

  function setMainImage(file) {
    const url = URL.createObjectURL(file);
    objectUrls.push(url);

    avatar.innerHTML = "";

    const img = document.createElement("img");
    img.src = url;
    img.alt = "Aperçu image évènement";

    img.onerror = showPlaceholder;

    avatar.appendChild(img);
  }

  function render(files) {

    cleanupUrls();
    thumbs.innerHTML = "";

    const images = Array.from(files || [])
      .filter(f => f && f.type && f.type.startsWith("image/"));

    if (images.length === 0) {
      showPlaceholder();
      return;
    }

    // Image principale = première sélectionnée
    setMainImage(images[0]);

    // Miniatures
    images.forEach((file, index) => {

      const btn = document.createElement("button");
      btn.type = "button";
      btn.className = "thumb" + (index === 0 ? " is-active" : "");

      const url = URL.createObjectURL(file);
      objectUrls.push(url);

      const img = document.createElement("img");
      img.src = url;

      btn.appendChild(img);

      btn.addEventListener("click", () => {

        document.querySelectorAll("#eventThumbs .thumb")
          .forEach(t => t.classList.remove("is-active"));

        btn.classList.add("is-active");

        setMainImage(file);
      });

      thumbs.appendChild(btn);
    });
  }

  fileInput.addEventListener("change", () => {
    render(fileInput.files);
  });

  showPlaceholder();

})();