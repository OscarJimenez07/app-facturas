<template>
    <table class="table table-dark custom-table">
      <tbody>
        <tr>
          <td><strong>Archivo PDF:</strong></td>
        </tr>
        <tr>
          <td>
            <iframe :src="pdfUrl" width="100%" height="1000px"></iframe>
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
      pdfUrl: ""
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
    buscarPdf() {
      const adjunto = this.IdFactura(this.$route.params.Id)?.Adjunto;
      if (adjunto) {
        this.pdfUrl = `http://localhost:8081/DatosFacturas/archivos/${adjunto}/${adjunto}.pdf`;
      }
    },
    getFacturas() {
    axios.get('http://localhost:8081/DatosFacturas/facturas.php') 
      .then(response => {
        this.facturas = response.data;
        this.buscarPdf();
      })
  }
  },
  
};
</script>