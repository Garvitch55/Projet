document.addEventListener("DOMContentLoaded", () => {
  const rueInput = document.getElementById("rue");
  const cpInput = document.getElementById("cp");
  const villeInput = document.getElementById("ville");
  const suggestionsBox = document.getElementById("suggestions");

  // Positionner suggestions juste sous le champ (le parent doit être position:relative)
  const parent = rueInput.parentElement;
  parent.style.position = 'relative';

  rueInput.addEventListener("input", async () => {
    const query = rueInput.value.trim();

    // Si moins de 3 caractères, on vide la liste
    if (query.length < 3) {
      suggestionsBox.innerHTML = "";
      return;
    }

    const url = `https://api-adresse.data.gouv.fr/search/?q=${encodeURIComponent(query)}&limit=5`;
    try {
      const response = await fetch(url);
      if (!response.ok) throw new Error("Erreur API");
      const data = await response.json();

      suggestionsBox.innerHTML = "";

      if (!data.features || data.features.length === 0) return;

      data.features.forEach(feature => {
        const props = feature.properties;
        const suggestionItem = document.createElement("div");
        suggestionItem.className = "suggestion";

        suggestionItem.textContent = `${props.name || ""}, ${props.postcode || ""} ${props.city || ""}`;

        suggestionItem.addEventListener("click", () => {
          rueInput.value = props.name || "";
          cpInput.value = props.postcode || "";
          villeInput.value = props.city || "";
          suggestionsBox.innerHTML = "";
        });

        suggestionsBox.appendChild(suggestionItem);
      });

    } catch (error) {
      console.error("Erreur lors de la récupération des adresses :", error);
      suggestionsBox.innerHTML = "";
    }
  });

  // Optionnel : fermer suggestions si clic hors du champ
  document.addEventListener("click", (e) => {
    if (!parent.contains(e.target)) {
      suggestionsBox.innerHTML = "";
    }
  });
});