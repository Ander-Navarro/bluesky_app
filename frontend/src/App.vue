<template>
  <div id="app">
    <SearchBar @search="fetchPosts" />
    <PostList :posts="posts" :loading="loading" />
    <!-- Mostrar el botón solo si no está cargando y si hay un cursor -->
    <div v-if="cursor && !loading" class="load-more-container">
      <button @click="loadMorePosts" class="load-more-btn">Cargar más posts</button>
    </div>
  </div>
</template>

<script>
import SearchBar from './components/SearchBar.vue';
import PostList from './components/PostList.vue';

export default {
  components: {
    SearchBar,
    PostList,
  },
  data() {
    return {
      posts: [], // Almacenar los posts obtenidos
      loading: false, // Control de estado de carga
      cursor: null, // Indica si hay más posts para cargar
      query: '', // Consulta de búsqueda
      sortByLikes: null, // Ordenar por likes (si está definido)
      sortByDate: null, // Ordenar por fecha (si está definido)
      scrollPositionBefore: 0, // Almacenar la posición del scroll
    };
  },
  methods: {
    // Método para hacer la búsqueda inicial
    fetchPosts({ query, type, language, sortByLikes, sortByDate, mentions }) {
      console.log(`Buscando: ${query} (${type}) en ${language}`);
      this.loading = true;
      this.query = query;
      this.sortByLikes = sortByLikes;
      this.sortByDate = sortByDate;

      // Reinicializamos para la nueva búsqueda:
      this.posts = [];    // Limpiar resultados previos
      this.cursor = null; // Reiniciar cursor

      // Construir la URL de búsqueda (ajusta la ruta según la estructura en el servidor)
      let postsUrl = `/backend/index.php?q=${encodeURIComponent(query)}&language=${language}`;

      // Agregar parámetro de menciones si existe
      if (mentions) {
        postsUrl += `&mentions=${encodeURIComponent(mentions)}`;
      }
      // Agregar otros filtros si están definidos
      if (this.sortByLikes) postsUrl += `&sort=top`;
      if (this.sortByDate) postsUrl += `&sortAt=${this.sortByDate}`;

      console.log('URL de búsqueda:', postsUrl);

      fetch(postsUrl)
        .then(response => response.json())
        .then(data => {
          if (data.posts && Array.isArray(data.posts)) {
            this.posts = data.posts; // Asigna los nuevos 25 posts
            this.cursor = data.cursor; // Guarda el cursor para cargar más
            this.applyFilters();
          } else {
            console.error("Datos inválidos:", data);
            this.posts = [];
            this.cursor = null;
          }
        })
        .catch(error => console.error("Error en la búsqueda:", error))
        .finally(() => {
          this.loading = false;
        });
    },

    // Método para cargar más posts
    loadMorePosts() {
      if (!this.cursor || this.loading) return;

      // Guardar la posición del scroll antes de cargar más posts
      this.scrollPositionBefore = window.scrollY;
      console.log('Posición de scroll antes de cargar más posts:', this.scrollPositionBefore);

      this.loading = true;

      let postsUrl = `/backend/index.php?q=${encodeURIComponent(this.query)}&cursor=${this.cursor}`;
      console.log('URL de carga de más posts:', postsUrl);

      fetch(postsUrl)
        .then(response => response.json())
        .then(data => {
          if (data.posts && Array.isArray(data.posts)) {
            // Filtrar nuevos posts para evitar duplicados
            const newPosts = data.posts.filter(post => !this.posts.some(existingPost => existingPost.cid === post.cid));
            this.posts.push(...newPosts);
            this.cursor = data.cursor; // Actualizar cursor con el nuevo valor
            if (!this.cursor) {
              console.log('No hay más posts para cargar, ocultando el botón');
            }
            this.applyFilters();
          }
        })
        .catch(error => console.error("Error al cargar más posts:", error))
        .finally(() => {
          this.loading = false;
          this.$nextTick(() => {
            console.log('Restaurando la posición del scroll a:', this.scrollPositionBefore);
            window.scrollTo(0, this.scrollPositionBefore);
          });
        });
    },

    // Método para aplicar los filtros de likes y fecha
    applyFilters() {
      if (this.sortByLikes !== null) {
        this.posts.sort((a, b) => {
          return this.sortByLikes === 'desc'
            ? b.likeCount - a.likeCount
            : a.likeCount - b.likeCount;
        });
      }
      if (this.sortByDate !== null) {
        this.posts.sort((a, b) => {
          const dateA = new Date(a.record.createdAt);
          const dateB = new Date(b.record.createdAt);
          return this.sortByDate === 'desc'
            ? dateB - dateA
            : dateA - dateB;
        });
      }
    },
  },
};
</script>

<style scoped>
/* Estilo para el contenedor principal */
#app {
  display: flex;
  flex-direction: column;
  align-items: center;
  margin-top: 20px;
  width: 100%;
}

.loading {
  font-size: 18px;
  font-weight: bold;
  color: #007bff;
}

.load-more-container {
  margin-top: 20px;
}

.load-more-btn {
  padding: 10px 20px;
  font-size: 16px;
  color: white;
  background-color: #007bff;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}

.load-more-btn:hover {
  background-color: #0056b3;
}
</style>
