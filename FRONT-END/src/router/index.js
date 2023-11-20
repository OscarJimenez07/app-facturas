import { createRouter, createWebHistory } from 'vue-router';
import Lista from '../views/VistaLista.vue';
import Factura from '../views/VistaFactura.vue';
import Login from '../views/VistaLogin.vue';

const ROLES = {
  ADMIN: '1',
  USER: '2',
};

const routes = [
  {
    path: '/Lista',
    name: 'VistaLista',
    component: Lista,
    meta: { requiresAuth: true, requiredRoles: [ROLES.ADMIN, ROLES.USER] },
  },
  {
    path: '/Factura/:Id',
    name: 'VistaFactura',
    component: Factura,
    meta: { requiresAuth: true, requiredRoles: [ROLES.ADMIN, ROLES.USER] },
  },
  {
    path: '/Login',
    name: 'VistaLogin',
    component: Login,
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach((to, from, next) => {
  if (to.matched.some((record) => record.meta.requiresAuth)) {
    if (!sessionStorage.getItem('loggedin')) {
      // Almacena la ruta a la que intentó acceder antes de redirigir al inicio de sesión
      sessionStorage.setItem('redirectPath', to.fullPath);
      next('/Login');
    } else {
      const userRole = sessionStorage.getItem('userRole');
      const requiredRoles = to.meta.requiredRoles;
      if (!userRole || !requiredRoles.includes(userRole)) {
        const errorMessage = 'No tienes permisos para acceder a este módulo';
        alert(errorMessage);
      } else {
        next();
      }
    }
  } else {
    next();
  }
});

export default router;
