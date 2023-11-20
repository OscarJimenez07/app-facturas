<template>
  <table class="table table-dark table-striped">
    <thead>
      <tr>
        <th scope="col">Id</th>
        <th scope="col">Estado</th>
        <th scope="col">Nit del Proveedor</th>
        <th scope="col">Nombre del Proveedor</th>
        <th scope="col">Número de Factura</th>
        <th scope="col">Encargado Asignado</th>
        <th scope="col">Adjunto</th>
        <th scope="col">Acción</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="Factura in facturas" :key="Factura.Id">
        <td>{{ Factura.Id }}</td>
        <td :class="getColor(Factura.Estado)">{{ Factura.Estado }}</td>
        <td>{{ Factura.Nit }}</td>
        <td>{{ Factura.Nombre }}</td>
        <td>{{ Factura.Numero }}</td>   
        <td>{{ Factura.Encargado }}</td>     
        <td><i class="bi bi-filetype-pdf" style="font-size:25px"></i> {{ Factura.Adjunto }}</td>      
        <td>
          <router-link :to="{ name: 'VistaFactura', params: { Id: Factura.Id } }" class="btn btn-info" style="float:; color: black;">
            <i class="bi bi-pencil-square" style="color: black; font-size: 20px;"></i> Ver
          </router-link>
        </td>
      </tr>
    </tbody>
  </table>
</template>

<script>
import axios from 'axios';
export default {
  data() {
    return {
      facturas: [],
    };
  },
  mounted() {
    axios.get('http://localhost:8081/DatosFacturas/facturas.php') 
      .then(response => {
        this.facturas = response.data;
      })
  },
  methods: {
    getColor(estado) {
      return estado === 'CORRECTA' ? 'green-background' : estado === 'INCORRECTA' ? 'red-background' : '';
    },
  },
};
</script>

<style scoped>
.green-background {
  background-color: green;
  width: 100%;
  height: 30%; 
  display: inline-block;
}

.red-background {
  background-color: red;
  width: 100%; 
  height: 30%;
  display: inline-block; 
}
</style>
