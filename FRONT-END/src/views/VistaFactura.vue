<template>
  <div class="factura-detalles">
    <header>
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
      <h3 style="color: black;   background-image: linear-gradient(to right, transparent, white 50%, transparent);
    background-size: 100% 1px; 
    background-repeat: repeat;
  min-width: 1px;"><strong>DETALLES DE LA FACTURA</strong></h3>
    </header>
    <p></p>
    <p></p>
    <div class="row">

       <div class="col-7" >      
        <FacturaCargar/>
      </div>

      <div class="col-5">
        <div class="border p-1 mb-1" style="background-color:#212529;"> 
        <FacturaDetalle/>
        <div class="col-9 mx-auto text-center">          
              <button class="btn btn-info" @click="openPdf" style="color:#212529;"><i class="bi bi-eye-fill" style="color:#212529;"></i> Ver Factura</button>   
          </div>
              <p></p>
              <p></p>
              <FacturaReasignar/>     
        </div>
        <FacturaLog/>
      </div> 
    </div>
    <div v-if="showPdf" class="pdf-popup">
      <div class="pdf-content"  >
        <button  class="btn btn-danger" @click="closePdf">X</button>
        <FacturaPdf/>
      </div>
    </div>
  </div>
</template>

<script>
import FacturaDetalle from '../components/FacturaDetalle.vue';
import FacturaPdf from '../components/FacturaPdf.vue';
import FacturaReasignar from '../components/FacturaReasignar.vue'
import FacturaCargar from '../components/FacturaCargar.vue'
import FacturaLog from '../components/FacturaLog.vue'

export default {
  components: {
    FacturaDetalle,
    FacturaPdf,
    FacturaReasignar,
    FacturaCargar,
    FacturaLog
  },
  data() {
    return {
      showPdf: false,
    };
  },
  methods: {
    openPdf() {
      this.showPdf = true;
    },
    closePdf() {
      this.showPdf = false;
    }
  },
  mounted(){
              console.log("%c¡Detente!","color:red;font-size:50px;font-weight:bold");
              console.log("Esta función del navegador está pensada SOLO para desarrolladores, estas siendo monitoreado.")
              console.log("App Web desarrollada por Sr. Oscar Jimenez")
              console.log("Ardisa S.A.")
         } 
};
</script>

<style scoped>
.pdf-popup {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
}

.pdf-content {
  background: grey;
  padding: 20px;
  width: 80%;
  height: 100%;
  max-width: 59%;
  max-height: 89%;
  overflow: auto;
  box-sizing: border-box;
}
</style>
