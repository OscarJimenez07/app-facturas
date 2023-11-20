<template>
  <div class="table-container">
    <div class="row"> 
      <div class="col-md-12">
        <h2 class="text-center"  style="font-size: 20px; margin-right: 5px; background-color: #202124; color: white; padding: 10px;">
          <i class="bi bi-send-arrow-up" style="font-size: 25px; margin-left: 5px; color:white "></i> Facturas </h2>
        <ListaFacturas/>           
    </div>     
   </div>
  </div>
</template>

<script>
import ListaFacturas from '@/components/ListaFacturas.vue';
export default {
  components: {
    ListaFacturas
  }
}
</script>

