document.addEventListener("DOMContentLoaded", () => {
  const fileInput = document.getElementById("eventImage");
  const avatar = document.getElementById("eventAvatar");

  if (!fileInput || !avatar) return;

  function resetAvatar() {
    avatar.innerHTML = '<span class="event-avatar__placeholder">📷</span>';
  }

  fileInput.addEventListener("change", () => {
    const file = fileInput.files && fileInput.files[0];
    if (!file || !file.type.startsWith("image/")) {
      resetAvatar();
      return;
    }

    const reader = new FileReader();
    reader.onload = () => {
      avatar.innerHTML = "";
      const img = document.createElement("img");
      img.src = reader.result;
      img.alt = "Aperçu image évènement";
      avatar.appendChild(img);
    };
    reader.readAsDataURL(file);
  });

  resetAvatar();
});