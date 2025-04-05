<template>
  <div class="filter-container">
    <label for="language">Idioma:</label>
    <select v-model="filters.language">
      <option value="">Cualquiera</option>
      <option value="en">Inglés</option>
      <option value="es">Español</option>
      <option value="pt">Portugués</option>
    </select>

    <label for="start_date">Fecha de inicio:</label>
    <input type="date" v-model="filters.start_date" />

    <label for="end_date">Fecha de fin:</label>
    <input type="date" v-model="filters.end_date" />

    <button @click="applyFilters" class="apply-filters-btn">Aplicar filtros</button>
  </div>
</template>

<script>
export default {
  data() {
    return {
      filters: {
        language: '',
        start_date: '',
        end_date: ''
      }
    };
  },
  methods: {
    applyFilters() {
      this.$emit('apply-filters', this.filters);
    }
  }
};
</script>

<style scoped>
.filter-container {
  display: flex;
  gap: 15px;
  justify-content: center;
  margin-bottom: 20px;
}

label {
  font-size: 16px;
}

select, input[type="date"] {
  padding: 5px;
  font-size: 16px;
  border-radius: 4px;
}

.apply-filters-btn {
  padding: 8px 15px;
  background-color: #28a745;
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
}

.apply-filters-btn:hover {
  background-color: #218838;
}
</style>
