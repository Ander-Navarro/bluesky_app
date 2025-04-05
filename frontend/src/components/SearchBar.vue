<template>
  <div class="search-container">
    <!-- Fila 1: Campo de búsqueda y botón -->
    <div class="top-row">
      <input 
        v-model="searchQuery" 
        @keyup.enter="emitSearch"
        placeholder="Escribe aquí..."
        class="search-input"
      />
      <button @click="emitSearch" class="search-button">🔍 Buscar</button>
    </div>

    <!-- Fila 2: Filtros -->
    <div class="bottom-row">
      <select v-model="searchType" class="search-select">
        <option value="keyword">🔎 Palabra clave</option>
        <option value="hashtag"># Hashtag</option>
        <option value="mention">@ Mención</option>
      </select>

      <select v-model="selectedLanguage" class="language-select">
        <option value="">🌍 Todos los idiomas</option>
        <option value="en">🇬🇧 Inglés</option>
        <option value="es">🇪🇸 Español</option>
        <option value="fr">🇫🇷 Francés</option>
      </select>

      <!-- Botón filtro de fecha -->
      <button 
        @click="toggleSortByDate" 
        class="sort-button"
        :class="{ active: sortByDate }"
      >
        {{ sortByDate === 'desc' ? '📅 Más recientes' : (sortByDate === 'asc' ? '📆 Más antiguos' : '📅 Sin filtro de fecha') }}
      </button>

      <!-- Botón filtro de likes -->
      <button 
        @click="toggleSortByLikes" 
        class="sort-button"
        :class="{ active: sortByLikes, likes: true }"
      >
        {{ sortByLikes === 'desc' ? '👍 Más likes' : (sortByLikes === 'asc' ? '👎 Menos likes' : '👍 Sin filtro de likes') }}
      </button>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      searchQuery: "", // La consulta de búsqueda
      searchType: "keyword", // El tipo de búsqueda seleccionado (keyword, hashtag, mention)
      selectedLanguage: "", // Idioma seleccionado
      sortByLikes: null, // Filtro de likes
      sortByDate: null, // Filtro de fecha
    };
  },
  methods: {
   emitSearch() {
      let query = this.searchQuery.trim();
      if (!query) return;

      // Si el tipo de búsqueda es "hashtag" y la consulta no empieza con "#", se le agrega "#"
      if (this.searchType === "hashtag" && !query.startsWith("#")) {
        query = `#${query}`;
      } 
      // Si el tipo de búsqueda es "mention" y la consulta no empieza con "@", se le agrega ".bsky.social"
      else if (this.searchType === "mention") {
        // No añadimos @ al query
        query = query; // Lo dejamos tal cual
      }

      // Emitir los parámetros de búsqueda hacia el componente padre (App.vue)
      this.$emit("search", {
        query,
        type: this.searchType,
        language: this.selectedLanguage,
        sortByLikes: this.sortByLikes,
        sortByDate: this.sortByDate,
        mentions: this.searchType === "mention" ? `${query}.bsky.social` : null, // Solo si es de tipo mention, agregamos el parámetro mentions
      });
    },

    toggleSortByLikes() {
      // Lógica para alternar el filtro de orden de likes
      if (this.sortByLikes === null) {
        this.sortByLikes = 'desc';
        this.sortByDate = null;
      } else if (this.sortByLikes === 'desc') {
        this.sortByLikes = 'asc';
      } else {
        this.sortByLikes = null;
      }
      this.emitSearch();
    },

    toggleSortByDate() {
      // Lógica para alternar el filtro de orden de fechas
      if (this.sortByDate === null) {
        this.sortByDate = 'desc';
        this.sortByLikes = null;
      } else if (this.sortByDate === 'desc') {
        this.sortByDate = 'asc';
      } else {
        this.sortByDate = null;
      }
      this.emitSearch();
    },
  },
};
</script>

<style scoped>
.search-container {
  display: flex;
  flex-direction: column;
  gap: 16px;
  padding: 16px;
  background-color: #f8f9fa;
  border-radius: 12px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
  margin: 20px auto;
  max-width: 800px;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

/* Centrar contenido */
.top-row,
.bottom-row {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 10px;
  width: 100%;
}

/* Input de búsqueda */
.search-input {
  flex: 1 1 300px;
  padding: 5px 8px; /* Reducido el padding */
  font-size: 14px; /* Tamaño de fuente reducido */
  border: 2px solid #007bff;
  border-radius: 8px;
  background-color: #fff;
  color: #333;
  max-height:35px;
  min-width: 200px;
  max-width: 400px;
  box-sizing: border-box; /* Asegura que el padding no aumente el tamaño */
}

/* Botón de búsqueda */
.search-button {
  padding: 8px 16px;
  font-size: 14px;
  font-weight: 600;
  background-color: #007bff;
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.search-button:hover {
  background-color: #0056b3;
}

/* Selectores */
.search-select,
.language-select {
  min-width: 140px;
  padding: 8px 12px;
  font-size: 14px;
  border-radius: 8px;
  border: 1px solid #ccc;
  background-color: #fff;
  color: #333;
  appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg fill='gray' height='12' viewBox='0 0 24 24' width='12' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M7 10l5 5 5-5z'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 10px center;
  background-size: 12px;
  padding-right: 30px;
}

/* Botones de orden */
.sort-button {
  padding: 8px 14px;
  font-size: 13px;
  font-weight: 500;
  color: #333;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: background-color 0.3s ease;
  min-width: 140px;
}

.sort-button:hover {
  background-color: #0056b3;
  color: white;
}

/* Responsive */
@media (max-width: 600px) {
  .search-container {
    padding: 12px;
  }

  .search-input,
  .search-button,
  .search-select,
  .language-select,
  .sort-button {
    width: 100%;
    max-width: none;
  }

  .top-row,
  .bottom-row {
    flex-direction: column;
    align-items: stretch;
  }

  /* Ajuste del input en móvil */
  .search-input {
    padding: 5px 8px; /* Reducido el padding */
    font-size: 13px; /* Font size más pequeño */
    height: 28px; /* Reducir aún más la altura */
  }
}

/* Activos */
.sort-button.active {
  background-color: #007bff;
  color: white;
}

.sort-button.likes.active {
  background-color: #007bff;
  color: white;
}
</style>
