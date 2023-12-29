<template>
 <div class="dashboard-app">
         <header class="dashboard-toolbar" style="background-color: #202124;" >
            <i class="bi bi-clipboard2-check-fill" style="font-size: 34px; margin-right: 5px; color: white;"></i>
            <span style="font-size: 17px; margin-right: 5px; color: white; ">Facturas Ardisa S.A.</span>
          </header>
    <div class="container py-5">
      <div class="row">
        <div style="width: 30%; margin-left: 23%;" class="mt-5 text-center">     
      <LoginSesion/> 
       </div>
      </div>
    </div>  
  </div>  
  </template>
  
<script>
  import LoginSesion from '../components/LoginSesion.vue';  
     export default {
         components: {
          LoginSesion
         },
         mounted(){
              console.log("%c¡Detente!","color:red;font-size:50px;font-weight:bold");
              console.log("Esta función del navegador está pensada SOLO para desarrolladores, estas siendo monitoreado.")
              console.log("App Web desarrollada por Sr. Oscar Jimenez")
              console.log("Ardisa S.A.")
         }        
     }
</script>

<style>
@media (min-width: 992px) {
  .dashboard-toolbar{margin-left: -20px;
                     min-height: 70px;
  }
}
.custom-login-card {
  border: 1px solid #343a40;
  border-radius: 8px;
  box-shadow: 0 7px 20px white;
  border: 1px solid #343a40;
  border-radius: 8px;
  box-shadow: 0 2px 10px white;
}

.custom-card-body {
  background-color: #68767d;
  color: #fff; 
  background-color: #202124;
  color: #fff;
  text-align: center;
  box-shadow: 0 2px 10px white;
}


.dashboard-toolbar {
    min-height: 70px;
    background-color: white;
    display: -webkit-box;
    display: -webkit-flex;
    display: -ms-flexbox;
    display: flex;
    -webkit-box-align: center;
    -webkit-align-items: center;
    -ms-flex-align: center;
    align-items: center;
    padding: 8px 27px;
    position: fixed;
    top: 0;
    right: 0;
    left: 0;
    z-index: 1000;
  border-bottom: 3px solid white;
  box-shadow: 0 1px 5px white;
}

</style>