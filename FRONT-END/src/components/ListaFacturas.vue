<template>
  <div>
    <div style="background-color: #212529; display: flex; justify-content: center; align-items: center;">
      <th scope="col" style="display: flex; align-items: center;">
        <i class="bi bi-funnel-fill" style="color: white; font-size: 20px; margin-right: 7px;"></i>
        <label style="color: white; margin-right: 10px;">Estado:</label>

        <div class="btn-group">
          <select v-model="estadoFilter" id="estadoFilter" class="btn btn-light" style="font-weight: bold; font-size: 12px; margin-right: 20px; margin-left: 7px;">
            <option value="" style="font-weight: bold; font-size: 12px;">TODOS</option>
            <option value="TRAMITADA" style="font-weight: bold; font-size: 12px;">TRAMITADA</option>
            <option value="PENDIENTE POR AUTORIZAR" style="font-weight: bold; font-size: 12px;">PENDIENTE</option>
          </select>
        </div>

        <div class="btn-group">
          <i class="bi bi-calendar-check" style="color: white; font-size: 20px; margin-right: 7px;"></i>
          <label style="color: white; margin-right: 10px;">Fecha:</label>
          <input type="date" v-model="fechaSeleccionada" class="btn btn-light" style="font-weight: bold; font-size: 12px; margin-right: 20px;">
        </div>

        <div class="btn-group">
          <i class="bi bi-calendar2-week" style="color: white; font-size: 20px; margin-right: 7px;"></i>
          <label style="color: white; margin-right: 10px;">Rango de Fechas:</label>
          <input type="date" v-model="fechaInicio" class="btn btn-light" style="font-weight: bold; font-size: 12px; margin-right: 5px;">
          <input type="date" v-model="fechaFin" class="btn btn-light" style="font-weight: bold; font-size: 12px; margin-right: 20px;">
        </div>
      </th>
    </div>

    <table class="table table-dark table-striped">
      <thead>
        <tr>
          <th scope="col">Id</th>
          <th scope="col">Estado</th>
          <th scope="col">Fecha</th>
          <th scope="col">Nit</th>
          <th scope="col">Nombre</th>
          <th scope="col">Número</th>
          <th scope="col">Encargado</th>
          <th scope="col">Acción</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="Factura in facturasPaginadas" :key="Factura.Id">
          <td>{{ Factura.Id }}</td>
          <td :class="(Factura.Estado)">
            <span v-if="Factura.Estado === 'CORRECTA'">
              <i class="bi bi-check2-square" style="font-size: 27px; margin-right: 40px; color: #2271b3;"></i>
              CORRECTA
            </span>
            <span v-else-if="Factura.Estado === 'INCORRECTA'">
              <i class="bi bi-x-square" style="font-size: 22px; margin-right: 22px; color: red;"></i>
              INCORRECTA
            </span>
            <span v-else-if="Factura.Estado === 'PENDIENTE POR AUTORIZAR'">
              <i class="bi bi-hourglass-split" style="font-size: 24px; margin-right: 34px; color: yellow;"></i>
              PENDIENTE
            </span>
            <span v-else-if="Factura.Estado === 'TRAMITADA'">
              <i class="bi bi-check-square-fill" style="font-size: 24px; margin-right: 28px; color: green;"></i>
              TRAMITADA
            </span>
            <span v-else-if="Factura.Estado === 'CONTABILIZADA'">
              <i class="bi bi-cash-coin" style="font-size: 27px; margin-right: 1px; color: green;"></i>
              CONTABILIZADA
            </span>
            <span v-else-if="Factura.Estado === 'REASIGNADA'">
              <i class="bi bi-bootstrap-reboot" style="font-size: 27px; margin-right: 18px; color: orange;"></i>
              REASIGNADA
            </span>
            <span v-else>
              {{ Factura.Estado }}
            </span>
          </td>
          <td>{{ Factura.Fecha }}</td>
          <td>{{ Factura.Nit }}</td>
          <td>{{ Factura.Nombre }}</td>
          <td>{{ Factura.Numero }}</td>
          <td>{{ Factura.Encargado }}</td>
          <td>
            <router-link :to="{ name: 'VistaFactura', params: { Id: Factura.Id } }" class="btn btn-info" style="float:; color: black;">
              <i class="bi bi-pencil-square" style="color: black; font-size: 20px; margin-right: 7px;"></i>
              <i class="bi bi-eye-fill" style="color: black; font-size: 20px;"></i>
            </router-link>
          </td>
        </tr>
      </tbody>
    </table>

    <div class="pagination">
      <button @click="previousPage" :disabled="currentPage === 1" class="btn btn-light"><i class="bi bi-arrow-left-square" style="color: black;"></i></button>
      <span> Página {{ currentPage }} de {{ totalPages }} </span>
      <button @click="nextPage" :disabled="currentPage === totalPages" class="btn btn-light"><i class="bi bi-arrow-right-square" style="color: black;"></i></button>
    </div>

  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      facturas: [],
      estadoFilter: '',
      fechaInicio: '',
      fechaFin: '',
      fechaSeleccionada: '',
      itemsPerPage: 10,
      currentPage: 1,
    };
  },
  computed: {
    totalPages() {
      return Math.ceil(this.filteredFacturas.length / this.itemsPerPage);
    },
    startIndex() {
      return (this.currentPage - 1) * this.itemsPerPage;
    },
    endIndex() {
      return this.startIndex + this.itemsPerPage;
    },

    filteredFacturas() {
      return this.facturas.filter((factura) => {
        const estadoMatch = !this.estadoFilter || factura.Estado === this.estadoFilter;
        let fechaMatch = true;
        if (this.fechaSeleccionada) {
          fechaMatch = factura.Fecha === this.fechaSeleccionada;
        }
        if (this.fechaInicio && this.fechaFin) {
          fechaMatch = fechaMatch && factura.Fecha >= this.fechaInicio && factura.Fecha <= this.fechaFin;
        }
        return estadoMatch && fechaMatch;
      });
    },
    facturasPaginadas() {
      return this.filteredFacturas.slice(this.startIndex, this.endIndex);
    },
  },
  methods: {
    previousPage() {
      if (this.currentPage > 1) {
        this.currentPage--;
      }
    },
    nextPage() {
      if (this.currentPage < this.totalPages) {
        this.currentPage++;
      }
    },
  },
    mounted() {
      axios.get('http://localhost:8081/DatosFacturas/facturas.php?consultar')
           .then((respuesta)=>{
                this.facturas = respuesta.data;

           })
    }
};
</script>
<style scoped>
.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  margin-top: 20px; /* Ajusta según sea necesario */
  color: white;
}

.pagination button {
  margin: 0 20px; /* Ajusta el espaciado horizontal entre los botones */
  /* Puedes agregar estilos adicionales para los botones de paginación si lo necesitas */
}
</style>
