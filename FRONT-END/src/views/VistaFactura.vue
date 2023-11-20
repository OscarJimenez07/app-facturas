<template>
  <div class="factura-detalles">
    <header>
       <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
       <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
      <h2 style="color: white;">Detalles de la Factura</h2>
    </header>    
    <div class="row">  
      
      <div class="col-5"> 

      </div>

    <div class="col-md-3">
        <FacturaDetalle/>
      <div class="col-9 mx-auto text-center">
        <FacturaReasignar/>
      </div>
    </div>


      <div class="col-md-4">
        <facturaPdf/>   
      </div>

    </div>   
  </div>
</template>

<script>
import FacturaDetalle from '../components/FacturaDetalle.vue';
import FacturaPdf from '../components/FacturaPdf.vue';
import FacturaReasignar from '../components/FacturaReasignar.vue'
export default {
  components: {
    FacturaDetalle,
    FacturaPdf,
    FacturaReasignar
  }
};
</script>

<style scoped>
.factura-detalles { background-color: #272c31a9; padding: 20px; }
</style>


  
  
  