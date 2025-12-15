(function () {
  const pwd = document.getElementById("password");
  const pwd2 = document.getElementById("password_confirm");
  const box = document.getElementById("password-rules-box");

  if (!pwd || !pwd2 || !box) return;

  const ruleLength = document.getElementById("rule-length");
  const ruleMatch  = document.getElementById("rule-match");

  function setRule(el, ok, okText, badText) {
    el.classList.remove("good", "bad");
    if (ok) {
      el.classList.add("good");
      el.textContent = "✔ " + okText;
    } else {
      el.classList.add("bad");
      el.textContent = "✖ " + badText;
    }
  }

  function update() {
    const v1 = pwd.value || "";
    const v2 = pwd2.value || "";

    // Affiche la box dès qu'on commence à taper dans l'un des deux champs
    const shouldShow = v1.length > 0 || v2.length > 0 || document.activeElement === pwd || document.activeElement === pwd2;
    box.hidden = !shouldShow;

    setRule(
      ruleLength,
      v1.length >= 4,
      "Au moins 4 caractères",
      "Au moins 4 caractères"
    );

    setRule(
      ruleMatch,
      v1.length > 0 && v2.length > 0 && v1 === v2,
      "Les deux mots de passe sont identiques",
      "Les deux mots de passe sont identiques"
    );
  }

  // Events
  ["input", "focus", "blur"].forEach(evt => {
    pwd.addEventListener(evt, update);
    pwd2.addEventListener(evt, update);
  });

  update();
})();