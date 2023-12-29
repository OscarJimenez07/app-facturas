<template>
  <div>
    <label style="color: white;"><strong>INFORMACIÓN FACTURA</strong></label>
    <table class="table table-dark custom-table">
      <tbody>
        <tr>
          <th>Encargado de la Factura:</th>
          <td>{{ facturas.length > 0 ? facturas[0].Encargado : 'No hay datos' }}</td>
        </tr>
        <tr>
          <th>Monto Disponible Encargado:</th>
          <td>$ {{ formatNumber(monto.length > 0 ? monto[0].Cupo : 'No hay datos') }}</td>
        </tr>
        <tr>
          <th>Nit del Proveedor:</th>
          <td>{{ facturas.length > 0 ? facturas[0].Nit : 'No hay datos' }}</td>
        </tr>
        <tr>
          <th>Nombre del Proveedor:</th>
          <td>{{ facturas.length > 0 ? facturas[0].Nombre : 'No hay datos' }}</td>
        </tr>
        <tr>
          <th>Número de Factura:</th>
          <td>{{ facturas.length > 0 ? facturas[0].Numero : 'No hay datos' }}</td>
        </tr>
        <tr>
          <th>Estado de la Factura:</th>
          <td>{{ facturas.length > 0 ? facturas[0].Estado : 'No hay datos' }}</td>
        </tr>
        <tr>
          <th>Valor de la Factura:</th>
          <td>$ {{ formatNumber( facturas.length > 0 ? facturas[0].Valor : 'No hay datos') }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>

export default {
  data() {
    return {
      facturas: [],
      valor: [],
      monto: [] 
    };
  },
  created: function() {
    this.getFacturas();
    this.getMonto();
  
  },
  methods: {
    getFacturas(){
      fetch('http://localhost:8081/DatosFacturas/facturas.php?consultarFactura=' + this.$route.params.Id)
           .then((respuesta) => respuesta.json())
           .then((datosRespuesta) => {
              this.facturas = datosRespuesta
           })
 }, 
    getMonto(){
      const usuario = sessionStorage.getItem('username');
      fetch ('http://localhost:8081/DatosFacturas/encargados.php?Encargado=' + usuario)
        .then((respuesta) => respuesta.json())
        .then((datosRespuesta) => {
          this.monto = datosRespuesta;
    })
 },
    formatNumber(value) {
      const parts = value.toString().split(".");
      parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");
      return parts.join(",");
    },
    
    
  },

  
};
</script>


