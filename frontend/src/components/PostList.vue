<template>
  <div class="post-list-container">
    <!-- Cargando... -->
    <div v-if="loading" class="loading">
      ⏳ Cargando posts...
    </div>

    <!-- Lista de posts -->
    <div v-if="!loading && posts.length > 0" class="post-list">
      <div v-for="(post, index) in posts" :key="index" class="post-card">
        <div class="card-header">
          <!-- Avatar del autor -->
          <img :src="post.author.avatar" alt="Avatar" class="avatar">
          <div class="author-info">
            <p class="author-name">{{ post.author.displayName || post.author.handle }}</p>
            <p class="post-date">{{ formatDate(post.record.createdAt) }}</p>
          </div>
        </div>

        <!-- Contenido del post -->
        <div class="card-content">
          <p>{{ post.record.text || '📌 No hay contenido disponible' }}</p>

          <!-- Etiquetas (si las hay) -->
          <div v-if="post.record.tags && post.record.tags.length > 0" class="tags">
            <span v-for="(tag, index) in post.record.tags" :key="index" class="tag">#{{ tag }}</span>
          </div>
        </div>

        <!-- Interacciones: Solo Likes -->
        <div class="interactions">
          <div class="likes">
            ❤️ <strong>{{ post.likeCount || 0 }} Likes</strong>
          </div>
        </div>
      </div>
    </div>

    <!-- Si no hay posts -->
    <div v-if="!loading && posts.length === 0" class="no-results">
      No se encontraron posts.
    </div>
  </div>
</template>

<script>
export default {
  props: {
    posts: Array,
    loading: Boolean,
  },
  methods: {
    formatDate(dateString) {
      const date = new Date(dateString);
      return `${date.toLocaleDateString()} ${date.toLocaleTimeString()}`;
    },
  },
};
</script>

<style scoped>
/* Contenedor general */
.post-list-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  margin-top: 20px;
}

/* Mensaje de carga */
.loading {
  font-size: 18px;
  font-weight: bold;
  color: #007bff;
}

/* Lista de posts */
.post-list {
  width: 90%;
  max-width: 800px;
  margin-top: 20px;
}

/* Tarjeta de post */
.post-card {
  background-color: white;
  padding: 20px;
  margin-bottom: 15px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  border-radius: 12px;
  transition: transform 0.3s ease;
  border-left: 5px solid #007bff; /* Barra de color para la tarjeta */
}

.post-card:hover {
  transform: translateY(-5px);
}

/* Cabecera del post */
.card-header {
  display: flex;
  align-items: center;
  margin-bottom: 10px;
}

.avatar {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  border: 2px solid #007bff;
  margin-right: 15px;
}

.author-info {
  display: flex;
  flex-direction: column;
}

.author-name {
  font-weight: bold;
  font-size: 16px;
  color: #333;
}

.post-date {
  font-size: 14px;
  color: #888;
}

/* Contenido del post */
.card-content p {
  font-size: 16px;
  color: #333;
  line-height: 1.4;
  margin-top: 10px;
}

/* Estilos para las etiquetas */
.tags {
  display: flex;
  flex-wrap: wrap;
  margin-top: 10px;
}

.tag {
  background-color: #f1f1f1;
  color: #007bff;
  padding: 5px 10px;
  margin: 5px;
  border-radius: 15px;
  font-size: 14px;
  font-weight: bold;
}

/* Estilo para las interacciones (Likes) */
.interactions {
  display: flex;
  justify-content: flex-end; /* Alinea las interacciones a la derecha */
  margin-top: 10px;
}

.likes {
  font-size: 14px;
  font-weight: bold;
  color: #007bff; /* Color para los likes */
}
</style>
