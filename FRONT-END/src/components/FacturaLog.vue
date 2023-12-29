<template>
<div class="border" style="background-color:#212529;">
    <label style="color: white;"><strong>LOG FACTURA</strong></label>
  <table class="table table-striped table-dark"> 
    <thead>
        <tr>
            <th>Fecha & Hora</th>
            <th>Encargado</th>
            <th>Estado</th>
            <th>Envió</th>
        </tr>
    </thead>    
    <tbody>
          <tr v-for="Log in Logs" :key="Log.Id">
              <td>{{ Log.Fecha_Hora }}</td>
              <td>{{ Log.Encargado }}</td>
              <td>{{ Log.Estado }}</td>
              <td>{{ Log.Modifico }}</td>
            </tr>
    </tbody>
  </table>
</div>
</template>

<script>
export default {
  data() {
    return {
      Logs : []
    }
  },
  created:function (){
    this.getLogs();
  },
  methods: {
    getLogs(){
      fetch ('http://localhost:8081/DatosFacturas/log.php?Id=' + this.$route.params.Id).then((respuesta) => respuesta.json())
      .then((datosRespuesta) => {
         this.Logs = datosRespuesta;
    })
 }
  }

}
</script>

