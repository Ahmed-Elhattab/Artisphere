(function () {
  const fileInput = document.getElementById("importImage");
  const avatar = document.getElementById("productAvatar");

  if (!fileInput || !avatar) return;

  function showPlaceholder() {
    avatar.innerHTML = '<span class="product-avatar__placeholder">📷</span>';
  }

  function showImage(file) {
    // sécurité : uniquement images
    if (!file.type || !file.type.startsWith("image/")) {
      showPlaceholder();
      return;
    }

    const url = URL.createObjectURL(file);

    avatar.innerHTML = "";
    const img = document.createElement("img");
    img.src = url;
    img.alt = "Aperçu image produit";
    img.onload = () => URL.revokeObjectURL(url);
    img.onerror = () => showPlaceholder();

    avatar.appendChild(img);
  }

  fileInput.addEventListener("change", () => {
    const file = fileInput.files && fileInput.files[0];
    if (!file) {
      showPlaceholder();
      return;
    }
    showImage(file);
  });

  showPlaceholder();
})();