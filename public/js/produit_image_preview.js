(function () {
  const fileInput = document.getElementById("importImage");
  const avatar = document.getElementById("productAvatar");
  const thumbs = document.getElementById("productThumbs");

  if (!fileInput || !avatar || !thumbs) return;

  console.log("produit_image_preview.js chargé ✅");

  const objectUrls = [];
  function cleanupUrls() {
    while (objectUrls.length) URL.revokeObjectURL(objectUrls.pop());
  }

  function showPlaceholder() {
    cleanupUrls();
    avatar.innerHTML = '<span class="product-avatar__placeholder">📷</span>';
    thumbs.innerHTML = "";
  }

  function setMainFromFile(file) {
    const url = URL.createObjectURL(file);
    objectUrls.push(url);

    avatar.innerHTML = "";
    const img = document.createElement("img");
    img.src = url;
    img.alt = "Aperçu image produit";
    img.onload = () => {}; // url révoquée au cleanup global
    img.onerror = showPlaceholder;
    avatar.appendChild(img);
  }

  function render(files) {
    cleanupUrls();
    thumbs.innerHTML = "";

    const images = Array.from(files || []).filter(f => f && f.type && f.type.startsWith("image/"));
    if (images.length === 0) {
      showPlaceholder();
      return;
    }

    // image principale = première
    setMainFromFile(images[0]);

    // miniatures
    images.forEach((file, idx) => {
      const btn = document.createElement("button");
      btn.type = "button";
      btn.className = "thumb" + (idx === 0 ? " is-active" : "");

      const url = URL.createObjectURL(file);
      objectUrls.push(url);

      const img = document.createElement("img");
      img.src = url;
      img.alt = "";
      btn.appendChild(img);

      btn.addEventListener("click", () => {
        thumbs.querySelectorAll(".thumb").forEach(b => b.classList.remove("is-active"));
        btn.classList.add("is-active");
        setMainFromFile(file);
      });

      thumbs.appendChild(btn);
    });
  }

  fileInput.addEventListener("change", () => {
    render(fileInput.files);
  });

  showPlaceholder();
})();