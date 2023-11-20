<template>
        <table class="table table-dark custom-table">
          <tbody>
            <tr>
              <td><strong>Encargado de la Factura:</strong></td>
              <td>{{ IdFactura($route.params.Id)?.Encargado }}</td>
            </tr>
            <tr>
              <td><strong>Nit del Proveedor:</strong></td>
              <td>{{ IdFactura($route.params.Id)?.Nit }}</td>
            </tr>
            <tr>
              <td><strong>Nombre del Proveedor:</strong></td>
              <td>{{ IdFactura($route.params.Id)?.Nombre }}</td>
            </tr>
            <tr>
              <td><strong>Número de Factura:</strong></td>
              <td>{{ IdFactura($route.params.Id)?.Numero }}</td>
            </tr>
            <tr>
              <td><strong>Estado de la Factura:</strong></td>
              <td>{{ IdFactura($route.params.Id)?.Estado }}</td>
            </tr>
          </tbody>
        </table>
</template>

<script>
import axios from 'axios';
export default {
  data() {
    return {
      facturas: []
    }
  },
  created:function() {
    this.getFacturas();
  } ,
  methods: {
    IdFactura(Id) {
      if (this.facturas.length > 0) {
        return this.facturas.find(factura => factura.Id === Id);
      }
    },
    getFacturas() {
    axios.get('http://localhost:8081/DatosFacturas/facturas.php') 
      .then(response => {
        this.facturas = response.data;
      })
  }
  },
  
};
</script>

